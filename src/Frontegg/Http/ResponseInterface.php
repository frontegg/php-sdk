<?php

namespace Frontegg\Http;

interface ResponseInterface
{
    public const HTTP_STATUS_OK = 200;
    public const HTTP_STATUS_ACCEPTED = 202;

    public const HTTP_STATUS_UNAUTHORIZED = 401;
}
