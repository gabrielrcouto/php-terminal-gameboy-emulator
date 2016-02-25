<?php

namespace GameBoy\Canvas;

use Drawille\Canvas;
use GameBoy\Settings;

class TerminalCanvas implements DrawContextInterface
{
    protected $canvas;
    protected $currentSecond = 0;
    protected $framesInSecond = 0;
    protected $fps = 0;

    private $width = 0;
    private $height = 0;

    public function __construct()
    {
        $this->canvas = new Canvas();
    }

    /**
     * Draw image on canvas using braille font.
     *
     * @param object $canvasBuffer $data = Each pixel = 4 items on array (RGBA)
     * @param int    $left
     * @param int    $top
     */
    public function draw($canvasBuffer, $left, $top)
    {
        //Corner pixel, to draw same size each time
        $this->canvas->set(0, 0);
        $this->canvas->set(159, 143);

        $y = 0;

        for ($i = 0; $i < count($canvasBuffer); $i = $i + 4) {
            // Sum of all colors, Ignore alpha
            $total = $canvasBuffer[$i] + $canvasBuffer[$i + 1] + $canvasBuffer[$i + 2];

            $x = ($i / 4) % 160;

            // 350 is a good threshold for black and white
            if ($total > 350) {
                $this->canvas->set($x, $y);
            }

            if ($x == 159) {
                ++$y;
            }
        }

        if ($this->currentSecond != time()) {
            $this->fps = $this->framesInSecond;
            $this->currentSecond = time();
            $this->framesInSecond = 1;
        } else {
            ++$this->framesInSecond;
        }

        $frame = $this->canvas->frame();
        $content = "\e[H\e[2J";

        if ($this->height > 0 && $this->width > 0) {
            $content = "\e[{$this->height}A\e[{$this->width}D";
        }

        $content .= sprintf('FPS: %d - Frame Skip: %s'.PHP_EOL, $this->fps, Settings::$settings[4]);
        $content .= $frame;

        echo $content;

        $this->canvas->clear();

        $this->height = substr_count($frame, PHP_EOL) + 1;
        $this->width = strpos($frame, PHP_EOL);
    }
}
