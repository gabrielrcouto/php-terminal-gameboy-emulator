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

use Drawille\Turtle;

$turtle = new Turtle();

for ($x = 0; $x < 36; $x++) {
    $turtle->right(10);

    for ($y = 0; $y < 36; $y++) {
        $turtle->right(10);
        $turtle->forward(8);
    }
}

echo $turtle->frame(), "\n";