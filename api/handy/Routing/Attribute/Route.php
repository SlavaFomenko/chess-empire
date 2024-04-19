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
     * @var bool
     */
    private bool $inFamily;

    /**
     * @param string $name
     * @param string $path
     * @param array $methods
     * @param bool $inFamily
     */
    public function __construct(string $name, string $path, array $methods = Request::METHODS, bool $inFamily = true)
    {
        $this->name = $name;
        $this->path = $path;
        $this->methods = $methods;
        $this->inFamily = $inFamily;
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

    /**
     * @return bool
     */
    public function isInFamily(): bool
    {
        return $this->inFamily;
    }

}