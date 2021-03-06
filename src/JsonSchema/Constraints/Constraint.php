<?php

/*
 * This file is part of the JsonSchema package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JsonSchema\Constraints;

use JsonSchema\Uri\UriRetriever;
use JsonSchema\Uri\UriResolver;
use JsonSchema\Exception\InvalidSourceUriException;

/**
 * The Base Constraints, all Validators should extend this class
 *
 * @author Robert Schönthal <seroscho@googlemail.com>
 * @author Bruno Prieto Reis <bruno.p.reis@gmail.com>
 */
abstract class Constraint implements ConstraintInterface
{
    protected $checkMode = self::CHECK_MODE_NORMAL;
    protected $uriRetriever;
    protected $uriResolver;
    protected $errors = array();
    protected $inlineSchemaProperty = '$schema';
    protected $schemaId;

    const CHECK_MODE_NORMAL = 1;
    const CHECK_MODE_TYPE_CAST = 2;

    /**
     * @param int          $checkMode
     * @param UriRetriever $uriRetriever
     */
    public function __construct($checkMode = self::CHECK_MODE_NORMAL, UriRetriever $uriRetriever = null, UriResolver $uriResolver = null, $schemaId = null)
    {
        $this->checkMode    = $checkMode;
        $this->uriRetriever = $uriRetriever;
        $this->uriResolver = $uriResolver;
        $this->schemaId = $schemaId;
    }

    /**
     * @return UriResolver
     */
    public function getUriResolver()
    {
        if (is_null($this->uriResolver))
        {
            $this->setUriResolver(new UriResolver);
        }

        return $this->uriResolver;
    }


    /**
     * @param UriResolver $uriResolver
     */
    public function setUriResolver(UriResolver $uriResolver)
    {
        $this->uriResolver = $uriResolver;
    }


    /**
     * @return UriRetriever
     */
    public function getUriRetriever()
    {
        if (is_null($this->uriRetriever))
        {
            $this->setUriRetriever(new UriRetriever);
        }

        return $this->uriRetriever;
    }

    /**
     * @param UriRetriever $uriRetriever
     */
    public function setUriRetriever(UriRetriever $uriRetriever)
    {
        $this->uriRetriever = $uriRetriever;
    }

    /**
     * {@inheritDoc}
     */
    public function addError($path, $message)
    {
        $this->errors[] = array(
            'property' => $path,
            'message' => $message
        );
    }

    /**
     * {@inheritDoc}
     */
    public function addErrors(array $errors)
    {
        $this->errors = array_merge($this->errors, $errors);
    }

    /**
     * {@inheritDoc}
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * {@inheritDoc}
     */
    public function isValid()
    {
        return !$this->getErrors();
    }

    /**
     * Clears any reported errors.  Should be used between
     * multiple validation checks.
     */
    public function reset()
    {
        $this->errors = array();
    }

    /**
     * Bubble down the path
     *
     * @param string $path Current path
     * @param mixed  $i    What to append to the path
     *
     * @return string
     */
    protected function incrementPath($path, $i)
    {
        if ($path !== '') {
            if (is_int($i)) {
                $path .= '[' . $i . ']';
            } elseif ($i == '') {
                $path .= '';
            } else {
                $path .= '.' . $i;
            }
        } else {
            $path = $i;
        }

        return $path;
    }

    /**
     * Validates an array
     *
     * @param mixed $value
     * @param mixed $schema
     * @param mixed $path
     * @param mixed $i
     */
    protected function checkArray($value, $schema = null, $path = null, $i = null)
    {
        $validator = new CollectionConstraint($this->checkMode, $this->uriRetriever, $this->uriResolver, $this->schemaId);
        $validator->check($value, $schema, $path, $i);

        $this->addErrors($validator->getErrors());
    }

    /**
     * Validates an object
     *
     * @param mixed $value
     * @param mixed $schema
     * @param mixed $path
     * @param mixed $i
     * @param mixed $patternProperties
     */
    protected function checkObject($value, $schema = null, $path = null, $i = null, $patternProperties = null)
    {
        $validator = new ObjectConstraint($this->checkMode, $this->uriRetriever, $this->uriResolver, $this->schemaId);
        $validator->check($value, $schema, $path, $i, $patternProperties);

        $this->addErrors($validator->getErrors());
    }

    /**
     * Validates the type of a property
     *
     * @param mixed $value
     * @param mixed $schema
     * @param mixed $path
     * @param mixed $i
     */
    protected function checkType($value, $schema = null, $path = null, $i = null)
    {
        $validator = new TypeConstraint($this->checkMode, $this->uriRetriever, $this->uriResolver, $this->schemaId);
        $validator->check($value, $schema, $path, $i);

        $this->addErrors($validator->getErrors());
    }

    /**
     * Checks a undefined element
     *
     * @param mixed $value
     * @param mixed $schema
     * @param mixed $path
     * @param mixed $i
     */
    protected function checkUndefined($value, $schema = null, $path = null, $i = null)
    {
        $validator = new UndefinedConstraint($this->checkMode, $this->uriRetriever, $this->uriResolver, $this->schemaId);
        $validator->check($value, $schema, $path, $i);

        $this->addErrors($validator->getErrors());
    }

    /**
     * Checks a string element
     *
     * @param mixed $value
     * @param mixed $schema
     * @param mixed $path
     * @param mixed $i
     */
    protected function checkString($value, $schema = null, $path = null, $i = null)
    {
        $validator = new StringConstraint($this->checkMode, $this->uriRetriever, $this->uriResolver, $this->schemaId);
        $validator->check($value, $schema, $path, $i);

        $this->addErrors($validator->getErrors());
    }

    /**
     * Checks a number element
     *
     * @param mixed $value
     * @param mixed $schema
     * @param mixed $path
     * @param mixed $i
     */
    protected function checkNumber($value, $schema = null, $path = null, $i = null)
    {
        $validator = new NumberConstraint($this->checkMode, $this->uriRetriever, $this->uriResolver, $this->schemaId);
        $validator->check($value, $schema, $path, $i);

        $this->addErrors($validator->getErrors());
    }

    /**
     * Checks a enum element
     *
     * @param mixed $value
     * @param mixed $schema
     * @param mixed $path
     * @param mixed $i
     */
    protected function checkEnum($value, $schema = null, $path = null, $i = null)
    {
        $validator = new EnumConstraint($this->checkMode, $this->uriRetriever, $this->uriResolver, $this->schemaId);
        $validator->check($value, $schema, $path, $i);

        $this->addErrors($validator->getErrors());
    }

    protected function checkFormat($value, $schema = null, $path = null, $i = null)
    {
        $validator = new FormatConstraint($this->checkMode, $this->uriRetriever, $this->uriResolver, $this->schemaId);
        $validator->check($value, $schema, $path, $i);

        $this->addErrors($validator->getErrors());
    }

    /**
     * Checks a schema element
     *
     * @param mixed $value
     * @param mixed $schema
     * @param mixed $path
     * @param mixed $i
     */
    protected function checkSchema($value, $schema, $path = null, $i = null)
    {
        if (is_object($schema)) {
            $retriever = $this->getUriRetriever();
            $schemaId = $retriever->cacheSchema($schema);
        } elseif (is_string($schema)) {
            $schema = $this->retrieveSchema($schema);
            $schemaId = $schema->id;
        }

        $validator = new SchemaConstraint($this->checkMode, $this->uriRetriever, $this->uriResolver, $schemaId);
        $validator->check($value, $schema, $path, $i);

        $this->addErrors($validator->getErrors());
    }


    /**
     * retrieve a schema from a URI
     *
     * @param string $schemaUri
     * @return object
     */
    protected function retrieveSchema($schemaUri)
    {
        $resolver = $this->getUriResolver();
        $retriever = $this->getUriRetriever();

        if ($resolver->isValid($schemaUri)) {
            $schema = $retriever->retrieve($schemaUri, $this->schemaId);
            return $schema;
        } else {
            throw new InvalidSourceUriException(
                '"'.$schemaUri. '" is not a valid URI.'
            );
        }
    }

    // /**
    //  * @param string $uri JSON Schema URI
    //  * @return string JSON Schema contents
    //  */
    // protected function retrieveUri($uri)
    // {
    //     if (null === $this->uriRetriever) {
    //         $this->setUriRetriever(new UriRetriever);
    //     }
    //     $jsonSchema = $this->uriRetriever->retrieve($uri);
    //     // TODO validate using schema
    //     return $jsonSchema;
    // }
}
