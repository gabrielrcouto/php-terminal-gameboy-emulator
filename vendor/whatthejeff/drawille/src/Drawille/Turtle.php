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
 * Basic turtle graphics interface
 *
 * @author Jeff Welch <whatthejeff@gmail.com>
 * @see    http://en.wikipedia.org/wiki/Turtle_graphics
 */
class Turtle extends Canvas
{
    /**
     * Current x position
     *
     * @var integer
     */
    private $x = 0;
    /**
     * Current y position
     *
     * @var integer
     */
    private $y = 0;
    /**
     * Current canvas rotation
     *
     * @var integer
     */
    private $rotation = 0;

    /**
     * If the pen is up
     *
     * @var boolean
     */
    private $up = false;

    /**
     * Constructor
     *
     * @param int $y starting x position
     * @param int $y starting y position
     */
    public function __construct($x = 0, $y = 0) {
        parent::__construct();

        $this->x = $x;
        $this->y = $y;
    }

    /**
     * Gets the current x position.
     *
     * @return integer x position
     */
    public function getX() {
        return $this->x;
    }

    /**
     * Gets the current y position.
     *
     * @return integer y position
     */
    public function getY() {
        return $this->y;
    }

    /**
     * Gets the current canvas rotation
     *
     * @return integer current canvas rotation
     */
    public function getRotation() {
        return $this->rotation;
    }

    /**
     * Push the pen down
     */
    public function down() {
        $this->up = false;
    }

    /**
     * Pull the pen up
     */
    public function up() {
        $this->up = true;
    }

    /**
     * Move the pen forward
     *
     * @param integer $length distance to move forward
     */
    public function forward($length) {
        $theta = $this->rotation / 180.0 * M_PI;
        $x     = $this->x + $length * cos($theta);
        $y     = $this->y + $length * sin($theta);

        $this->move($x, $y);
    }

    /**
     * Move the pen backwards
     *
     * @param integer $length distance to move backwards
     */
    public function back($length) {
        $this->forward(-$length);
    }

    /**
     * Angle the canvas to the right.
     *
     * @param integer $angle degree to angle
     */
    public function right($angle) {
        $this->rotation += $angle;
    }

    /**
     * Angle the canvas to the left.
     *
     * @param integer $angle degree to angle
     */
    public function left($angle) {
        $this->rotation -= $angle;
    }

    /**
     * Move the pen, drawing if the pen is down.
     *
     * @param int $y new x position
     * @param int $y new y position
     */
    public function move($x, $y) {
      if(!$this->up) {
        $x1 = round($this->x);
        $y1 = round($this->y);
        $x2 = $x;
        $y2 = $y;

        $xdiff = max($x1, $x2) - min($x1, $x2);
        $ydiff = max($y1, $y2) - min($y1, $y2);

        $xdir = $x1 <= $x2 ? 1 : -1;
        $ydir = $y1 <= $y2 ? 1 : -1;

        $r = max($xdiff, $ydiff);

        for($i = 0; $i <= $r; $i++) {
            $x = $x1;
            $y = $y1;

            if ($ydiff > 0) {
                $y += ((float)$i*$ydiff)/$r*$ydir;
            }

            if($xdiff > 0) {
                $x += ((float)$i*$xdiff)/$r*$xdir;
            }

            $this->set($x, $y);
        }
      }

      $this->x = $x;
      $this->y = $y;
    }

    /**
     * Pull the pen up
     */
    public function pu() {
         $this->up();
    }

    /**
     * Push the pen up
     */
    public function pd() {
         $this->down();
    }

    /**
     * Move the pen forward
     *
     * @param integer $length distance to move forward
     */
    public function fd($length) {
         $this->forward($length);
    }

    /**
     * Move the pen, drawing if the pen is down.
     *
     * @param int $y new x position
     * @param int $y new y position
     */
    public function mv($x, $y) {
         $this->move($x, $y);
    }

    /**
     * Angle the canvas to the right.
     *
     * @param integer $angle degree to angle
     */
    public function rt($angle) {
         $this->right($angle);
    }

    /**
     * Angle the canvas to the left.
     *
     * @param integer $angle degree to angle
     */
    public function lt($angle) {
         $this->left($angle);
    }

    /**
     * Move the pen backwards
     *
     * @param integer $length distance to move backwards
     */
    public function bk($length) {
         $this->back($length);
    }
}