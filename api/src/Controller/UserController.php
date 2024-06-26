<?php

namespace App\Controller;

use App\Entity\RatingRange;
use App\Entity\User;
use Handy\Context;
use Handy\Controller\BaseController;
use Handy\Http\JsonResponse;
use Handy\Http\Request;
use Handy\Http\Response;
use Handy\ORM\QueryBuilder;
use Handy\Routing\Attribute\Route;
use Handy\Security\Exception\ForbiddenException;

class UserController extends BaseController
{
    public const USERS_PER_PAGE = 10;

    #[Route(name: "user_create", path: "/users", methods: [Request::METHOD_POST])]
    public function register(): Response
    {
        $body = $this->request->getContent();

        if (!isset($body["username"],
            $body["firstName"],
            $body["lastName"],
            $body["email"],
            $body["password"])) {
            return new JsonResponse(["message" => "Missing fields in request body"], 400);
        }

        $username = trim($body["username"]);
        $email = trim($body["email"]);
        $password = trim($body["password"]);
        $firstName = trim($body["firstName"]);
        $lastName = trim($body["lastName"]);

        if (!$this->validateEmail($email) ||
            !$this->validateName($firstName) ||
            !$this->validateName($lastName) ||
            !$this->validatePassword($password) ||
            !$this->validateUsername($username)) {
            return new JsonResponse(["message" => "Invalid data"], 400);
        }

        $hashedPassword = hash_hmac('sha256', $password, $_ENV["PASSWORD_HASH_KEY"]);

        $repo = $this->em->getRepository(User::class);
        if (!empty($repo->findBy(["username" => $username]))) {
            return new JsonResponse([
                "message" => "The user with this username already exists"
            ], 400);
        }
        if (!empty($repo->findBy(["email" => $email]))) {
            return new JsonResponse([
                "message" => "The user with this email already exists"
            ], 400);
        }

        $user = new User();

        $user->setEmail($email)
            ->setFirstName($firstName)
            ->setUserName($username)
            ->setLastName($lastName)
            ->setPassword($hashedPassword)
            ->setRating(100)
            ->setRole(User::ROLE_USER);

        $this->em->persist($user);
        $this->em->flush();

        $user = $repo->findOneBy(["email" => $email]);

        return new JsonResponse($user, 201);
    }

    #[Route(name: "get_all_users", path: "/users", methods: [Request::METHOD_GET], roles: User::ROLE_USER_OR_ADMIN)]
    public function getAll(): Response
    {
        $query = $this->request->getQuery();

        [
            $limit,
            $offset
        ] = $this->pagination(self::USERS_PER_PAGE);

        $qb = new QueryBuilder();
        $qb->from("user");

        if (isset($query["name"])) {
            $qb->andWhere("(user.username LIKE :name1 OR user.first_name LIKE :name2 OR user.last_name LIKE :name3)")
                ->setParam([
                    "name1" => "%" . $query["name"] . "%",
                    "name2" => "%" . $query["name"] . "%",
                    "name3" => "%" . $query["name"] . "%"
                ]);
        }

        if (isset($query["ratingMin"])) {
            $qb->andWhere("user.rating >= :ratingMin")->setParam([
                "ratingMin" => $query["ratingMin"]
            ]);
        }

        if (isset($query["ratingMax"])) {
            $qb->andWhere("user.rating <= :ratingMax")->setParam([
                "ratingMax" => $query["ratingMax"]
            ]);
        }

        $orderBy = [];

        if (isset($query["orderBy"]) && in_array($query["orderBy"], [
                "id",
                "role",
                "email",
                "username",
                "first_name",
                "last_name",
                "rating"
            ])) {
            $order = @$query["order"] === "desc" ? "DESC" : "ASC";
            $orderBy = [
                [
                    "user." . $query["orderBy"],
                    $order
                ]
            ];
        }

        if (isset($query["friend"])){
            $userId = Context::$security->getData()->id;
            $selectA = "SELECT fp.sender_id FROM friend_pair AS fp WHERE fp.receiver_id = :user_id1 AND fp.accepted = TRUE";
            $selectB = "SELECT fp.receiver_id FROM friend_pair fp WHERE fp.sender_id = :user_id2 AND fp.accepted = TRUE";
            $qb->andWhere("user.id " . ($query["friend"] === "true" ? "" : "NOT ") . "IN ($selectA UNION $selectB)")
                ->setParam([
                    "user_id1" => $userId,
                    "user_id2" => $userId
                ]);
        }

        if (isset($query["request"])){
            $userId = Context::$security->getData()->id;
            $select = "SELECT fp.sender_id FROM friend_pair fp WHERE fp.receiver_id = :user_id3 AND fp.accepted = FALSE";
            $qb->andWhere("user.id " . ($query["request"] === "true" ? "" : "NOT ") . "IN ($select)")
                ->andWhere("user.id <> :user_id4")
                ->setParam([
                    "user_id3" => $userId,
                    "user_id4" => $userId,
                ]);
        }

        if (isset($query["pending"])){
            $userId = Context::$security->getData()->id;
            $select = "SELECT fp.receiver_id FROM friend_pair fp WHERE fp.sender_id = :user_id5 AND fp.accepted = FALSE";
            $qb->andWhere("user.id " . ($query["request"] === "true" ? "" : "NOT ") . "IN ($select)")
                ->andWhere("user.id <> :user_id6")
                ->setParam([
                    "user_id5" => $userId,
                    "user_id6" => $userId,
                ]);
        }

        $qb->orderBy($orderBy);

        $count = @(int)Context::$connection->execute($qb->select(["COUNT(id)"])->getQuery())[0][0];
        $users = Context::$connection->execute($qb->select(User::class)->limit($limit)->offset($offset)->getQuery(), User::class);

        $ratingRangeRepo = $this->em->getRepository(RatingRange::class);
        $users = array_map(function ($user) use ($ratingRangeRepo) {
            $ratingRange = current($ratingRangeRepo->findBy(["min_rating" => "<= " . $user->getRating()], orderBy: [
                [
                    "min_rating",
                    "DESC"
                ]
            ]));
            return [
                ...$user->jsonSerialize(),
                "ratingTitle" => $ratingRange ? $ratingRange->getTitle() : null
            ];
        }, $users);
        return new JsonResponse([
            'count' => $count,
            'pagesCount' => ceil($count / self::USERS_PER_PAGE),
            "users"      => $users
        ], 200);
    }

    #[Route(name: "get_user_by_id", path: "/users/{id}", methods: [Request::METHOD_GET])]
    public function getByID(int $id): Response
    {
        $userRepo = $this->em->getRepository(User::class);
        $user = $userRepo->findOneBy(["id" => $id]);
        if (empty($user)) {
            return new JsonResponse(["message" => "User not found"], 404);
        }
        $ratingRangeRepo = $this->em->getRepository(RatingRange::class);
        $ratingRange = current($ratingRangeRepo->findBy(["min_rating" => "<= " . $user->getRating()], orderBy: [
            [
                "min_rating",
                "DESC"
            ]
        ]));
        return new JsonResponse([
            'user' => [
                ...$user->jsonSerialize(),
                "ratingTitle" => $ratingRange ? $ratingRange->getTitle() : null
            ]
        ], 200);
    }

    #[Route(name: "patch_user", path: "/users/{id}", methods: [Request::METHOD_PATCH], roles: User::ROLE_USER_OR_ADMIN)]
    public function patchUser(int $id): Response
    {
        if (Context::$security->getData()->id !== $id) {
            return new JsonResponse(["message" => "Only profile owner can change it"], 403);
        }

        $repo = $this->em->getRepository(User::class);
        /** @var User $user */
        $user = $repo->findOneBy(["id" => $id]);
        if (empty($user)) {
            return new JsonResponse(["message" => "User not found"], 404);
        }

        $data = $this->request->getContent() ?? [];

        $error = null;
        if (isset($data["oldPassword"], $data["newPassword"])) {
            $oldHashedPassword = hash_hmac('sha256', $data["oldPassword"], $_ENV["PASSWORD_HASH_KEY"]);
            if ($oldHashedPassword !== $user->getPassword()) {
                $error = "Invalid password";
            } else if (!$this->validatePassword($data["newPassword"])) {
                $error = "Invalid new password";
            } else {
                $user->setPassword(hash_hmac('sha256', $data["newPassword"], $_ENV["PASSWORD_HASH_KEY"]));
            }
        } else if (isset($data["username"])) {
            if (!empty($repo->findBy(["username" => $data["username"]]))) {
                $error = "The user with this username already exists";
            } else if (!$this->validateUsername($data["username"])) {
                $error = "Invalid username";
            }
        } else if (isset($data["email"])) {
            if (!empty($repo->findBy(["email" => $data["email"]]))) {
                $error = "The user with this email already exists";
            } else if (!$this->validateEmail($data["email"])) {
                $error = "Invalid email";
            }
        } else if (isset($data["firstName"]) && !$this->validateName($data["firstName"])) {
            $error = "Invalid first name";
        } else if (isset($data["lastName"]) && !$this->validateName($data["lastName"])) {
            $error = "Invalid last name";
        }

        if ($error !== null) {
            return new JsonResponse([
                "message" => $error
            ], 400);
        }

        if (isset($data["profilePic"]) && $data["profilePic"] === "REMOVE") {
            if (is_file($_SERVER['DOCUMENT_ROOT'] . "/" . $user->getProfilePic())) {
                unlink($_SERVER['DOCUMENT_ROOT'] . "/" . $user->getProfilePic());
            }
            $user->setProfilePic(null);
        }

        unset($data["id"], $data["hashedPassword"], $data["rating"], $data["role"], $data["profilePic"]);
        $user->fromArray($data);
        $this->em->persist($user);
        $this->em->flush();

        return new JsonResponse($user, 201);
    }

    #[Route(name: "patch_user_pic", path: "/users/{id}/pic", methods: [Request::METHOD_POST], roles: User::ROLE_USER_OR_ADMIN)]
    public function patchUserPic(int $id): Response
    {
        if (Context::$security->getData()->id !== $id) {
            throw new ForbiddenException("Only profile owner can change it");
        }

        $files = $this->request->getFiles();
        if (!$files || !isset($files["pic"])) {
            return new JsonResponse(["message" => "File is not provided"], 400);
        }

        if ($_FILES["pic"]["error"] > 0) {
            return new JsonResponse(["message" => "Error uploading the file"], 409);
        }

        $split = explode(".", $_FILES["pic"]["name"]);
        $fileExt = end($split);
        if (!in_array($fileExt, [
            "png",
            "jpg",
            "jpeg"
        ])) {
            return new JsonResponse(["message" => "Unsupported file extension"]);
        }

        $repo = $this->em->getRepository(User::class);
        /** @var User $user */
        $user = $repo->findOneBy(["id" => $id]);
        if (empty($user)) {
            return new JsonResponse(["message" => "User not found"], 404);
        }

        $fileName = "profile-pic-" . $id . ".png";

        if (!is_dir($_SERVER['DOCUMENT_ROOT'] . "/img/")) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . "/img/", 0777, true);
        }

        $uploadName = "img/" . strtolower($fileName);
        $uploadName = preg_replace('/\s+/', '-', $uploadName);

        if ($this->resizeImage($_FILES["pic"]["tmp_name"], $_SERVER['DOCUMENT_ROOT'] . "/" . $uploadName)) {
            $user->setProfilePic($uploadName);
            $this->em->persist($user);
            $this->em->flush();
            return new JsonResponse($user, 201);
        }

        return new JsonResponse(["message" => "Error uploading the file"], 409);
    }

    public function resizeImage($file, $targetFile): bool
    {
        $width = 512;
        $height = 512;

        [
            $originalWidth,
            $originalHeight
        ] = getimagesize($file);

        $src = null;

        $split = explode(".", $_FILES["pic"]["name"]);
        $fileExt = end($split);
        if ($fileExt == 'jpg' || $fileExt == 'jpeg') {
            $src = imagecreatefromjpeg($file);
        } elseif ($fileExt == 'png') {
            $src = imagecreatefrompng($file);
        }

        if ($src) {
            $dst = imagecreatetruecolor($width, $height);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);

            imagepng($dst, $targetFile);

            imagedestroy($src);
            imagedestroy($dst);

            return true;
        }

        return false;
    }

    #[Route(name: "delete_user", path: "/users/{id}", methods: [Request::METHOD_DELETE], roles: [User::ROLE_ADMIN])]
    public function delete(int $id): Response
    {
        $repo = $this->em->getRepository(User::class);
        $user = $repo->find($id);
        if ($user === null) {
            return new JsonResponse(["message" => "User with id $id not found"], 404);
        }
        if ($user->getRole() === User::ROLE_ADMIN) {
            return new JsonResponse(["message" => "You cannot delete admin"], 400);
        }
        $this->em->remove($user);
        $this->em->flush();
        return new JsonResponse(null, 204);
    }

    #[Route(name: "leaderboard", path: "/leaderboard", methods: [Request::METHOD_GET])]
    public function leaderboard(): Response
    {
        $repo = $this->em->getRepository(User::class);
        $users = $repo->findAll(3, 0, [["rating", "DESC"]]);
        return new JsonResponse($users, 200);
    }

    function validateEmail($email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    function validatePassword($password): bool
    {
        return preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $password) === 1;
    }

    function validateUsername($username): bool
    {
        return preg_match('/^[a-zA-Z0-9]+$/', $username) === 1;
    }

    function validateName($name): bool
    {
        return preg_match('/^[a-zA-Z]+$/', $name) === 1;
    }

}