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
 * Tries to retrieve JSON schemas from a URI using cURL library
 *
 * @author Sander Coolen <sander@jibber.nl>
 */
class CurlLoader implements LoaderInterface
{
    protected $contentType;
    protected $messageBody;

    /**
     * @throws \RuntimeException When cURL extension is not installed.
     */
    public function __construct()
    {
        // @codeCoverageIgnoreStart
        if (!function_exists('curl_init')) {
            throw new \RuntimeException("cURL not installed");
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * {@inheritDoc}
     */
    public function load($uri)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: ' . LoaderInterface::SCHEMA_MEDIA_TYPE));

        $response = curl_exec($ch);
        if (false === $response || 404 === curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
            throw new ResourceNotFoundException('JSON schema not found');
        }

        $this->fetchMessageBody($response);
        $this->fetchContentType($response);

        curl_close($ch);

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
        return Uri::isValid($uri);
    }

    /**
     * @param string $response cURL HTTP response
     *
     * @return boolean Whether the Content-Type header was found or not
     */
    protected function fetchContentType($response)
    {
        if (0 < preg_match("/Content-Type:([^;]*)/ims", $response, $match)) {
            $this->contentType = trim($match[1]);

            return true;
        }

        // @codeCoverageIgnoreStart
        return false;
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string $response cURL HTTP response
     */
    private function fetchMessageBody($response)
    {
        preg_match("/(?:\r\n){2}(.*)$/ms", $response, $match);
        $this->messageBody = $match[1];
    }
}
