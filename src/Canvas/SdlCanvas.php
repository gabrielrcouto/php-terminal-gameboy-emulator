<?php

namespace GameBoy\Canvas;

use GameBoy\Settings;

class SdlCanvas implements DrawContextInterface
{
    private $serial = 0;
    public $colorEnabled = true;
    public $sdl;
    public $renderer;
    public $pixels;
    public $greenoffset = 0;
    public $core;

    public function __construct() {
      $this->core = $core;
  		$this->sdl = SDL_CreateWindow('PHP TerminalGameboy', SDL_WINDOWPOS_UNDEFINED, SDL_WINDOWPOS_UNDEFINED, 640, 576, SDL_WINDOW_SHOWN);
  		$this->renderer = SDL_CreateRenderer($this->sdl, 0, SDL_RENDERER_SOFTWARE);
  		SDL_SetRenderDrawColor($this->renderer, 0, 0, 0, 255);
  		SDL_RenderClear($this->renderer);
      if($this->core->cGBC == false) {
        $this->greenoffset = 50;
      }
    }
    public function draw($canvasBuffer)
    {
      //print_r($canvasBuffer); die();
      //echo count($canvasBuffer);
        if(count($canvasBuffer) > 0) {
            for ($y = 0; $y < 144; $y++) {
                for ($x = 0; $x < 160; $x++) {
                    $index = ($x + ($y * 160))*4;
                    if($canvasBuffer[$index] != "") {
                        $fill = [$canvasBuffer[$index], $canvasBuffer[$index+1], $canvasBuffer[$index+2]];
                        if(!isset($this->pixels[$x][$y])) {
                          $this->pixels[$x][$y] = new \SDL_Rect($x*4, $y*4, 4, 4);
                        }
                        SDL_SetRenderDrawColor($this->renderer, $fill[0], $fill[1], $fill[2], 255);
                        SDL_RenderFillRect($this->renderer, $this->pixels[$x][$y]);
                    }

                  }
          }
          SDL_RenderPresent($this->renderer);
        }
    }
}
