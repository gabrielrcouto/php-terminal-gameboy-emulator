<?php
namespace GameBoy;

use Drawille\Canvas;

class DrawContext
{
    // 160 x 144

    protected $canvas;

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
            $total = $canvasBuffer[$i] + $canvasBuffer[$i + 1] + $canvasBuffer[$i + 2]; // + $canvasBuffer[$i + 3];

            $x = ($i / 4) % 160;
            $y = ceil(($i / 4) / 160);

            if ($total > 350) {
                $this->canvas->set($x, $y);
                // echo 'SET ' . $x . ' - ' . $y . PHP_EOL;
            }
        }

        echo "\e[H\e[2J";
        echo $this->canvas->frame();
        $this->canvas->clear();

        // echo 'Draw' . PHP_EOL;
    }

    public function fillRect($left, $top, $width, $height)
    {
        $this->canvas->clear();
        // echo 'Fill' . PHP_EOL;
    }
}