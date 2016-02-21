<?php

/*
 * This file is part of php-drawille
 *
 * (c) Jeff Welch <whatthejeff@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;

use Drawille\Canvas;

class ImagePrinter
{
    private $image;
    private $threshold;
    private $ratio;
    private $invert;

    public function __construct($image, $threshold = 385.2, $ratio = null, $invert = false) {
        $this->image = $image;
        $this->threshold = (float) $threshold;
        $this->ratio = $ratio;
        $this->invert = $invert;
    }

    public function run($terminalWidth, $terminalHeight) {
        $imagine = new Imagine();
        $image = $imagine->open($this->image);

        $size = $image->getSize();
        $width = $size->getWidth();
        $height = $size->getHeight();

        if ($this->ratio) {
            $ratio = (float) $this->ratio;
            $width = floor($width * $ratio);
            $height = floor($height * $ratio);
            $image->resize(new Box($width, $height));
        }

        else {
            $height_ratio = $terminalHeight * 4 / $height;
            $width_ratio = $terminalWidth * 2 / $width;
            $ratio = min($height_ratio, $width_ratio);

            if ($ratio < 1.0) {
                $width = floor($width * $ratio);
                $height = floor($height * $ratio);
                $image->resize(new Box($width, $height));
            }
        }

        $canvas = new Canvas();

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $color = $image->getColorAt(new Point($x, $y));
                $total = $color->getRed() + $color->getGreen() + $color->getBlue();

                if (!$this->invert ^ $total > $this->threshold) {
                    $canvas->set($x, $y);
                }
            }
        }

        echo $canvas->frame(), "\n";
    }
}

?>