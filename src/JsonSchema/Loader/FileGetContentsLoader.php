<?php

/*
 * This file is part of the JsonSchema package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JsonSchema\Loader;

use JsonSchema\Exception\ResourceNotFoundException;
use JsonSchema\Util\Uri;
use JsonSchema\Validator;

/**
 * Tries to retrieve JSON schemas from a URI using file_get_contents()
 *
 * @author Sander Coolen <sander@jibber.nl>
 */
class FileGetContentsLoader implements LoaderInterface
{
    protected $contentType;
    protected $messageBody;

    /**
     * {@inheritDoc}
     */
    public function load($uri)
    {
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'GET',
                'header' => "Accept: " . LoaderInterface::SCHEMA_MEDIA_TYPE
            )
        ));

        $response = @file_get_contents($uri, false, $context);
        if (false === $response) {
            throw new ResourceNotFoundException('JSON schema not found');
        }

        $this->messageBody = $response;

        if (isset($http_response_header)) {
            $this->fetchContentType($http_response_header);
        }

        return $this->messageBody;
    }

    /**
     * {@inheritDoc}
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($uri)
    {
        return true;
    }

    /**
     * @param array $headers HTTP Response Headers
     *
     * @return boolean Whether the Content-Type header was found or not
     */
    private function fetchContentType(array $headers)
    {
        foreach ($headers as $header) {
            if ($this->contentType = $this->getContentTypeMatchInHeader($header)) {
                return true;
            }
        }

        // @codeCoverageIgnoreStart
        return false;
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string $header
     *
     * @return string|null
     */
    private function getContentTypeMatchInHeader($header)
    {
        if (0 < preg_match("/Content-Type:([^;]*)/ims", $header, $match)) {
            return trim($match[1]);
        }
    }
}
