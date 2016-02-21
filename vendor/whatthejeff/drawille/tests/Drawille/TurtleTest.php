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

class TurtleTest extends \PHPUnit_Framework_TestCase
{
    private $turtle;

    protected function setUp() {
        $this->turtle = new Turtle();
    }

    public function testPosition() {
        $this->assertEquals(0, $this->turtle->getX());
        $this->assertEquals(0, $this->turtle->getY());

        $this->turtle->move(1, 2);
        $this->assertEquals(1, $this->turtle->getX());
        $this->assertEquals(2, $this->turtle->getY());
    }

    public function testRotation() {
        $this->assertEquals(0, $this->turtle->getRotation());

        $this->turtle->right(30);
        $this->assertEquals(30, $this->turtle->getRotation());


        $this->turtle->left(30);
        $this->assertEquals(0, $this->turtle->getRotation());
    }

    public function testBrush() {
        $this->assertFalse($this->turtle->get($this->turtle->getX(), $this->turtle->getY()));

        $this->turtle->forward(1);
        $this->assertTrue($this->turtle->get(0, 0));
        $this->assertTrue($this->turtle->get($this->turtle->getX(), $this->turtle->getY()));

        $this->turtle->up();
        $this->turtle->move(2, 0);
        $this->assertFalse($this->turtle->get($this->turtle->getX(), $this->turtle->getY()));

        $this->turtle->down();
        $this->turtle->move(3, 0);
        $this->assertTrue($this->turtle->get($this->turtle->getX(), $this->turtle->getY()));
    }
}