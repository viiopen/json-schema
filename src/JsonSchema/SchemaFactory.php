<?php

/*
 * This file is part of the JsonSchema package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JsonSchema;

use JsonSchema\Exception\InvalidArgumentException;
use JsonSchema\Exception\InvalidSchemaException;
use JsonSchema\Exception\InvalidSchemaMediaTypeException;
use JsonSchema\Exception\ResourceNotFoundException;
use JsonSchema\Exception\JsonDecodingException;
use JsonSchema\Loader\LoaderInterface;

/**
 * Factory for creating Schema instances.
 *
 * ```
 * $schema = JsonSchema\SchemaFactory::create(<<<EOT
 * {
 *     "type":"object",
 *     "properties":{
 *         "state":{"type":"string","requires":"city"},
 *         "city":{"type":"string"}
 *     }
 * }
 * EOT
 * );
 * ```
 *
 * @author Justin Rainbow <justin.rainbow@gmail.com>
 */
class SchemaFactory
{
    private static $loaders = array();

    /**
     * Creates a new Schema instance from either a JSON string, or from
     * a PHP object.
     *
     * @param string|object $schema
     *
     * @return Schema
     *
     * @throws InvalidSchemaException
     */
    public static function create($schema)
    {
        if (is_string($schema)) {
            $schema = json_decode($schema);

            if (JSON_ERROR_NONE < $error = json_last_error()) {
                throw new JsonDecodingException($error);
            }
        }

        if (!is_object($schema) && !is_array($schema)) {
            throw new InvalidSchemaException(sprintf(
                'Expected an object or array instead of %s',
                gettype($schema)
            ));
        }

        $obj = new Schema();
        $refl = new \ReflectionObject($obj);

        foreach ($schema as $field => $value) {
            $setter = 'set' . ucfirst($field);
            if ($refl->hasMethod($setter)) {
                $obj->{$setter}($value);
            } else {
                $obj->{$field} = $value;
            }
        }

        return $obj;
    }

    public static function createProperty($name, $property)
    {
        $prop = new Property();
        $prop->name = $name;

        $refl = new \ReflectionObject($prop);

        foreach ($property as $field => $value) {
            $setter = 'set' . ucfirst($field);
            if ($refl->hasMethod($setter)) {
                $prop->{$setter}($value);
            } else {
                $prop->{$field} = $value;
            }
        }

        return $prop;
    }

    /**
     * Loads a JSON Schema from the provided resource URI.
     *
     * @param string $uri
     *
     * @return Schema
     *
     * @throws InvalidSchemaMediaTypeException An invalid JSON Schema was returned
     *     from the loader.
     * @throws JsonDecodingException The returned JSON Schema is invalid, or is
     *     somehow unable to be converted into a Schema object.
     * @throws InvalidSchemaException None of the configured LoaderInterface objects
     *     were able to load the URI.
     */
    public static function import($uri)
    {
        foreach (static::$loaders as $loader) {
            if (!$loader->supports($uri)) {
                continue;
            }

            $contents = $loader->load($uri);
            if (LoaderInterface::SCHEMA_MEDIA_TYPE !== $type = $loader->getContentType()) {
                throw new InvalidSchemaMediaTypeException(sprintf(
                    'Media type %s expected but got %s',
                    LoaderInterface::SCHEMA_MEDIA_TYPE,
                    $type
                ));
            }

            $schema = static::create($contents);

            // TODO validate using schema
            $schema->setId($uri);

            return $schema;
        }

        throw new ResourceNotFoundException(sprintf(
            'Unable to load "%s".  No loader was able to process this resource.',
            $uri
        ));
    }

    /**
     * Replaces the list of available LoaderInterface objects.
     *
     * @param LoaderInterface[] $loaders
     *
     * @throws InvalidArgumentException
     */
    public static function setLoaders(array $loaders)
    {
        foreach ($loaders as $loader) {
            if (!$loader instanceof LoaderInterface) {
                throw new InvalidArgumentException(
                    'A loader must implement the JsonSchema\Loader\LoaderInterface.'
                );
            }
        }

        static::$loaders = $loaders;
    }

    /**
     * Adds a LoaderInterface object to the list of known loaders.
     *
     * @param LoaderInterface $loader
     */
    public static function addLoader(LoaderInterface $loader)
    {
        static::$loaders[] = $loader;
    }
}