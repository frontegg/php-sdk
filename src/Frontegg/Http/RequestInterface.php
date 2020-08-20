<?php

namespace Frontegg\Http;

interface RequestInterface
{
    public const METHOD_CONNECT = 'CONNECT';
    public const METHOD_DELETE = 'DELETE';
    public const METHOD_GET = 'GET';
    public const METHOD_HEAD = 'HEAD';
    public const METHOD_OPTIONS = 'OPTIONS';
    public const METHOD_PATCH = 'PATCH';
    public const METHOD_POST = 'POST';
    public const METHOD_PUT = 'PUT';
    public const METHOD_TRACE = 'TRACE';

    /**
     * @const HTTP client request waiting timeout in seconds.
     */
    public const HTTP_REQUEST_TIMEOUT = 10;
}
