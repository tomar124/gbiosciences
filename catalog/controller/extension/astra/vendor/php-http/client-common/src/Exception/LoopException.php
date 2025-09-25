<?php

declare (strict_types=1);
namespace AstraPrefixed\Http\Client\Common\Exception;

use AstraPrefixed\Http\Client\Exception\RequestException;
/**
 * Thrown when the Plugin Client detects an endless loop.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
final class LoopException extends RequestException
{
}
