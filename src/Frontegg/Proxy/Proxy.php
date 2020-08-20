<?php

namespace Frontegg\Proxy;

use Frontegg\Authenticator\Authenticator;
use Frontegg\Exception\UnexpectedValueException;
use Frontegg\Http\ApiRawResponse;
use Frontegg\Http\Uri;
use Frontegg\HttpClient\FronteggHttpClientInterface;
use Frontegg\Proxy\Adapter\AdapterInterface;
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
     * @var callable[]
     */
    protected $filters = [];

    /**
     * @TODO: Refactor this later.
     *
     * Proxy constructor.
     *
     * @param Authenticator $authenticator
     * @param AdapterInterface $adapter
     */
    public function __construct(
        Authenticator $authenticator,
        AdapterInterface $adapter
    ) {
        $this->authenticator = $authenticator;
        $this->adapter = $adapter;
    }


    /**
     * Prepare the proxy to forward a request instance.
     *
     * @param RequestInterface $request
     * @return $this
     */
    public function forward(RequestInterface $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Forward the request to the target url and return the response.
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

        $target = new Uri($target);

        // Overwrite target scheme, host and port.
        $uri = $this->request->getUri()
            ->withScheme($target->getScheme())
            ->withHost($target->getHost())
            ->withPort($target->getPort());

        // Check for subdirectory.
        if ($path = $target->getPath()) {
            $uri = $uri->withPath(rtrim($path, '/') . '/' . ltrim($uri->getPath(), '/'));
        }

        $request = $this->request->withUri($uri);

        $stack = $this->filters;

        $stack[] = function (RequestInterface $request, ResponseInterface $response, callable $next) {
            $response = $this->adapter->send($request);

            return $next($request, $response);
        };

        $relay = (new RelayBuilder)->newInstance($stack);

        $response = $relay($request, new Response);

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
     * @return FronteggHttpClientInterface
     */
    protected function getHttpClient(): FronteggHttpClientInterface
    {
        return $this->getAuthenticator()->getClient();
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
            $response->getBody(),
            $response->getStatusCode()
        );
    }
}