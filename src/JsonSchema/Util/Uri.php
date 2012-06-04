<?php

/*
 * This file is part of the JsonSchema package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JsonSchema\Util;

use JsonSchema\Exception\UriResolverException;

/**
 * Resolves JSON Schema URIs
 *
 * @author Sander Coolen <sander@jibber.nl>
 * @author Justin Rainbow <justin.rainbow@gmail.com>
 */
class Uri
{
    /**
     * Tries to glue a relative path onto an absolute one
     *
     * @param string $relativePath
     * @param string $basePath
     *
     * @return string Merged path
     *
     * @throws UriResolverException
     */
    public static function addPath($relativePath, $basePath)
    {
        $relativePath = self::normalizePath($relativePath);
        $basePathSegments = self::getPathSegments($basePath);

        preg_match('|^/?(\.\./(?:\./)*)*|', $relativePath, $match);
        $numLevelUp = strlen($match[0]) /3 + 1;
        if ($numLevelUp >= count($basePathSegments)) {
            throw new UriResolverException(sprintf("Unable to resolve URI '%s' from base '%s'", $relativePath, $basePath));
        }

        $basePathSegments = array_slice($basePathSegments, 0, -$numLevelUp);
        $path = preg_replace('|^/?(\.\./(\./)*)*|', '', $relativePath);

        return implode(DIRECTORY_SEPARATOR, $basePathSegments) . '/' . $path;
    }

    /**
     * Normalizes a URI path component by removing dot-slash and double slashes
     *
     * @param string $path
     *
     * @return string
     */
    public static function normalizePath($path)
    {
        $path = preg_replace('|((?<!\.)\./)*|', '', $path);
        $path = preg_replace('|//|', '/', $path);

        return $path;
    }

    /**
     * @param string $path
     *
     * @return array
     */
    public static function getPathSegments($path)
    {
        return explode(DIRECTORY_SEPARATOR, $path);
    }

    /**
     * Parses a URI into five main components
     *
     * @param string $uri
     *
     * @return array
     */
    public static function parse($uri)
    {
        preg_match('|^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?|', $uri, $match);

        $components = array();
        if (5 < count($match)) {
            $components =  array(
                'scheme'    => $match[2],
                'authority' => $match[4],
                'path'      => $match[5]
            );
        }

        if (7 < count($match)) {
            $components['query'] = $match[7];
        }

        if (9 < count($match)) {
            $components['fragment'] = $match[9];
        }

        return $components;
    }

    /**
     * Builds a URI based on n array with the main components
     *
     * @param array $components
     *
     * @return string
     */
    public static function generate(array $components)
    {
        $uri = $components['scheme'] . '://'
             . $components['authority']
             . $components['path'];

        if (array_key_exists('query', $components)) {
            $uri .= '?' . $components['query'];
        }

        if (array_key_exists('fragment', $components)) {
            $uri .= '#' . $components['fragment'];
        }

        return $uri;
    }

    /**
     * @param string $uri
     *
     * @return boolean
     */
    public static function isValid($uri)
    {
        $components = static::parse($uri);

        if (!isset($components['scheme']) || empty($components['scheme'])) {
            return false;
        }

        return !empty($components);
    }
}
