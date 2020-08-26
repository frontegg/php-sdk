<?php

namespace Frontegg\Http;

class Response
{
    public const HTTP_STATUS_OK = 200;
    public const HTTP_STATUS_CREATED = 201;
    public const HTTP_STATUS_ACCEPTED = 202;
    public const HTTP_STATUS_NON_AUTHORITATIVE_INFORMATION = 203;
    public const HTTP_STATUS_NO_CONTENT = 204;
    public const HTTP_STATUS_RESET_CONTENT = 205;
    public const HTTP_STATUS_PARTIAL_CONTENT = 206;
    public const HTTP_STATUS_MULTI_STATUS = 207;
    public const HTTP_STATUS_ALREADY_REPORTED = 208;

    public const HTTP_STATUS_UNAUTHORIZED = 401;

    public const HTTP_STATUS_INTERNAL_SERVER_ERROR = 500;

    /**
     * Returns all success status codes (2XX).
     *
     * @return int[]
     */
    public static function getSuccessHttpStatuses(): array
    {
        return [
            static::HTTP_STATUS_OK,
            static::HTTP_STATUS_CREATED,
            static::HTTP_STATUS_ACCEPTED,
            static::HTTP_STATUS_NON_AUTHORITATIVE_INFORMATION,
            static::HTTP_STATUS_NO_CONTENT,
            static::HTTP_STATUS_RESET_CONTENT,
            static::HTTP_STATUS_PARTIAL_CONTENT,
            static::HTTP_STATUS_MULTI_STATUS,
            static::HTTP_STATUS_ALREADY_REPORTED,
        ];
    }
}
