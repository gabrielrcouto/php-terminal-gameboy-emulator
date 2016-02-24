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

    //Updated upon ROM loading...
    public $usedBootROM = false;

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

    //Array of functions mapped to read back memory
    public $memoryReader = [];

    //Array of functions mapped to write to memory
    public $memoryWriter = [];

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

    //Tracker for STAT triggering.
    public $STATTracker = 0;

    //The scan line mode (for lines 1-144 it's 2-3-0, for 145-154 it's 1)
    public $modeSTAT = 0;

    //Should we trigger an interrupt if LY==LYC?
    public $LYCMatchTriggerSTAT = false;

    //Should we trigger an interrupt if in mode 2?
    public $mode2TriggerSTAT = false;

    //Should we trigger an interrupt if in mode 1?
    public $mode1TriggerSTAT = false;

    //Should we trigger an interrupt if in mode 0?
    public $mode0TriggerSTAT = false;

    //Is the emulated LCD controller on?
    public $LCDisOn = false;

    //Array of functions to handle each scan line we do (onscreen + offscreen)
    public $LINECONTROL;

    public $DISPLAYOFFCONTROL = [];

    //Pointer to either LINECONTROL or DISPLAYOFFCONTROL.
    public $LCDCONTROL = null;

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

    //Actual scan line...
    public $actualScanLine = 0;

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

    public $widthRatio;

    public $heightRatio;

    //Pointer to the current palette we're using (Used for palette switches during boot or so it can be done anytime)
    public $palette = null;

    //
    //Data
    //

    public $DAATable;

    public $ffxxDump;

    public $OPCODE;

    public $CBOPCODE;

    public $TICKTable;

    public $SecondaryTICKTable;

    // Added

    public $cTIMER = null;

    public function __construct($ROMImage, $drawContext)
    {
        $this->drawContext = $drawContext;
        $this->ROMImage = $ROMImage;

        $this->DISPLAYOFFCONTROL[] = function ($parentObj) {
            //Array of line 0 function to handle the LCD controller when it's off (Do nothing!).
        };

        $this->tileCountInvalidator = $this->tileCount * 4;

        $this->ROMBanks[0x52] = 72;
        $this->ROMBanks[0x53] = 80;
        $this->ROMBanks[0x54] = 96;

        $this->frameCount = Settings::$settings[12];
        $this->pixelCount = $this->width * $this->height;
        $this->rgbCount = $this->pixelCount * 4;
        $this->widthRatio = 160 / $this->width;
        $this->heightRatio = 144 / $this->height;

        // Copy Data
        $this->DAATable = Data::$DAATable;
        $this->ffxxDump = Data::$ffxxDump;

        $opcode = new Opcode();
        $this->OPCODE = $opcode->get();

        $cbopcode = new Cbopcode();
        $this->CBOPCODE = $cbopcode->get();

        $this->TICKTable = TICKTables::$primary;
        $this->SecondaryTICKTable = TICKTables::$secondary;

        $this->LINECONTROL = array_fill(0, 154, null);
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
            $this->modeSTAT,
            $this->LYCMatchTriggerSTAT,
            $this->mode2TriggerSTAT,
            $this->mode1TriggerSTAT,
            $this->mode0TriggerSTAT,
            $this->LCDisOn,
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
            $this->actualScanLine,
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
            $this->usedBootROM,
            $this->skipPCIncrement,
            $this->STATTracker,
            $this->gbcRamBankPositionECHO,
            $this->numRAMBanks,
        ];
    }

    public function returnFromState($returnedFrom)
    {
        $index = 0;
        $state = $returnedFrom->slice(0);

        $this->ROM = $this->toTypedArray($state[$index++], false, false);
        $this->inBootstrap = $state[$index++];
        $this->registerA = $state[$index++];
        $this->FZero = $state[$index++];
        $this->FSubtract = $state[$index++];
        $this->FHalfCarry = $state[$index++];
        $this->FCarry = $state[$index++];
        $this->registerB = $state[$index++];
        $this->registerC = $state[$index++];
        $this->registerD = $state[$index++];
        $this->registerE = $state[$index++];
        $this->registersHL = $state[$index++];
        $this->stackPointer = $state[$index++];
        $this->programCounter = $state[$index++];
        $this->halt = $state[$index++];
        $this->IME = $state[$index++];
        $this->hdmaRunning = $state[$index++];
        $this->CPUTicks = $state[$index++];
        $this->multiplier = $state[$index++];
        $this->memory = $this->toTypedArray($state[$index++], false, false);
        $this->MBCRam = $this->toTypedArray($state[$index++], false, false);
        $this->VRAM = $this->toTypedArray($state[$index++], false, false);
        $this->currVRAMBank = $state[$index++];
        $this->GBCMemory = $this->toTypedArray($state[$index++], false, false);
        $this->MBC1Mode = $state[$index++];
        $this->MBCRAMBanksEnabled = $state[$index++];
        $this->currMBCRAMBank = $state[$index++];
        $this->currMBCRAMBankPosition = $state[$index++];
        $this->cGBC = $state[$index++];
        $this->gbcRamBank = $state[$index++];
        $this->gbcRamBankPosition = $state[$index++];
        $this->ROMBank1offs = $state[$index++];
        $this->currentROMBank = $state[$index++];
        $this->cartridgeType = $state[$index++];
        $this->name = $state[$index++];
        $this->gameCode = $state[$index++];
        $this->modeSTAT = $state[$index++];
        $this->LYCMatchTriggerSTAT = $state[$index++];
        $this->mode2TriggerSTAT = $state[$index++];
        $this->mode1TriggerSTAT = $state[$index++];
        $this->mode0TriggerSTAT = $state[$index++];
        $this->LCDisOn = $state[$index++];
        $this->gfxWindowY = $state[$index++];
        $this->gfxWindowDisplay = $state[$index++];
        $this->gfxSpriteShow = $state[$index++];
        $this->gfxSpriteDouble = $state[$index++];
        $this->gfxBackgroundY = $state[$index++];
        $this->gfxBackgroundX = $state[$index++];
        $this->TIMAEnabled = $state[$index++];
        $this->DIVTicks = $state[$index++];
        $this->LCDTicks = $state[$index++];
        $this->timerTicks = $state[$index++];
        $this->TACClocker = $state[$index++];
        $this->untilEnable = $state[$index++];
        $this->lastIteration = $state[$index++];
        $this->cMBC1 = $state[$index++];
        $this->cMBC2 = $state[$index++];
        $this->cMBC3 = $state[$index++];
        $this->cMBC5 = $state[$index++];
        $this->cSRAM = $state[$index++];
        $this->cMMMO1 = $state[$index++];
        $this->cRUMBLE = $state[$index++];
        $this->cCamera = $state[$index++];
        $this->cTAMA5 = $state[$index++];
        $this->cHuC3 = $state[$index++];
        $this->cHuC1 = $state[$index++];
        $this->drewBlank = $state[$index++];
        $this->tileData = $state[$index++];
        $this->frameBuffer = $this->toTypedArray($state[$index++], true, false);
        $this->tileCount = $state[$index++];
        $this->colorCount = $state[$index++];
        $this->gbPalette = $state[$index++];
        $this->gbcRawPalette = $state[$index++];
        $this->gbcPalette = $state[$index++];
        $this->transparentCutoff = $state[$index++];
        $this->bgEnabled = $state[$index++];
        $this->spritePriorityEnabled = $state[$index++];
        $this->tileReadState = $this->toTypedArray($state[$index++], false, false);
        $this->windowSourceLine = $state[$index++];
        $this->actualScanLine = $state[$index++];
        $this->RTCisLatched = $state[$index++];
        $this->latchedSeconds = $state[$index++];
        $this->latchedMinutes = $state[$index++];
        $this->latchedHours = $state[$index++];
        $this->latchedLDays = $state[$index++];
        $this->latchedHDays = $state[$index++];
        $this->RTCSeconds = $state[$index++];
        $this->RTCMinutes = $state[$index++];
        $this->RTCHours = $state[$index++];
        $this->RTCDays = $state[$index++];
        $this->RTCDayOverFlow = $state[$index++];
        $this->RTCHALT = $state[$index++];
        $this->gbColorizedPalette = $state[$index++];
        $this->usedBootROM = $state[$index++];
        $this->skipPCIncrement = $state[$index++];
        $this->STATTracker = $state[$index++];
        $this->gbcRamBankPositionECHO = $state[$index++];
        $this->numRAMBanks = $state[$index];
        $this->tileCountInvalidator = $this->tileCount * 4;
        $this->fromSaveState = true;
        $this->checkPaletteType();
        $this->initializeLCDController();
        $this->memoryReadJumpCompile();
        $this->memoryWriteJumpCompile();
        $this->initLCD();
        $this->drawToCanvas();
    }

    public function start()
    {
        Settings::$settings[4] = 0; //Reset the frame skip setting.
        $this->initializeLCDController(); //Compile the LCD controller functions.
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
        $index = 0x3F;

        while ($index >= 0) {
            $this->gbcPalette[$index] = ($index < 0x20) ? -1 : 0;
            --$index;
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
        $index = 0xFF;

        while ($index >= 0) {
            if ($index >= 0x30 && $index < 0x40) {
                $this->memoryWrite(0xFF00 + $index, $this->ffxxDump[$index]);
            } else {
                switch ($index) {
                    case 0x00:
                    case 0x01:
                    case 0x02:
                    case 0x07:
                    case 0x0F:
                    case 0x40:
                    case 0xFF:
                        $this->memoryWrite(0xFF00 + $index, $this->ffxxDump[$index]);
                        break;
                    default:
                        $this->memory[0xFF00 + $index] = $this->ffxxDump[$index];
                }
            }
            --$index;
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

        $this->usedBootROM = Settings::$settings[16];

        for ($romIndex = 0; $romIndex < strlen($this->ROMImage); ++$romIndex) {
            $this->ROM[$romIndex] = (ord($this->ROMImage[$romIndex]) & 0xFF);
            if ($romIndex < 0x8000) {
                if (!$this->usedBootROM || $romIndex >= 0x900 || ($romIndex >= 0x100 && $romIndex < 0x200)) {
                    $this->memory[$romIndex] = $this->ROM[$romIndex]; //Load in the game ROM.
                } else {
                    // Removed GBCROM due copyright ;-)
                    // $this->memory[$romIndex] = $this->GBCBOOTROM[$romIndex]; //Load in the GameBoy Color BOOT ROM.
                }
            }
        }
        // ROM name
        for ($index = 0x134; $index < 0x13F; ++$index) {
            if (ord($this->ROMImage[$index]) > 0) {
                $this->name .= $this->ROMImage[$index];
            }
        }

        // ROM game code (for newer games)
        for ($index = 0x13F; $index < 0x143; ++$index) {
            if (ord($this->ROMImage[$index]) > 0) {
                $this->gameCode .= $this->ROMImage[$index];
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
                if (!Settings::$settings[9]) {
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
                echo 'Cartridge type is unknown.'.PHP_EOL;

                // @TODO
                //pause();
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
        if (!$this->usedBootROM) {
            switch ($this->ROM[0x143]) {
                case 0x00: //Only GB mode
                    $this->cGBC = false;
                    echo 'Only GB mode detected.'.PHP_EOL;
                    break;
                case 0x80: //Both GB + GBC modes
                    $this->cGBC = !Settings::$settings[2];
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
        } else {
            $this->cGBC = true; //Allow the GBC boot ROM to run in GBC mode...
            $this->setupRAM(); //CPU/(V)RAM initialization.
            $this->initBootstrap();
        }
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
        for ($index = 0; $index < 0x900; ++$index) {
            //Skip the already loaded in ROM header.
            if ($index < 0x100 || $index >= 0x200) {
                $this->memory[$index] = $this->ROM[$index]; //Replace the GameBoy Color boot ROM with the game ROM.
            }
        }
        $this->checkPaletteType();

        if (!$this->cGBC) {
            //Clean up the post-boot (GB mode only) state:
            echo 'Stepping down from GBC mode.'.PHP_EOL;
            $this->tileCount /= 2;
            $this->tileCountInvalidator = $this->tileCount * 4;
            if (!Settings::$settings[17]) {
                $this->transparentCutoff = 4;
            }
            $this->colorCount = 12;

            // @TODO
            // $this->tileData.length = $this->tileCount * $this->colorCount;

            unset($this->VRAM);
            unset($this->GBCMemory);
            //Possible Extra: shorten some gfx arrays to the length that we need (Remove the unused indices)
        }

        $this->memoryReadJumpCompile();
        $this->memoryWriteJumpCompile();
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
        $this->memoryReadJumpCompile();
        $this->memoryWriteJumpCompile();
    }

    public function MBCRAMUtilized()
    {
        return $this->cMBC1 || $this->cMBC2 || $this->cMBC3 || $this->cMBC5 || $this->cRUMBLE;
    }

    public function initLCD()
    {
        $this->transparentCutoff = (Settings::$settings[17] || $this->cGBC) ? 32 : 4;
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
        $this->canvasBuffer = array_fill(0, 4 * $this->width * $this->height, 255);

        $index = $this->pixelCount;
        $index2 = $this->rgbCount;

        while ($index > 0) {
            $this->frameBuffer[--$index] = 0x00FFFFFF;
            $this->canvasBuffer[$index2 -= 4] = 0xFF;
            $this->canvasBuffer[$index2 + 1] = 0xFF;
            $this->canvasBuffer[$index2 + 2] = 0xFF;
            $this->canvasBuffer[$index2 + 3] = 0xFF;
        }

        $this->drawContext->draw($this->canvasBuffer, 0, 0);
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
                        $this->OPCODE[0x76]($this);
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
            $this->OPCODE[$op]($this);
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

    public function scanLineMode2()
    { // OAM in use
        if ($this->modeSTAT != 2) {
            if ($this->mode2TriggerSTAT) {
                $this->memory[0xFF0F] |= 0x2; // set IF bit 1
            }
            $this->STATTracker = 1;
            $this->modeSTAT = 2;
        }
    }

    public function scanLineMode3()
    { // OAM in use
        if ($this->modeSTAT != 3) {
            if ($this->mode2TriggerSTAT && $this->STATTracker == 0) {
                $this->memory[0xFF0F] |= 0x2; // set IF bit 1
            }
            $this->STATTracker = 1;
            $this->modeSTAT = 3;
        }
    }

    public function scanLineMode0()
    { // H-Blank
        if ($this->modeSTAT != 0) {
            if ($this->hdmaRunning && !$this->halt && $this->LCDisOn) {
                $this->performHdma(); //H-Blank DMA
            }
            if ($this->mode0TriggerSTAT || ($this->mode2TriggerSTAT && $this->STATTracker == 0)) {
                $this->memory[0xFF0F] |= 0x2; // if STAT bit 3 -> set IF bit1
            }
            $this->notifyScanline();
            $this->STATTracker = 2;
            $this->modeSTAT = 0;
        }
    }

    public function matchLYC()
    { // LY - LYC Compare
        // If LY==LCY
        if ($this->memory[0xFF44] == $this->memory[0xFF45]) {
            $this->memory[0xFF41] |= 0x04; // set STAT bit 2: LY-LYC coincidence flag
            if ($this->LYCMatchTriggerSTAT) {
                $this->memory[0xFF0F] |= 0x2; // set IF bit 1
            }
        } else {
            $this->memory[0xFF41] &= 0xFB; // reset STAT bit 2 (LY!=LYC)
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
        $this->LCDCONTROL[$this->actualScanLine]($this); //Scan Line and STAT Mode Control

        //Audio Timing
        $this->audioTicks += $timedTicks; //Not the same as the LCD timing (Cannot be altered by display on/off changes!!!).

        //Are we past the granularity setting?
        if ($this->audioTicks >= Settings::$settings[11]) {
            //Emulator Timing (Timed against audio for optimization):
            $this->emulatorTicks += $this->audioTicks;
            if ($this->emulatorTicks >= Settings::$settings[13]) {
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

    public function initializeLCDController()
    {
        //Display on hanlding:
        $line = 0;

        while ($line < 154) {
            if ($line < 143) {
                //We're on a normal scan line:
                $this->LINECONTROL[$line] = function ($parentObj) {
                    if ($parentObj->LCDTicks < 20) {
                        $parentObj->scanLineMode2(); // mode2: 80 cycles
                    } elseif ($parentObj->LCDTicks < 63) {
                        $parentObj->scanLineMode3(); // mode3: 172 cycles
                    } elseif ($parentObj->LCDTicks < 114) {
                        $parentObj->scanLineMode0(); // mode0: 204 cycles
                    } else {
                        //We're on a new scan line:
                        $parentObj->LCDTicks -= 114;
                        $parentObj->actualScanLine = ++$parentObj->memory[0xFF44];
                        $parentObj->matchLYC();
                        if ($parentObj->STATTracker != 2) {
                            if ($parentObj->hdmaRunning && !$parentObj->halt && $parentObj->LCDisOn) {
                                $parentObj->performHdma(); //H-Blank DMA
                            }
                            if ($parentObj->mode0TriggerSTAT) {
                                $parentObj->memory[0xFF0F] |= 0x2; // set IF bit 1
                            }
                        }
                        $parentObj->STATTracker = 0;
                        $parentObj->scanLineMode2(); // mode2: 80 cycles
                        if ($parentObj->LCDTicks >= 114) {
                            //We need to skip 1 or more scan lines:
                            $parentObj->notifyScanline();
                            $parentObj->LCDCONTROL[$parentObj->actualScanLine]($parentObj); //Scan Line and STAT Mode Control
                        }
                    }
                };
            } elseif ($line == 143) {
                //We're on the last visible scan line of the LCD screen:
                $this->LINECONTROL[143] = function ($parentObj) {
                    if ($parentObj->LCDTicks < 20) {
                        $parentObj->scanLineMode2(); // mode2: 80 cycles
                    } elseif ($parentObj->LCDTicks < 63) {
                        $parentObj->scanLineMode3(); // mode3: 172 cycles
                    } elseif ($parentObj->LCDTicks < 114) {
                        $parentObj->scanLineMode0(); // mode0: 204 cycles
                    } else {
                        //Starting V-Blank:
                        //Just finished the last visible scan line:
                        $parentObj->LCDTicks -= 114;
                        $parentObj->actualScanLine = ++$parentObj->memory[0xFF44];
                        $parentObj->matchLYC();
                        if ($parentObj->mode1TriggerSTAT) {
                            $parentObj->memory[0xFF0F] |= 0x2; // set IF bit 1
                        }
                        if ($parentObj->STATTracker != 2) {
                            if ($parentObj->hdmaRunning && !$parentObj->halt && $parentObj->LCDisOn) {
                                $parentObj->performHdma(); //H-Blank DMA
                            }
                            if ($parentObj->mode0TriggerSTAT) {
                                $parentObj->memory[0xFF0F] |= 0x2; // set IF bit 1
                            }
                        }
                        $parentObj->STATTracker = 0;
                        $parentObj->modeSTAT = 1;
                        $parentObj->memory[0xFF0F] |= 0x1; // set IF flag 0
                        //LCD off takes at least 2 frames.
                        if ($parentObj->drewBlank > 0) {
                            --$parentObj->drewBlank;
                        }
                        if ($parentObj->LCDTicks >= 114) {
                            //We need to skip 1 or more scan lines:
                            $parentObj->LCDCONTROL[$parentObj->actualScanLine]($parentObj); //Scan Line and STAT Mode Control
                        }
                    }
                };
            } elseif ($line < 153) {
                //In VBlank
                $this->LINECONTROL[$line] = function ($parentObj) {
                    if ($parentObj->LCDTicks >= 114) {
                        //We're on a new scan line:
                        $parentObj->LCDTicks -= 114;
                        $parentObj->actualScanLine = ++$parentObj->memory[0xFF44];
                        $parentObj->matchLYC();
                        if ($parentObj->LCDTicks >= 114) {
                            //We need to skip 1 or more scan lines:
                            $parentObj->LCDCONTROL[$parentObj->actualScanLine]($parentObj); //Scan Line and STAT Mode Control
                        }
                    }
                };
            } else {
                //VBlank Ending (We're on the last actual scan line)
                $this->LINECONTROL[153] = function ($parentObj) {
                    if ($parentObj->memory[0xFF44] == 153) {
                        $parentObj->memory[0xFF44] = 0; //LY register resets to 0 early.
                        $parentObj->matchLYC(); //LY==LYC Test is early here (Fixes specific one-line glitches (example: Kirby2 intro)).
                    }
                    if ($parentObj->LCDTicks >= 114) {
                        //We reset back to the beginning:
                        $parentObj->LCDTicks -= 114;
                        $parentObj->actualScanLine = 0;
                        $parentObj->scanLineMode2(); // mode2: 80 cycles
                        if ($parentObj->LCDTicks >= 114) {
                            //We need to skip 1 or more scan lines:
                            $parentObj->LCDCONTROL[$parentObj->actualScanLine]($parentObj); //Scan Line and STAT Mode Control
                        }
                    }
                };
            }
            ++$line;
        }
        $this->LCDCONTROL = ($this->LCDisOn) ? $this->LINECONTROL : $this->DISPLAYOFFCONTROL;
    }

    public function displayShowOff()
    {
        if ($this->drewBlank == 0) {
            $this->canvasBuffer = array_fill(0, 4 * $this->width * $this->height, 255);
            $this->drawContext->draw($this->canvasBuffer, 0, 0);
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
        if (Settings::$settings[7] || $this->cTIMER) {
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
            if (Settings::$settings[7]) {
                //Auto Frame Skip:
                if ($timeElapsed > Settings::$settings[20]) {
                    //Did not finish in time...
                    if (Settings::$settings[4] < Settings::$settings[8]) {
                        ++Settings::$settings[4];
                    }
                } elseif (Settings::$settings[4] > 0) {
                    //We finished on time, decrease frame skipping (throttle to somewhere just below full speed)...
                    --Settings::$settings[4];
                }
            }
            $this->lastIteration = (int) (microtime(true) * 1000);
        }
    }

    public function drawToCanvas()
    {
        //Draw the frame buffer to the canvas:
        if (Settings::$settings[4] == 0 || $this->frameCount > 0) {
            //Copy and convert the framebuffer data to the CanvasPixelArray format.
            $bufferIndex = $this->pixelCount;
            $canvasIndex = $this->rgbCount;

            while ($canvasIndex > 3) {
                //Red
                $this->canvasBuffer[$canvasIndex -= 4] = ($this->frameBuffer[--$bufferIndex] >> 16) & 0xFF;
                //Green
                $this->canvasBuffer[$canvasIndex + 1] = ($this->frameBuffer[$bufferIndex] >> 8) & 0xFF;
                //Blue
                $this->canvasBuffer[$canvasIndex + 2] = $this->frameBuffer[$bufferIndex] & 0xFF;
            }

            //Draw out the CanvasPixelArray data:
            $this->drawContext->draw($this->canvasBuffer, 0, 0);

            if (Settings::$settings[4] > 0) {
                //Decrement the frameskip counter:
                $this->frameCount -= Settings::$settings[4];
            }
        } else {
            //Reset the frameskip counter:
            $this->frameCount += Settings::$settings[12];
        }
    }

    public function invalidateAll($pal)
    {
        $stop = ($pal + 1) * $this->tileCountInvalidator;
        for ($r = $pal * $this->tileCountInvalidator; $r < $stop; ++$r) {
            $this->tileData[$r] = null;
        }
    }

    public function setGBCPalettePre($index_, $data)
    {
        if ($this->gbcRawPalette[$index_] == $data) {
            return;
        }
        $this->gbcRawPalette[$index_] = $data;
        if ($index_ >= 0x40 && ($index_ & 0x6) == 0) {
            // stay transparent
            return;
        }
        $value = ($this->gbcRawPalette[$index_ | 1] << 8) + $this->gbcRawPalette[$index_ & -2];
        $this->gbcPalette[$index_ >> 1] = 0x80000000 + (($value & 0x1F) << 19) + (($value & 0x3E0) << 6) + (($value & 0x7C00) >> 7);
        $this->invalidateAll($index_ >> 3);
    }

    public function setGBCPalette($index_, $data)
    {
        $this->setGBCPalettePre($index_, $data);
        if (($index_ & 0x6) == 0) {
            $this->gbcPalette[$index_ >> 1] &= 0x00FFFFFF;
        }
    }

    public function decodePalette($startIndex, $data)
    {
        if (!$this->cGBC) {
            $this->gbPalette[$startIndex] = $this->colors[$data & 0x03] & 0x00FFFFFF; // color 0: transparent
            $this->gbPalette[$startIndex + 1] = $this->colors[($data >> 2) & 0x03];
            $this->gbPalette[$startIndex + 2] = $this->colors[($data >> 4) & 0x03];
            $this->gbPalette[$startIndex + 3] = $this->colors[$data >> 6];

            //Do palette conversions if we did the GBC bootup:
            if ($this->usedBootROM) {
                //GB colorization:
                $startOffset = ($startIndex >= 4) ? 0x20 : 0;
                $pal2 = $this->gbcPalette[$startOffset + (($data >> 2) & 0x03)];
                $pal3 = $this->gbcPalette[$startOffset + (($data >> 4) & 0x03)];
                $pal4 = $this->gbcPalette[$startOffset + ($data >> 6)];
                $this->gbColorizedPalette[$startIndex] = $this->gbcPalette[$startOffset + ($data & 0x03)] & 0x00FFFFFF;
                $this->gbColorizedPalette[$startIndex + 1] = ($pal2 >= 0x80000000) ? $pal2 : 0xFFFFFFFF;
                $this->gbColorizedPalette[$startIndex + 2] = ($pal3 >= 0x80000000) ? $pal3 : 0xFFFFFFFF;
                $this->gbColorizedPalette[$startIndex + 3] = ($pal4 >= 0x80000000) ? $pal4 : 0xFFFFFFFF;
            }

            //@PHP - Need to copy the new palette
            $this->checkPaletteType();
        }
    }

    public function notifyScanline()
    {
        if ($this->actualScanLine == 0) {
            $this->windowSourceLine = 0;
        }
        // determine the left edge of the window (160 if window is inactive)
        $windowLeft = ($this->gfxWindowDisplay && $this->memory[0xFF4A] <= $this->actualScanLine) ? min(160, $this->memory[0xFF4B] - 7) : 160;
        // step 1: background+window
        $skippedAnything = $this->drawBackgroundForLine($this->actualScanLine, $windowLeft, 0);
        // At this point, the high (alpha) byte in the frameBuffer is 0xff for colors 1,2,3 and
        // 0x00 for color 0. Foreground sprites draw on all colors, background sprites draw on
        // top of color 0 only.
        // step 2: sprites
        $this->drawSpritesForLine($this->actualScanLine);
        // step 3: prio tiles+window
        if ($skippedAnything) {
            $this->drawBackgroundForLine($this->actualScanLine, $windowLeft, 0x80);
        }
        if ($windowLeft < 160) {
            ++$this->windowSourceLine;
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
        $this->palette = ($this->cGBC) ? $this->gbcPalette : (($this->usedBootROM && Settings::$settings[17]) ? $this->gbColorizedPalette : $this->gbPalette);
    }

    public function updateImage($tileIndex, $attribs)
    {
        $index_ = $tileIndex + $this->tileCount * $attribs;
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
        $this->tileData[$index_] = ($transparent) ? true : $tempPix;

        $this->tileReadState[$tileIndex] = 1;

        return $this->tileData[$index_];
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
        //Act as a wrapper for reading the returns from the compiled jumps to memory.
        return $this->memoryReader[$address]($this, $address); //This seems to be faster than the usual if/else.
    }

    public function memoryReadJumpCompile()
    {
        //Faster in some browsers, since we are doing less conditionals overall by implementing them in advance.
        for ($index = 0x0000; $index <= 0xFFFF; ++$index) {
            if ($index < 0x4000) {
                $this->memoryReader[$index] = function ($parentObj, $address) { //memoryReadNormal
                    return $parentObj->memory[$address];
                };
            } elseif ($index < 0x8000) {
                $this->memoryReader[$index] = function ($parentObj, $address) { //memoryReadROM
                    return $parentObj->ROM[$parentObj->currentROMBank + $address];
                };
            } elseif ($index >= 0x8000 && $index < 0xA000) {
                $VRAMReadCGBCPU = function ($parentObj, $address) {
                    //CPU Side Reading The VRAM (Optimized for GameBoy Color)
                    return ($parentObj->modeSTAT > 2) ? 0xFF : (($parentObj->currVRAMBank == 0) ? $parentObj->memory[$address] : $parentObj->VRAM[$address - 0x8000]);
                };

                $VRAMReadDMGCPU = function ($parentObj, $address) {
                    //CPU Side Reading The VRAM (Optimized for classic GameBoy)
                    return ($parentObj->modeSTAT > 2) ? 0xFF : $parentObj->memory[$address];
                };

                $this->memoryReader[$index] = ($this->cGBC) ? $VRAMReadCGBCPU : $VRAMReadDMGCPU;
            } elseif ($index >= 0xA000 && $index < 0xC000) {
                if (($this->numRAMBanks == 1 / 16 && $index < 0xA200) || $this->numRAMBanks >= 1) {
                    if (!$this->cMBC3) {
                        $this->memoryReader[$index] = function ($parentObj, $address) { //memoryReadMBC
                            //Switchable RAM
                            if ($parentObj->MBCRAMBanksEnabled || Settings::$settings[10]) {
                                return $parentObj->MBCRam[$address + $parentObj->currMBCRAMBankPosition];
                            }
                            //cout("Reading from disabled RAM.", 1);
                            return 0xFF;
                        };
                    } else {
                        //MBC3 RTC + RAM:
                        $this->memoryReader[$index] = function ($parentObj, $address) { //memoryReadMBC3
                            //Switchable RAM
                            if ($parentObj->MBCRAMBanksEnabled || Settings::$settings[10]) {
                                switch ($parentObj->currMBCRAMBank) {
                                    case 0x00:
                                    case 0x01:
                                    case 0x02:
                                    case 0x03:
                                        return $parentObj->MBCRam[$address + $parentObj->currMBCRAMBankPosition];
                                        break;
                                    case 0x08:
                                        return $parentObj->latchedSeconds;
                                        break;
                                    case 0x09:
                                        return $parentObj->latchedMinutes;
                                        break;
                                    case 0x0A:
                                        return $parentObj->latchedHours;
                                        break;
                                    case 0x0B:
                                        return $parentObj->latchedLDays;
                                        break;
                                    case 0x0C:
                                        return ((($parentObj->RTCDayOverFlow) ? 0x80 : 0) + (($parentObj->RTCHALT) ? 0x40 : 0)) + $parentObj->latchedHDays;
                                }
                            }
                            //cout("Reading from invalid or disabled RAM.", 1);
                            return 0xFF;
                        };
                    }
                } else {
                    $this->memoryReader[$index] = function ($parentObj, $address) { //memoryReadBAD
                        return 0xFF;
                    };
                }
            } elseif ($index >= 0xC000 && $index < 0xE000) {
                if (!$this->cGBC || $index < 0xD000) {
                    $this->memoryReader[$index] = function ($parentObj, $address) { //memoryReadNormal
                        return $parentObj->memory[$address];
                    };
                } else {
                    $this->memoryReader[$index] = function ($parentObj, $address) { //memoryReadGBCMemory
                        return $parentObj->GBCMemory[$address + $parentObj->gbcRamBankPosition];
                    };
                }
            } elseif ($index >= 0xE000 && $index < 0xFE00) {
                if (!$this->cGBC || $index < 0xF000) {
                    $this->memoryReader[$index] = function ($parentObj, $address) { //memoryReadECHONormal
                        return $parentObj->memory[$address - 0x2000];
                    };
                } else {
                    $this->memoryReader[$index] = function ($parentObj, $address) { //memoryReadECHOGBCMemory
                        return $parentObj->GBCMemory[$address + $parentObj->gbcRamBankPositionECHO];
                    };
                }
            } elseif ($index < 0xFEA0) {
                $this->memoryReader[$index] = function ($parentObj, $address) { //memoryReadOAM
                    return ($parentObj->modeSTAT > 1) ? 0xFF : $parentObj->memory[$address];
                };
            } elseif ($this->cGBC && $index >= 0xFEA0 && $index < 0xFF00) {
                $this->memoryReader[$index] = function ($parentObj, $address) { //memoryReadNormal
                    return $parentObj->memory[$address];
                };
            } elseif ($index >= 0xFF00) {
                switch ($index) {
                    case 0xFF00:
                        $this->memoryReader[0xFF00] = function ($parentObj, $address) {
                            return 0xC0 | $parentObj->memory[0xFF00]; //Top nibble returns as set.
                        };
                        break;
                    case 0xFF01:
                        $this->memoryReader[0xFF01] = function ($parentObj, $address) {
                            return (($parentObj->memory[0xFF02] & 0x1) == 0x1) ? 0xFF : $parentObj->memory[0xFF01];
                        };
                        break;
                    case 0xFF02:
                        if ($this->cGBC) {
                            $this->memoryReader[0xFF02] = function ($parentObj, $address) {
                                return 0x7C | $parentObj->memory[0xFF02];
                            };
                        } else {
                            $this->memoryReader[0xFF02] = function ($parentObj, $address) {
                                return 0x7E | $parentObj->memory[0xFF02];
                            };
                        }
                        break;
                    case 0xFF07:
                        $this->memoryReader[0xFF07] = function ($parentObj, $address) {
                            return 0xF8 | $parentObj->memory[0xFF07];
                        };
                        break;
                    case 0xFF0F:
                        $this->memoryReader[0xFF0F] = function ($parentObj, $address) {
                            return 0xE0 | $parentObj->memory[0xFF0F];
                        };
                        break;
                    case 0xFF10:
                        $this->memoryReader[0xFF10] = function ($parentObj, $address) {
                            return 0x80 | $parentObj->memory[0xFF10];
                        };
                        break;
                    case 0xFF11:
                        $this->memoryReader[0xFF11] = function ($parentObj, $address) {
                            return 0x3F | $parentObj->memory[0xFF11];
                        };
                        break;
                    case 0xFF14:
                        $this->memoryReader[0xFF14] = function ($parentObj, $address) {
                            return 0xBF | $parentObj->memory[0xFF14];
                        };
                        break;
                    case 0xFF16:
                        $this->memoryReader[0xFF16] = function ($parentObj, $address) {
                            return 0x3F | $parentObj->memory[0xFF16];
                        };
                        break;
                    case 0xFF19:
                        $this->memoryReader[0xFF19] = function ($parentObj, $address) {
                            return 0xBF | $parentObj->memory[0xFF19];
                        };
                        break;
                    case 0xFF1A:
                        $this->memoryReader[0xFF1A] = function ($parentObj, $address) {
                            return 0x7F | $parentObj->memory[0xFF1A];
                        };
                        break;
                    case 0xFF1B:
                        $this->memoryReader[0xFF1B] = function ($parentObj, $address) {
                            return 0xFF;
                        };
                        break;
                    case 0xFF1C:
                        $this->memoryReader[0xFF1C] = function ($parentObj, $address) {
                            return 0x9F | $parentObj->memory[0xFF1C];
                        };
                        break;
                    case 0xFF1E:
                        $this->memoryReader[0xFF1E] = function ($parentObj, $address) {
                            return 0xBF | $parentObj->memory[0xFF1E];
                        };
                        break;
                    case 0xFF20:
                        $this->memoryReader[0xFF20] = function ($parentObj, $address) {
                            return 0xFF;
                        };
                        break;
                    case 0xFF23:
                        $this->memoryReader[0xFF23] = function ($parentObj, $address) {
                            return 0xBF | $parentObj->memory[0xFF23];
                        };
                        break;
                    case 0xFF26:
                        $this->memoryReader[0xFF26] = function ($parentObj, $address) {
                            return 0x70 | $parentObj->memory[0xFF26];
                        };
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
                        $this->memoryReader[$index] = function ($parentObj, $address) {
                            return (($parentObj->memory[0xFF26] & 0x4) == 0x4) ? 0xFF : $parentObj->memory[$address];
                        };
                        break;
                    case 0xFF41:
                        $this->memoryReader[0xFF41] = function ($parentObj, $address) {
                            return 0x80 | $parentObj->memory[0xFF41] | $parentObj->modeSTAT;
                        };
                        break;
                    case 0xFF44:
                        $this->memoryReader[0xFF44] = function ($parentObj, $address) {
                            return ($parentObj->LCDisOn) ? $parentObj->memory[0xFF44] : 0;
                        };
                        break;
                    case 0xFF4F:
                        $this->memoryReader[0xFF4F] = function ($parentObj, $address) {
                            return $parentObj->currVRAMBank;
                        };
                        break;
                    default:
                        $this->memoryReader[$index] = function ($parentObj, $address) { //memoryReadNormal
                            return $parentObj->memory[$address];
                        };
                }
            } else {
                $this->memoryReader[$index] = function ($parentObj, $address) { //memoryReadBAD
                    return 0xFF;
                };
            }
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
        //Act as a wrapper for writing by compiled jumps to specific memory writing functions.
        $this->memoryWriter[$address]($this, $address, $data);
    }

    public function memoryWriteJumpCompile()
    {
        $MBCWriteEnable = function ($parentObj, $address, $data) {
            //MBC RAM Bank Enable/Disable:
            $parentObj->MBCRAMBanksEnabled = (($data & 0x0F) == 0x0A); //If lower nibble is 0x0A, then enable, otherwise disable.
        };

        $MBC3WriteROMBank = function ($parentObj, $address, $data) {
            //MBC3 ROM bank switching:
            $parentObj->ROMBank1offs = $data & 0x7F;
            $parentObj->setCurrentMBC2AND3ROMBank();
        };

        $cartIgnoreWrite = function ($parentObj, $address, $data) {
            //We might have encountered illegal RAM writing or such, so just do nothing...
        };

        //Faster in some browsers, since we are doing less conditionals overall by implementing them in advance.
        for ($index = 0x0000; $index <= 0xFFFF; ++$index) {
            if ($index < 0x8000) {
                if ($this->cMBC1) {
                    if ($index < 0x2000) {
                        $this->memoryWriter[$index] = $MBCWriteEnable;
                    } elseif ($index < 0x4000) {
                        $this->memoryWriter[$index] = function ($parentObj, $address, $data) { // MBC1WriteROMBank
                            //MBC1 ROM bank switching:
                            $parentObj->ROMBank1offs = ($parentObj->ROMBank1offs & 0x60) | ($data & 0x1F);
                            $parentObj->setCurrentMBC1ROMBank();
                        };
                    } elseif ($index < 0x6000) {
                        $this->memoryWriter[$index] = function ($parentObj, $address, $data) { //MBC1WriteRAMBank
                            //MBC1 RAM bank switching
                            if ($parentObj->MBC1Mode) {
                                //4/32 Mode
                                $parentObj->currMBCRAMBank = $data & 0x3;
                                $parentObj->currMBCRAMBankPosition = ($parentObj->currMBCRAMBank << 13) - 0xA000;
                            } else {
                                //16/8 Mode
                                $parentObj->ROMBank1offs = (($data & 0x03) << 5) | ($parentObj->ROMBank1offs & 0x1F);
                                $parentObj->setCurrentMBC1ROMBank();
                            }
                        };
                    } else {
                        $this->memoryWriter[$index] = function ($parentObj, $address, $data) { //MBC1WriteType
                            //MBC1 mode setting:
                            $parentObj->MBC1Mode = (($data & 0x1) == 0x1);
                        };
                    }
                } elseif ($this->cMBC2) {
                    if ($index < 0x1000) {
                        $this->memoryWriter[$index] = $MBCWriteEnable;
                    } elseif ($index >= 0x2100 && $index < 0x2200) {
                        $this->memoryWriter[$index] = function ($parentObj, $address, $data) { //MBC2WriteROMBank
                            //MBC2 ROM bank switching:
                            $parentObj->ROMBank1offs = $data & 0x0F;
                            $parentObj->setCurrentMBC2AND3ROMBank();
                        };
                    } else {
                        $this->memoryWriter[$index] = $cartIgnoreWrite;
                    }
                } elseif ($this->cMBC3) {
                    if ($index < 0x2000) {
                        $this->memoryWriter[$index] = $MBCWriteEnable;
                    } elseif ($index < 0x4000) {
                        $this->memoryWriter[$index] = $MBC3WriteROMBank;
                    } elseif ($index < 0x6000) {
                        $this->memoryWriter[$index] = function ($parentObj, $address, $data) { //MBC3WriteRAMBank
                            $parentObj->currMBCRAMBank = $data;
                            if ($data < 4) {
                                //MBC3 RAM bank switching
                                $parentObj->currMBCRAMBankPosition = ($parentObj->currMBCRAMBank << 13) - 0xA000;
                            }
                        };
                    } else {
                        $this->memoryWriter[$index] = function ($parentObj, $address, $data) { //MBC3WriteRTCLatch
                            if ($data == 0) {
                                $parentObj->RTCisLatched = false;
                            } elseif (!$parentObj->RTCisLatched) {
                                //Copy over the current RTC time for reading.
                                $parentObj->RTCisLatched = true;
                                $parentObj->latchedSeconds = floor($parentObj->RTCSeconds);
                                $parentObj->latchedMinutes = $parentObj->RTCMinutes;
                                $parentObj->latchedHours = $parentObj->RTCHours;
                                $parentObj->latchedLDays = ($parentObj->RTCDays & 0xFF);
                                $parentObj->latchedHDays = $parentObj->RTCDays >> 8;
                            }
                        };
                    }
                } elseif ($this->cMBC5 || $this->cRUMBLE) {
                    if ($index < 0x2000) {
                        $this->memoryWriter[$index] = $MBCWriteEnable;
                    } elseif ($index < 0x3000) {
                        $this->memoryWriter[$index] = function ($parentObj, $address, $data) { //MBC5WriteROMBankLow
                            //MBC5 ROM bank switching:
                            $parentObj->ROMBank1offs = ($parentObj->ROMBank1offs & 0x100) | $data;
                            $parentObj->setCurrentMBC5ROMBank();
                        };
                    } elseif ($index < 0x4000) {
                        $this->memoryWriter[$index] = function ($parentObj, $address, $data) { //MBC5WriteROMBankHigh
                            //MBC5 ROM bank switching (by least significant bit):
                            $parentObj->ROMBank1offs = (($data & 0x01) << 8) | ($parentObj->ROMBank1offs & 0xFF);
                            $parentObj->setCurrentMBC5ROMBank();
                        };
                    } elseif ($index < 0x6000) {
                        $RUMBLEWriteRAMBank = function ($parentObj, $address, $data) {
                            //MBC5 RAM bank switching
                            //Like MBC5, but bit 3 of the lower nibble is used for rumbling and bit 2 is ignored.
                            $parentObj->currMBCRAMBank = $data & 0x3;
                            $parentObj->currMBCRAMBankPosition = ($parentObj->currMBCRAMBank << 13) - 0xA000;
                        };

                        $MBC5WriteRAMBank = function ($parentObj, $address, $data) {
                            //MBC5 RAM bank switching
                            $parentObj->currMBCRAMBank = $data & 0xF;
                            $parentObj->currMBCRAMBankPosition = ($parentObj->currMBCRAMBank << 13) - 0xA000;
                        };

                        $this->memoryWriter[$index] = ($this->cRUMBLE) ? $RUMBLEWriteRAMBank : $MBC5WriteRAMBank;
                    } else {
                        $this->memoryWriter[$index] = $cartIgnoreWrite;
                    }
                } elseif ($this->cHuC3) {
                    if ($index < 0x2000) {
                        $this->memoryWriter[$index] = $MBCWriteEnable;
                    } elseif ($index < 0x4000) {
                        $this->memoryWriter[$index] = $MBC3WriteROMBank;
                    } elseif ($index < 0x6000) {
                        //HuC3WriteRAMBank
                        $this->memoryWriter[$index] = function ($parentObj, $address, $data) {
                            //HuC3 RAM bank switching
                            $parentObj->currMBCRAMBank = $data & 0x03;
                            $parentObj->currMBCRAMBankPosition = ($parentObj->currMBCRAMBank << 13) - 0xA000;
                        };
                    } else {
                        $this->memoryWriter[$index] = $cartIgnoreWrite;
                    }
                } else {
                    $this->memoryWriter[$index] = $cartIgnoreWrite;
                }
            } elseif ($index < 0xA000) {
                // VRAMWrite
                $this->memoryWriter[$index] = function ($parentObj, $address, $data) {
                    //VRAM cannot be written to during mode 3
                    if ($parentObj->modeSTAT < 3) {
                        // Bkg Tile data area
                        if ($address < 0x9800) {
                            $tileIndex = (($address - 0x8000) >> 4) + (384 * $parentObj->currVRAMBank);
                            if ($parentObj->tileReadState[$tileIndex] == 1) {
                                $r = count($parentObj->tileData) - $parentObj->tileCount + $tileIndex;
                                do {
                                    $parentObj->tileData[$r] = null;
                                    $r -= $parentObj->tileCount;
                                } while ($r >= 0);
                                $parentObj->tileReadState[$tileIndex] = 0;
                            }
                        }
                        if ($parentObj->currVRAMBank == 0) {
                            $parentObj->memory[$address] = $data;
                        } else {
                            $parentObj->VRAM[$address - 0x8000] = $data;
                        }
                    }
                };
            } elseif ($index < 0xC000) {
                if (($this->numRAMBanks == 1 / 16 && $index < 0xA200) || $this->numRAMBanks >= 1) {
                    if (!$this->cMBC3) {
                        $this->memoryWriter[$index] = function ($parentObj, $address, $data) { //memoryWriteMBCRAM
                            if ($parentObj->MBCRAMBanksEnabled || Settings::$settings[10]) {
                                $parentObj->MBCRam[$address + $parentObj->currMBCRAMBankPosition] = $data;
                            }
                        };
                    } else {
                        //MBC3 RTC + RAM:
                        $this->memoryWriter[$index] = function ($parentObj, $address, $data) { //memoryWriteMBC3RAM
                            if ($parentObj->MBCRAMBanksEnabled || Settings::$settings[10]) {
                                switch ($parentObj->currMBCRAMBank) {
                                    case 0x00:
                                    case 0x01:
                                    case 0x02:
                                    case 0x03:
                                        $parentObj->MBCRam[$address + $parentObj->currMBCRAMBankPosition] = $data;
                                        break;
                                    case 0x08:
                                        if ($data < 60) {
                                            $parentObj->RTCSeconds = $data;
                                        } else {
                                            echo '(Bank #' + $parentObj->currMBCRAMBank + ') RTC write out of range: ' + $data.PHP_EOL;
                                        }
                                        break;
                                    case 0x09:
                                        if ($data < 60) {
                                            $parentObj->RTCMinutes = $data;
                                        } else {
                                            echo '(Bank #' + $parentObj->currMBCRAMBank + ') RTC write out of range: ' + $data.PHP_EOL;
                                        }
                                        break;
                                    case 0x0A:
                                        if ($data < 24) {
                                            $parentObj->RTCHours = $data;
                                        } else {
                                            echo '(Bank #' + $parentObj->currMBCRAMBank + ') RTC write out of range: ' + $data.PHP_EOL;
                                        }
                                        break;
                                    case 0x0B:
                                        $parentObj->RTCDays = ($data & 0xFF) | ($parentObj->RTCDays & 0x100);
                                        break;
                                    case 0x0C:
                                        $parentObj->RTCDayOverFlow = ($data & 0x80) == 0x80;
                                        $parentObj->RTCHalt = ($data & 0x40) == 0x40;
                                        $parentObj->RTCDays = (($data & 0x1) << 8) | ($parentObj->RTCDays & 0xFF);
                                        break;
                                    default:
                                        echo 'Invalid MBC3 bank address selected: ' + $parentObj->currMBCRAMBank.PHP_EOL;
                                }
                            }
                        };
                    }
                } else {
                    $this->memoryWriter[$index] = $cartIgnoreWrite;
                }
            } elseif ($index < 0xE000) {
                if ($this->cGBC && $index >= 0xD000) {
                    $this->memoryWriter[$index] = function ($parentObj, $address, $data) { //memoryWriteGBCRAM
                        $parentObj->GBCMemory[$address + $parentObj->gbcRamBankPosition] = $data;
                    };
                } else {
                    $this->memoryWriter[$index] = function ($parentObj, $address, $data) { //memoryWriteNormal
                        $parentObj->memory[$address] = $data;
                    };
                }
            } elseif ($index < 0xFE00) {
                if ($this->cGBC && $index >= 0xF000) {
                    //memoryWriteECHOGBCRAM
                    $this->memoryWriter[$index] = function ($parentObj, $address, $data) {
                        $parentObj->GBCMemory[$address + $parentObj->gbcRamBankPositionECHO] = $data;
                    };
                } else {
                    //memoryWriteECHONormal
                    $this->memoryWriter[$index] = function ($parentObj, $address, $data) {
                        $parentObj->memory[$address - 0x2000] = $data;
                    };
                }
            } elseif ($index <= 0xFEA0) {
                //memoryWriteOAMRAM
                $this->memoryWriter[$index] = function ($parentObj, $address, $data) {
                    //OAM RAM cannot be written to in mode 2 & 3
                    if ($parentObj->modeSTAT < 2) {
                        $parentObj->memory[$address] = $data;
                    }
                };
            } elseif ($index < 0xFF00) {
                //Only GBC has access to this RAM.
                if ($this->cGBC) {
                    //memoryWriteNormal
                    $this->memoryWriter[$index] = function ($parentObj, $address, $data) {
                        $parentObj->memory[$address] = $data;
                    };
                } else {
                    $this->memoryWriter[$index] = $cartIgnoreWrite;
                }
            } else {
                //Start the I/O initialization by filling in the slots as normal memory:
                //memoryWriteNormal
                $this->memoryWriter[$index] = function ($parentObj, $address, $data) {
                    $parentObj->memory[$address] = $data;
                };
            }
        }
        $this->registerWriteJumpCompile(); //Compile the I/O write functions separately...
    }

    public function registerWriteJumpCompile()
    {
        //I/O Registers (GB + GBC):
        $this->memoryWriter[0xFF00] = function ($parentObj, $address, $data) {
            $parentObj->memory[0xFF00] = ($data & 0x30) | (((($data & 0x20) == 0) ? ($parentObj->JoyPad >> 4) : 0xF) & ((($data & 0x10) == 0) ? ($parentObj->JoyPad & 0xF) : 0xF));
        };
        $this->memoryWriter[0xFF02] = function ($parentObj, $address, $data) {
            if ((($data & 0x1) == 0x1)) {
                //Internal clock:
                $parentObj->memory[0xFF02] = ($data & 0x7F);
                $parentObj->memory[0xFF0F] |= 0x8; //Get this time delayed...
            } else {
                //External clock:
                $parentObj->memory[0xFF02] = $data;
                //No connected serial device, so don't trigger interrupt...
            }
        };
        $this->memoryWriter[0xFF04] = function ($parentObj, $address, $data) {
            $parentObj->memory[0xFF04] = 0;
        };
        $this->memoryWriter[0xFF07] = function ($parentObj, $address, $data) {
            $parentObj->memory[0xFF07] = $data & 0x07;
            $parentObj->TIMAEnabled = ($data & 0x04) == 0x04;
            $parentObj->TACClocker = pow(4, (($data & 0x3) != 0) ? ($data & 0x3) : 4); //TODO: Find a way to not make a conditional in here...
        };

        // BEGIN - Audio Writers
        $this->memoryWriter[0xFF10] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF11] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF12] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF13] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF14] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF16] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF17] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF18] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF19] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF1A] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF1B] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF1C] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF1D] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF1E] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF20] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF21] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF22] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF23] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF24] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF25] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF26] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF30] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF31] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF32] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF33] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF34] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF35] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF36] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF37] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF38] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF39] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF3A] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF3B] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF3C] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF3D] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF3E] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF3F] = function ($parentObj, $address, $data) {
        };
        $this->memoryWriter[0xFF44] = function ($parentObj, $address, $data) {
            //Read only
        };
        // END - Audio Writers
        //
        $this->memoryWriter[0xFF45] = function ($parentObj, $address, $data) {
            $parentObj->memory[0xFF45] = $data;
            if ($parentObj->LCDisOn) {
                $parentObj->matchLYC(); //Get the compare of the first scan line.
            }
        };
        $this->memoryWriter[0xFF46] = function ($parentObj, $address, $data) {
            $parentObj->memory[0xFF46] = $data;
            //DMG cannot DMA from the ROM banks.
            if ($parentObj->cGBC || $data > 0x7F) {
                $data <<= 8;
                $address = 0xFE00;
                while ($address < 0xFEA0) {
                    $parentObj->memory[$address++] = $parentObj->memoryReader[$data]($parentObj, $data++);
                }
            }
        };
        $this->memoryWriter[0xFF47] = function ($parentObj, $address, $data) {
            $parentObj->decodePalette(0, $data);
            if ($parentObj->memory[0xFF47] != $data) {
                $parentObj->memory[0xFF47] = $data;
                $parentObj->invalidateAll(0);
            }
        };
        $this->memoryWriter[0xFF48] = function ($parentObj, $address, $data) {
            $parentObj->decodePalette(4, $data);
            if ($parentObj->memory[0xFF48] != $data) {
                $parentObj->memory[0xFF48] = $data;
                $parentObj->invalidateAll(1);
            }
        };
        $this->memoryWriter[0xFF49] = function ($parentObj, $address, $data) {
            $parentObj->decodePalette(8, $data);
            if ($parentObj->memory[0xFF49] != $data) {
                $parentObj->memory[0xFF49] = $data;
                $parentObj->invalidateAll(2);
            }
        };
        if ($this->cGBC) {
            //GameBoy Color Specific I/O:
            $this->memoryWriter[0xFF40] = function ($parentObj, $address, $data) {
                $temp_var = ($data & 0x80) == 0x80;
                if ($temp_var != $parentObj->LCDisOn) {
                    //When the display mode changes...
                    $parentObj->LCDisOn = $temp_var;
                    $parentObj->memory[0xFF41] &= 0xF8;
                    $parentObj->STATTracker = $parentObj->modeSTAT = $parentObj->LCDTicks = $parentObj->actualScanLine = $parentObj->memory[0xFF44] = 0;
                    if ($parentObj->LCDisOn) {
                        $parentObj->matchLYC(); //Get the compare of the first scan line.
                        $parentObj->LCDCONTROL = $parentObj->LINECONTROL;
                    } else {
                        $parentObj->LCDCONTROL = $parentObj->DISPLAYOFFCONTROL;
                        $parentObj->displayShowOff();
                    }
                    $parentObj->memory[0xFF0F] &= 0xFD;
                }
                $parentObj->gfxWindowY = ($data & 0x40) == 0x40;
                $parentObj->gfxWindowDisplay = ($data & 0x20) == 0x20;
                $parentObj->gfxBackgroundX = ($data & 0x10) == 0x10;
                $parentObj->gfxBackgroundY = ($data & 0x08) == 0x08;
                $parentObj->gfxSpriteDouble = ($data & 0x04) == 0x04;
                $parentObj->gfxSpriteShow = ($data & 0x02) == 0x02;
                $parentObj->spritePriorityEnabled = ($data & 0x01) == 0x01;
                $parentObj->memory[0xFF40] = $data;
            };
            $this->memoryWriter[0xFF41] = function ($parentObj, $address, $data) {
                $parentObj->LYCMatchTriggerSTAT = (($data & 0x40) == 0x40);
                $parentObj->mode2TriggerSTAT = (($data & 0x20) == 0x20);
                $parentObj->mode1TriggerSTAT = (($data & 0x10) == 0x10);
                $parentObj->mode0TriggerSTAT = (($data & 0x08) == 0x08);
                $parentObj->memory[0xFF41] = ($data & 0xF8);
            };
            $this->memoryWriter[0xFF4D] = function ($parentObj, $address, $data) {
                $parentObj->memory[0xFF4D] = ($data & 0x7F) + ($parentObj->memory[0xFF4D] & 0x80);
            };
            $this->memoryWriter[0xFF4F] = function ($parentObj, $address, $data) {
                $parentObj->currVRAMBank = $data & 0x01;
                //Only writable by GBC.
            };
            $this->memoryWriter[0xFF51] = function ($parentObj, $address, $data) {
                if (!$parentObj->hdmaRunning) {
                    $parentObj->memory[0xFF51] = $data;
                }
            };
            $this->memoryWriter[0xFF52] = function ($parentObj, $address, $data) {
                if (!$parentObj->hdmaRunning) {
                    $parentObj->memory[0xFF52] = $data & 0xF0;
                }
            };
            $this->memoryWriter[0xFF53] = function ($parentObj, $address, $data) {
                if (!$parentObj->hdmaRunning) {
                    $parentObj->memory[0xFF53] = $data & 0x1F;
                }
            };
            $this->memoryWriter[0xFF54] = function ($parentObj, $address, $data) {
                if (!$parentObj->hdmaRunning) {
                    $parentObj->memory[0xFF54] = $data & 0xF0;
                }
            };
            $this->memoryWriter[0xFF55] = function ($parentObj, $address, $data) {
                if (!$parentObj->hdmaRunning) {
                    if (($data & 0x80) == 0) {
                        //DMA
                        $parentObj->CPUTicks += 1 + ((8 * (($data & 0x7F) + 1)) * $parentObj->multiplier);
                        $dmaSrc = ($parentObj->memory[0xFF51] << 8) + $parentObj->memory[0xFF52];
                        $dmaDst = 0x8000 + ($parentObj->memory[0xFF53] << 8) + $parentObj->memory[0xFF54];
                        $endAmount = ((($data & 0x7F) * 0x10) + 0x10);
                        for ($loopAmount = 0; $loopAmount < $endAmount; ++$loopAmount) {
                            $parentObj->memoryWrite($dmaDst++, $parentObj->memoryRead($dmaSrc++));
                        }
                        $parentObj->memory[0xFF51] = (($dmaSrc & 0xFF00) >> 8);
                        $parentObj->memory[0xFF52] = ($dmaSrc & 0x00F0);
                        $parentObj->memory[0xFF53] = (($dmaDst & 0x1F00) >> 8);
                        $parentObj->memory[0xFF54] = ($dmaDst & 0x00F0);
                        $parentObj->memory[0xFF55] = 0xFF; //Transfer completed.
                    } else {
                        //H-Blank DMA
                        if ($data > 0x80) {
                            $parentObj->hdmaRunning = true;
                            $parentObj->memory[0xFF55] = $data & 0x7F;
                        } else {
                            $parentObj->memory[0xFF55] = 0xFF;
                        }
                    }
                } elseif (($data & 0x80) == 0) {
                    //Stop H-Blank DMA
                    $parentObj->hdmaRunning = false;
                    $parentObj->memory[0xFF55] |= 0x80;
                }
            };
            $this->memoryWriter[0xFF68] = function ($parentObj, $address, $data) {
                $parentObj->memory[0xFF69] = 0xFF & $parentObj->gbcRawPalette[$data & 0x3F];
                $parentObj->memory[0xFF68] = $data;
            };
            $this->memoryWriter[0xFF69] = function ($parentObj, $address, $data) {
                $parentObj->setGBCPalette($parentObj->memory[0xFF68] & 0x3F, $data);
                // high bit = autoincrement
                if ($parentObj->usbtsb($parentObj->memory[0xFF68]) < 0) {
                    $next = (($parentObj->usbtsb($parentObj->memory[0xFF68]) + 1) & 0x3F);
                    $parentObj->memory[0xFF68] = ($next | 0x80);
                    $parentObj->memory[0xFF69] = 0xFF & $parentObj->gbcRawPalette[$next];
                } else {
                    $parentObj->memory[0xFF69] = $data;
                }
            };
            $this->memoryWriter[0xFF6A] = function ($parentObj, $address, $data) {
                $parentObj->memory[0xFF6B] = 0xFF & $parentObj->gbcRawPalette[($data & 0x3F) | 0x40];
                $parentObj->memory[0xFF6A] = $data;
            };
            $this->memoryWriter[0xFF6B] = function ($parentObj, $address, $data) {
                $parentObj->setGBCPalette(($parentObj->memory[0xFF6A] & 0x3F) + 0x40, $data);
                // high bit = autoincrement
                if ($parentObj->usbtsb($parentObj->memory[0xFF6A]) < 0) {
                    $next = (($parentObj->memory[0xFF6A] + 1) & 0x3F);
                    $parentObj->memory[0xFF6A] = ($next | 0x80);
                    $parentObj->memory[0xFF6B] = 0xFF & $parentObj->gbcRawPalette[$next | 0x40];
                } else {
                    $parentObj->memory[0xFF6B] = $data;
                }
            };
            $this->memoryWriter[0xFF70] = function ($parentObj, $address, $data) {
                $addressCheck = ($parentObj->memory[0xFF51] << 8) | $parentObj->memory[0xFF52]; //Cannot change the RAM bank while WRAM is the source of a running HDMA.
                if (!$parentObj->hdmaRunning || $addressCheck < 0xD000 || $addressCheck >= 0xE000) {
                    $parentObj->gbcRamBank = max($data & 0x07, 1); //Bank range is from 1-7
                    $parentObj->gbcRamBankPosition = (($parentObj->gbcRamBank - 1) * 0x1000) - 0xD000;
                    $parentObj->gbcRamBankPositionECHO = (($parentObj->gbcRamBank - 1) * 0x1000) - 0xF000;
                }
                $parentObj->memory[0xFF70] = ($data | 0x40); //Bit 6 cannot be written to.
            };
        } else {
            //Fill in the GameBoy Color I/O registers as normal RAM for GameBoy compatibility:
            $this->memoryWriter[0xFF40] = function ($parentObj, $address, $data) {
                $temp_var = ($data & 0x80) == 0x80;
                if ($temp_var != $parentObj->LCDisOn) {
                    //When the display mode changes...
                    $parentObj->LCDisOn = $temp_var;
                    $parentObj->memory[0xFF41] &= 0xF8;
                    $parentObj->STATTracker = $parentObj->modeSTAT = $parentObj->LCDTicks = $parentObj->actualScanLine = $parentObj->memory[0xFF44] = 0;
                    if ($parentObj->LCDisOn) {
                        $parentObj->matchLYC(); //Get the compare of the first scan line.
                        $parentObj->LCDCONTROL = $parentObj->LINECONTROL;
                    } else {
                        $parentObj->LCDCONTROL = $parentObj->DISPLAYOFFCONTROL;
                        $parentObj->displayShowOff();
                    }
                    $parentObj->memory[0xFF0F] &= 0xFD;
                }
                $parentObj->gfxWindowY = ($data & 0x40) == 0x40;
                $parentObj->gfxWindowDisplay = ($data & 0x20) == 0x20;
                $parentObj->gfxBackgroundX = ($data & 0x10) == 0x10;
                $parentObj->gfxBackgroundY = ($data & 0x08) == 0x08;
                $parentObj->gfxSpriteDouble = ($data & 0x04) == 0x04;
                $parentObj->gfxSpriteShow = ($data & 0x02) == 0x02;
                if (($data & 0x01) == 0) {
                    // this emulates the gbc-in-gb-mode, not the original gb-mode
                    $parentObj->bgEnabled = false;
                    $parentObj->gfxWindowDisplay = false;
                } else {
                    $parentObj->bgEnabled = true;
                }
                $parentObj->memory[0xFF40] = $data;
            };
            $this->memoryWriter[0xFF41] = function ($parentObj, $address, $data) {
                $parentObj->LYCMatchTriggerSTAT = (($data & 0x40) == 0x40);
                $parentObj->mode2TriggerSTAT = (($data & 0x20) == 0x20);
                $parentObj->mode1TriggerSTAT = (($data & 0x10) == 0x10);
                $parentObj->mode0TriggerSTAT = (($data & 0x08) == 0x08);
                $parentObj->memory[0xFF41] = ($data & 0xF8);
                if ($parentObj->LCDisOn && $parentObj->modeSTAT < 2) {
                    $parentObj->memory[0xFF0F] |= 0x2;
                }
            };
            $this->memoryWriter[0xFF4D] = function ($parentObj, $address, $data) {
                $parentObj->memory[0xFF4D] = $data;
            };
            $this->memoryWriter[0xFF4F] = function ($parentObj, $address, $data) {
                //Not writable in DMG mode.
            };
            $this->memoryWriter[0xFF55] = function ($parentObj, $address, $data) {
                $parentObj->memory[0xFF55] = $data;
            };
            $this->memoryWriter[0xFF68] = function ($parentObj, $address, $data) {
                $parentObj->memory[0xFF68] = $data;
            };
            $this->memoryWriter[0xFF69] = function ($parentObj, $address, $data) {
                $parentObj->memory[0xFF69] = $data;
            };
            $this->memoryWriter[0xFF6A] = function ($parentObj, $address, $data) {
                $parentObj->memory[0xFF6A] = $data;
            };
            $this->memoryWriter[0xFF6B] = function ($parentObj, $address, $data) {
                $parentObj->memory[0xFF6B] = $data;
            };
            $this->memoryWriter[0xFF70] = function ($parentObj, $address, $data) {
                $parentObj->memory[0xFF70] = $data;
            };
        }
        //Boot I/O Registers:
        if ($this->inBootstrap) {
            $this->memoryWriter[0xFF50] = function ($parentObj, $address, $data) {
                echo 'Boot ROM reads blocked: Bootstrap process has ended.'.PHP_EOL;
                $parentObj->inBootstrap = false;
                $parentObj->disableBootROM(); //Fill in the boot ROM ranges with ROM  bank 0 ROM ranges
                $parentObj->memory[0xFF50] = $data; //Bits are sustained in memory?
            };
            $this->memoryWriter[0xFF6C] = function ($parentObj, $address, $data) {
                if ($parentObj->inBootstrap) {
                    $parentObj->cGBC = ($data == 0x80);
                    echo 'Booted to GBC Mode: ' + $parentObj->cGBC.PHP_EOL;
                }
                $parentObj->memory[0xFF6C] = $data;
            };
        } else {
            //Lockout the ROMs from accessing the BOOT ROM control register:
            $this->memoryWriter[0xFF6C] = $this->memoryWriter[0xFF50] = function ($parentObj, $address, $data) {
            };
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
            for ($index = 0; $index < count($baseArray); ++$index) {
                $typedArrayTemp[$index] = $baseArray[$index];
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
            for ($index = 0; $index < count($baseArray); ++$index) {
                $arrayTemp[$index] = $baseArray[$index];
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
