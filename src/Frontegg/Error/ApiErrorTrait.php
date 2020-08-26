<?php

namespace Frontegg\Error;

trait ApiErrorTrait
{
    /**
     * @var ApiError|null
     */
    protected $apiError;

    /**
     * @return ApiError|null
     */
    public function getApiError(): ?ApiError
    {
        return $this->apiError;
    }

    /**
     * @param string   $error
     * @param string   $message
     * @param int|null $statusCode
     */
    protected function setApiError(
        string $error,
        string $message,
        int $statusCode = null
    ): void {
        $this->apiError = new ApiError(
            $error,
            $message,
            $statusCode
        );
    }
}
