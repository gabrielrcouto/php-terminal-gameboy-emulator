<?php
namespace GameBoy;

use Drawille\Canvas;

class DrawContext
{
    // 160 x 144

    protected $canvas;
    protected $currentSecond = 0;
    protected $framesInSecond = 0;
    protected $fps = 0;

    public function __construct()
    {
        $this->canvas = new Canvas();
    }

    /**
     * Put image on canvas
     * @param  Object $canvasBuffer $data = Each pixel = 4 items on array (RGBA)
     * @param  int $left
     * @param  int $top
     */
	public function putImageData($canvasBuffer, $left, $top)
    {
        $canvasBuffer = $canvasBuffer->data;

        for ($i = 0; $i < count($canvasBuffer); $i = $i + 4) {
            // IGNORE ALPHA
            $total = $canvasBuffer[$i] + $canvasBuffer[$i + 1] + $canvasBuffer[$i + 2];

            $x = ($i / 4) % 160;
            $y = ceil(($i / 4) / 160);

            if ($total > 350) {
                $this->canvas->set($x, $y);
            }
        }

        if ($this->currentSecond != time()) {
            $this->fps = $this->framesInSecond;
            $this->currentSecond = time();
            $this->framesInSecond = 1;
        } else {
            $this->framesInSecond++;
        }

        echo "\e[H\e[2J";
        echo 'FPS: ' . $this->fps . ' - Frame Skip: ' . Settings::$settings[4] . PHP_EOL;
        echo $this->canvas->frame();
        $this->canvas->clear();
    }

    public function fillRect($left, $top, $width, $height)
    {
        // echo 'Fill' . PHP_EOL;
    }
}