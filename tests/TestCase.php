<?php

declare(strict_types=1);

namespace Bic\Image\Converter\Tests;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase as BaseTestCase;

#[Group('bic-engine/image-converter')]
abstract class TestCase extends BaseTestCase
{
    protected const OFFSET_BMP = 54;

    protected static function rgba(string $r = "\0", string $g = "\0", string $b = "\0", string $a = "\0"): string
    {
        return \sprintf('rgba(%d, %d, %d, %d)', \ord($r), \ord($g), \ord($b), \ord($a));
    }
}
