<?php

namespace Handy\Http;

class Response
{
    protected mixed $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function __toString(): string
    {
        return (string) $this->data;
    }

}