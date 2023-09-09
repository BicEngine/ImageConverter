<?php

declare(strict_types=1);

namespace Bic\Image\Converter;

use Bic\Image\Compression;
use Bic\Image\Converter\Exception\CompressedImageException;
use Bic\Image\Image;
use Bic\Image\ImageInterface;
use Bic\Image\PixelFormatInterface;

final class SoftwareConverter implements ConverterInterface
{
    public function convert(ImageInterface $image, PixelFormatInterface $format): ImageInterface
    {
        if ($image->getCompression() !== Compression::NONE) {
            throw new CompressedImageException('Could not convert compressed image');
        }

        if ($image->getFormat() === $format) {
            return $image;
        }

        $result = '';
        $input = $image->getFormat();

        $ir = $input->getRedColor();
        $ig = $input->getGreenColor();
        $ib = $input->getBlueColor();
        $ia = $input->getAlphaColor();
        $ip = $input->getBytesPerPixel();

        $or = $format->getRedColor();
        $og = $format->getGreenColor();
        $ob = $format->getBlueColor();
        $oa = $format->getAlphaColor();
        $op = $format->getBytesPerPixel();

        $source = $image->getData();
        $suffix = \str_repeat("\0", 4 - $ip);

        for ($offset = 0, $length = $image->getBytes(); $offset < $length; $offset += $ip) {
            $slice = \substr($source, $offset, $ip) . $suffix;

            $inputPixel = \unpack('N*', $slice)[1];

            $outputPixel = (
                (($inputPixel & $ir->getMask()) >> $ir->getOffset()) << $or->getOffset()
            ) | (
                (($inputPixel & $ig->getMask()) >> $ig->getOffset()) << $og->getOffset()
            ) | (
                (($inputPixel & $ib->getMask()) >> $ib->getOffset()) << $ob->getOffset()
            ) | (
                (($inputPixel & $ia->getMask()) >> $ia->getOffset()) << $oa->getOffset()
            );

            $result .= \substr(\pack('N', $outputPixel), 0, $op);
        }

        return new Image(
            format: $format,
            width: $image->getWidth(),
            height: $image->getHeight(),
            data: $result,
        );
    }
}
