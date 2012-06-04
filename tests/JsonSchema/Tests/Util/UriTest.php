<?php

/*
 * This file is part of the JsonSchema package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JsonSchema\Tests\Util;

use JsonSchema\Util\Uri;

class UriTest extends \PHPUnit_Framework_TestCase
{
    public function testParsingUrls()
    {
        $url = Uri::parse('http://localhost/?test#ing');
        $expects = array(
            'scheme' => 'http',
            'authority' => 'localhost',
            'path' => '/',
            'query' => 'test',
            'fragment' => 'ing'
        );

        $this->assertEquals($expects, $url);
    }

    public function testGeneratingUrls()
    {
        $this->assertEquals('http://localhost/?test#ing', Uri::generate(array(
            'scheme' => 'http',
            'authority' => 'localhost',
            'path' => '/',
            'query' => 'test',
            'fragment' => 'ing'
        )));
    }

    public function testValidatingUrls()
    {
        $this->assertFalse(Uri::isValid('/'));
    }

    public function testExtractingPathSegments()
    {
        $this->assertEquals(array('one', 'two'), Uri::getPathSegments('one/two'));
    }
}