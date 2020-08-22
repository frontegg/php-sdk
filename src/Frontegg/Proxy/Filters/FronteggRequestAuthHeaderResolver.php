<?php

namespace Frontegg\Proxy\Filters;

use Frontegg\Authenticator\Authenticator;
use Frontegg\Exception\AuthenticationException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class FronteggRequestAuthHeaderResolver implements FilterInterface
{
    /**
     * @var Authenticator
     */
    protected $authenticator;

    /**
     * @var callable
     */
    protected $contextResolver;

    /**
     * FronteggRequestAuthHeaderResolver constructor.
     *
     * @param Authenticator $authenticator
     * @param callable      $contextResolver
     */
    public function __construct(Authenticator $authenticator, callable $contextResolver)
    {
        $this->authenticator = $authenticator;
        $this->contextResolver = $contextResolver;
    }

    public function __invoke(
        RequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        if (!$this->authenticator->getAccessToken()) {
            throw new AuthenticationException('Authentication problem');
        }

        $context = $this->getResolvedContext($request);

        $request = $request->withHeader(
            'x-access-token',
            $this->authenticator->getAccessToken()->getValue()
        );
        $request = $request->withHeader(
            'frontegg-tenant-id',
            $context['tenantId'] ?? ''
        );
        $request = $request->withHeader(
            'frontegg-user-id',
            $context['userId'] ?? ''
        );

        return $next($request, $response);
    }

    /**
     * @param RequestInterface $request
     *
     * @return array
     */
    protected function getResolvedContext(RequestInterface $request): array
    {
        return call_user_func($this->contextResolver, $request);
    }
}
