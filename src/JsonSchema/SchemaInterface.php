<?php

/*
 * This file is part of the JsonSchema package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JsonSchema;

/**
 * Interface defining the base Schema features.
 *
 * @author Justin Rainbow <justin.rainbow@gmail.com>
 */
interface SchemaInterface extends PropertyInterface
{
    function getId();

    function getType();

    function getDescription();

    function getExtends();

    function getProperties();

    function getLinks();

    function getProperty($name);

    function hasProperty($name);
}