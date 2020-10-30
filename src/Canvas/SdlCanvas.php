<?php

namespace GameBoy\Canvas;

use GameBoy\Settings;

class SdlCanvas implements DrawContextInterface
{
    private $serial = 0;
    public $colorEnabled = false;
    public $sdl;
    public $renderer;
    public $pixels;

    public function __construct() {
  		$this->sdl = SDL_CreateWindow('PHP TerminalGameboy', SDL_WINDOWPOS_UNDEFINED, SDL_WINDOWPOS_UNDEFINED, 640, 576, SDL_WINDOW_SHOWN);
  		$this->renderer = SDL_CreateRenderer($this->sdl, 0, SDL_RENDERER_SOFTWARE);
  		SDL_SetRenderDrawColor($this->renderer, 0, 0, 0, 255);
  		SDL_RenderClear($this->renderer);
    }
    public function draw($canvasBuffer)
    {
        if(count($canvasBuffer) > 0) {
          for ($y = 0; $y < 160; $y++) {
              for ($x = 0; $x < 144; $x++) {
                  $index = ($x + ($y * 160));
                  if($canvasBuffer[$index] == 1) {
                    $fill = 155;
                  } else {
                    $fill = 0;
                  }
                  if(!isset($this->pixels[$x][$y])) {
                    $this->pixels[$x][$y] = new \SDL_Rect($x*4, $y*4, 4, 4);
                  }
                  SDL_SetRenderDrawColor($this->renderer, $fill, $fill, $fill, 155);
                  SDL_RenderFillRect($this->renderer, $this->pixels[$x][$y]);
              }
          }
          SDL_RenderPresent($this->renderer);
        }
    }
}
