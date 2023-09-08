<?php

declare(strict_types=1);

namespace Bic\Image\Converter;

use Bic\Image\Converter\Exception\ConverterExceptionInterface;
use Bic\Image\ImageInterface;
use Bic\Image\PixelFormatInterface;

interface ConverterInterface
{
    /**
     * @throws ConverterExceptionInterface
     */
    public function convert(ImageInterface $image, PixelFormatInterface $output): ImageInterface;
}
