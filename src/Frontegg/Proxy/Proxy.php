<?php

namespace Frontegg\Proxy;

use Frontegg\Authenticator\Authenticator;
use Frontegg\Config\Config;
use Frontegg\Exception\AuthenticationException;
use Frontegg\Exception\UnexpectedValueException;
use Frontegg\Http\ApiRawResponse;
use Frontegg\Http\Uri;
use Frontegg\HttpClient\FronteggHttpClientInterface;
use Frontegg\Proxy\Adapter\AdapterInterface;
use Frontegg\Proxy\Filters\FilterInterface;
use Frontegg\Proxy\Filters\FronteggRequestAuthHeaderResolver;
use Frontegg\Proxy\Filters\FronteggRequestHeaderResolver;
use Frontegg\Proxy\Filters\FronteggRequestMethodResolver;
use Frontegg\Proxy\Filters\FronteggResponseErrorResolver;
use Frontegg\Proxy\Filters\FronteggSendRequestResolver;
use Frontegg\Proxy\Filters\FronteggResponseHeaderResolver;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Relay\RelayBuilder;

class Proxy
{
    /**
     * @var Authenticator
     */
    protected $authenticator;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @var callable
     */
    protected $context;

    /**
     * @var callable[]
     */
    protected $filters = [];

    /**
     * Proxy constructor.
     *
     * @param Authenticator $authenticator
     * @param AdapterInterface $adapter
     * @param callable $context
     */
    public function __construct(
        Authenticator $authenticator,
        AdapterInterface $adapter,
        callable $context
    ) {
        $this->authenticator = $authenticator;
        $this->adapter = $adapter;
        $this->context = $context;

        $this->filters = $this->getDefaultFilters();
    }

    /**
     * Forwards the request to the target Frontegg API url and returns the response.
     *
     * @param RequestInterface $request
     * @param string           $target
     *
     * @throws AuthenticationException
     *
     * @return ApiRawResponse
     */
    public function forwardTo(RequestInterface $request, string $target): ApiRawResponse
    {
        $this->request = $request;

        $this->authenticator->validateAuthentication();
        if (!$this->authenticator->getAccessToken()) {
            throw new AuthenticationException('Authentication problem');
        }

        $request = $this->getOverridenRequest($target);

        $relay = (new RelayBuilder())->newInstance($this->filters);

        $response = $relay($request, new Response());

        // @TODO: Refactor this later.
        $apiRawResponse = $this->getAdaptedApiRawResponse($response);

        return $apiRawResponse;
    }

    /**
     * Overwrite request target scheme, host and port.
     *
     * @param string $target
     *
     * @return UriInterface
     */
    protected function getOverridenRequestUri(string $target): UriInterface
    {
        $target = new Uri($target);

        return $this->request->getUri()
            ->withScheme($target->getScheme())
            ->withHost($target->getHost())
            ->withPort($target->getPort());
    }

    /**
     * Sanitize the Frontegg proxy URI prefix from the request URI.
     *
     * @param UriInterface $uri
     *
     * @return UriInterface
     */
    protected function sanitizeFronteggProxyPathFromRequestUri(UriInterface $uri): UriInterface
    {
        $uriPath = $uri->getPath();
        if (strpos($uriPath, Config::PROXY_URL) !== 0) {
            return $uri;
        }

        return $uri->withPath(substr($uriPath, strlen(Config::PROXY_URL)));
    }

    /**
     * Returns request with overriden and sanitized URI.
     *
     * @param string $target
     *
     * @return RequestInterface
     */
    protected function getOverridenRequest(string $target): RequestInterface
    {
        $uri = $this->getOverridenRequestUri($target);
        $uri = $this->sanitizeFronteggProxyPathFromRequestUri($uri);

        return $this->request->withUri($uri);
    }

    /**
     * @return Authenticator
     */
    public function getAuthenticator(): Authenticator
    {
        return $this->authenticator;
    }

    /**
     * @TODO: Refactor this later.
     *
     * @param ResponseInterface $response
     *
     * @return ApiRawResponse
     */
    protected function getAdaptedApiRawResponse(ResponseInterface $response): ApiRawResponse
    {
        return new ApiRawResponse(
            $response->getHeaders(),
            $response->getBody()->getContents(),
            $response->getStatusCode()
        );
    }

    /**
     * Returns default filters list.
     *
     * @return FilterInterface[]
     */
    protected function getDefaultFilters(): array
    {
        return [
            new FronteggRequestAuthHeaderResolver(
                $this->authenticator,
                $this->context
            ),
            new FronteggRequestHeaderResolver(),
            new FronteggRequestMethodResolver(),
            new FronteggSendRequestResolver(
                $this->adapter,
                $this->authenticator
            ),
            new FronteggResponseHeaderResolver(),
            new FronteggResponseErrorResolver()
        ];
    }
}
