<?php

/*
 * This file is part of the JsonSchema package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JsonSchema\Loader;

use JsonSchema\Schema;

/**
 * Interface for URI retrievers
 *
 * @author Sander Coolen <sander@jibber.nl>
 * @author Justin Rainbow <justin.rainbow@gmail.com>
 */
interface LoaderInterface
{
    const SCHEMA_MEDIA_TYPE = 'application/schema+json';

    /**
     * Loads a JSON Schema from a given location.  If the location refers to
     * a known JSON Schema by name, then the existing schema will be used.
     *
     * @param string $uri
     *
     * @return Schema
     */
    function load($uri);

    /**
     * After a JSON Schema has loaded, this will return the Content-Type
     * associated with the request.
     *
     * @return string
     */
    function getContentType();

    /**
     * Determines whether or not this Loader is capable of loading the resource.
     *
     * @param string $uri
     *
     * @return Boolean
     */
    function supports($uri);
}
