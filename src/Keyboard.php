<?php

namespace GameBoy;

class Keyboard
{
    public $core;
    public $file;
    public $keyPressing = null;
    public $started = false;

    public function __construct(Core $core)
    {
        $this->core = $core;
        exec('stty -icanon -echo');
        $this->file = fopen('php://stdin', 'r');
        stream_set_blocking($this->file, false);
    }

    public function check()
    {
        $key = fread($this->file, 1);

        if (!empty($key)) {
            $this->keyDown($key);
        } elseif (!empty($this->keyPressing)) {
            $this->keyUp($this->keyPressing);
        }

        $this->keyPressing = $key;
    }

    public function matchKey($key)
    {
        //Maps a keyboard key to a gameboy key.
        //Order: Right, Left, Up, Down, A, B, Select, Start

        $keyIndex = array_search($key, Settings::$settings[3]);

        if ($keyIndex === false) {
            return -1;
        }

        return $keyIndex;
    }

    public function keyDown($key)
    {
        $keyCode = $this->matchKey($key);

        if ($keyCode > -1) {
            $this->core->joyPadEvent($keyCode, true);
        }
    }

    public function keyUp($key)
    {
        $keyCode = $this->matchKey($key);

        if ($keyCode > -1) {
            $this->core->joyPadEvent($keyCode, false);
        }
    }
}
