<?php

namespace Handy\Http;

class Request
{

    public const METHOD_GET    = 'GET';
    public const METHOD_POST   = 'POST';
    public const METHOD_PUT    = 'PUT';
    public const METHOD_PATCH  = 'PATCH';
    public const METHOD_DELETE = 'DELETE';
    public const METHODS = [self::METHOD_GET, self::METHOD_POST, self::METHOD_PUT, self::METHOD_PATCH, self::METHOD_DELETE];

    /**
     * @var string
     */
    protected string $method;

    /**
     * @var array
     */
    protected array $headers;

    /**
     * @var array
     */
    protected array $query;

    /**
     * @var mixed
     */
    protected mixed $content;

    /**
     * @var string
     */
    protected string $url;

    /**
     * @var string
     */
    protected string $path;

    public function __construct()
    {

    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return self
     */
    public function setMethod(string $method): self
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     * @return self
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Returns request query (GET params)
     * @return array
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * Sets request query (GET params)
     * @param array $query
     * @return self
     */
    public function setQuery(array $query): self
    {
        $this->query = $query;
        return $this;
    }

    /**
     * Returns request content (body)
     * @return mixed
     */
    public function getContent(): mixed
    {
        return $this->content;
    }

    /**
     * Sets request content (body)
     * @param mixed $content
     * @return self
     */
    public function setContent(mixed $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return self
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return self
     */
    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }


}