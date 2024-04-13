<?php

namespace Handy\Routing\Attribute;

use Attribute;
use Handy\Http\Request;

#[Attribute]
class Route
{

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $path;

    /**
     * @var array|string[]
     */
    private array $methods;

    /**
     * @param string $name
     * @param string $path
     * @param array $methods
     */
    public function __construct(string $name, string $path, array $methods = Request::METHODS)
    {
        $this->name = $name;
        $this->path = $path;
        $this->methods = $methods;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }


}