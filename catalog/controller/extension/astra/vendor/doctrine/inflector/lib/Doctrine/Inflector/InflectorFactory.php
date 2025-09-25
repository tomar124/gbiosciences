<?php

declare (strict_types=1);
namespace AstraPrefixed\Doctrine\Inflector;

use AstraPrefixed\Doctrine\Inflector\Rules\English;
use AstraPrefixed\Doctrine\Inflector\Rules\French;
use AstraPrefixed\Doctrine\Inflector\Rules\NorwegianBokmal;
use AstraPrefixed\Doctrine\Inflector\Rules\Portuguese;
use AstraPrefixed\Doctrine\Inflector\Rules\Spanish;
use AstraPrefixed\Doctrine\Inflector\Rules\Turkish;
use InvalidArgumentException;
use function sprintf;
final class InflectorFactory
{
    public static function create() : LanguageInflectorFactory
    {
        return self::createForLanguage(Language::ENGLISH);
    }
    public static function createForLanguage(string $language) : LanguageInflectorFactory
    {
        switch ($language) {
            case Language::ENGLISH:
                return new English\InflectorFactory();
            case Language::FRENCH:
                return new French\InflectorFactory();
            case Language::NORWEGIAN_BOKMAL:
                return new NorwegianBokmal\InflectorFactory();
            case Language::PORTUGUESE:
                return new Portuguese\InflectorFactory();
            case Language::SPANISH:
                return new Spanish\InflectorFactory();
            case Language::TURKISH:
                return new Turkish\InflectorFactory();
            default:
                throw new InvalidArgumentException(sprintf('Language "%s" is not supported.', $language));
        }
    }
}
