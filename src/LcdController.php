<?php

namespace GameBoy;

class LcdController
{
    protected $core;

	public function __construct($core)
	{
        $this->core = $core;
	}

    /**
     * Scan Line and STAT Mode Control
     * @param  int $line Memory Scanline
     */
    public function scanLine($line)
    {
        //When turned off = Do nothing!
        //@TODO - Move LCDisOn to this class
        if ($this->core->LCDisOn) {
            if ($line < 143) {
                //We're on a normal scan line:
                if ($this->core->LCDTicks < 20) {
                    $this->core->scanLineMode2(); // mode2: 80 cycles
                } elseif ($this->core->LCDTicks < 63) {
                    $this->core->scanLineMode3(); // mode3: 172 cycles
                } elseif ($this->core->LCDTicks < 114) {
                    $this->core->scanLineMode0(); // mode0: 204 cycles
                } else {
                    //We're on a new scan line:
                    $this->core->LCDTicks -= 114;
                    $this->core->actualScanLine = ++$this->core->memory[0xFF44];
                    $this->core->matchLYC();
                    if ($this->core->STATTracker != 2) {
                        if ($this->core->hdmaRunning && !$this->core->halt && $this->core->LCDisOn) {
                            $this->core->performHdma(); //H-Blank DMA
                        }
                        if ($this->core->mode0TriggerSTAT) {
                            $this->core->memory[0xFF0F] |= 0x2; // set IF bit 1
                        }
                    }
                    $this->core->STATTracker = 0;
                    $this->core->scanLineMode2(); // mode2: 80 cycles
                    if ($this->core->LCDTicks >= 114) {
                        //We need to skip 1 or more scan lines:
                        $this->core->notifyScanline();
                        $this->scanLine($this->core->actualScanLine); //Scan Line and STAT Mode Control
                    }
                }
            } elseif ($line == 143) {
                //We're on the last visible scan line of the LCD screen:
                if ($this->core->LCDTicks < 20) {
                    $this->core->scanLineMode2(); // mode2: 80 cycles
                } elseif ($this->core->LCDTicks < 63) {
                    $this->core->scanLineMode3(); // mode3: 172 cycles
                } elseif ($this->core->LCDTicks < 114) {
                    $this->core->scanLineMode0(); // mode0: 204 cycles
                } else {
                    //Starting V-Blank:
                    //Just finished the last visible scan line:
                    $this->core->LCDTicks -= 114;
                    $this->core->actualScanLine = ++$this->core->memory[0xFF44];
                    $this->core->matchLYC();
                    if ($this->core->mode1TriggerSTAT) {
                        $this->core->memory[0xFF0F] |= 0x2; // set IF bit 1
                    }
                    if ($this->core->STATTracker != 2) {
                        if ($this->core->hdmaRunning && !$this->core->halt && $this->core->LCDisOn) {
                            $this->core->performHdma(); //H-Blank DMA
                        }
                        if ($this->core->mode0TriggerSTAT) {
                            $this->core->memory[0xFF0F] |= 0x2; // set IF bit 1
                        }
                    }
                    $this->core->STATTracker = 0;
                    $this->core->modeSTAT = 1;
                    $this->core->memory[0xFF0F] |= 0x1; // set IF flag 0
                    //LCD off takes at least 2 frames.
                    if ($this->core->drewBlank > 0) {
                        --$this->core->drewBlank;
                    }
                    if ($this->core->LCDTicks >= 114) {
                        //We need to skip 1 or more scan lines:
                        $this->scanLine($this->core->actualScanLine); //Scan Line and STAT Mode Control
                    }
                }
            } elseif ($line < 153) {
                //In VBlank
                if ($this->core->LCDTicks >= 114) {
                    //We're on a new scan line:
                    $this->core->LCDTicks -= 114;
                    $this->core->actualScanLine = ++$this->core->memory[0xFF44];
                    $this->core->matchLYC();
                    if ($this->core->LCDTicks >= 114) {
                        //We need to skip 1 or more scan lines:
                        $this->scanLine($this->core->actualScanLine); //Scan Line and STAT Mode Control
                    }
                }
            } else {
                //VBlank Ending (We're on the last actual scan line)
                if ($this->core->memory[0xFF44] == 153) {
                    $this->core->memory[0xFF44] = 0; //LY register resets to 0 early.
                    $this->core->matchLYC(); //LY==LYC Test is early here (Fixes specific one-line glitches (example: Kirby2 intro)).
                }
                if ($this->core->LCDTicks >= 114) {
                    //We reset back to the beginning:
                    $this->core->LCDTicks -= 114;
                    $this->core->actualScanLine = 0;
                    $this->core->scanLineMode2(); // mode2: 80 cycles
                    if ($this->core->LCDTicks >= 114) {
                        //We need to skip 1 or more scan lines:
                        $this->scanLine($this->core->actualScanLine); //Scan Line and STAT Mode Control
                    }
                }
            }
        }
    }
}