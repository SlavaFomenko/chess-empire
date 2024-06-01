<?php

namespace App\Controller;

use App\Entity\User;
use Handy\Controller\BaseController;
use Handy\Http\JsonResponse;
use Handy\Http\Request;
use Handy\Http\Response;
use Handy\Routing\Attribute\Route;

class UserController extends BaseController
{

    #[Route(name: "user_create", path: "/users", methods: [Request::METHOD_POST])]
    public function register(): Response
    {
        $body = $this->request->getContent();

        if (!isset($body["username"],
            $body["first_name"],
            $body["last_name"],
            $body["email"],
            $body["password"])) {
            return new JsonResponse(["message" => "Missing fields in request body"], 400);
        }

        $username = trim($body["username"]);
        $email = trim($body["email"]);
        $password = trim($body["password"]);
        $firstName = trim($body["first_name"]);
        $lastName = trim($body["last_name"]);

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

    #[Route(name: "get_user_by_id", path: "/users/{id}", methods: [Request::METHOD_GET])]
    public function getByID(int $id): Response
    {
        $repo = $this->em->getRepository(User::class);
        $user = $repo->findOneBy(["id" => $id]);
        if (empty($user)) {
            return new JsonResponse(["message" => "User not found"], 404);
        }
        return new JsonResponse(['user' => $user], 200);
    }

    #[Route(name: "get_all_users", path: "/users", methods: [Request::METHOD_GET])]
    public function getAll(): Response
    {
        $query = $this->request->getQuery();
        $criteria = [];
        [
            $limit,
            $offset
        ] = $this->pagination();

        if (isset($query["name"])) {
            $criteria = [
                "username"   => "LIKE " . $query["name"],
                "first_name" => "LIKE " . $query["name"],
                "last_name"  => "LIKE " . $query["name"]
            ];
        }

        $repo = $this->em->getRepository(User::class);
        $users = $repo->findBy($criteria, true, $limit, $offset);
        return new JsonResponse($users, 200);
    }

    #[Route(name: "patch_user_pic", path: "/users/{id}/pic", methods: [Request::METHOD_POST])]
    public function patchUserPic(int $id): Response
    {
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

        $fileName = uniqid('profile-pic-') . "." . $fileExt;
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

        [$originalWidth, $originalHeight] = getimagesize($file);

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

            if ($fileExt == 'jpg' || $fileExt == 'jpeg') {
                imagejpeg($dst, $targetFile);
            } elseif ($fileExt == 'png') {
                imagepng($dst, $targetFile);
            }

            imagedestroy($src);
            imagedestroy($dst);

            return true;
        }

        return false;
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