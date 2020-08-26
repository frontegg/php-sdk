<?php

namespace Frontegg\Json;

use Frontegg\Error\ApiErrorTrait;
use JsonException;

trait ApiJsonTrait
{
    use ApiErrorTrait;

    /**
     * Returns JSON data decoded into array.
     *
     * @param string|null $jsonData
     *
     * @return array|null
     */
    protected function getDecodedJsonData(?string $jsonData): ?array
    {
        if (empty($jsonData)) {
            $this->setApiError(
                'Invalid JSON',
                'An empty string can\'t be parsed as valid JSON.'
            );

            return null;
        }

        try {
            return json_decode(
                $jsonData,
                true,
                JsonInterface::JSON_DECODE_DEPTH,
                JSON_THROW_ON_ERROR
            );
        } catch (JsonException $e) {
            $this->setApiError('Invalid JSON', $e->getMessage());
        }

        return null;
    }
}
