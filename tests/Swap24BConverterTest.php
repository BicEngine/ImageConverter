<?php

declare(strict_types=1);

namespace Bic\Image\Converter\Tests;

use Bic\Image\Converter\SoftwareConverter;
use Bic\Image\Converter\ConverterInterface;
use Bic\Image\Image;
use Bic\Image\ImageInterface;
use Bic\Image\PixelFormat;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\Group;

#[Group('bic-engine/image-converter')]
final class Swap24BConverterTest extends TestCase
{
    private readonly ConverterInterface $converter;

    #[Before]
    protected function setUpConverter(): void
    {
        $this->converter = new SoftwareConverter();
    }

    protected function bgr(): ImageInterface
    {
        return new Image(
            format: PixelFormat::B8G8R8,
            width: 16,
            height: 16,
            data: \file_get_contents(__DIR__ . '/stubs/24bit.bmp', offset: self::OFFSET_BMP),
        );
    }

    protected function bgra(): ImageInterface
    {
        return new Image(
            format: PixelFormat::B8G8R8A8,
            width: 16,
            height: 16,
            data: \file_get_contents(__DIR__ . '/stubs/32bit.bmp', offset: self::OFFSET_BMP),
        );
    }

    public function testBGRtoRGB(): void
    {
        $before = $this->bgr();
        $expected = $before->getData();

        $after = $this->converter->convert($before, PixelFormat::R8G8B8);
        $actual = $after->getData();

        $this->assertSame($before->getBytes(), $after->getBytes(), 'Image size should not change');

        $number = 0;
        for ($i = 0, $length = $before->getBytes(); $i < $length; $i += 3, ++$number) {
            $this->assertSame(
                self::rgba($expected[$i], $expected[$i + 1], $expected[$i + 2]),
                self::rgba($actual[$i + 2], $actual[$i + 1], $actual[$i]),
                \sprintf('Pixel #%d format comparison', $number)
            );
        }
    }

    public function testBGRAtoRGB(): void
    {
        $before = $this->bgra();
        $expected = $before->getData();

        $after = $this->converter->convert($before, PixelFormat::R8G8B8);
        $actual = $after->getData();

        $this->assertSame(
            (int)($before->getBytes() / 4 * 3),
            $after->getBytes(),
            'Image size should not change'
        );

        $number = 0;
        for ($i = 0, $o = 0, $length = $before->getBytes(); $i < $length; $i += 4, $o += 3, ++$number) {
            $this->assertSame(
                self::rgba($expected[$i], $expected[$i + 1], $expected[$i + 2]),
                self::rgba($actual[$o + 2], $actual[$o + 1], $actual[$o]),
                \sprintf('Pixel #%d format comparison', $number),
            );
        }
    }

    public function testBGRtoRGBA(): void
    {
        $before = $this->bgr();
        $expected = $before->getData();

        $after = $this->converter->convert($before, PixelFormat::R8G8B8A8);
        $actual = $after->getData();

        $this->assertSame(
            $before->getBytes(),
            (int)($after->getBytes() / 4 * 3),
            'Image size should not change'
        );

        $number = 0;
        for ($i = 0, $o = 0, $length = $before->getBytes(); $i < $length; $i += 3, $o += 4, ++$number) {
            $this->assertSame(
                self::rgba($expected[$i], $expected[$i + 1], $expected[$i + 2]),
                self::rgba($actual[$o + 2], $actual[$o + 1], $actual[$o]),
                \sprintf('Pixel #%d format comparison', $number),
            );
        }
    }
}
