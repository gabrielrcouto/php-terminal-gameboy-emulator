<p align="center"><img src="https://cloud.githubusercontent.com/assets/2197005/13260438/2f6e96ac-da3a-11e5-86cf-bbfa15083f74.gif" width="560" alt="PHP Terminal GameBoy Emulator" /></p>

[![Build Status](https://travis-ci.org/gabrielrcouto/php-terminal-gameboy-emulator.svg?branch=master)](https://travis-ci.org/gabrielrcouto/php-terminal-gameboy-emulator)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](http://gabrielrcouto.mit-license.org/)
[![Packagist](https://img.shields.io/badge/packagist-install-brightgreen.svg)](https://packagist.org/packages/gabrielrcouto/php-terminal-gameboy-emulator)

Want to play Dr Mario or Pokémon on your server terminal? That's for you!

## Table of Contents

+ [Why](#why)
+ [Requirements](#requirements)
+ [Running](#running)
+ [Controls](#controls)
+ [Tests](#tests)
+ [TO-DO](#todo)
+ [Credits](#credits)
+ [Legal](#legal)

## Why

Some people will ask me: _"Why you did that?"_

Well, a friend asked me _"What PHP can do?"_. I thought about that awhile and
the idea came up. With PHP7's performance improvement now it's possible to
emulate some systems :smile: and, come on, that's funny! :dancers:

It's based on the [GameBoy JS Emulator](https://github.com/taisel/GameBoy-Online).

## Requirements

The following PHP versions are supported:

+ PHP 5.6
+ PHP 7
+ HHVM

You will need a good terminal! I've tested only on MacOSX and Linux. I'm sorry
about that Windows guys :disappointed:

## Installation

Using composer:

```bash
$ composer g require gabrielrcouto/php-terminal-gameboy-emulator:dev-master
```

Using PHAR:

```bash
$ wget https://raw.githubusercontent.com/gabrielrcouto/php-terminal-gameboy-emulator/master/bin/php-gameboy.phar
$ chmod +x php-gameboy.phar
$ mv php-gameboy.phar /usr/local/bin/php-gameboy
```

## Running

```bash
$ php-gameboy --help
```

Your roms are loaded from the directory you are running the `php-gameboy` command.

```bash
$ php-gameboy play drmario.gb
$ php-gameboy play pokemon.gbc
```

If you like to run this emulator locally, simple clone the repository:

```bash
$ git clone https://github.com/gabrielrcouto/php-terminal-gameboy-emulator.git
$ cd php-terminal-gameboy-emulator
$ composer install -o
```

For running roms, pass the full path to your rom or put then in the `php-terminal-gameboy-emulator` folder:

```bash
$ bin/php-gameboy play pokemon.gbc
$ bin/php-gameboy play /full/path/to/your/rom/drmario.gb
```

## Controls

```bash
_n_________________
|_|_______________|_|
|  ,-------------.  |
| |  .---------.  | |
| |  |         |  | |
| |  |         |  | |
| |  |         |  | |
| |  |         |  | |
| |  `---------'  | |
| `---------------' |
|   _               |
| _|W|_         ,-. |
||A   D|   ,-. "._,"|
|  |S|    "._," Dot |
|    _  _ Comma     |
|   // //           |
|  // //    \\\\\\  |
|  N  M      \\\\\\ ,
|________...______,"
```

+ Left = A
+ Up = W
+ Down = S
+ Right = D
+ A = Comma (,)
+ B = Dot (.)
+ Select = N
+ Start = M

## Tests

You can use the following command to run the most common checks, such as `php -l`, `phpcs`:

    $ ant check


## TO-DO

Converting from the JS paradigm was a lot of work, and I still need to adapt somethings like:

- [x] Code standard - PSRs, please!
- [ ] Array of functions - Maybe in PHP it's not the best approach
- [ ] Pixel auxiliary array - Very CPU intersive to convert RGBA every time
- [ ] Classes - Core is too big!
- [ ] Profiling and otimizing - XHProf to find the most intensive functions
- [ ] Save/Restore - I need to save my Pokémon, please!

## Credits

[@gabrielrcouto](http://www.twitter.com/gabrielrcouto)

## Legal

The purpose of this project was to study all the capabilities of PHP.

It does not have any commercial or profitable intentions.

The user is responsible to use this code and its content in the terms of the law.

The author is completely against piracy and respects all the copyrights, trademarks and patents of Nintendo.
