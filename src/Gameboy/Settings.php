<?php
namespace GameBoy;

class Settings
{
    //Some settings.
	public static $settings = [
        //Turn on sound.
		false,

        //Force Mono sound.
		false,

        //Give priority to GameBoy mode
		true,

        //Keyboard button map.
		[39, 37, 38, 40, 88, 90, 16, 13],

        //Frameskip Amount (Auto frameskip setting allows the script to change this.)
		0,

        //Use the data URI BMP method over the canvas tag method?
		false,

        //How many tiles in each direction when using the BMP method (width * height).
		[16, 12],

        //Auto Frame Skip
		true,

        //Maximum Frame Skip
		29,

        //Override to allow for MBC1 instead of ROM only (compatibility for broken 3rd-party cartridges).
		true,

        //Override MBC RAM disabling and always allow reading and writing to the banks.
		true,

        //Audio granularity setting (Sampling of audio every x many machine cycles)
		20,

        //Frameskip base factor
		10,

        //Target number of machine cycles per loop. (4,194,300 / 1000 * 17)
		17826,

        //Sample Rate
		70000,

        //How many bits per WAV PCM sample (For browsers that fall back to WAV PCM generation)
		0x10,

        //Use the GBC BIOS?
		false,

        //Colorize GB mode?
		false,

        //Sample size for webkit audio.
		512,

        //Whether to display the canvas at 144x160 on fullscreen or as stretched.
		false,

        //Interval for the emulator loop.
		17,

        //Render nearest-neighbor scaling in javascript?
		false,

        //Disallow typed arrays?
		false
	];
}