<?php

/*
 * This file is part of the JsonSchema package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JsonSchema\Tests\Loader;

use JsonSchema\Loader\LoaderInterface;
use JsonSchema\Loader\UriResolver;
use JsonSchema\SchemaFactory;
use JsonSchema\Validator;

class UriResolverTest extends \PHPUnit_Framework_TestCase
{
    protected $resolver;

    /**
     * @dataProvider getRelativePaths
     */
    public function testResolvingRelativePaths($expected, $uri, $base)
    {
        $this->assertEquals($expected, (string) $this->resolver->resolve($uri, $base));
    }

    public function getRelativePaths()
    {
        return array(
            array('http://localhost/schema', 'schema', 'http://localhost/'),
            array('http://localhost/schema', 'schema', 'http://localhost')
        );
    }

    protected function setUp()
    {
        $this->resolver = new UriResolver();
    }
}
