<?php

namespace Handy\Http;

class Response
{
    protected mixed $data;

    public function status(int $code): self
    {
        http_response_code($code);
        return $this;
    }

    public function __construct($data = null, int $code = 200)
    {
        $this->data = $data;
        http_response_code($code);
    }

    public function __toString(): string
    {
        return (string) $this->data;
    }

}