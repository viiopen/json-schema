<?php

/*
 * This file is part of the JsonSchema package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JsonSchema;

use JsonSchema\Exception\JsonDecodingException;

/**
 * Base Schema
 *
 * @author Justin Rainbow <justin.rainbow@gmail.com>
 */
class Schema implements SchemaInterface
{
    public $id;

    public $type;

    public $name;

    public $description;

    public $extends;

    public $properties;

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->id;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return $this->type;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    public function setExtends($extends)
    {
        $this->extends = $extends;
    }

    /**
     * {@inheritDoc}
     */
    public function getExtends()
    {
        return $this->extends;
    }

    public function hasExtends()
    {
        return null !== $this->extends;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function setProperties($properties)
    {
        $this->properties = array();

        foreach ($properties as $name => $property) {
            $this->addProperty($name, $property);
        }
    }

    public function addProperty($name, $property)
    {
        $this->properties[$name] = SchemaFactory::createProperty($name, $property);
    }

    /**
     * {@inheritDoc}
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * {@inheritDoc}
     */
    public function getProperty($name)
    {
        return $this->properties[$name];
    }

    /**
     * {@inheritDoc}
     */
    public function hasProperty($name)
    {
        return array_key_exists($name, $this->properties);
    }

    public function setLinks($links)
    {
        $this->links = $links;
    }

    /**
     * {@inheritDoc}
     */
    public function getLinks()
    {
        return $this->links;
    }
}