<?php

namespace App\Enums;

enum PostType: string
{
    case Post = 'post';
    case Video = 'video';
    case Reel = 'reel';
    case Carousel = 'carousel';

    public static function imageFeedTypes(): array
    {
        return [self::Post->value, self::Carousel->value, self::Video->value];
    }

    public static function videoTypes(): array
    {
        return [self::Video->value, self::Reel->value];
    }

    public static function userPostFilterTypes(): array
    {
        return [self::Post->value, self::Carousel->value];
    }

    public static function userVideoFilterTypes(): array
    {
        return [self::Video->value, self::Reel->value];
    }
}
