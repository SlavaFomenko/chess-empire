<?php

namespace Handy\Utils;

interface JsonSerializable
{

    /**
     * @return array
     */
    public function jsonSerialize(): array;

}