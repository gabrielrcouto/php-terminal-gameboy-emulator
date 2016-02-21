<?php
namespace GameBoy\Canvas;

/**
 * Interface to draw the GameBoy output
 * GameBoy screen size: 160 x 144
 */
interface DrawContextInterface
{
	/**
     * Draw image on canvas
     *
     * @param  Array $canvasBuffer  Each pixel => 4 items on array (RGBA)
     * @param  int $left
     * @param  int $top
     */
	public function draw($canvasBuffer, $left, $top);
}