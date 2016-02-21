<?php

/*
 * This file is part of php-drawille
 *
 * (c) Jeff Welch <whatthejeff@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drawille;

/**
 * Pixel surface
 *
 * @author Jeff Welch <whatthejeff@gmail.com>
 */
class Canvas
{
    /**
     * Dots:
     *
     *   ,___,
     *   |1 4|
     *   |2 5|
     *   |3 6|
     *   |7 8|
     *   `````
     *
     * @var array
     * @see http://www.alanwood.net/unicode/braille_patterns.html
     */
    private static $pixel_map = [
        [0x01, 0x08],
        [0x02, 0x10],
        [0x04, 0x20],
        [0x40, 0x80]
    ];

    /**
     * Braille characters starts at 0x2800
     *
     * @var integer
     */
    private static $braille_char_offset = 0x2800;

    /**
     * Canvas representation
     *
     * @var array
     */
    private $chars = [];

    /**
     * Constructor
     */
    public function __construct() {
        $this->clear();
    }

    /**
     * Clears the canvas
     */
    public function clear() {
        $this->chars = [];
    }

    /**
     * Sets a pixel at the given position
     *
     * @param integer $x x position
     * @param integer $y y position
     */
    public function set($x, $y) {
        list($x, $y, $px, $py) = $this->prime($x, $y);
        $this->chars[$py][$px] |= $this->getDotFromMap($x, $y);
    }

    /**
     * Unsets a pixel at the given position
     *
     * @param integer $x x position
     * @param integer $y y position
     */
    public function reset($x, $y) {
        list($x, $y, $px, $py) = $this->prime($x, $y);
        $this->chars[$py][$px] &= ~$this->getDotFromMap($x, $y);
    }

    /**
     * Gets the pixel state at a given position
     *
     * @param integer $x x position
     * @param integer $y y position
     *
     * @return bool the pixel state
     */
    public function get($x, $y) {
        list($x, $y, , , $char) = $this->prime($x, $y);
        return (bool)($char & $this->getDotFromMap($x, $y));
    }

    /**
     * Toggles the pixel state on/off at a given position
     *
     * @param integer $x x position
     * @param integer $y y position
     */
    public function toggle($x, $y) {
        $this->get($x, $y) ? $this->reset($x, $y) : $this->set($x, $y);
    }

    /**
     * Gets a line
     *
     * @param integer $y     y position
     * @param array $options options
     *
     * @return string line
     */
    public function row($y, array $options = []) {
        $row = isset($this->chars[$y]) ? $this->chars[$y] : [];

        if(!isset($options['min_x']) || !isset($options['max_x'])) {
            if(!($keys = array_keys($row))) {
                return '';
            }
        }

        $min = isset($options['min_x']) ? $options['min_x'] : min($keys);
        $max = isset($options['max_x']) ? $options['max_x'] : max($keys);

        return array_reduce(range($min, $max), function ($carry, $item) use ($row) {
            return $carry .= $this->toBraille(isset($row[$item]) ? $row[$item] : 0);
        }, '');
    }

    /**
     * Gets all lines
     *
     * @param array $options options
     *
     * @return array line
     */
    public function rows(array $options = []) {
        if(!isset($options['min_y']) || !isset($options['max_y'])) {
            if(!($keys = array_keys($this->chars))) {
                return [];
            }
        }

        $min = isset($options['min_y']) ? $options['min_y'] : min($keys);
        $max = isset($options['max_y']) ? $options['max_y'] : max($keys);

        if(!isset($options['min_x']) || !isset($options['max_x'])) {
            $flattened = array();
            foreach($this->chars as $key => $char) {
                $flattened = array_merge($flattened, array_keys($char));
            }
        }

        $options['min_x'] = isset($options['min_x']) ? $options['min_x'] : min($flattened);
        $options['max_x'] = isset($options['max_x']) ? $options['max_x'] : max($flattened);

        return array_map(function ($i) use ($options) {
            return $this->row($i, $options);
        }, range($min, $max));
    }

    /**
     * Gets a string representation of the canvas
     *
     * @param array $options options
     *
     * @return string representation
     */
    public function frame(array $options = []) {
        return join("\n", $this->rows($options));
    }

    /**
     * Gets the canvas representation.
     *
     * @return array characters
     */
    public function getChars() {
      return $this->chars;
    }

    /**
     * Gets a braille unicode character
     *
     * @param integer $code character code
     *
     * @return string braille
     */
    private function toBraille($code) {
        return html_entity_decode('&#' . (self::$braille_char_offset + $code) . ';', ENT_NOQUOTES, 'UTF-8');
    }

    /**
     * Gets a dot from the pixel map.
     *
     * @param integer $x x position
     * @param integer $y y position
     *
     * @return integer dot
     */
    private function getDotFromMap($x, $y) {
        $y = $y % 4;
        $x = $x % 2;

        return self::$pixel_map[$y < 0 ? 4 + $y : $y][$x < 0 ? 2 + $x : $x];
    }

    /**
     * Autovivification for a canvas position.
     *
     * @param integer $x x position
     * @param integer $y y position
     *
     * @return array
     */
    private function prime($x, $y) {
        $x = round($x);
        $y = round($y);
        $px = floor($x / 2);
        $py = floor($y / 4);

        if(!isset($this->chars[$py][$px])) {
            $this->chars[$py][$px] = 0;
        }

        return [$x, $y, $px, $py, $this->chars[$py][$px]];
    }
}