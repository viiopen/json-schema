<?php

/*
 * This file is part of the JsonSchema package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JsonSchema\Loader;

use JsonSchema\Exception\UriResolverException;
use JsonSchema\Util\Uri;

/**
 * Resolves JSON Schema URIs
 *
 * @author Sander Coolen <sander@jibber.nl>
 */
class UriResolver implements ResolverInterface
{
    /**
     * Resolves a URI
     *
     * @param string $uri     Absolute or relative
     * @param type   $baseUri Optional base URI
     *
     * @return string
     */
    public function resolve($uri, $baseUri = null)
    {
        if (null === $baseUri) {
            return $uri;
        }

        $base = \Zend\Uri\UriFactory::factory($uri);

        return $base->resolve($baseUri);
    }

    /**
     * @param string $uri
     *
     * @return boolean
     */
    public function isValid($uri)
    {
        return Uri::isValid($uri);
    }
}
