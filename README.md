<p align="center"><img src="https://www.dropbox.com/s/hi6hmiv6ygs950o/HsaaQZKHrA.gif?dl=1" width="560" alt="PHP Terminal GameBoy Emulator" /></p>

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](http://gabrielrcouto.mit-license.org/)

Want to play Dr Mario or Pokémon on your server terminal? That's for you!

## Table of Contents

+ [Why](#why)
+ [Requirements](#requirements)
+ [Running](#running)
+ [Controls](#controls)
+ [TO-DO](#todo)
+ [Credits](#credits)

## Why

Some people will ask me: "Why you did that?"

Well, a friend asked me "What PHP can do?". I thinked about a while, and the idea comes. With PHP 7, it's now possible to emulate some systems because of the performance improvement :-) And come on, it's funny! \o/

It's based on a [GameBoy JS Emulator](https://github.com/taisel/GameBoy-Online).

## Requirements

The following versions of PHP are supported by this version.

+ PHP 5.6
+ PHP 7
+ HHVM

You will need a good terminal! Tested on MacOSX and Linux. Sorry Windows guys :-(

## Running

```bash
$ composer install
$ php boot.php drmario.rom
$ php boot.php pokemon.rom
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
