<?php

namespace Frontegg\Http;

interface Request
{
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';

    /**
     * @const HTTP client request waiting timeout in seconds.
     */
    public const HTTP_REQUEST_TIMEOUT = 10;
}