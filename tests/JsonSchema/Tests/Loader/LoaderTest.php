<?php

/*
 * This file is part of the JsonSchema package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JsonSchema\Tests\Loader;

use JsonSchema\Loader\LoaderInterface;
use JsonSchema\SchemaFactory;
use JsonSchema\Validator;

class LoaderTest extends \PHPUnit_Framework_TestCase
{
    protected $validator;

    private static function setParentSchemaExtendsValue(&$parentSchema, $value)
    {
        $parentSchemaDecoded = json_decode($parentSchema, true);
        $parentSchemaDecoded['extends'] = $value;
        $parentSchema = json_encode($parentSchemaDecoded);
    }

    /**
     * @dataProvider jsonProvider
     */
    public function testChildExtendsParent($childSchema, $parentSchema)
    {
        $curlLoaderMock = $this->getCurlLoaderMock($parentSchema);

        SchemaFactory::setLoaders(array($curlLoaderMock));

        $json = '{"childProp":"infant", "parentProp":false}';
        $decodedJson = json_decode($json);
        $decodedJsonSchema = json_decode($childSchema);

        $this->validator->check($decodedJson, $decodedJsonSchema);
        $this->assertTrue($this->validator->isValid());
    }

    /**
     * @dataProvider jsonProvider
     */
    public function testResolveRelativeUri($childSchema, $parentSchema)
    {
        self::setParentSchemaExtendsValue($parentSchema, 'grandparent');
        $curlLoaderMock = $this->getCurlLoaderMock($parentSchema);

        $curlLoaderMock
            ->expects($this->at(2))
            ->method('load')
            ->with($this->equalTo('http://some.host.at/somewhere/grandparent'))
            ->will($this->returnValue('{"type":"object","title":"grand-parent"}'));

        SchemaFactory::setLoaders(array($curlLoaderMock));

        $json = '{"childProp":"infant", "parentProp":false}';
        $decodedJson = json_decode($json);
        $decodedJsonSchema = json_decode($childSchema);

        $this->validator->check($decodedJson, $decodedJsonSchema);
        $this->assertTrue($this->validator->isValid());
    }

    /**
     * @dataProvider jsonProvider
     * @expectedException JsonSchema\Exception\InvalidSchemaMediaTypeException
     */
    public function testInvalidSchemaMediaType($childSchema, $parentSchema)
    {
        $curlLoaderMock = $this->getCurlLoaderMock($parentSchema, 'text/html');

        SchemaFactory::setLoaders(array($curlLoaderMock));

        $json = '{"childProp":"infant", "parentProp":false}';
        $decodedJson = json_decode($json);
        $decodedJsonSchema = json_decode($childSchema);

        $this->validator->check($decodedJson, $decodedJsonSchema);
    }

    /**
     * @dataProvider jsonProvider
     * @expectedException JsonSchema\Exception\JsonDecodingException
     */
    public function testParentJsonError($childSchema, $parentSchema)
    {
        $curlLoaderMock = $this->getCurlLoaderMock('<html>', 'application/schema+json');

        SchemaFactory::setLoaders(array($curlLoaderMock));

        $json = '{}';
        $decodedJson = json_decode($json);
        $decodedJsonSchema = json_decode($childSchema);

        $this->validator->check($decodedJson, $decodedJsonSchema);
    }

    public function jsonProvider()
    {
        $childSchema = <<<EOF
{
    "type":"object",
    "title":"child",
    "extends":"http://some.host.at/somewhere/parent",
    "properties":
    {
        "childProp":
        {
            "type":"string"
        }
    }
}
EOF;
        $parentSchema = <<<EOF
{
    "type":"object",
    "title":"parent",
    "properties":
    {
        "parentProp":
        {
            "type":"boolean"
        }
    }
}
EOF;

        return array(
            array($childSchema, $parentSchema)
        );
    }

    protected function setUp()
    {
        $this->validator = new Validator();
    }

    private function getCurlLoaderMock($returnSchema, $returnMediaType = LoaderInterface::SCHEMA_MEDIA_TYPE)
    {
        $loader = $this->getMock('JsonSchema\Loader\CurlLoader', array('load', 'getContentType'));

        $loader
            ->expects($this->at(0))
            ->method('load')
            ->with($this->equalTo('http://some.host.at/somewhere/parent'))
            ->will($this->returnValue($returnSchema));

        $loader
            ->expects($this->atLeastOnce()) // index 1 and/or 3
            ->method('getContentType')
            ->will($this->returnValue($returnMediaType));

        return $loader;
    }
}
