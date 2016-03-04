<?php

namespace GameBoy;

class Settings
{
    //Audio granularity setting (Sampling of audio every x many machine cycles)
    public static $audioGranularity = 20;

    //Auto Frame Skip
    public static $autoFrameskip = true;

    //Colorize GB mode?
    public static $colorize = false;

    //Keyboard button map.
    //Order: Right, Left, Up, Down, A, B, Select, Start
    public static $keyboardButtonMap = ['d', 'a', 'w', 's', ',', '.', 'n', 'm'];

    //Frameskip Amount (Auto frameskip setting allows the script to change this.)
    public static $frameskipAmout = 0;

    //Frameskip base factor
    public static $frameskipBaseFactor = 10;

    //Maximum Frame Skip
    public static $frameskipMax = 29;

    //Interval for the emulator loop.
    public static $loopInterval = 17;

    //Target number of machine cycles per loop. (4,194,300 / 1000 * 17)
    public static $machineCyclesPerLoop = 17826;

    //Override MBC RAM disabling and always allow reading and writing to the banks.
    public static $overrideMBC = true;

    //Override to allow for MBC1 instead of ROM only (compatibility for broken 3rd-party cartridges).
    public static $overrideMBC1 = true;

    //Give priority to GameBoy mode
    public static $priorizeGameBoyMode = true;

    //Sample Rate
    public static $sampleRate = 70000;
}
