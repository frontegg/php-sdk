<?php

namespace Frontegg\Proxy\Filters;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface FilterInterface
{
    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next);
}