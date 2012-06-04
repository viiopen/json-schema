<?php

/*
 * This file is part of the JsonSchema package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JsonSchema\Tests;

use JsonSchema\Loader\LoaderInterface;
use JsonSchema\Schema;
use JsonSchema\SchemaFactory;

class SchemaFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testImportingLocalSchema()
    {
        $file = __DIR__ . '/../../../src/JsonSchema/Resources/schemas/schema';
        $loaderMock = $this->getMock('JsonSchema\Loader\FileGetContentsLoader', array('getContentType'));
        $loaderMock
            ->expects($this->atLeastOnce())
            ->method('getContentType')
            ->will($this->returnValue(LoaderInterface::SCHEMA_MEDIA_TYPE));

        SchemaFactory::setLoaders(array(
            new \JsonSchema\Loader\CurlLoader(),
            $loaderMock
        ));

        $schema = SchemaFactory::import($file);

        $this->assertInstanceOf('JsonSchema\Schema', $schema);
    }

    public function testImportingRemoteSchema()
    {
        SchemaFactory::setLoaders(array(
            new \JsonSchema\Loader\FileGetContentsLoader()
        ));

        $schema = SchemaFactory::import('http://json-schema.org/schema');

        $this->assertInstanceOf('JsonSchema\Schema', $schema);

        SchemaFactory::setLoaders(array(
            new \JsonSchema\Loader\CurlLoader()
        ));

        $schema = SchemaFactory::import('http://json-schema.org/schema');

        $this->assertInstanceOf('JsonSchema\Schema', $schema);
    }

    public function testAddingLoader()
    {
        SchemaFactory::addLoader(new \JsonSchema\Loader\FileGetContentsLoader());
    }

    /**
     * @expectedException JsonSchema\Exception\ResourceNotFoundException
     */
    public function testImportingMissingRemoteSchemasWithFileGetContents()
    {
        SchemaFactory::setLoaders(array(
            new \JsonSchema\Loader\FileGetContentsLoader()
        ));

        $schema = SchemaFactory::import('http://json-schema.org/unknown-schema');
    }

    /**
     * @expectedException JsonSchema\Exception\ResourceNotFoundException
     */
    public function testImportingMissingRemoteSchemasWithCurl()
    {
        SchemaFactory::setLoaders(array(
            new \JsonSchema\Loader\CurlLoader()
        ));

        $schema = SchemaFactory::import('http://json-schema.org/unknown-schema');
    }

    /**
     * @expectedException JsonSchema\Exception\ResourceNotFoundException
     */
    public function testLoadingSchemaWithNoLoadersDefined()
    {
        SchemaFactory::setLoaders(array());

        $schema = SchemaFactory::import('http://json-schema.org/unknown-schema');
    }

    /**
     * @expectedException JsonSchema\Exception\InvalidSchemaException
     */
    public function testCreatingInvalidSchema()
    {
        SchemaFactory::create(null);
    }

    /**
     * @expectedException JsonSchema\Exception\InvalidArgumentException
     */
    public function testSettingAnInvalidLoader()
    {
        SchemaFactory::setLoaders(array(null));
    }
}