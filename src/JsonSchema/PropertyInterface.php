<?php

/*
 * This file is part of the JsonSchema package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JsonSchema;

/**
 * Property interface
 *
 * @author Justin Rainbow <justin.rainbow@gmail.com>
 */
interface PropertyInterface
{
    function getName();

    function getType();

    function getDescription();

    function getDefault();

    function hasDefault();

    function isRequired();
}