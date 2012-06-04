<?php

/*
 * This file is part of the JsonSchema package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JsonSchema\Exception;

/**
 * Provides a common interface for all JsonSchema exceptions.  This allows
 * for simpler `try { } catch (ExceptionInterface $e) { }` blocks.
 *
 * @author Justin Rainbow <justin.rainbow@gmail.com>
 */
interface ExceptionInterface
{
}
