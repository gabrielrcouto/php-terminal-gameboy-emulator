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

        for ($i = 0; $i < count($canvasBuffer); $i = $i + 4) {
            // Sum of all colors, Ignore alpha
            $total = $canvasBuffer[$i] + $canvasBuffer[$i + 1] + $canvasBuffer[$i + 2];

            $x = ($i / 4) % 160;
            $y = ceil(($i / 4) / 160);

            // 350 is a good threshold for black and white
            if ($total > 350) {
                $this->canvas->set($x, $y);
            }
        }

        if ($this->currentSecond != time()) {
            $this->fps = $this->framesInSecond;
            $this->currentSecond = time();
            $this->framesInSecond = 1;
        } else {
            ++$this->framesInSecond;
        }

        echo "\e[H\e[2J";
        echo 'FPS: ' . $this->fps . ' - Frame Skip: ' . Settings::$settings[4] . PHP_EOL;
        echo $this->canvas->frame();
        $this->canvas->clear();
    }
}
