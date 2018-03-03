<?php

namespace GameBoy;

class LcdController
{
    //Actual scan line...
    public $actualScanLine = 0;

    protected $core;

    //Is the emulated LCD controller on?
    public $LCDisOn = false;

    //Should we trigger an interrupt if LY==LYC?
    public $LYCMatchTriggerSTAT = false;

    //The scan line mode (for lines 1-144 it's 2-3-0, for 145-154 it's 1)
    public $modeSTAT = 0;

    //Should we trigger an interrupt if in mode 0?
    public $mode0TriggerSTAT = false;

    //Should we trigger an interrupt if in mode 1?
    public $mode1TriggerSTAT = false;

    //Should we trigger an interrupt if in mode 2?
    public $mode2TriggerSTAT = false;

    //Tracker for STAT triggering.
    public $STATTracker = 0;

    public function __construct($core)
    {
        $this->core = $core;
    }

    public function matchLYC()
    {
        // LY - LYC Compare
        // If LY==LCY
        if ($this->core->memory[0xFF44] == $this->core->memory[0xFF45]) {
            $this->core->memory[0xFF41] |= 0x04; // set STAT bit 2: LY-LYC coincidence flag
            if ($this->LYCMatchTriggerSTAT) {
                $this->core->memory[0xFF0F] |= 0x2; // set IF bit 1
            }
        } else {
            $this->core->memory[0xFF41] &= 0xFB; // reset STAT bit 2 (LY!=LYC)
        }
    }

    public function notifyScanline()
    {
        if ($this->actualScanLine == 0) {
            $this->core->windowSourceLine = 0;
        }
        // determine the left edge of the window (160 if window is inactive)
        $windowLeft = ($this->core->gfxWindowDisplay && $this->core->memory[0xFF4A] <= $this->actualScanLine) ? min(160, $this->core->memory[0xFF4B] - 7) : 160;
        // step 1: background+window
        $skippedAnything = $this->core->drawBackgroundForLine($this->actualScanLine, $windowLeft, 0);
        // At this point, the high (alpha) byte in the frameBuffer is 0xff for colors 1,2,3 and
        // 0x00 for color 0. Foreground sprites draw on all colors, background sprites draw on
        // top of color 0 only.
        // step 2: sprites
        $this->core->drawSpritesForLine($this->actualScanLine);
        // step 3: prio tiles+window
        if ($skippedAnything) {
            $this->core->drawBackgroundForLine($this->actualScanLine, $windowLeft, 0x80);
        }
        if ($windowLeft < 160) {
            ++$this->core->windowSourceLine;
        }
    }

    /**
     * Scan Line and STAT Mode Control
     * @param  int $line Memory Scanline
     */
    public function scanLine($line)
    {
        //When turned off = Do nothing!
        if (!$this->LCDisOn) {
            return;
        }
    
        if ($line <= 143 && $this->core->LCDTicks < 20) {
            return $this->scanLineMode2(); // mode2: 80 cycles
        }
        if ($line <= 143 && $this->core->LCDTicks < 63) {
            return $this->scanLineMode3(); // mode3: 172 cycles
        }
        if ($line <= 143 && $this->core->LCDTicks < 114) {
            return $this->scanLineMode0(); // mode0: 204 cycles
        }
        
        if ($line <= 143) {
            $this->core->LCDTicks -= 114;
            $this->actualScanLine = ++$this->core->memory[0xFF44];
            $this->matchLYC();
            if ($this->mode1TriggerSTAT) {
                $this->core->memory[0xFF0F] |= 0x2; // set IF bit 1
            }
            if ($this->STATTracker != 2) {
                if ($this->core->hdmaRunning && !$this->core->halt && $this->LCDisOn) {
                    $this->core->performHdma(); //H-Blank DMA
                }
                if ($this->mode0TriggerSTAT) {
                    $this->core->memory[0xFF0F] |= 0x2; // set IF bit 1
                }
            }
            $this->STATTracker = 0;
        }
    
        //We're on a normal scan line:
        if ($line < 143) {
            //We're on a new scan line:
            $this->scanLineMode2(); // mode2: 80 cycles
            if ($this->core->LCDTicks >= 114) {
                //We need to skip 1 or more scan lines:
                $this->core->notifyScanline();
                $this->scanLine($this->actualScanLine); //Scan Line and STAT Mode Control
            }
            
            return;
        }
        
        if ($line == 143) {
            //We're on the last visible scan line of the LCD screen:
            
            //Starting V-Blank:
            //Just finished the last visible scan line:
            $this->modeSTAT = 1;
            $this->core->memory[0xFF0F] |= 0x1; // set IF flag 0
            //LCD off takes at least 2 frames.
            if ($this->core->drewBlank > 0) {
                --$this->core->drewBlank;
            }
            if ($this->core->LCDTicks >= 114) {
                //We need to skip 1 or more scan lines:
                $this->scanLine($this->actualScanLine); //Scan Line and STAT Mode Control
            }
            
            return;
        } 
        if ($line < 153) {
            if ($this->core->LCDTicks < 114) {
                return;
            }
            //In VBlank
            
            //We're on a new scan line:
            $this->core->LCDTicks -= 114;
            $this->actualScanLine = ++$this->core->memory[0xFF44];
            $this->matchLYC();
            if ($this->core->LCDTicks >= 114) {
                //We need to skip 1 or more scan lines:
                $this->scanLine($this->actualScanLine); //Scan Line and STAT Mode Control
            }
            
            return;
        }
        
        //VBlank Ending (We're on the last actual scan line)
        if ($this->core->memory[0xFF44] == 153) {
            $this->core->memory[0xFF44] = 0; //LY register resets to 0 early.
            $this->matchLYC(); //LY==LYC Test is early here (Fixes specific one-line glitches (example: Kirby2 intro)).
        }
        if ($this->core->LCDTicks >= 114) {
            //We reset back to the beginning:
            $this->core->LCDTicks -= 114;
            $this->actualScanLine = 0;
            $this->scanLineMode2(); // mode2: 80 cycles
            if ($this->core->LCDTicks >= 114) {
                //We need to skip 1 or more scan lines:
                $this->scanLine($this->actualScanLine); //Scan Line and STAT Mode Control
            }
        }
    }

    public function scanLineMode0()
    {
        // H-Blank
        if ($this->modeSTAT != 0) {
            if ($this->core->hdmaRunning && !$this->core->halt && $this->LCDisOn) {
                $this->performHdma(); //H-Blank DMA
            }
            if ($this->mode0TriggerSTAT || ($this->mode2TriggerSTAT && $this->STATTracker == 0)) {
                $this->core->memory[0xFF0F] |= 0x2; // if STAT bit 3 -> set IF bit1
            }
            $this->notifyScanline();
            $this->STATTracker = 2;
            $this->modeSTAT = 0;
        }
    }

    public function scanLineMode2()
    {
        // OAM in use
        if ($this->modeSTAT != 2) {
            if ($this->mode2TriggerSTAT) {
                $this->core->memory[0xFF0F] |= 0x2; // set IF bit 1
            }
            $this->STATTracker = 1;
            $this->modeSTAT = 2;
        }
    }

    public function scanLineMode3()
    {
        // OAM in use
        if ($this->modeSTAT != 3) {
            if ($this->mode2TriggerSTAT && $this->STATTracker == 0) {
                $this->core->memory[0xFF0F] |= 0x2; // set IF bit 1
            }
            $this->STATTracker = 1;
            $this->modeSTAT = 3;
        }
    }
}
