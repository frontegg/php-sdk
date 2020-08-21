<?php

namespace Frontegg\Proxy;

use Frontegg\Authenticator\Authenticator;
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
use Frontegg\Proxy\Filters\FronteggSendRequestResolver;
use Frontegg\Proxy\Filters\FronteggResponseHeaderResolver;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
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
     * Prepares the proxy to forward a request instance.
     *
     * @param RequestInterface $request
     * @return $this
     */
    public function forward(RequestInterface $request): self
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Forwards the request to the target url and return the response.
     *
     * @param string $target
     *
     * @throws UnexpectedValueException
     *
     * @return ApiRawResponse
     */
    public function to(string $target): ApiRawResponse
    {
        if ($this->request === null) {
            throw new UnexpectedValueException('Missing request instance.');
        }

        $this->authenticator->validateAuthentication();
        if (!$this->authenticator->getAccessToken()) {
            throw new AuthenticationException('Authentication problem');
        }

        $target = new Uri($target);

        // Overwrite target scheme, host and port.
        $uri = $this->request->getUri()
            ->withScheme($target->getScheme())
            ->withHost($target->getHost())
            ->withPort($target->getPort());

        // Check for subdirectory.
        // @TODO: Check this
//        if ($path = $target->getPath()) {
//            $uri = $uri->withPath(rtrim($path, '/') . '/' . ltrim($uri->getPath(), '/'));
//        }

        $request = $this->request->withUri($uri);

        $relay = (new RelayBuilder())->newInstance($this->filters);

        $response = $relay($request, new Response());

        // @TODO: Refactor this later.
        $apiRawResponse = $this->getAdaptedApiRawResponse($response);

        return $apiRawResponse;
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
                $this->adapter
            ),
            new FronteggResponseHeaderResolver(),
        ];
    }
}
