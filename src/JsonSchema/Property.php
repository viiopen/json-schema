<?php

/*
 * This file is part of the JsonSchema package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JsonSchema;

use JsonSchema\PropertyInterface;

class Property implements PropertyInterface
{
    public $additionalItems;
    public $additionalProperties = true;
    public $description;
    public $disallow;
    public $enum;
    public $exclusiveMaximum;
    public $exclusiveMinimum;
    public $extends;
    public $format;
    public $items;
    public $maxDecimal;
    public $maxItems;
    public $maxLength;
    public $maximum;
    public $minItems;
    public $minLength;
    public $minimum;
    public $pattern;
    public $properties;
    public $required;
    public $requires;
    public $title;
    public $type;
    public $uniqueItems;

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getDefault()
    {
        return $this->default;
    }

    public function hasDefault()
    {
        return null !== $this->default;
    }

    public function isRequired()
    {
        return $this->required == true;
    }
}