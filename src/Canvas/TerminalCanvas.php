<?php

namespace GameBoy\Canvas;

use Drawille\Canvas;
use GameBoy\Settings;

class TerminalCanvas implements DrawContextInterface
{
    protected $canvas;
    /**
     * If is a color enabled canvas, set to true
     * @var Boolean
     */
    public $colorEnabled = false;
    protected $currentSecond = 0;
    protected $framesInSecond = 0;
    protected $fps = 0;
    protected $lastFrame;
    protected $lastFrameCanvasBuffer;

    private $width = 0;
    private $height = 0;

    public function __construct()
    {
        $this->canvas = new Canvas();
    }

    /**
     * Draw image on canvas using braille font.
     *
     * @param object $canvasBuffer $data = Each pixel (true/false)
     * @param int    $left
     * @param int    $top
     */
    public function draw($canvasBuffer, $left, $top)
    {
        //Calculate current FPS
        if ($this->currentSecond != time()) {
            $this->fps = $this->framesInSecond;
            $this->currentSecond = time();
            $this->framesInSecond = 1;
        } else {
            ++$this->framesInSecond;
        }

        //If the last frame changed, we draw
        // @TODO - The FPS will be wrong, need to find a way to update
        // without redraw
        if ($canvasBuffer != $this->lastFrameCanvasBuffer) {
            //Clear the pixels from the canvas
            $this->canvas->clear();

            //Corner pixel, to draw same size each time
            $this->canvas->set(0, 0);
            $this->canvas->set(159, 143);

            $y = 0;
            $count = count($canvasBuffer);

            for ($i = 0; $i < $count; $i++) {
                $x = $i % 160;

                if ($canvasBuffer[$i]) {
                    $this->canvas->set($x, $y);
                }

                if ($x == 159) {
                    ++$y;
                }
            }

            $frame = $this->canvas->frame([
                'min_x' => 0,
                'max_x' => 79
            ]);

            $this->lastFrame = $frame;
            $this->lastFrameCanvasBuffer = $canvasBuffer;

            $content = "\e[H\e[2J";

            if ($this->height > 0 && $this->width > 0) {
                $content = "\e[{$this->height}A\e[{$this->width}D";
            }

            $content .= sprintf('FPS: %d - Frame Skip: %s'.PHP_EOL, $this->fps, Settings::$frameskipAmout);
            $content .= $frame;

            echo $content;

            $this->height = substr_count($frame, PHP_EOL) + 1;
            $this->width = strpos($frame, PHP_EOL);
        }
    }
}
