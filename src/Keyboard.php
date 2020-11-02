<?php

namespace GameBoy;

class Keyboard
{
    public $core;
    public $event;
    public $file;
    public $keymap;
    public $keyPressing = null;
    public $started = false;
    public $close = 0;

    public function __construct(Core $core)
    {
        $this->core = $core;
        $this->event = new \SDL_Event;
        $this->keymap =
        [
          "119"=>'w',"97"=>'a',"115"=>'s',"100"=>'d',"110"=>'n',"109"=>'m',"44"=>',',"46"=>'.'
        ];
        //exec('stty -icanon -echo');
        //$this->file = fopen('php://stdin', 'r');
        //stream_set_blocking($this->file, false);
    }

    public function check()
    {
      /*
        $key = fread($this->file, 1);

        if (!empty($key)) {
            $this->keyDown($key);
        } elseif (!empty($this->keyPressing)) {
            $this->keyUp($this->keyPressing);
        }

        $this->keyPressing = $key;
        */
        while( SDL_PollEvent( $this->event ) ){
  		    /* We are only worried about SDL_KEYDOWN and SDL_KEYUP events */
          if(isset($this->event->key->keysym)) {
          if(isset($this->keymap[$this->event->key->keysym->sym])) {
            $key = $this->keymap[$this->event->key->keysym->sym];
    		    switch($this->event->type){
    		      case SDL_KEYDOWN:
                $this->keyDown($key);
                echo "SDL_KEYDOWN ".$this->event->key->keysym->sym."\n";
    		        break;
    		      case SDL_KEYUP:
    					  $this->keyUp($key);
                echo "SDL_KEYUP ".$this->event->key->keysym->sym."\n";
    						break;
    		      default:
    		        break;
    		    }
            $this->keyPressing = $key;
          } }
          break;
  		  }
    }

    public function matchKey($key)
    {
        //Maps a keyboard key to a gameboy key.
        //Order: Right, Left, Up, Down, A, B, Select, Start

        $keyIndex = array_search($key, Settings::$keyboardButtonMap);

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
