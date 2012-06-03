<?php

/*
 * This file is part of the JsonSchema package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JsonSchema\Loader;

/**
 * Interface for resolving paths for loading schemas.
 *
 * @author Justin Rainbow <justin.rainbow@gmail.com>
 */
interface ResolverInterface
{
    function resolve($uri, $baseUri = null);
}