<?php

namespace App\Controller;

use App\Entity\RatingRange;
use App\Entity\User;
use Handy\Controller\BaseController;
use Handy\Http\JsonResponse;
use Handy\Http\Request;
use Handy\Http\Response;
use Handy\Routing\Attribute\Route;

class RatingRangeController extends BaseController
{

    #[Route(name: "rating_range_create", path: "/rating-ranges", methods: [Request::METHOD_POST], roles: [User::ROLE_ADMIN])]
    public function post(): Response
    {
        $body = $this->request->getContent();

        if (!isset($body["minRating"],
            $body["win"],
            $body["loss"],
            $body["title"])) {
            return new JsonResponse(["message" => "Missing fields in request body"], 400);
        }

        $error = $this->validateMinRating($body["minRating"]) ??
            $this->validateWin($body["win"]) ??
            $this->validateLoss($body["loss"]) ??
            $this->validateTitle($body["title"]);
        if ($error !== null) return new JsonResponse(["message" => $error], 400);

        $data = [
            "minRating" => filter_var($body["minRating"], FILTER_VALIDATE_INT),
            "win"       => filter_var($body["win"], FILTER_VALIDATE_INT),
            "loss"      => filter_var($body["loss"], FILTER_VALIDATE_INT),
            "title"     => trim($body["title"])
        ];

        $ratingRange = new RatingRange();
        $ratingRange->fromArray($data);

        $this->em->persist($ratingRange);
        $this->em->flush();

        $repo = $this->em->getRepository(RatingRange::class);
        return new JsonResponse($repo->findOneBy(["min_rating" => $data["minRating"]]), 201);
    }

    #[Route(name: "get_all_rating_ranges", path: "/rating-ranges", methods: [Request::METHOD_GET])]
    public function getAll(): Response
    {
        $repo = $this->em->getRepository(RatingRange::class);
        $ratingRanges = $repo->findAll(orderBy: [
            [
                "min_rating",
                "ASC"
            ]
        ]);
        return new JsonResponse($ratingRanges, 200);
    }

    #[Route(name: "get_rating_range_by_id", path: "/rating-ranges/{id}", methods: [Request::METHOD_GET])]
    public function getById(int $id): Response
    {
        $repo = $this->em->getRepository(RatingRange::class);
        $ratingRange = $repo->find($id);
        if ($ratingRange === null) {
            return new JsonResponse(["message" => "Rating range with id $id not found"], 404);
        }
        return new JsonResponse($ratingRange, 200);
    }

    #[Route(name: "rating_range_patch", path: "/rating-ranges/{id}", methods: [Request::METHOD_PATCH], roles: [User::ROLE_ADMIN])]
    public function patch(int $id): Response
    {
        $repo = $this->em->getRepository(RatingRange::class);
        $ratingRange = $repo->find($id);
        if ($ratingRange === null) {
            return new JsonResponse(["message" => "Rating range with id $id not found"], 404);
        }

        $body = $this->request->getContent();

        $error = $error ?? (isset($body["minRating"]) ? $this->validateMinRating($body["minRating"]) : null);
        $error = $error ?? (isset($body["win"]) ? $this->validateWin($body["win"]) : null);
        $error = $error ?? (isset($body["loss"]) ? $this->validateLoss($body["loss"]) : null);
        $error = $error ?? (isset($body["title"]) ? $this->validateTitle($body["title"]) : null);
        if ($error !== null) return new JsonResponse(["message" => $error], 400);

        if (isset($body["minRating"]) && $body["minRating"] !== 0 && $ratingRange->getMinRating() === 0) {
            return new JsonResponse(["message" => "You cannot remove 0-rating range"], 400);
        }

        $data = [];
        isset($body["minRating"]) && $data["minRating"] = filter_var($body["minRating"], FILTER_VALIDATE_INT);
        isset($body["win"]) && $data["win"] = filter_var($body["win"], FILTER_VALIDATE_INT);
        isset($body["loss"]) && $data["loss"] = filter_var($body["loss"], FILTER_VALIDATE_INT);
        isset($body["title"]) && $data["title"] = trim($body["title"]);

        $ratingRange->fromArray($data);

        $this->em->persist($ratingRange);
        $this->em->flush();

        return new JsonResponse($ratingRange, 201);
    }

    #[Route(name: "delete_rating_range", path: "/rating-ranges/{id}", methods: [Request::METHOD_DELETE], roles: [User::ROLE_ADMIN])]
    public function delete(int $id): Response
    {
        $repo = $this->em->getRepository(RatingRange::class);
        $ratingRange = $repo->find($id);
        if ($ratingRange === null) {
            return new JsonResponse(["message" => "Rating range with id $id not found"], 404);
        }
        if ($ratingRange->getMinRating() === 0) {
            return new JsonResponse(["message" => "You cannot remove 0-rating range"], 400);
        }
        $this->em->remove($ratingRange);
        $this->em->flush();
        return new JsonResponse(null, 204);
    }

    public function validateMinRating(mixed $value): ?string
    {
        $intValue = filter_var($value, FILTER_VALIDATE_INT);
        if ($intValue === false) {
            return "Min rating must be an integer";
        }

        if ($intValue < 0) {
            return "Min rating cannot be less than 0";
        }

        $repo = $this->em->getRepository(RatingRange::class);
        $duplicate = $repo->findOneBy(["min_rating" => $intValue]);
        if ($duplicate !== null) {
            return "Range with this min rating already exists";
        }

        return null;
    }

    public function validateWin(mixed $value): ?string
    {
        $intValue = filter_var($value, FILTER_VALIDATE_INT);
        if ($intValue === false) {
            return "Win cost must be an integer";
        }

        if ($intValue <= 0) {
            return "Win cost must be greater than 0";
        }

        return null;
    }

    public function validateLoss(mixed $value): ?string
    {
        $intValue = filter_var($value, FILTER_VALIDATE_INT);
        if ($intValue === false) {
            return "Loss cost must be an integer";
        }

        if ($intValue > 0) {
            return "Loss cost cannot be be greater than 0";
        }

        return null;
    }

    public function validateTitle(mixed $value): ?string
    {
        if (strlen(trim((string)$value)) === 0) {
            return "Title cannot be empty";
        }

        return null;
    }


}