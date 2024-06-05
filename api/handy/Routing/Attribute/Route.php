<?php

namespace Handy\Routing\Attribute;

use Attribute;
use Handy\Http\Request;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD)]
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
     * @var array
     */
    private array $methods;

    /**
     * @var array
     */
    private array $roles;

    /**
     * @var bool
     */
    private bool $inFamily;

    /**
     * @param string $name
     * @param string $path
     * @param array $methods
     * @param bool $inFamily
     * @param array $roles
     */
    public function __construct(string $name, string $path, array $methods = Request::METHODS, bool $inFamily = true, array $roles = [])
    {
        $this->name = $name;
        $this->path = $path;
        $this->methods = $methods;
        $this->roles = $roles;
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
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @return bool
     */
    public function isInFamily(): bool
    {
        return $this->inFamily;
    }

}