<?php

/*
 * This file is part of the JsonSchema package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JsonSchema\Tests\Constraints;

use JsonSchema\Validator;

class SelfDefinedSchemaTest extends BaseTestCase
{
    /**
     * @expectedException JsonSchema\Exception\InvalidArgumentException
     */
    public function testAttemptingToValidateAgainstNoSchema()
    {
        $validator = new Validator();

        $validator->check(json_decode('{"name":"nothing"}'), null);
    }

    public function getInvalidTests()
    {
        return array(
            array(
                '{
                    "$schema": {
                        "properties": {
                            "name": {
                                "type": "string"
                            },
                            "age" : {
                                "type": "integer",
                                "maximum": 25
                            }
                        }
                    },
                    "name" : "John Doe",
                    "age" : 30,
                    "type" : "object"
                }',
                ''
            )
        );
    }

    public function getValidTests()
    {
        return array(
            array(
                '{
                    "$schema": {
                        "properties": {
                            "name": {
                                "type": "string"
                            },
                            "age" : {
                                "type": "integer",
                                "maximum": 125
                            }
                        }
                    },
                    "name" : "John Doe",
                    "age" : 30,
                    "type" : "object"
                }',
                ''
            )
        );
    }
}
