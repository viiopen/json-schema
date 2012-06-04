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
    }
}