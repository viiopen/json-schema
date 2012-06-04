<?php

/*
 * This file is part of the JsonSchema package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JsonSchema\Tests;

use JsonSchema\Schema;
use JsonSchema\SchemaFactory;

class SchemaTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadingValidSchema()
    {
        $schema = SchemaFactory::create(<<<SCHEMA
{
    "type":"object",
    "properties":{
        "state":{"type":"string","requires":"city"},
        "city":{"type":"string"}
    }
}
SCHEMA
        );

        $this->assertInstanceOf('JsonSchema\Schema', $schema);
        $this->assertEquals('object', $schema->getType());
        $this->assertTrue($schema->hasProperty('state'));
        $this->assertInstanceOf('JsonSchema\PropertyInterface', $schema->getProperty('state'));
        $this->assertEquals('string', $schema->getProperty('state')->getType());
        $this->assertEquals('city', $schema->getProperty('state')->getRequires());
        $this->assertTrue($schema->hasProperty('city'));
        $this->assertInstanceOf('JsonSchema\PropertyInterface', $schema->getProperty('city'));
        $this->assertEquals('string', $schema->getProperty('city')->getType());
        $this->assertEquals('city', $schema->getProperty('city')->getName());
        $this->assertEquals(null, $schema->getProperty('city')->getDescription());
        $this->assertEquals(null, $schema->getProperty('city')->getDefault());
        $this->assertFalse($schema->getProperty('city')->hasDefault());
        $this->assertFalse($schema->getProperty('city')->isRequired());

        $this->assertEquals(null, $schema->getId());
        $this->assertEquals(null, $schema->getDescription());
        $this->assertEquals(null, $schema->getName());
        $this->assertEquals(null, $schema->getLinks());
        $this->assertFalse($schema->hasDefault());
        $this->assertFalse($schema->isRequired());
        $this->assertFalse($schema->hasExtends());
        $this->assertEquals(null, $schema->getExtends());
        $this->assertCount(2, $schema->getProperties());
    }
}