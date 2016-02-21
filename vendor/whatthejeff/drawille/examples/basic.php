<?php

/*
 * This file is part of php-drawille
 *
 * (c) Jeff Welch <whatthejeff@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Drawille\Canvas;

$canvas = new Canvas();

for ($x = 0; $x <= 1800; $x++) {
    $canvas->set($x / 10, sin($x * M_PI / 180) * 10);
}

echo $canvas->frame(), "\n";
$canvas->clear();

for ($x = 0; $x <= 1800; $x += 10) {
    $canvas->set($x / 10, 10 + sin($x * M_PI / 180) * 10);
    $canvas->set($x / 10, 10 + cos($x * M_PI / 180) * 10);
}

echo $canvas->frame(), "\n";
$canvas->clear();

for ($x = 0; $x <= 3600; $x += 20) {
    $canvas->set($x / 20, 4 + sin($x * M_PI / 180) * 4);
}

echo $canvas->frame(), "\n";
$canvas->clear();

for ($x = 0; $x <= 360; $x += 4) {
    $canvas->set($x / 4, 30 + sin($x * M_PI / 180) * 30);
}

for ($x = 0; $x <= 30; $x++) {
    for ($y = 0; $y <= 30; $y++) {
        $canvas->set($x, $y);
        $canvas->toggle($x+30, $y+30);
        $canvas->toggle($x+60, $y);
    }
}

echo $canvas->frame(), "\n";