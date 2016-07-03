<?php

namespace GameBoy\Canvas;

/**
 * Interface to draw the GameBoy output
 * GameBoy screen size: 160 x 144.
 */
interface DrawContextInterface
{
    /**
     * Draw image on canvas.
     *
     * @param array $canvasBuffer If colored, each pixel => 4 items on array (RGBA)
     */
    public function draw($canvasBuffer);
}
