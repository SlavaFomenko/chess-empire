<?php

namespace Handy\Http;

use Handy\Utils\JsonSerializable;

class JsonResponse extends Response
{

    public function __construct($data = null, int $code = 200)
    {
        parent::__construct($data, $code);
        header('Content-Type: application/json');
    }

    public function __toString(): string
    {
        $data = [];
        if (is_array($this->data)) {
            $data = $this->serializeRecursive($this->data);
        } else if (is_a($this->data, JsonSerializable::class)) {
            $data = $this->data->jsonSerialize();
        }
        return json_encode($data);
    }

    private function serializeRecursive(mixed $data)
    {
        if (is_a($data, JsonSerializable::class)) {
            return $data->jsonSerialize();
        }
        if (!is_array($data)) {
            return $data;
        }
        return array_map(fn($item) => $this->serializeRecursive($item), $data);
    }

}