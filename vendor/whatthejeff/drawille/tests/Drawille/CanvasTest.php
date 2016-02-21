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

class CanvasTest extends \PHPUnit_Framework_TestCase
{
    private $canvas;

    protected function setUp() {
        $this->canvas = new Canvas();
    }

    public function testSet() {
        $this->canvas->set(0, 0);
        $this->assertEquals([[1]], $this->canvas->getChars());
    }

    /**
     * @depends testSet
     */
    public function testReset() {
        $this->canvas->set(0, 0);
        $this->canvas->reset(0, 0);
        $this->assertEquals([[0]], $this->canvas->getChars());
    }

    /**
     * @depends testSet
     */
    public function testClear() {
        $this->canvas->set(0, 0);
        $this->canvas->clear();
        $this->assertEmpty($this->canvas->getChars());
    }

    public function testToggle() {
        $this->canvas->toggle(0, 0);
        $this->assertEquals([[1]], $this->canvas->getChars());

        $this->canvas->toggle(0, 0);
        $this->assertEquals([[0]], $this->canvas->getChars());
    }

    /**
     * @depends testSet
     */
    public function testFrame() {
        $this->assertEquals($this->canvas->frame(), '');

        $this->canvas->set(0, 0);
        $this->assertEquals($this->canvas->frame(), '⠁');
    }

    /**
     * @depends testSet
     */
    public function testGet() {
        $this->assertFalse($this->canvas->get(0, 0));
        $this->canvas->set(0, 0);

        $this->assertTrue($this->canvas->get(0, 0));
        $this->assertFalse($this->canvas->get(1, 0));
        $this->assertFalse($this->canvas->get(0, 1));
        $this->assertFalse($this->canvas->get(1, 1));
    }
}