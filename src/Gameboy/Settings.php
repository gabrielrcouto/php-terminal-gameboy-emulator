<?php
namespace GameBoy;

class Settings
{
    //Some settings.
	public static $settings = [
        //[0] - Turn on sound.
		false,

        //[1] - Force Mono sound.
		false,

        //[2] - Give priority to GameBoy mode
		true,

        //[3] - Keyboard button map.
		[39, 37, 38, 40, 88, 90, 16, 13],

        //[4] - Frameskip Amount (Auto frameskip setting allows the script to change this.)
		0,

        //[5] - Use the data URI BMP method over the canvas tag method?
		false,

        //[6] - How many tiles in each direction when using the BMP method (width * height).
		[16, 12],

        //[7] - Auto Frame Skip
		true,

        //[8] - Maximum Frame Skip
		29,

        //[9] - Override to allow for MBC1 instead of ROM only (compatibility for broken 3rd-party cartridges).
		true,

        //[10] - Override MBC RAM disabling and always allow reading and writing to the banks.
		true,

        //[11] - Audio granularity setting (Sampling of audio every x many machine cycles)
		20,

        //[12] - Frameskip base factor
		10,

        //[13] - Target number of machine cycles per loop. (4,194,300 / 1000 * 17)
		17826,

        //[14] - Sample Rate
		70000,

        //[15] - How many bits per WAV PCM sample (For browsers that fall back to WAV PCM generation)
		0x10,

        //[16] - Use the GBC BIOS?
		false,

        //[17] - Colorize GB mode?
		false,

        //[18] - Sample size for webkit audio.
		512,

        //[19] - Whether to display the canvas at 144x160 on fullscreen or as stretched.
		false,

        //[20] - Interval for the emulator loop.
		17,
	];
}