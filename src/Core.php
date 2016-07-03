<?php

namespace GameBoy;

class Core
{
    // LCD Context
    public $drawContext = null;

    //The game's ROM.
    public $ROMImage;

    //The full ROM file dumped to an array.
    public $ROM = [];

    //Whether we're in the GBC boot ROM.
    public $inBootstrap = true;

    // Accumulator (default is GB mode)
    public $registerA = 0x01;

    // bit 7 - Zero
    public $FZero = true;

    // bit 6 - Sub
    public $FSubtract = false;

    // bit 5 - Half Carry
    public $FHalfCarry = true;

    // bit 4 - Carry
    public $FCarry = true;

    // Register B
    public $registerB = 0x00;

    // Register C
    public $registerC = 0x13;

    // Register D
    public $registerD = 0x00;

    // Register E
    public $registerE = 0xD8;

    // Registers H and L
    public $registersHL = 0x014D;

    // Stack Pointer
    public $stackPointer = 0xFFFE;

    // Program Counter
    public $programCounter = 0x0100;

    //Has the CPU been suspended until the next interrupt?
    public $halt = false;

    //Did we trip the DMG Halt bug?
    public $skipPCIncrement = false;

    //Has the emulation been paused or a frame has ended?
    public $stopEmulator = 3;

    //Are interrupts enabled?
    public $IME = true;

    //HDMA Transfer Flag - GBC only
    public $hdmaRunning = false;

    //The number of clock cycles emulated.
    public $CPUTicks = 0;

    //GBC Speed Multiplier
    public $multiplier = 1;

    //
    //Main RAM, MBC RAM, GBC Main RAM, VRAM, etc.
    //

    //Main Core Memory
    public $memory = [];

    //Switchable RAM (Used by games for more RAM) for the main memory range 0xA000 - 0xC000.
    public $MBCRam = [];

    //Extra VRAM bank for GBC.
    public $VRAM = [];

    //Current VRAM bank for GBC.
    public $currVRAMBank = 0;

    //GBC main RAM Banks
    public $GBCMemory = [];

    //MBC1 Type (4/32, 16/8)
    public $MBC1Mode = false;

    //MBC RAM Access Control.
    public $MBCRAMBanksEnabled = false;

    //MBC Currently Indexed RAM Bank
    public $currMBCRAMBank = 0;

    //MBC Position Adder;
    public $currMBCRAMBankPosition = -0xA000;

    //GameBoy Color detection.
    public $cGBC = false;

    //Currently Switched GameBoy Color ram bank
    public $gbcRamBank = 1;

    //GBC RAM offset from address start.
    public $gbcRamBankPosition = -0xD000;

    //GBC RAM (ECHO mirroring) offset from address start.
    public $gbcRamBankPositionECHO = -0xF000;

    //Used to map the RAM banks to maximum size the MBC used can do.
    public $RAMBanks = [0, 1, 2, 4, 16];

    //Offset of the ROM bank switching.
    public $ROMBank1offs = 0;

    //The parsed current ROM bank selection.
    public $currentROMBank = 0;

    //Cartridge Type
    public $cartridgeType = 0;

    //Name of the game
    public $name = '';

    //Game code (Suffix for older games)
    public $gameCode = '';

    //A boolean to see if this was loaded in as a save state.
    public $fromSaveState = false;

    //When loaded in as a save state, this will not be empty.
    public $savedStateFileName = '';

    //lcdControllerler object
    public $lcdController = null;

    public $gfxWindowY = false;

    public $gfxWindowDisplay = false;

    public $gfxSpriteShow = false;

    public $gfxSpriteDouble = false;

    public $gfxBackgroundY = false;

    public $gfxBackgroundX = false;

    public $TIMAEnabled = false;

    //Joypad State (two four-bit states actually)
    public $JoyPad = 0xFF;

    //
    //RTC:
    //
    public $RTCisLatched = true;

    public $latchedSeconds = 0;

    public $latchedMinutes = 0;

    public $latchedHours = 0;

    public $latchedLDays = 0;

    public $latchedHDays = 0;

    public $RTCSeconds = 0;

    public $RTCMinutes = 0;

    public $RTCHours = 0;

    public $RTCDays = 0;

    public $RTCDayOverFlow = false;

    public $RTCHALT = false;

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

    //
    //ROM Cartridge Components:
    //

    //Does the cartridge use MBC1?
    public $cMBC1 = false;

    //Does the cartridge use MBC2?
    public $cMBC2 = false;

    //Does the cartridge use MBC3?
    public $cMBC3 = false;

    //Does the cartridge use MBC5?
    public $cMBC5 = false;

    //Does the cartridge use save RAM?
    public $cSRAM = false;

    public $cMMMO1 = false;

    //Does the cartridge use the RUMBLE addressing (modified MBC5)?
    public $cRUMBLE = false;

    public $cCamera = false;

    public $cTAMA5 = false;

    public $cHuC3 = false;

    public $cHuC1 = false;

    // 1 Bank = 16 KBytes = 256 Kbits
    public $ROMBanks = [
        2, 4, 8, 16, 32, 64, 128, 256, 512,
    ];

    //How many RAM banks were actually allocated?
    public $numRAMBanks = 0;

    //
    //Graphics Variables
    //

    //To prevent the repeating of drawing a blank screen.
    public $drewBlank = 0;

    // tile data arrays
    public $tileData = [];

    public $frameBuffer = [];

    public $canvasBuffer;

    public $gbcRawPalette = [];

    //GB: 384, GBC: 384 * 2
    public $tileCount = 384;

    public $tileCountInvalidator;

    public $colorCount = 12;

    public $gbPalette = [];

    public $gbColorizedPalette = [];

    public $gbcPalette = [];

    // min "attrib" value where transparency can occur (Default is 4 (GB mode))
    public $transparentCutoff = 4;

    public $bgEnabled = true;

    public $spritePriorityEnabled = true;

    // true if there are any images to be invalidated
    public $tileReadState = [];

    public $windowSourceLine = 0;

    //"Classic" GameBoy palette colors.
    public $colors = [0x80EFFFDE, 0x80ADD794, 0x80529273, 0x80183442];

    //Frame skip tracker
    public $frameCount;

    public $weaveLookup = [];

    public $width = 160;

    public $height = 144;

    public $pixelCount;

    public $rgbCount;

    //Pointer to the current palette we're using (Used for palette switches during boot or so it can be done anytime)
    public $palette = null;

    //
    //Data
    //

    public $DAATable;

    public $ffxxDump;

    public $TICKTable;

    public $SecondaryTICKTable;

    // Added

    public $cTIMER = null;

    public function __construct($ROMImage, $drawContext)
    {
        $this->drawContext = $drawContext;
        $this->ROMImage = $ROMImage;

        $this->tileCountInvalidator = $this->tileCount * 4;

        $this->ROMBanks[0x52] = 72;
        $this->ROMBanks[0x53] = 80;
        $this->ROMBanks[0x54] = 96;

        $this->frameCount = Settings::$frameskipBaseFactor;
        $this->pixelCount = $this->width * $this->height;
        $this->rgbCount = $this->pixelCount * 4;

        // Copy Data
        $this->DAATable = Data::$DAATable;
        $this->ffxxDump = Data::$ffxxDump;

        $this->TICKTable = TICKTables::$primary;
        $this->SecondaryTICKTable = TICKTables::$secondary;

        //Initialize the LCD Controller
        $this->lcdController = new LcdController($this);
    }

    public function saveState()
    {
        return [
            $this->fromTypedArray($this->ROM),
            $this->inBootstrap,
            $this->registerA,
            $this->FZero,
            $this->FSubtract,
            $this->FHalfCarry,
            $this->FCarry,
            $this->registerB,
            $this->registerC,
            $this->registerD,
            $this->registerE,
            $this->registersHL,
            $this->stackPointer,
            $this->programCounter,
            $this->halt,
            $this->IME,
            $this->hdmaRunning,
            $this->CPUTicks,
            $this->multiplier,
            $this->fromTypedArray($this->memory),
            $this->fromTypedArray($this->MBCRam),
            $this->fromTypedArray($this->VRAM),
            $this->currVRAMBank,
            $this->fromTypedArray($this->GBCMemory),
            $this->MBC1Mode,
            $this->MBCRAMBanksEnabled,
            $this->currMBCRAMBank,
            $this->currMBCRAMBankPosition,
            $this->cGBC,
            $this->gbcRamBank,
            $this->gbcRamBankPosition,
            $this->ROMBank1offs,
            $this->currentROMBank,
            $this->cartridgeType,
            $this->name,
            $this->gameCode,
            $this->lcdController->modeSTAT,
            $this->lcdController->LYCMatchTriggerSTAT,
            $this->lcdController->mode2TriggerSTAT,
            $this->lcdController->mode1TriggerSTAT,
            $this->lcdController->mode0TriggerSTAT,
            $this->lcdController->LCDisOn,
            $this->gfxWindowY,
            $this->gfxWindowDisplay,
            $this->gfxSpriteShow,
            $this->gfxSpriteDouble,
            $this->gfxBackgroundY,
            $this->gfxBackgroundX,
            $this->TIMAEnabled,
            $this->DIVTicks,
            $this->LCDTicks,
            $this->timerTicks,
            $this->TACClocker,
            $this->untilEnable,
            $this->lastIteration,
            $this->cMBC1,
            $this->cMBC2,
            $this->cMBC3,
            $this->cMBC5,
            $this->cSRAM,
            $this->cMMMO1,
            $this->cRUMBLE,
            $this->cCamera,
            $this->cTAMA5,
            $this->cHuC3,
            $this->cHuC1,
            $this->drewBlank,
            $this->tileData,
            $this->fromTypedArray($this->frameBuffer),
            $this->tileCount,
            $this->colorCount,
            $this->gbPalette,
            $this->gbcRawPalette,
            $this->gbcPalette,
            $this->transparentCutoff,
            $this->bgEnabled,
            $this->spritePriorityEnabled,
            $this->fromTypedArray($this->tileReadState),
            $this->windowSourceLine,
            $this->lcdController->actualScanLine,
            $this->RTCisLatched,
            $this->latchedSeconds,
            $this->latchedMinutes,
            $this->latchedHours,
            $this->latchedLDays,
            $this->latchedHDays,
            $this->RTCSeconds,
            $this->RTCMinutes,
            $this->RTCHours,
            $this->RTCDays,
            $this->RTCDayOverFlow,
            $this->RTCHALT,
            $this->gbColorizedPalette,
            $this->skipPCIncrement,
            $this->lcdController->STATTracker,
            $this->gbcRamBankPositionECHO,
            $this->numRAMBanks,
        ];
    }

    public function returnFromState($returnedFrom)
    {
        $address = 0;
        $state = $returnedFrom->slice(0);

        $this->ROM = $this->toTypedArray($state[$address++], false, false);
        $this->inBootstrap = $state[$address++];
        $this->registerA = $state[$address++];
        $this->FZero = $state[$address++];
        $this->FSubtract = $state[$address++];
        $this->FHalfCarry = $state[$address++];
        $this->FCarry = $state[$address++];
        $this->registerB = $state[$address++];
        $this->registerC = $state[$address++];
        $this->registerD = $state[$address++];
        $this->registerE = $state[$address++];
        $this->registersHL = $state[$address++];
        $this->stackPointer = $state[$address++];
        $this->programCounter = $state[$address++];
        $this->halt = $state[$address++];
        $this->IME = $state[$address++];
        $this->hdmaRunning = $state[$address++];
        $this->CPUTicks = $state[$address++];
        $this->multiplier = $state[$address++];
        $this->memory = $this->toTypedArray($state[$address++], false, false);
        $this->MBCRam = $this->toTypedArray($state[$address++], false, false);
        $this->VRAM = $this->toTypedArray($state[$address++], false, false);
        $this->currVRAMBank = $state[$address++];
        $this->GBCMemory = $this->toTypedArray($state[$address++], false, false);
        $this->MBC1Mode = $state[$address++];
        $this->MBCRAMBanksEnabled = $state[$address++];
        $this->currMBCRAMBank = $state[$address++];
        $this->currMBCRAMBankPosition = $state[$address++];
        $this->cGBC = $state[$address++];
        $this->gbcRamBank = $state[$address++];
        $this->gbcRamBankPosition = $state[$address++];
        $this->ROMBank1offs = $state[$address++];
        $this->currentROMBank = $state[$address++];
        $this->cartridgeType = $state[$address++];
        $this->name = $state[$address++];
        $this->gameCode = $state[$address++];
        $this->lcdController->modeSTAT = $state[$address++];
        $this->lcdController->LYCMatchTriggerSTAT = $state[$address++];
        $this->lcdController->mode2TriggerSTAT = $state[$address++];
        $this->lcdController->mode1TriggerSTAT = $state[$address++];
        $this->lcdController->mode0TriggerSTAT = $state[$address++];
        $this->lcdController->LCDisOn = $state[$address++];
        $this->gfxWindowY = $state[$address++];
        $this->gfxWindowDisplay = $state[$address++];
        $this->gfxSpriteShow = $state[$address++];
        $this->gfxSpriteDouble = $state[$address++];
        $this->gfxBackgroundY = $state[$address++];
        $this->gfxBackgroundX = $state[$address++];
        $this->TIMAEnabled = $state[$address++];
        $this->DIVTicks = $state[$address++];
        $this->LCDTicks = $state[$address++];
        $this->timerTicks = $state[$address++];
        $this->TACClocker = $state[$address++];
        $this->untilEnable = $state[$address++];
        $this->lastIteration = $state[$address++];
        $this->cMBC1 = $state[$address++];
        $this->cMBC2 = $state[$address++];
        $this->cMBC3 = $state[$address++];
        $this->cMBC5 = $state[$address++];
        $this->cSRAM = $state[$address++];
        $this->cMMMO1 = $state[$address++];
        $this->cRUMBLE = $state[$address++];
        $this->cCamera = $state[$address++];
        $this->cTAMA5 = $state[$address++];
        $this->cHuC3 = $state[$address++];
        $this->cHuC1 = $state[$address++];
        $this->drewBlank = $state[$address++];
        $this->tileData = $state[$address++];
        $this->frameBuffer = $this->toTypedArray($state[$address++], true, false);
        $this->tileCount = $state[$address++];
        $this->colorCount = $state[$address++];
        $this->gbPalette = $state[$address++];
        $this->gbcRawPalette = $state[$address++];
        $this->gbcPalette = $state[$address++];
        $this->transparentCutoff = $state[$address++];
        $this->bgEnabled = $state[$address++];
        $this->spritePriorityEnabled = $state[$address++];
        $this->tileReadState = $this->toTypedArray($state[$address++], false, false);
        $this->windowSourceLine = $state[$address++];
        $this->lcdController->actualScanLine = $state[$address++];
        $this->RTCisLatched = $state[$address++];
        $this->latchedSeconds = $state[$address++];
        $this->latchedMinutes = $state[$address++];
        $this->latchedHours = $state[$address++];
        $this->latchedLDays = $state[$address++];
        $this->latchedHDays = $state[$address++];
        $this->RTCSeconds = $state[$address++];
        $this->RTCMinutes = $state[$address++];
        $this->RTCHours = $state[$address++];
        $this->RTCDays = $state[$address++];
        $this->RTCDayOverFlow = $state[$address++];
        $this->RTCHALT = $state[$address++];
        $this->gbColorizedPalette = $state[$address++];
        $this->skipPCIncrement = $state[$address++];
        $this->lcdController->STATTracker = $state[$address++];
        $this->gbcRamBankPositionECHO = $state[$address++];
        $this->numRAMBanks = $state[$address];
        $this->tileCountInvalidator = $this->tileCount * 4;
        $this->fromSaveState = true;
        $this->checkPaletteType();
        $this->initLCD();
        $this->drawToCanvas();
    }

    public function start()
    {
        Settings::$frameskipAmout = 0; //Reset the frame skip setting.
        $this->initMemory(); //Write the startup memory.
        $this->ROMLoad(); //Load the ROM into memory and get cartridge information from it.
        $this->initLCD(); //Initializae the graphics.
        $this->run(); //Start the emulation.
    }

    public function initMemory()
    {
        //Initialize the RAM:
        $this->memory = $this->getTypedArray(0x10000, 0, 'uint8');
        $this->frameBuffer = $this->getTypedArray(23040, 0x00FFFFFF, 'int32');
        $this->gbPalette = $this->arrayPad(12, 0); //32-bit signed
        $this->gbColorizedPalette = $this->arrayPad(12, 0); //32-bit signed
        $this->gbcRawPalette = $this->arrayPad(0x80, -1000); //32-bit signed
        $this->gbcPalette = [0x40]; //32-bit signed
        //Initialize the GBC Palette:
        $address = 0x3F;

        while ($address >= 0) {
            $this->gbcPalette[$address] = ($address < 0x20) ? -1 : 0;
            --$address;
        }
    }

    public function initSkipBootstrap()
    {
        //Start as an unset device:
        echo 'Starting without the GBC boot ROM'.PHP_EOL;

        $this->programCounter = 0x100;
        $this->stackPointer = 0xFFFE;
        $this->IME = true;
        $this->LCDTicks = 15;
        $this->DIVTicks = 14;
        $this->registerA = ($this->cGBC) ? 0x11 : 0x1;
        $this->registerB = 0;
        $this->registerC = 0x13;
        $this->registerD = 0;
        $this->registerE = 0xD8;
        $this->FZero = true;
        $this->FSubtract = false;
        $this->FHalfCarry = true;
        $this->FCarry = true;
        $this->registersHL = 0x014D;

        //Fill in the boot ROM set register values
        //Default values to the GB boot ROM values, then fill in the GBC boot ROM values after ROM loading
        $address = 0xFF;

        while ($address >= 0) {
            if ($address >= 0x30 && $address < 0x40) {
                $this->memoryWrite(0xFF00 + $address, $this->ffxxDump[$address]);
            } else {
                switch ($address) {
                    case 0x00:
                    case 0x01:
                    case 0x02:
                    case 0x07:
                    case 0x0F:
                    case 0x40:
                    case 0xFF:
                        $this->memoryWrite(0xFF00 + $address, $this->ffxxDump[$address]);
                        break;
                    default:
                        $this->memory[0xFF00 + $address] = $this->ffxxDump[$address];
                }
            }
            --$address;
        }
    }

    public function initBootstrap()
    {
        //Start as an unset device:
        echo 'Starting the GBC boot ROM.'.PHP_EOL;

        $this->programCounter = 0;
        $this->stackPointer = 0;
        $this->IME = false;
        $this->LCDTicks = 0;
        $this->DIVTicks = 0;
        $this->registerA = 0;
        $this->registerB = 0;
        $this->registerC = 0;
        $this->registerD = 0;
        $this->registerE = 0;
        $this->FZero = $this->FSubtract = $this->FHalfCarry = $this->FCarry = false;
        $this->registersHL = 0;
        $this->memory[0xFF00] = 0xF; //Set the joypad state.
    }

    public function ROMLoad()
    {
        //Load the first two ROM banks (0x0000 - 0x7FFF) into regular gameboy memory:
        $this->ROM = $this->getTypedArray(strlen($this->ROMImage), 0, 'uint8');

        for ($romIndex = 0; $romIndex < strlen($this->ROMImage); ++$romIndex) {
            $this->ROM[$romIndex] = (ord($this->ROMImage[$romIndex]) & 0xFF);
            if ($romIndex < 0x8000) {
                $this->memory[$romIndex] = $this->ROM[$romIndex]; //Load in the game ROM.
            }
        }
        // ROM name
        for ($address = 0x134; $address < 0x13F; ++$address) {
            if (ord($this->ROMImage[$address]) > 0) {
                $this->name .= $this->ROMImage[$address];
            }
        }

        // ROM game code (for newer games)
        for ($address = 0x13F; $address < 0x143; ++$address) {
            if (ord($this->ROMImage[$address]) > 0) {
                $this->gameCode .= $this->ROMImage[$address];
            }
        }

        echo 'Game Title: '.$this->name.'['.$this->gameCode.']['.$this->ROMImage[0x143].']'.PHP_EOL;

        echo 'Game Code: '.$this->gameCode.PHP_EOL;

        // Cartridge type
        $this->cartridgeType = $this->ROM[0x147];
        echo 'Cartridge type #'.$this->cartridgeType.PHP_EOL;

        //Map out ROM cartridge sub-types.
        $MBCType = '';

        switch ($this->cartridgeType) {
            case 0x00:
                //ROM w/o bank switching
                if (!Settings::$overrideMBC1) {
                    $MBCType = 'ROM';
                    break;
                }
                // no break
            case 0x01:
                $this->cMBC1 = true;
                $MBCType = 'MBC1';
                break;
            case 0x02:
                $this->cMBC1 = true;
                $this->cSRAM = true;
                $MBCType = 'MBC1 + SRAM';
                break;
            case 0x03:
                $this->cMBC1 = true;
                $this->cSRAM = true;
                $this->cBATT = true;
                $MBCType = 'MBC1 + SRAM + BATT';
                break;
            case 0x05:
                $this->cMBC2 = true;
                $MBCType = 'MBC2';
                break;
            case 0x06:
                $this->cMBC2 = true;
                $this->cBATT = true;
                $MBCType = 'MBC2 + BATT';
                break;
            case 0x08:
                $this->cSRAM = true;
                $MBCType = 'ROM + SRAM';
                break;
            case 0x09:
                $this->cSRAM = true;
                $this->cBATT = true;
                $MBCType = 'ROM + SRAM + BATT';
                break;
            case 0x0B:
                $this->cMMMO1 = true;
                $MBCType = 'MMMO1';
                break;
            case 0x0C:
                $this->cMMMO1 = true;
                $this->cSRAM = true;
                $MBCType = 'MMMO1 + SRAM';
                break;
            case 0x0D:
                $this->cMMMO1 = true;
                $this->cSRAM = true;
                $this->cBATT = true;
                $MBCType = 'MMMO1 + SRAM + BATT';
                break;
            case 0x0F:
                $this->cMBC3 = true;
                $this->cTIMER = true;
                $this->cBATT = true;
                $MBCType = 'MBC3 + TIMER + BATT';
                break;
            case 0x10:
                $this->cMBC3 = true;
                $this->cTIMER = true;
                $this->cBATT = true;
                $this->cSRAM = true;
                $MBCType = 'MBC3 + TIMER + BATT + SRAM';
                break;
            case 0x11:
                $this->cMBC3 = true;
                $MBCType = 'MBC3';
                break;
            case 0x12:
                $this->cMBC3 = true;
                $this->cSRAM = true;
                $MBCType = 'MBC3 + SRAM';
                break;
            case 0x13:
                $this->cMBC3 = true;
                $this->cSRAM = true;
                $this->cBATT = true;
                $MBCType = 'MBC3 + SRAM + BATT';
                break;
            case 0x19:
                $this->cMBC5 = true;
                $MBCType = 'MBC5';
                break;
            case 0x1A:
                $this->cMBC5 = true;
                $this->cSRAM = true;
                $MBCType = 'MBC5 + SRAM';
                break;
            case 0x1B:
                $this->cMBC5 = true;
                $this->cSRAM = true;
                $this->cBATT = true;
                $MBCType = 'MBC5 + SRAM + BATT';
                break;
            case 0x1C:
                $this->cRUMBLE = true;
                $MBCType = 'RUMBLE';
                break;
            case 0x1D:
                $this->cRUMBLE = true;
                $this->cSRAM = true;
                $MBCType = 'RUMBLE + SRAM';
                break;
            case 0x1E:
                $this->cRUMBLE = true;
                $this->cSRAM = true;
                $this->cBATT = true;
                $MBCType = 'RUMBLE + SRAM + BATT';
                break;
            case 0x1F:
                $this->cCamera = true;
                $MBCType = 'GameBoy Camera';
                break;
            case 0xFD:
                $this->cTAMA5 = true;
                $MBCType = 'TAMA5';
                break;
            case 0xFE:
                $this->cHuC3 = true;
                $MBCType = 'HuC3';
                break;
            case 0xFF:
                $this->cHuC1 = true;
                $MBCType = 'HuC1';
                break;
            default:
                $MBCType = 'Unknown';
                throw new RuntimeException('Cartridge type is unknown.');
        }

        echo 'Cartridge Type: '.$MBCType.PHP_EOL;

        // ROM and RAM banks
        $this->numROMBanks = $this->ROMBanks[$this->ROM[0x148]];

        echo $this->numROMBanks.' ROM banks.'.PHP_EOL;

        switch ($this->RAMBanks[$this->ROM[0x149]]) {
            case 0:
                echo 'No RAM banking requested for allocation or MBC is of type 2.'.PHP_EOL;
                break;
            case 2:
                echo '1 RAM bank requested for allocation.'.PHP_EOL;
                break;
            case 3:
                echo '4 RAM banks requested for allocation.'.PHP_EOL;
                break;
            case 4:
                echo '16 RAM banks requested for allocation.'.PHP_EOL;
                break;
            default:
                echo 'RAM bank amount requested is unknown, will use maximum allowed by specified MBC type.'.PHP_EOL;
        }

        //Check the GB/GBC mode byte:
        switch ($this->ROM[0x143]) {
            case 0x00: //Only GB mode
                $this->cGBC = false;
                echo 'Only GB mode detected.'.PHP_EOL;
                break;
            case 0x80: //Both GB + GBC modes
                $this->cGBC = !Settings::$priorizeGameBoyMode;
                echo 'GB and GBC mode detected.'.PHP_EOL;
                break;
            case 0xC0: //Only GBC mode
                $this->cGBC = true;
                echo 'Only GBC mode detected.'.PHP_EOL;
                break;
            default:
                $this->cGBC = false;
                echo 'Unknown GameBoy game type code #'.$this->ROM[0x143].", defaulting to GB mode (Old games don't have a type code).".PHP_EOL;
        }

        $this->inBootstrap = false;
        $this->setupRAM(); //CPU/(V)RAM initialization.
        $this->initSkipBootstrap();

        $this->checkPaletteType();
        //License Code Lookup:
        $cOldLicense = $this->ROM[0x14B];
        $cNewLicense = ($this->ROM[0x144] & 0xFF00) | ($this->ROM[0x145] & 0xFF);
        if ($cOldLicense != 0x33) {
            //Old Style License Header
            echo 'Old style license code: '.$cOldLicense.PHP_EOL;
        } else {
            //New Style License Header
            echo 'New style license code: '.$cNewLicense.PHP_EOL;
        }
    }

    public function disableBootROM()
    {
        //Remove any traces of the boot ROM from ROM memory.
        for ($address = 0; $address < 0x900; ++$address) {
            //Skip the already loaded in ROM header.
            if ($address < 0x100 || $address >= 0x200) {
                $this->memory[$address] = $this->ROM[$address]; //Replace the GameBoy Color boot ROM with the game ROM.
            }
        }
        $this->checkPaletteType();

        if (!$this->cGBC) {
            //Clean up the post-boot (GB mode only) state:
            echo 'Stepping down from GBC mode.'.PHP_EOL;
            $this->tileCount /= 2;
            $this->tileCountInvalidator = $this->tileCount * 4;
            if (!Settings::$colorize) {
                $this->transparentCutoff = 4;
            }
            $this->colorCount = 12;

            // @TODO
            // $this->tileData.length = $this->tileCount * $this->colorCount;

            unset($this->VRAM);
            unset($this->GBCMemory);
            //Possible Extra: shorten some gfx arrays to the length that we need (Remove the unused indices)
        }
    }

    public function setupRAM()
    {
        //Setup the auxilliary/switchable RAM to their maximum possible size (Bad headers can lie).
        if ($this->cMBC2) {
            $this->numRAMBanks = 1 / 16;
        } elseif ($this->cMBC1 || $this->cRUMBLE || $this->cMBC3 || $this->cHuC3) {
            $this->numRAMBanks = 4;
        } elseif ($this->cMBC5) {
            $this->numRAMBanks = 16;
        } elseif ($this->cSRAM) {
            $this->numRAMBanks = 1;
        }
        if ($this->numRAMBanks > 0) {
            if (!$this->MBCRAMUtilized()) {
                //For ROM and unknown MBC cartridges using the external RAM:
                $this->MBCRAMBanksEnabled = true;
            }
            //Switched RAM Used
            $this->MBCRam = $this->getTypedArray($this->numRAMBanks * 0x2000, 0, 'uint8');
        }
        echo 'Actual bytes of MBC RAM allocated: '.($this->numRAMBanks * 0x2000).PHP_EOL;
        //Setup the RAM for GBC mode.
        if ($this->cGBC) {
            $this->VRAM = $this->getTypedArray(0x2000, 0, 'uint8');
            $this->GBCMemory = $this->getTypedArray(0x7000, 0, 'uint8');
            $this->tileCount *= 2;
            $this->tileCountInvalidator = $this->tileCount * 4;
            $this->colorCount = 64;
            $this->transparentCutoff = 32;
        }
        $this->tileData = $this->arrayPad($this->tileCount * $this->colorCount, null);
        $this->tileReadState = $this->getTypedArray($this->tileCount, 0, 'uint8');
    }

    public function MBCRAMUtilized()
    {
        return $this->cMBC1 || $this->cMBC2 || $this->cMBC3 || $this->cMBC5 || $this->cRUMBLE;
    }

    public function initLCD()
    {
        $this->transparentCutoff = (Settings::$colorize || $this->cGBC) ? 32 : 4;
        if (count($this->weaveLookup) == 0) {
            //Setup the image decoding lookup table:
            $this->weaveLookup = $this->getTypedArray(256, 0, 'uint16');
            for ($i_ = 0x1; $i_ <= 0xFF; ++$i_) {
                for ($d_ = 0; $d_ < 0x8; ++$d_) {
                    $this->weaveLookup[$i_] += (($i_ >> $d_) & 1) << ($d_ * 2);
                }
            }
        }

        $this->width = 160;
        $this->height = 144;

        //Get a CanvasPixelArray buffer:
        //Create a white screen

        if ($this->drawContext->colorEnabled) {
            $this->canvasBuffer = array_fill(0, 4 * $this->width * $this->height, 255);

            $address = $this->pixelCount;
            $address2 = $this->rgbCount;

            while ($address > 0) {
                $this->frameBuffer[--$address] = 0x00FFFFFF;
                $this->canvasBuffer[$address2 -= 4] = 0xFF;
                $this->canvasBuffer[$address2 + 1] = 0xFF;
                $this->canvasBuffer[$address2 + 2] = 0xFF;
                $this->canvasBuffer[$address2 + 3] = 0xFF;
            }
        } else {
            $this->canvasBuffer = array_fill(0, 4 * $this->width * $this->height, false);
            $this->frameBuffer = array_fill(0, $this->pixelCount, 0x00FFFFFF);
        }

        $this->drawContext->draw($this->canvasBuffer);
    }

    public function joyPadEvent($key, $down)
    {
        if ($down) {
            $this->JoyPad &= 0xFF ^ (1 << $key);
        } else {
            $this->JoyPad |= (1 << $key);
        }
        $this->memory[0xFF00] = ($this->memory[0xFF00] & 0x30) + (((($this->memory[0xFF00] & 0x20) == 0) ? ($this->JoyPad >> 4) : 0xF) & ((($this->memory[0xFF00] & 0x10) == 0) ? ($this->JoyPad & 0xF) : 0xF));
    }

    public function run()
    {
        //The preprocessing before the actual iteration loop:
        try {
            if (($this->stopEmulator & 2) == 0) {
                if (($this->stopEmulator & 1) == 1) {
                    $this->stopEmulator = 0;
                    $this->clockUpdate(); //Frame skip and RTC code.

                    //If no HALT... Execute normally
                    if (!$this->halt) {
                        $this->executeIteration();
                    //If we bailed out of a halt because the iteration ran down its timing.
                    } else {
                        $this->CPUTicks = 1;
                        Opcode::run($this, 0x76);
                        //Execute Interrupt:
                        $this->runInterrupt();
                        //Timing:
                        $this->updateCore();
                        $this->executeIteration();
                    }
                //We can only get here if there was an internal error, but the loop was restarted.
                } else {
                    echo 'Iterator restarted a faulted core.'.PHP_EOL;
                    pause();
                }
            }
        } catch (\Exception $error) {
            if ($error->getMessage() != 'HALT_OVERRUN') {
                echo 'GameBoy runtime error'.PHP_EOL;
            }
        }
    }

    public function executeIteration()
    {
        //Iterate the interpreter loop:
        $op = 0;

        while ($this->stopEmulator == 0) {
            //Fetch the current opcode.
            $op = $this->memoryRead($this->programCounter);
            if (!$this->skipPCIncrement) {
                //Increment the program counter to the next instruction:
                $this->programCounter = ($this->programCounter + 1) & 0xFFFF;
            }
            $this->skipPCIncrement = false;
            //Get how many CPU cycles the current op code counts for:
            $this->CPUTicks = $this->TICKTable[$op];
            //Execute the OP code instruction:
            Opcode::{'opcode'.$op}($this);
            //Interrupt Arming:
            switch ($this->untilEnable) {
                case 1:
                    $this->IME = true;
                    // no break
                case 2:
                    $this->untilEnable--;
                    // no break
            }
            //Execute Interrupt:
            if ($this->IME) {
                $this->runInterrupt();
            }
            //Timing:
            $this->updateCore();
        }
    }

    public function runInterrupt()
    {
        $bitShift = 0;
        $testbit = 1;
        $interrupts = $this->memory[0xFFFF] & $this->memory[0xFF0F];

        while ($bitShift < 5) {
            //Check to see if an interrupt is enabled AND requested.
            if (($testbit & $interrupts) == $testbit) {
                $this->IME = false; //Reset the interrupt enabling.
                $this->memory[0xFF0F] -= $testbit; //Reset the interrupt request.
                //Set the stack pointer to the current program counter value:
                $this->stackPointer = $this->unswtuw($this->stackPointer - 1);
                $this->memoryWrite($this->stackPointer, $this->programCounter >> 8);
                $this->stackPointer = $this->unswtuw($this->stackPointer - 1);
                $this->memoryWrite($this->stackPointer, $this->programCounter & 0xFF);
                //Set the program counter to the interrupt's address:
                $this->programCounter = 0x0040 + ($bitShift * 0x08);
                //Interrupts have a certain clock cycle length:
                $this->CPUTicks += 5; //People say it's around 5.
                break; //We only want the highest priority interrupt.
            }

            $testbit = 1 << ++$bitShift;
        }
    }

    public function updateCore()
    {
        // DIV control
        $this->DIVTicks += $this->CPUTicks;
        if ($this->DIVTicks >= 0x40) {
            $this->DIVTicks -= 0x40;
            $this->memory[0xFF04] = ($this->memory[0xFF04] + 1) & 0xFF; // inc DIV
        }
        //LCD Controller Ticks
        $timedTicks = $this->CPUTicks / $this->multiplier;
        // LCD Timing
        $this->LCDTicks += $timedTicks; //LCD timing
        $this->lcdController->scanLine($this->lcdController->actualScanLine); //Scan Line and STAT Mode Control

        //Audio Timing
        $this->audioTicks += $timedTicks; //Not the same as the LCD timing (Cannot be altered by display on/off changes!!!).

        //Are we past the granularity setting?
        if ($this->audioTicks >= Settings::$audioGranularity) {
            //Emulator Timing (Timed against audio for optimization):
            $this->emulatorTicks += $this->audioTicks;
            if ($this->emulatorTicks >= Settings::$machineCyclesPerLoop) {
                //Make sure we don't overdo the audio.
                if (($this->stopEmulator & 1) == 0) {
                    //LCD off takes at least 2 frames.
                    if ($this->drewBlank == 0) {
                        $this->drawToCanvas(); //Display frame
                    }
                }
                $this->stopEmulator |= 1; //End current loop.
                $this->emulatorTicks = 0;
            }
            $this->audioTicks = 0;
        }

        // Internal Timer
        if ($this->TIMAEnabled) {
            $this->timerTicks += $this->CPUTicks;
            while ($this->timerTicks >= $this->TACClocker) {
                $this->timerTicks -= $this->TACClocker;
                if ($this->memory[0xFF05] == 0xFF) {
                    $this->memory[0xFF05] = $this->memory[0xFF06];
                    $this->memory[0xFF0F] |= 0x4; // set IF bit 2
                } else {
                    ++$this->memory[0xFF05];
                }
            }
        }
    }

    public function displayShowOff()
    {
        if ($this->drewBlank == 0) {
            if ($this->drawContext->colorEnabled) {
                $this->canvasBuffer = array_fill(0, 4 * $this->width * $this->height, 255);
            } else {
                $this->canvasBuffer = array_fill(0, 4 * $this->width * $this->height, false);
            }

            $this->drawContext->draw($this->canvasBuffer);
            $this->drewBlank = 2;
        }
    }

    public function performHdma()
    {
        $this->CPUTicks += 1 + (8 * $this->multiplier);

        $dmaSrc = ($this->memory[0xFF51] << 8) + $this->memory[0xFF52];
        $dmaDstRelative = ($this->memory[0xFF53] << 8) + $this->memory[0xFF54];
        $dmaDstFinal = $dmaDstRelative + 0x10;
        $tileRelative = $this->tileData->length - $this->tileCount;

        if ($this->currVRAMBank == 1) {
            while ($dmaDstRelative < $dmaDstFinal) {
                // Bkg Tile data area
                if ($dmaDstRelative < 0x1800) {
                    $tileIndex = ($dmaDstRelative >> 4) + 384;
                    if ($this->tileReadState[$tileIndex] == 1) {
                        $r = $tileRelative + $tileIndex;
                        do {
                            $this->tileData[$r] = null;
                            $r -= $this->tileCount;
                        } while ($r >= 0);
                        $this->tileReadState[$tileIndex] = 0;
                    }
                }
                $this->VRAM[$dmaDstRelative++] = $this->memoryRead($dmaSrc++);
            }
        } else {
            while ($dmaDstRelative < $dmaDstFinal) {
                // Bkg Tile data area
                if ($dmaDstRelative < 0x1800) {
                    $tileIndex = $dmaDstRelative >> 4;
                    if ($this->tileReadState[$tileIndex] == 1) {
                        $r = $tileRelative + $tileIndex;

                        do {
                            $this->tileData[$r] = null;
                            $r -= $this->tileCount;
                        } while ($r >= 0);

                        $this->tileReadState[$tileIndex] = 0;
                    }
                }
                $this->memory[0x8000 + $dmaDstRelative++] = $this->memoryRead($dmaSrc++);
            }
        }

        $this->memory[0xFF51] = (($dmaSrc & 0xFF00) >> 8);
        $this->memory[0xFF52] = ($dmaSrc & 0x00F0);
        $this->memory[0xFF53] = (($dmaDstFinal & 0x1F00) >> 8);
        $this->memory[0xFF54] = ($dmaDstFinal & 0x00F0);
        if ($this->memory[0xFF55] == 0) {
            $this->hdmaRunning = false;
            $this->memory[0xFF55] = 0xFF; //Transfer completed ("Hidden last step," since some ROMs don't imply this, but most do).
        } else {
            --$this->memory[0xFF55];
        }
    }

    public function clockUpdate()
    {
        //We're tying in the same timer for RTC and frame skipping, since we can and this reduces load.
        if (Settings::$autoFrameskip || $this->cTIMER) {
            $timeElapsed = ((int) (microtime(true) * 1000)) - $this->lastIteration; //Get the numnber of milliseconds since this last executed.
            if ($this->cTIMER && !$this->RTCHALT) {
                //Update the MBC3 RTC:
                $this->RTCSeconds += $timeElapsed / 1000;
                //System can stutter, so the seconds difference can get large, thus the "while".
                while ($this->RTCSeconds >= 60) {
                    $this->RTCSeconds -= 60;
                    ++$this->RTCMinutes;
                    if ($this->RTCMinutes >= 60) {
                        $this->RTCMinutes -= 60;
                        ++$this->RTCHours;
                        if ($this->RTCHours >= 24) {
                            $this->RTCHours -= 24;
                            ++$this->RTCDays;
                            if ($this->RTCDays >= 512) {
                                $this->RTCDays -= 512;
                                $this->RTCDayOverFlow = true;
                            }
                        }
                    }
                }
            }
            if (Settings::$autoFrameskip) {
                //Auto Frame Skip:
                if ($timeElapsed > Settings::$loopInterval) {
                    //Did not finish in time...
                    if (Settings::$frameskipAmout < Settings::$frameskipMax) {
                        ++Settings::$frameskipAmout;
                    }
                } elseif (Settings::$frameskipAmout > 0) {
                    //We finished on time, decrease frame skipping (throttle to somewhere just below full speed)...
                    --Settings::$frameskipAmout;
                }
            }
            $this->lastIteration = (int) (microtime(true) * 1000);
        }
    }

    public function drawToCanvas()
    {
        //Draw the frame buffer to the canvas:
        if (Settings::$frameskipAmout == 0 || $this->frameCount > 0) {
            //Copy and convert the framebuffer data to the CanvasPixelArray format.
            $bufferIndex = $this->pixelCount;
            $canvasIndex = $this->rgbCount;

            if ($this->drawContext->colorEnabled) {
                // Generate colored CanvasBuffer Version
                while ($canvasIndex > 3) {
                    //Red
                    $this->canvasBuffer[$canvasIndex -= 4] = ($this->frameBuffer[--$bufferIndex] >> 16) & 0xFF;
                    //Green
                    $this->canvasBuffer[$canvasIndex + 1] = ($this->frameBuffer[$bufferIndex] >> 8) & 0xFF;
                    //Blue
                    $this->canvasBuffer[$canvasIndex + 2] = $this->frameBuffer[$bufferIndex] & 0xFF;
                }
            } else {
                // Generate black&white CanvasBuffer Version
                while ($bufferIndex > 0) {
                    $r = ($this->frameBuffer[--$bufferIndex] >> 16) & 0xFF;
                    $g = ($this->frameBuffer[$bufferIndex] >> 8) & 0xFF;
                    $b = $this->frameBuffer[$bufferIndex] & 0xFF;

                    // 350 is a good threshold for black and white
                    if ($r + $g + $b > 350) {
                        $this->canvasBuffer[$bufferIndex] = true;
                    } else {
                        $this->canvasBuffer[$bufferIndex] = false;
                    }
                }
            }

            //Draw out the CanvasPixelArray data:
            $this->drawContext->draw($this->canvasBuffer);

            if (Settings::$frameskipAmout > 0) {
                //Decrement the frameskip counter:
                $this->frameCount -= Settings::$frameskipAmout;
            }
        } else {
            //Reset the frameskip counter:
            $this->frameCount += Settings::$frameskipBaseFactor;
        }
    }

    public function invalidateAll($pal)
    {
        $stop = ($pal + 1) * $this->tileCountInvalidator;
        for ($r = $pal * $this->tileCountInvalidator; $r < $stop; ++$r) {
            $this->tileData[$r] = null;
        }
    }

    public function setGBCPalettePre($address_, $data)
    {
        if ($this->gbcRawPalette[$address_] == $data) {
            return;
        }
        $this->gbcRawPalette[$address_] = $data;
        if ($address_ >= 0x40 && ($address_ & 0x6) == 0) {
            // stay transparent
            return;
        }
        $value = ($this->gbcRawPalette[$address_ | 1] << 8) + $this->gbcRawPalette[$address_ & -2];
        $this->gbcPalette[$address_ >> 1] = 0x80000000 + (($value & 0x1F) << 19) + (($value & 0x3E0) << 6) + (($value & 0x7C00) >> 7);
        $this->invalidateAll($address_ >> 3);
    }

    public function setGBCPalette($address_, $data)
    {
        $this->setGBCPalettePre($address_, $data);
        if (($address_ & 0x6) == 0) {
            $this->gbcPalette[$address_ >> 1] &= 0x00FFFFFF;
        }
    }

    public function decodePalette($startIndex, $data)
    {
        if (!$this->cGBC) {
            $this->gbPalette[$startIndex] = $this->colors[$data & 0x03] & 0x00FFFFFF; // color 0: transparent
            $this->gbPalette[$startIndex + 1] = $this->colors[($data >> 2) & 0x03];
            $this->gbPalette[$startIndex + 2] = $this->colors[($data >> 4) & 0x03];
            $this->gbPalette[$startIndex + 3] = $this->colors[$data >> 6];

            //@PHP - Need to copy the new palette
            $this->checkPaletteType();
        }
    }

    public function drawBackgroundForLine($line, $windowLeft, $priority)
    {
        $skippedTile = false;
        $tileNum = 0;
        $tileXCoord = 0;
        $tileAttrib = 0;
        $sourceY = $line + $this->memory[0xFF42];
        $sourceImageLine = $sourceY & 0x7;
        $tileX = $this->memory[0xFF43] >> 3;
        $memStart = (($this->gfxBackgroundY) ? 0x1C00 : 0x1800) + (($sourceY & 0xF8) << 2);
        $screenX = -($this->memory[0xFF43] & 7);

        for (; $screenX < $windowLeft; $tileX++, $screenX += 8) {
            $tileXCoord = ($tileX & 0x1F);
            $baseaddr = $this->memory[0x8000 + $memStart + $tileXCoord];
            $tileNum = ($this->gfxBackgroundX) ? $baseaddr : (($baseaddr > 0x7F) ? (($baseaddr & 0x7F) + 0x80) : ($baseaddr + 0x100));
            if ($this->cGBC) {
                $mapAttrib = $this->VRAM[$memStart + $tileXCoord];
                if (($mapAttrib & 0x80) != $priority) {
                    $skippedTile = true;
                    continue;
                }
                $tileAttrib = (($mapAttrib & 0x07) << 2) + (($mapAttrib >> 5) & 0x03);
                $tileNum += 384 * (($mapAttrib >> 3) & 0x01); // tile vram bank
            }
            $this->drawPartCopy($tileNum, $screenX, $line, $sourceImageLine, $tileAttrib);
        }

        if ($windowLeft < 160) {
            // window!
            $windowStartAddress = ($this->gfxWindowY) ? 0x1C00 : 0x1800;
            $windowSourceTileY = $this->windowSourceLine >> 3;
            $tileAddress = $windowStartAddress + ($windowSourceTileY * 0x20);
            $windowSourceTileLine = $this->windowSourceLine & 0x7;
            for ($screenX = $windowLeft; $screenX < 160; $tileAddress++, $screenX += 8) {
                $baseaddr = $this->memory[0x8000 + $tileAddress];
                $tileNum = ($this->gfxBackgroundX) ? $baseaddr : (($baseaddr > 0x7F) ? (($baseaddr & 0x7F) + 0x80) : ($baseaddr + 0x100));
                if ($this->cGBC) {
                    $mapAttrib = $this->VRAM[$tileAddress];
                    if (($mapAttrib & 0x80) != $priority) {
                        $skippedTile = true;
                        continue;
                    }
                    $tileAttrib = (($mapAttrib & 0x07) << 2) + (($mapAttrib >> 5) & 0x03); // mirroring
                    $tileNum += 384 * (($mapAttrib >> 3) & 0x01); // tile vram bank
                }
                $this->drawPartCopy($tileNum, $screenX, $line, $windowSourceTileLine, $tileAttrib);
            }
        }

        return $skippedTile;
    }

    public function drawPartCopy($tileIndex, $x, $y, $sourceLine, $attribs)
    {
        $image = $this->tileData[$tileIndex + $this->tileCount * $attribs] ? $this->tileData[$tileIndex + $this->tileCount * $attribs] : $this->updateImage($tileIndex, $attribs);
        $dst = $x + $y * 160;
        $src = $sourceLine * 8;
        $dstEnd = ($x > 152) ? (($y + 1) * 160) : ($dst + 8);
        // adjust left
        if ($x < 0) {
            $dst -= $x;
            $src -= $x;
        }

        while ($dst < $dstEnd) {
            $this->frameBuffer[$dst++] = $image[$src++];
        }
    }

    public function checkPaletteType()
    {
        //Reference the correct palette ahead of time...
        $this->palette = ($this->cGBC) ? $this->gbcPalette : ((Settings::$colorize) ? $this->gbColorizedPalette : $this->gbPalette);
    }

    public function updateImage($tileIndex, $attribs)
    {
        $address_ = $tileIndex + $this->tileCount * $attribs;
        $otherBank = ($tileIndex >= 384);
        $offset = $otherBank ? (($tileIndex - 384) << 4) : ($tileIndex << 4);
        $paletteStart = $attribs & 0xFC;
        $transparent = $attribs >= $this->transparentCutoff;
        $pixix = 0;
        $pixixdx = 1;
        $pixixdy = 0;
        $tempPix = $this->getTypedArray(64, 0, 'int32');
        if (($attribs & 2) != 0) {
            $pixixdy = -16;
            $pixix = 56;
        }
        if (($attribs & 1) == 0) {
            $pixixdx = -1;
            $pixix += 7;
            $pixixdy += 16;
        }
        for ($y = 8; --$y >= 0;) {
            $num = $this->weaveLookup[$this->VRAMReadGFX($offset++, $otherBank)] + ($this->weaveLookup[$this->VRAMReadGFX($offset++, $otherBank)] << 1);
            if ($num != 0) {
                $transparent = false;
            }
            for ($x = 8; --$x >= 0;) {
                $tempPix[$pixix] = $this->palette[$paletteStart + ($num & 3)] & -1;
                $pixix += $pixixdx;
                $num >>= 2;
            }
            $pixix += $pixixdy;
        }
        $this->tileData[$address_] = ($transparent) ? true : $tempPix;

        $this->tileReadState[$tileIndex] = 1;

        return $this->tileData[$address_];
    }

    public function drawSpritesForLine($line)
    {
        if (!$this->gfxSpriteShow) {
            return;
        }
        $minSpriteY = $line - (($this->gfxSpriteDouble) ? 15 : 7);
        // either only do priorityFlag == 0 (all foreground),
        // or first 0x80 (background) and then 0 (foreground)
        $priorityFlag = $this->spritePriorityEnabled ? 0x80 : 0;
        for (; $priorityFlag >= 0; $priorityFlag -= 0x80) {
            $oamIx = 159;
            while ($oamIx >= 0) {
                $attributes = 0xFF & $this->memory[0xFE00 + $oamIx--];
                if (($attributes & 0x80) == $priorityFlag || !$this->spritePriorityEnabled) {
                    $tileNum = (0xFF & $this->memory[0xFE00 + $oamIx--]);
                    $spriteX = (0xFF & $this->memory[0xFE00 + $oamIx--]) - 8;
                    $spriteY = (0xFF & $this->memory[0xFE00 + $oamIx--]) - 16;
                    $offset = $line - $spriteY;
                    if ($spriteX >= 160 || $spriteY < $minSpriteY || $offset < 0) {
                        continue;
                    }
                    if ($this->gfxSpriteDouble) {
                        $tileNum = $tileNum & 0xFE;
                    }
                    $spriteAttrib = ($attributes >> 5) & 0x03; // flipx: from bit 0x20 to 0x01, flipy: from bit 0x40 to 0x02
                    if ($this->cGBC) {
                        $spriteAttrib += 0x20 + (($attributes & 0x07) << 2); // palette
                        $tileNum += (384 >> 3) * ($attributes & 0x08); // tile vram bank
                    } else {
                        // attributes 0x10: 0x00 = OBJ1 palette, 0x10 = OBJ2 palette
                        // spriteAttrib: 0x04: OBJ1 palette, 0x08: OBJ2 palette
                        $spriteAttrib += 0x4 + (($attributes & 0x10) >> 2);
                    }
                    if ($priorityFlag == 0x80) {
                        // background
                        if ($this->gfxSpriteDouble) {
                            if (($spriteAttrib & 2) != 0) {
                                $this->drawPartBgSprite(($tileNum | 1) - ($offset >> 3), $spriteX, $line, $offset & 7, $spriteAttrib);
                            } else {
                                $this->drawPartBgSprite(($tileNum & -2) + ($offset >> 3), $spriteX, $line, $offset & 7, $spriteAttrib);
                            }
                        } else {
                            $this->drawPartBgSprite($tileNum, $spriteX, $line, $offset, $spriteAttrib);
                        }
                    } else {
                        // foreground
                        if ($this->gfxSpriteDouble) {
                            if (($spriteAttrib & 2) != 0) {
                                $this->drawPartFgSprite(($tileNum | 1) - ($offset >> 3), $spriteX, $line, $offset & 7, $spriteAttrib);
                            } else {
                                $this->drawPartFgSprite(($tileNum & -2) + ($offset >> 3), $spriteX, $line, $offset & 7, $spriteAttrib);
                            }
                        } else {
                            $this->drawPartFgSprite($tileNum, $spriteX, $line, $offset, $spriteAttrib);
                        }
                    }
                } else {
                    $oamIx -= 3;
                }
            }
        }
    }

    public function drawPartFgSprite($tileIndex, $x, $y, $sourceLine, $attribs)
    {
        $im = $this->tileData[$tileIndex + $this->tileCount * $attribs] ? $this->tileData[$tileIndex + $this->tileCount * $attribs] : $this->updateImage($tileIndex, $attribs);
        if ($im === true) {
            return;
        }
        $dst = $x + $y * 160;
        $src = $sourceLine * 8;
        $dstEnd = ($x > 152) ? (($y + 1) * 160) : ($dst + 8);
        // adjust left
        if ($x < 0) {
            $dst -= $x;
            $src -= $x;
        }

        while ($dst < $dstEnd) {
            $this->frameBuffer[$dst] = $im[$src];
            ++$dst;
            ++$src;
        }
    }

    public function drawPartBgSprite($tileIndex, $x, $y, $sourceLine, $attribs)
    {
        $im = $this->tileData[$tileIndex + $this->tileCount * $attribs] ? $this->tileData[$tileIndex + $this->tileCount * $attribs] : $this->updateImage($tileIndex, $attribs);
        if ($im === true) {
            return;
        }
        $dst = $x + $y * 160;
        $src = $sourceLine * 8;
        $dstEnd = ($x > 152) ? (($y + 1) * 160) : ($dst + 8);
        // adjust left
        if ($x < 0) {
            $dst -= $x;
            $src -= $x;
        }
        while ($dst < $dstEnd) {
            //if ($im[$src] < 0 && $this->frameBuffer[$dst] >= 0) {
            $this->frameBuffer[$dst] = $im[$src];
            // }
            ++$dst;
            ++$src;
        }
    }

    //Memory Reading:
    public function memoryRead($address)
    {
        if ($address < 0x4000) {
            return $this->memory[$address];
        } elseif ($address < 0x8000) {
            return $this->ROM[$this->currentROMBank + $address];
        } elseif ($address >= 0x8000 && $address < 0xA000) {
            if ($this->cGBC) {
                //CPU Side Reading The VRAM (Optimized for GameBoy Color)
                return ($this->lcdController->modeSTAT > 2) ? 0xFF : (($this->currVRAMBank == 0) ? $this->memory[$address] : $this->VRAM[$address - 0x8000]);
            }

            //CPU Side Reading The VRAM (Optimized for classic GameBoy)
            return ($this->lcdController->modeSTAT > 2) ? 0xFF : $this->memory[$address];
        } elseif ($address >= 0xA000 && $address < 0xC000) {
            if (($this->numRAMBanks == 1 / 16 && $address < 0xA200) || $this->numRAMBanks >= 1) {
                if (!$this->cMBC3) {
                    //memoryReadMBC
                    //Switchable RAM
                    if ($this->MBCRAMBanksEnabled || Settings::$overrideMBC) {
                        return $this->MBCRam[$address + $this->currMBCRAMBankPosition];
                    }
                    //cout("Reading from disabled RAM.", 1);
                    return 0xFF;
                } else {
                    //MBC3 RTC + RAM:
                    //memoryReadMBC3
                    //Switchable RAM
                    if ($this->MBCRAMBanksEnabled || Settings::$overrideMBC) {
                        switch ($this->currMBCRAMBank) {
                            case 0x00:
                            case 0x01:
                            case 0x02:
                            case 0x03:
                                return $this->MBCRam[$address + $this->currMBCRAMBankPosition];
                                break;
                            case 0x08:
                                return $this->latchedSeconds;
                                break;
                            case 0x09:
                                return $this->latchedMinutes;
                                break;
                            case 0x0A:
                                return $this->latchedHours;
                                break;
                            case 0x0B:
                                return $this->latchedLDays;
                                break;
                            case 0x0C:
                                return ((($this->RTCDayOverFlow) ? 0x80 : 0) + (($this->RTCHALT) ? 0x40 : 0)) + $this->latchedHDays;
                        }
                    }
                    //cout("Reading from invalid or disabled RAM.", 1);
                    return 0xFF;
                }
            } else {
                return 0xFF;
            }
        } elseif ($address >= 0xC000 && $address < 0xE000) {
            if (!$this->cGBC || $address < 0xD000) {
                return $this->memory[$address];
            } else {
                //memoryReadGBCMemory
                return $this->GBCMemory[$address + $this->gbcRamBankPosition];
            }
        } elseif ($address >= 0xE000 && $address < 0xFE00) {
            if (!$this->cGBC || $address < 0xF000) {
                //memoryReadECHONormal
                return $this->memory[$address - 0x2000];
            } else {
                //memoryReadECHOGBCMemory
                return $this->GBCMemory[$address + $this->gbcRamBankPositionECHO];
            }
        } elseif ($address < 0xFEA0) {
            //memoryReadOAM
            return ($this->lcdController->modeSTAT > 1) ? 0xFF : $this->memory[$address];
        } elseif ($this->cGBC && $address >= 0xFEA0 && $address < 0xFF00) {
            //memoryReadNormal
            return $this->memory[$address];
        } elseif ($address >= 0xFF00) {
            switch ($address) {
                case 0xFF00:
                    return 0xC0 | $this->memory[0xFF00]; //Top nibble returns as set.
                    break;
                case 0xFF01:
                    return (($this->memory[0xFF02] & 0x1) == 0x1) ? 0xFF : $this->memory[0xFF01];
                    break;
                case 0xFF02:
                    if ($this->cGBC) {
                        return 0x7C | $this->memory[0xFF02];
                    } else {
                        return 0x7E | $this->memory[0xFF02];
                    }
                    break;
                case 0xFF07:
                    return 0xF8 | $this->memory[0xFF07];
                    break;
                case 0xFF0F:
                    return 0xE0 | $this->memory[0xFF0F];
                    break;
                case 0xFF10:
                    return 0x80 | $this->memory[0xFF10];
                    break;
                case 0xFF11:
                    return 0x3F | $this->memory[0xFF11];
                    break;
                case 0xFF14:
                    return 0xBF | $this->memory[0xFF14];
                    break;
                case 0xFF16:
                    return 0x3F | $this->memory[0xFF16];
                    break;
                case 0xFF19:
                    return 0xBF | $this->memory[0xFF19];
                    break;
                case 0xFF1A:
                    return 0x7F | $this->memory[0xFF1A];
                    break;
                case 0xFF1B:
                    return 0xFF;
                    break;
                case 0xFF1C:
                    return 0x9F | $this->memory[0xFF1C];
                    break;
                case 0xFF1E:
                    return 0xBF | $this->memory[0xFF1E];
                    break;
                case 0xFF20:
                    return 0xFF;
                    break;
                case 0xFF23:
                    return 0xBF | $this->memory[0xFF23];
                    break;
                case 0xFF26:
                    return 0x70 | $this->memory[0xFF26];
                    break;
                case 0xFF30:
                case 0xFF31:
                case 0xFF32:
                case 0xFF33:
                case 0xFF34:
                case 0xFF35:
                case 0xFF36:
                case 0xFF37:
                case 0xFF38:
                case 0xFF39:
                case 0xFF3A:
                case 0xFF3B:
                case 0xFF3C:
                case 0xFF3D:
                case 0xFF3E:
                case 0xFF3F:
                    return (($this->memory[0xFF26] & 0x4) == 0x4) ? 0xFF : $this->memory[$address];
                    break;
                case 0xFF41:
                    return 0x80 | $this->memory[0xFF41] | $this->lcdController->modeSTAT;
                    break;
                case 0xFF44:
                    return ($this->lcdController->LCDisOn) ? $this->memory[0xFF44] : 0;
                    break;
                case 0xFF4F:
                    return $this->currVRAMBank;
                    break;
                default:
                    //memoryReadNormal
                    return $this->memory[$address];
            }
        } else {
            //memoryReadBAD
            return 0xFF;
        }
    }

    public function VRAMReadGFX($address, $gbcBank)
    {
        //Graphics Side Reading The VRAM
        return (!$gbcBank) ? $this->memory[0x8000 + $address] : $this->VRAM[$address];
    }

    public function setCurrentMBC1ROMBank()
    {
        //Read the cartridge ROM data from RAM memory:
        switch ($this->ROMBank1offs) {
            case 0x00:
            case 0x20:
            case 0x40:
            case 0x60:
                //Bank calls for 0x00, 0x20, 0x40, and 0x60 are really for 0x01, 0x21, 0x41, and 0x61.
                $this->currentROMBank = $this->ROMBank1offs * 0x4000;
                break;
            default:
                $this->currentROMBank = ($this->ROMBank1offs - 1) * 0x4000;
        }
        while ($this->currentROMBank + 0x4000 >= count($this->ROM)) {
            $this->currentROMBank -= count($this->ROM);
        }
    }

    public function setCurrentMBC2AND3ROMBank()
    {
        //Read the cartridge ROM data from RAM memory:
        //Only map bank 0 to bank 1 here (MBC2 is like MBC1, but can only do 16 banks, so only the bank 0 quirk appears for MBC2):
        $this->currentROMBank = max($this->ROMBank1offs - 1, 0) * 0x4000;
        while ($this->currentROMBank + 0x4000 >= count($this->ROM)) {
            $this->currentROMBank -= count($this->ROM);
        }
    }
    public function setCurrentMBC5ROMBank()
    {
        //Read the cartridge ROM data from RAM memory:
        $this->currentROMBank = ($this->ROMBank1offs - 1) * 0x4000;
        while ($this->currentROMBank + 0x4000 >= count($this->ROM)) {
            $this->currentROMBank -= count($this->ROM);
        }
    }

    //Memory Writing:
    public function memoryWrite($address, $data)
    {
        if ($address < 0x8000) {
            if ($this->cMBC1) {
                if ($address < 0x2000) {
                    //MBC RAM Bank Enable/Disable:
                    $this->MBCRAMBanksEnabled = (($data & 0x0F) == 0x0A); //If lower nibble is 0x0A, then enable, otherwise disable.
                } elseif ($address < 0x4000) {
                    // MBC1WriteROMBank
                    //MBC1 ROM bank switching:
                    $this->ROMBank1offs = ($this->ROMBank1offs & 0x60) | ($data & 0x1F);
                    $this->setCurrentMBC1ROMBank();
                } elseif ($address < 0x6000) {
                    //MBC1WriteRAMBank
                    //MBC1 RAM bank switching
                    if ($this->MBC1Mode) {
                        //4/32 Mode
                        $this->currMBCRAMBank = $data & 0x3;
                        $this->currMBCRAMBankPosition = ($this->currMBCRAMBank << 13) - 0xA000;
                    } else {
                        //16/8 Mode
                        $this->ROMBank1offs = (($data & 0x03) << 5) | ($this->ROMBank1offs & 0x1F);
                        $this->setCurrentMBC1ROMBank();
                    }
                } else {
                    //MBC1WriteType
                    //MBC1 mode setting:
                    $this->MBC1Mode = (($data & 0x1) == 0x1);
                }
            } elseif ($this->cMBC2) {
                if ($address < 0x1000) {
                    //MBC RAM Bank Enable/Disable:
                    $this->MBCRAMBanksEnabled = (($data & 0x0F) == 0x0A); //If lower nibble is 0x0A, then enable, otherwise disable.
                } elseif ($address >= 0x2100 && $address < 0x2200) {
                    //MBC2WriteROMBank
                    //MBC2 ROM bank switching:
                    $this->ROMBank1offs = $data & 0x0F;
                    $this->setCurrentMBC2AND3ROMBank();
                } else {
                    //We might have encountered illegal RAM writing or such, so just do nothing...
                }
            } elseif ($this->cMBC3) {
                if ($address < 0x2000) {
                    //MBC RAM Bank Enable/Disable:
                    $this->MBCRAMBanksEnabled = (($data & 0x0F) == 0x0A); //If lower nibble is 0x0A, then enable, otherwise disable.
                } elseif ($address < 0x4000) {
                    //MBC3 ROM bank switching:
                    $this->ROMBank1offs = $data & 0x7F;
                    $this->setCurrentMBC2AND3ROMBank();
                } elseif ($address < 0x6000) {
                    //MBC3WriteRAMBank
                    $this->currMBCRAMBank = $data;
                    if ($data < 4) {
                        //MBC3 RAM bank switching
                        $this->currMBCRAMBankPosition = ($this->currMBCRAMBank << 13) -   0xA000;
                    }
                } else {
                    //MBC3WriteRTCLatch
                    if ($data == 0) {
                        $this->RTCisLatched = false;
                    } elseif (!$this->RTCisLatched) {
                        //Copy over the current RTC time for reading.
                        $this->RTCisLatched = true;
                        $this->latchedSeconds = floor($this->RTCSeconds);
                        $this->latchedMinutes = $this->RTCMinutes;
                        $this->latchedHours = $this->RTCHours;
                        $this->latchedLDays = ($this->RTCDays & 0xFF);
                        $this->latchedHDays = $this->RTCDays >> 8;
                    }
                }
            } elseif ($this->cMBC5 || $this->cRUMBLE) {
                if ($address < 0x2000) {
                    //MBC RAM Bank Enable/Disable:
                    $this->MBCRAMBanksEnabled = (($data & 0x0F) == 0x0A); //If lower nibble is 0x0A, then enable, otherwise disable.
                } elseif ($address < 0x3000) {
                    //MBC5WriteROMBankLow
                    //MBC5 ROM bank switching:
                    $this->ROMBank1offs = ($this->ROMBank1offs & 0x100) | $data;
                    $this->setCurrentMBC5ROMBank();
                } elseif ($address < 0x4000) {
                    //MBC5WriteROMBankHigh
                    //MBC5 ROM bank switching (by least significant bit):
                    $this->ROMBank1offs = (($data & 0x01) << 8) | ($this->ROMBank1offs & 0xFF);
                    $this->setCurrentMBC5ROMBank();
                } elseif ($address < 0x6000) {
                    if ($this->cRUMBLE) {
                        //MBC5 RAM bank switching
                        //Like MBC5, but bit 3 of the lower nibble is used for rumbling and bit 2 is ignored.
                        $this->currMBCRAMBank = $data & 0x3;
                        $this->currMBCRAMBankPosition = ($this->currMBCRAMBank << 13) - 0xA000;
                    } else {
                        //MBC5 RAM bank switching
                        $this->currMBCRAMBank = $data & 0xF;
                        $this->currMBCRAMBankPosition = ($this->currMBCRAMBank << 13) - 0xA000;
                    }
                } else {
                    //We might have encountered illegal RAM writing or such, so just do nothing...
                }
            } elseif ($this->cHuC3) {
                if ($address < 0x2000) {
                    //MBC RAM Bank Enable/Disable:
                    $this->MBCRAMBanksEnabled = (($data & 0x0F) == 0x0A); //If lower nibble is 0x0A, then enable, otherwise disable.
                } elseif ($address < 0x4000) {
                    //MBC3 ROM bank switching:
                    $this->ROMBank1offs = $data & 0x7F;
                    $this->setCurrentMBC2AND3ROMBank();
                } elseif ($address < 0x6000) {
                    //HuC3WriteRAMBank
                    //HuC3 RAM bank switching
                    $this->currMBCRAMBank = $data & 0x03;
                    $this->currMBCRAMBankPosition = ($this->currMBCRAMBank << 13) - 0xA000;
                } else {
                    //We might have encountered illegal RAM writing or such, so just do nothing...
                }
            } else {
                //We might have encountered illegal RAM writing or such, so just do nothing...
            }
        } elseif ($address < 0xA000) {
            // VRAMWrite
            //VRAM cannot be written to during mode 3
            if ($this->lcdController->modeSTAT < 3) {
                // Bkg Tile data area
                if ($address < 0x9800) {
                    $tileIndex = (($address - 0x8000) >> 4) + (384 * $this->currVRAMBank);
                    if ($this->tileReadState[$tileIndex] == 1) {
                        $r = count($this->tileData) - $this->tileCount + $tileIndex;
                        do {
                            $this->tileData[$r] = null;
                            $r -= $this->tileCount;
                        } while ($r >= 0);
                        $this->tileReadState[$tileIndex] = 0;
                    }
                }
                if ($this->currVRAMBank == 0) {
                    $this->memory[$address] = $data;
                } else {
                    $this->VRAM[$address - 0x8000] = $data;
                }
            }
        } elseif ($address < 0xC000) {
            if (($this->numRAMBanks == 1 / 16 && $address < 0xA200) || $this->numRAMBanks >= 1) {
                if (!$this->cMBC3) {
                    //memoryWriteMBCRAM
                    if ($this->MBCRAMBanksEnabled || Settings::$overrideMBC) {
                        $this->MBCRam[$address + $this->currMBCRAMBankPosition] = $data;
                    }
                } else {
                    //MBC3 RTC + RAM:
                    //memoryWriteMBC3RAM
                    if ($this->MBCRAMBanksEnabled || Settings::$overrideMBC) {
                        switch ($this->currMBCRAMBank) {
                            case 0x00:
                            case 0x01:
                            case 0x02:
                            case 0x03:
                                $this->MBCRam[$address + $this->currMBCRAMBankPosition] = $data;
                                break;
                            case 0x08:
                                if ($data < 60) {
                                    $this->RTCSeconds = $data;
                                } else {
                                    echo '(Bank #'.$this->currMBCRAMBank.') RTC write out of range: '.$data.PHP_EOL;
                                }
                                break;
                            case 0x09:
                                if ($data < 60) {
                                    $this->RTCMinutes = $data;
                                } else {
                                    echo '(Bank #'.$this->currMBCRAMBank.') RTC write out of range: '.$data.PHP_EOL;
                                }
                                break;
                            case 0x0A:
                                if ($data < 24) {
                                    $this->RTCHours = $data;
                                } else {
                                    echo '(Bank #'.$this->currMBCRAMBank.') RTC write out of range: '.$data.PHP_EOL;
                                }
                                break;
                            case 0x0B:
                                $this->RTCDays = ($data & 0xFF) | ($this->RTCDays & 0x100);
                                break;
                            case 0x0C:
                                $this->RTCDayOverFlow = ($data & 0x80) == 0x80;
                                $this->RTCHalt = ($data & 0x40) == 0x40;
                                $this->RTCDays = (($data & 0x1) << 8) | ($this->RTCDays & 0xFF);
                                break;
                            default:
                                echo 'Invalid MBC3 bank address selected: '.$this->currMBCRAMBank.PHP_EOL;
                        }
                    }
                }
            } else {
                //We might have encountered illegal RAM writing or such, so just do nothing...
            }
        } elseif ($address < 0xE000) {
            if ($this->cGBC && $address >= 0xD000) {
                //memoryWriteGBCRAM
                $this->GBCMemory[$address + $this->gbcRamBankPosition] = $data;
            } else {
                //memoryWriteNormal
                $this->memory[$address] = $data;
            }
        } elseif ($address < 0xFE00) {
            if ($this->cGBC && $address >= 0xF000) {
                //memoryWriteECHOGBCRAM
                $this->GBCMemory[$address + $this->gbcRamBankPositionECHO] = $data;
            } else {
                //memoryWriteECHONormal
                $this->memory[$address - 0x2000] = $data;
            }
        } elseif ($address <= 0xFEA0) {
            //memoryWriteOAMRAM
            //OAM RAM cannot be written to in mode 2 & 3
            if ($this->lcdController->modeSTAT < 2) {
                $this->memory[$address] = $data;
            }
        } elseif ($address < 0xFF00) {
            //Only GBC has access to this RAM.
            if ($this->cGBC) {
                //memoryWriteNormal
                $this->memory[$address] = $data;
            } else {
                //We might have encountered illegal RAM writing or such, so just do nothing...
            }
        //I/O Registers (GB + GBC):
        } elseif ($address == 0xFF00) {
            $this->memory[0xFF00] = ($data & 0x30) | (((($data & 0x20) == 0) ? ($this->JoyPad >> 4) : 0xF) & ((($data & 0x10) == 0) ? ($this->JoyPad & 0xF) : 0xF));
        } elseif ($address == 0xFF02) {
            if ((($data & 0x1) == 0x1)) {
                //Internal clock:
                $this->memory[0xFF02] = ($data & 0x7F);
                $this->memory[0xFF0F] |= 0x8; //Get this time delayed...
            } else {
                //External clock:
                $this->memory[0xFF02] = $data;
                //No connected serial device, so don't trigger interrupt...
            }
        } elseif ($address == 0xFF04) {
            $this->memory[0xFF04] = 0;
        } elseif ($address == 0xFF07) {
            $this->memory[0xFF07] = $data & 0x07;
            $this->TIMAEnabled = ($data & 0x04) == 0x04;
            $this->TACClocker = pow(4, (($data & 0x3) != 0) ? ($data & 0x3) : 4); //TODO: Find a way to not make a conditional in here...
        } elseif ($address == 0xFF40) {
            if ($this->cGBC) {
                $temp_var = ($data & 0x80) == 0x80;
                if ($temp_var != $this->lcdController->LCDisOn) {
                    //When the display mode changes...
                    $this->lcdController->LCDisOn = $temp_var;
                    $this->memory[0xFF41] &= 0xF8;
                    $this->lcdController->STATTracker = $this->lcdController->modeSTAT = $this->LCDTicks = $this->lcdController->actualScanLine = $this->memory[0xFF44] = 0;
                    if ($this->lcdController->LCDisOn) {
                        $this->lcdController->matchLYC(); //Get the compare of the first scan line.
                    } else {
                        $this->displayShowOff();
                    }
                    $this->memory[0xFF0F] &= 0xFD;
                }
                $this->gfxWindowY = ($data & 0x40) == 0x40;
                $this->gfxWindowDisplay = ($data & 0x20) == 0x20;
                $this->gfxBackgroundX = ($data & 0x10) == 0x10;
                $this->gfxBackgroundY = ($data & 0x08) == 0x08;
                $this->gfxSpriteDouble = ($data & 0x04) == 0x04;
                $this->gfxSpriteShow = ($data & 0x02) == 0x02;
                $this->spritePriorityEnabled = ($data & 0x01) == 0x01;
                $this->memory[0xFF40] = $data;
            } else {
                $temp_var = ($data & 0x80) == 0x80;
                if ($temp_var != $this->lcdController->LCDisOn) {
                    //When the display mode changes...
                    $this->lcdController->LCDisOn = $temp_var;
                    $this->memory[0xFF41] &= 0xF8;
                    $this->lcdController->STATTracker = $this->lcdController->modeSTAT = $this->LCDTicks = $this->lcdController->actualScanLine = $this->memory[0xFF44] = 0;
                    if ($this->lcdController->LCDisOn) {
                        $this->lcdController->matchLYC(); //Get the compare of the first scan line.
                    } else {
                        $this->displayShowOff();
                    }
                    $this->memory[0xFF0F] &= 0xFD;
                }
                $this->gfxWindowY = ($data & 0x40) == 0x40;
                $this->gfxWindowDisplay = ($data & 0x20) == 0x20;
                $this->gfxBackgroundX = ($data & 0x10) == 0x10;
                $this->gfxBackgroundY = ($data & 0x08) == 0x08;
                $this->gfxSpriteDouble = ($data & 0x04) == 0x04;
                $this->gfxSpriteShow = ($data & 0x02) == 0x02;
                if (($data & 0x01) == 0) {
                    // this emulates the gbc-in-gb-mode, not the original gb-mode
                    $this->bgEnabled = false;
                    $this->gfxWindowDisplay = false;
                } else {
                    $this->bgEnabled = true;
                }
                $this->memory[0xFF40] = $data;
            }
        } elseif ($address == 0xFF41) {
            if ($this->cGBC) {
                $this->lcdController->LYCMatchTriggerSTAT = (($data & 0x40) == 0x40);
                $this->lcdController->mode2TriggerSTAT = (($data & 0x20) == 0x20);
                $this->lcdController->mode1TriggerSTAT = (($data & 0x10) == 0x10);
                $this->lcdController->mode0TriggerSTAT = (($data & 0x08) == 0x08);
                $this->memory[0xFF41] = ($data & 0xF8);
            } else {
                $this->lcdController->LYCMatchTriggerSTAT = (($data & 0x40) == 0x40);
                $this->lcdController->mode2TriggerSTAT = (($data & 0x20) == 0x20);
                $this->lcdController->mode1TriggerSTAT = (($data & 0x10) == 0x10);
                $this->lcdController->mode0TriggerSTAT = (($data & 0x08) == 0x08);
                $this->memory[0xFF41] = ($data & 0xF8);
                if ($this->lcdController->LCDisOn && $this->lcdController->modeSTAT < 2) {
                    $this->memory[0xFF0F] |= 0x2;
                }
            }
        } elseif ($address == 0xFF45) {
            $this->memory[0xFF45] = $data;
            if ($this->lcdController->LCDisOn) {
                $this->lcdController->matchLYC(); //Get the compare of the first scan line.
            }
        } elseif ($address == 0xFF46) {
            $this->memory[0xFF46] = $data;
            //DMG cannot DMA from the ROM banks.
            if ($this->cGBC || $data > 0x7F) {
                $data <<= 8;
                $address = 0xFE00;
                while ($address < 0xFEA0) {
                    $this->memory[$address++] = $this->memoryRead($data++);
                }
            }
        } elseif ($address == 0xFF47) {
            $this->decodePalette(0, $data);
            if ($this->memory[0xFF47] != $data) {
                $this->memory[0xFF47] = $data;
                $this->invalidateAll(0);
            }
        } elseif ($address == 0xFF48) {
            $this->decodePalette(4, $data);
            if ($this->memory[0xFF48] != $data) {
                $this->memory[0xFF48] = $data;
                $this->invalidateAll(1);
            }
        } elseif ($address == 0xFF49) {
            $this->decodePalette(8, $data);
            if ($this->memory[0xFF49] != $data) {
                $this->memory[0xFF49] = $data;
                $this->invalidateAll(2);
            }
        } elseif ($address == 0xFF4D) {
            if ($this->cGBC) {
                $this->memory[0xFF4D] = ($data & 0x7F) + ($this->memory[0xFF4D] & 0x80);
            } else {
                $this->memory[0xFF4D] = $data;
            }
        } elseif ($address == 0xFF4F) {
            if ($this->cGBC) {
                $this->currVRAMBank = $data & 0x01;
                //Only writable by GBC.
            }
        } elseif ($address == 0xFF50) {
            if ($this->inBootstrap) {
                echo 'Boot ROM reads blocked: Bootstrap process has ended.'.PHP_EOL;
                $this->inBootstrap = false;
                $this->disableBootROM(); //Fill in the boot ROM ranges with ROM  bank 0 ROM ranges
                $this->memory[0xFF50] = $data; //Bits are sustained in memory?
            }
        } elseif ($address == 0xFF51) {
            if ($this->cGBC) {
                if (!$this->hdmaRunning) {
                    $this->memory[0xFF51] = $data;
                }
            }
        } elseif ($address == 0xFF52) {
            if ($this->cGBC) {
                if (!$this->hdmaRunning) {
                    $this->memory[0xFF52] = $data & 0xF0;
                }
            }
        } elseif ($address == 0xFF53) {
            if ($this->cGBC) {
                if (!$this->hdmaRunning) {
                    $this->memory[0xFF53] = $data & 0x1F;
                }
            }
        } elseif ($address == 0xFF54) {
            if ($this->cGBC) {
                if (!$this->hdmaRunning) {
                    $this->memory[0xFF54] = $data & 0xF0;
                }
            }
        } elseif ($address == 0xFF55) {
            if ($this->cGBC) {
                if (!$this->hdmaRunning) {
                    if (($data & 0x80) == 0) {
                        //DMA
                        $this->CPUTicks += 1 + ((8 * (($data & 0x7F) + 1)) * $this->multiplier);
                        $dmaSrc = ($this->memory[0xFF51] << 8) + $this->memory[0xFF52];
                        $dmaDst = 0x8000 + ($this->memory[0xFF53] << 8) + $this->memory[0xFF54];
                        $endAmount = ((($data & 0x7F) * 0x10) + 0x10);
                        for ($loopAmount = 0; $loopAmount < $endAmount; ++$loopAmount) {
                            $this->memoryWrite($dmaDst++, $this->memoryRead($dmaSrc++));
                        }
                        $this->memory[0xFF51] = (($dmaSrc & 0xFF00) >> 8);
                        $this->memory[0xFF52] = ($dmaSrc & 0x00F0);
                        $this->memory[0xFF53] = (($dmaDst & 0x1F00) >> 8);
                        $this->memory[0xFF54] = ($dmaDst & 0x00F0);
                        $this->memory[0xFF55] = 0xFF; //Transfer completed.
                    } else {
                        //H-Blank DMA
                        if ($data > 0x80) {
                            $this->hdmaRunning = true;
                            $this->memory[0xFF55] = $data & 0x7F;
                        } else {
                            $this->memory[0xFF55] = 0xFF;
                        }
                    }
                } elseif (($data & 0x80) == 0) {
                    //Stop H-Blank DMA
                    $this->hdmaRunning = false;
                    $this->memory[0xFF55] |= 0x80;
                }
            } else {
                $this->memory[0xFF55] = $data;
            }
        } elseif ($address == 0xFF68) {
            if ($this->cGBC) {
                $this->memory[0xFF69] = 0xFF & $this->gbcRawPalette[$data & 0x3F];
                $this->memory[0xFF68] = $data;
            } else {
                $this->memory[0xFF68] = $data;
            }
        } elseif ($address == 0xFF69) {
            if ($this->cGBC) {
                $this->setGBCPalette($this->memory[0xFF68] & 0x3F, $data);
                // high bit = autoincrement
                if ($this->usbtsb($this->memory[0xFF68]) < 0) {
                    $next = (($this->usbtsb($this->memory[0xFF68]) + 1) & 0x3F);
                    $this->memory[0xFF68] = ($next | 0x80);
                    $this->memory[0xFF69] = 0xFF & $this->gbcRawPalette[$next];
                } else {
                    $this->memory[0xFF69] = $data;
                }
            } else {
                $this->memory[0xFF69] = $data;
            }
        } elseif ($address == 0xFF6A) {
            if ($this->cGBC) {
                $this->memory[0xFF6B] = 0xFF & $this->gbcRawPalette[($data & 0x3F) | 0x40];
                $this->memory[0xFF6A] = $data;
            } else {
                $this->memory[0xFF6A] = $data;
            }
        } elseif ($address == 0xFF6B) {
            if ($this->cGBC) {
                $this->setGBCPalette(($this->memory[0xFF6A] & 0x3F) + 0x40, $data);
                // high bit = autoincrement
                if ($this->usbtsb($this->memory[0xFF6A]) < 0) {
                    $next = (($this->memory[0xFF6A] + 1) & 0x3F);
                    $this->memory[0xFF6A] = ($next | 0x80);
                    $this->memory[0xFF6B] = 0xFF & $this->gbcRawPalette[$next | 0x40];
                } else {
                    $this->memory[0xFF6B] = $data;
                }
            } else {
                $this->memory[0xFF6B] = $data;
            }
        } elseif ($address == 0xFF6C) {
            if ($this->inBootstrap) {
                if ($this->inBootstrap) {
                    $this->cGBC = ($data == 0x80);
                    echo 'Booted to GBC Mode: '.$this->cGBC.PHP_EOL;
                }
                $this->memory[0xFF6C] = $data;
            }
        } elseif ($address == 0xFF70) {
            if ($this->cGBC) {
                $addressCheck = ($this->memory[0xFF51] << 8) | $this->memory[0xFF52]; //Cannot change the RAM bank while WRAM is the source of a running HDMA.
                if (!$this->hdmaRunning || $addressCheck < 0xD000 || $addressCheck >= 0xE000) {
                    $this->gbcRamBank = max($data & 0x07, 1); //Bank range is from 1-7
                    $this->gbcRamBankPosition = (($this->gbcRamBank - 1) * 0x1000) - 0xD000;
                    $this->gbcRamBankPositionECHO = (($this->gbcRamBank - 1) * 0x1000) - 0xF000;
                }
                $this->memory[0xFF70] = ($data | 0x40); //Bit 6 cannot be written to.
            } else {
                $this->memory[0xFF70] = $data;
            }
        } else {
            //Start the I/O initialization by filling in the slots as normal memory:
            //memoryWriteNormal
            $this->memory[$address] = $data;
        }
    }

    //Helper Functions
    public function usbtsb($ubyte)
    {
        //Unsigned byte to signed byte:
        return ($ubyte > 0x7F) ? (($ubyte & 0x7F) - 0x80) : $ubyte;
    }

    public function unsbtub($ubyte)
    {
        //Keep an unsigned byte unsigned:
        if ($ubyte < 0) {
            $ubyte += 0x100;
        }

        return $ubyte; //If this function is called, no wrapping requested.
    }

    public function nswtuw($uword)
    {
        //Keep an unsigned word unsigned:
        if ($uword < 0) {
            $uword += 0x10000;
        }

        return $uword & 0xFFFF; //Wrap also...
    }

    public function unswtuw($uword)
    {
        //Keep an unsigned word unsigned:
        if ($uword < 0) {
            $uword += 0x10000;
        }

        return $uword; //If this function is called, no wrapping requested.
    }

    public function toTypedArray($baseArray, $bit32, $unsigned)
    {
        try {
            $typedArrayTemp = ($bit32) ? (($unsigned) ? new Uint32Array(count($baseArray)) : new Int32Array(count($baseArray))) : new Uint8Array(count($baseArray));
            for ($address = 0; $address < count($baseArray); ++$address) {
                $typedArrayTemp[$address] = $baseArray[$address];
            }

            return $typedArrayTemp;
        } catch (\Exception $error) {
            echo 'Could not convert an array to a typed array'.PHP_EOL;

            return $baseArray;
        }
    }

    public function fromTypedArray($baseArray)
    {
        try {
            $arrayTemp = array_fill(0, count($baseArray), 0);
            for ($address = 0; $address < count($baseArray); ++$address) {
                $arrayTemp[$address] = $baseArray[$address];
            }

            return $arrayTemp;
        } catch (\Exception $error) {
            return $baseArray;
        }
    }

    public function getTypedArray($length, $defaultValue, $numberType)
    {
        // @PHP - We dont have typed arrays and unsigned int in PHP
        // This function just creates an array and initialize with a value
        $arrayHandle = array_fill(0, $length, $defaultValue);

        return $arrayHandle;
    }

    public function arrayPad($length, $defaultValue)
    {
        $arrayHandle = array_fill(0, $length, $defaultValue);

        return $arrayHandle;
    }
}
