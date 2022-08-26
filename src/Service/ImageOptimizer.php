<?php

namespace App\Service;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;

class ImageOptimizer
{
    private int $maxWidth;
    private int $maxHeight;
    private Imagine $imagine;

    public function __construct(int $maxWidth, int $maxHeight)
    {
        $this->imagine = new Imagine();
        $this->maxWidth = $maxWidth;
        $this->maxHeight = $maxHeight;
    }

    public function resize(string $filename): void
    {
        list($iwidth, $iheight) = getimagesize($filename);
        $ratio = $iwidth / $iheight;
        $width = $this->maxWidth;
        $height = $this->maxHeight;
        if ($width / $height > $ratio) {
            $width = $height * $ratio;
        } else {
            $height = $width / $ratio;
        }

        $photo = $this->imagine->open($filename);
        $photo->resize(new Box($width, $height))->save($filename);
    }
}