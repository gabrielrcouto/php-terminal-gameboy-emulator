<?php

namespace GameBoy\Canvas;

use GameBoy\Settings;

class TerminalCanvas implements DrawContextInterface
{
    /**
     * The blank brailler char
     * @var String
     */
    protected $brailleCharOffset;
    protected $canvas;
    /**
     * If is a color enabled canvas, set to true
     * @var Boolean
     */
    public $colorEnabled = false;
    protected $currentSecond = 0;
    protected $framesInSecond = 0;
    protected $fps = 0;
    protected $height = 0;
    protected $lastFrame;
    protected $lastFrameCanvasBuffer;
    /**
     * Braille Pixel Matrix
     *   ,___,
     *   |1 4|
     *   |2 5|
     *   |3 6|
     *   |7 8|
     *   `````
     * @var Array
     */
    protected $pixelMap;
    protected $width = 0;

    public function __construct()
    {
        $this->brailleCharOffset = html_entity_decode('&#' . (0x2800) . ';', ENT_NOQUOTES, 'UTF-8');
        $this->pixelMap = [
            [html_entity_decode('&#' . (0x2801) . ';', ENT_NOQUOTES, 'UTF-8'), html_entity_decode('&#' . (0x2808) . ';', ENT_NOQUOTES, 'UTF-8')],
            [html_entity_decode('&#' . (0x2802) . ';', ENT_NOQUOTES, 'UTF-8'), html_entity_decode('&#' . (0x2810) . ';', ENT_NOQUOTES, 'UTF-8')],
            [html_entity_decode('&#' . (0x2804) . ';', ENT_NOQUOTES, 'UTF-8'), html_entity_decode('&#' . (0x2820) . ';', ENT_NOQUOTES, 'UTF-8')],
            [html_entity_decode('&#' . (0x2840) . ';', ENT_NOQUOTES, 'UTF-8'), html_entity_decode('&#' . (0x2880) . ';', ENT_NOQUOTES, 'UTF-8')],
        ];
    }

    /**
     * Draw image on canvas using braille font.
     *
     * @param object $canvasBuffer $data = Each pixel (true/false)
     */
    public function draw($canvasBuffer)
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
            // Array with all braille chars of the frame, filled with the blank char
            // 2880 = total braille chars per frame
            $chars = array_fill(0, 2880, $this->brailleCharOffset);

            // Turn on the first and last pixels
            $chars[0] |= $this->pixelMap[0][0];
            $chars[2879] |= $this->pixelMap[0][0];

            // Frame string - It's a big braille chars string
            $frame = '';

            for ($y = 0; $y < 144; $y++) {
                for ($x = 0; $x < 160; $x++) {
                    $pixelCanvasNumber = $x + (160 * $y);
                    $charPosition = floor($x / 2) + (floor($y / 4) * 80);

                    if (isset($canvasBuffer[$pixelCanvasNumber]) && $canvasBuffer[$pixelCanvasNumber]) {
                        $chars[$charPosition] |= $this->pixelMap[$y % 4][$x % 2];
                    }

                    // Each braille frame has 8 pixels, when we reach the last pixel,
                    // we can append to the frame string
                    if ($x % 2 === 1 && $y % 4 === 3) {
                        $frame .= $chars[$charPosition];

                        if ($x % 159 === 0) {
                            $frame .= PHP_EOL;
                        }
                    }
                }
            }

            $this->lastFrame = $frame;
            $this->lastFrameCanvasBuffer = $canvasBuffer;

            $content = "\e[H\e[2J";

            if ($this->height > 0 && $this->width > 0) {
                $content = "\e[{$this->height}A\e[{$this->width}D";
            }

            $content .= sprintf('FPS: %3d - Frame Skip: %3d' . PHP_EOL, $this->fps, Settings::$frameskipAmout) . $frame;
            echo $content;

            $this->height = 37;
            $this->width = 80;
        }
    }
}
