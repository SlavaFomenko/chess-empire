<?php

namespace Handy\Http;

use Handy\Context;
use Handy\Http\Exception\InvalidRequestPathException;
use Handy\Http\Exception\UnsupportedRequestException;

class RequestParser
{

    /**
     * @return void
     * @throws InvalidRequestPathException
     * @throws UnsupportedRequestException
     */
    public static function parseRequest(): void
    {
        $request = new Request();

        if (!in_array($_SERVER['REQUEST_METHOD'], Request::METHODS)) {
            throw new UnsupportedRequestException("Unsupported request method: " . $_SERVER['REQUEST_METHOD']);
        }

        $headers = getallheaders();

        $protocol = empty($_SERVER['HTTPS']) ? "http" : "https";

        $path = strstr($_SERVER["REQUEST_URI"] . "?", "?", true);
        $globalPrefix = Context::$config->globalPathPrefix;
        if (!str_starts_with($path, $globalPrefix)) {
            throw new InvalidRequestPathException("Path \"" . $path . "\" does not start with global prefix \"" . $globalPrefix . "\"");
        }
        $path = substr($path, strlen($globalPrefix));
        if (!str_ends_with($path, "/")) {
            $path .= "/";
        }

        $content = null;
        $files = null;

        if ($headers["Content-Type"] == "application/json") {
            $content = json_decode(file_get_contents('php://input'), true);
        }
        if (str_starts_with($headers["Content-Type"], "multipart/form-data")) {
            $content = $_POST;
            $files = $_FILES;
        }

        $request->setMethod($_SERVER['REQUEST_METHOD'])
            ->setHeaders($headers)
            ->setUrl("$protocol://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]")
            ->setPath($path)
            ->setQuery($_GET)
            ->setContent($content)
            ->setFiles($files);

        Context::$request = $request;
    }

}