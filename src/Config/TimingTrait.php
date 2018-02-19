<?php

namespace GameBoy\Config;

trait timingTrait
{

    //
    //Timing Variables
    //

    //Used to sample the audio system every x CPU instructions.
    public $audioTicks = 0;

    //Times for how many instructions to execute before ending the loop.
    public $emulatorTicks = 0;

    // DIV Ticks Counter (Invisible lower 8-bit)
    public $DIVTicks = 14;

    // ScanLine Counter
    public $LCDTicks = 15;

    // Timer Ticks Count
    public $timerTicks = 0;

    // Timer Max Ticks
    public $TACClocker = 256;

    //Are the interrupts on queue to be enabled?
    public $untilEnable = 0;

    //The last time we iterated the main loop.
    public $lastIteration = 0;
}
