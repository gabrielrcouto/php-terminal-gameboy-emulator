<?php

namespace GameBoy;

class Opcode
{
    public $functionsArray = [];

    public function __construct()
    {
        //NOP
        //#0x00:
        $this->functionsArray[] = function ($parentObj) {
            //Do Nothing...
        };
        //LD BC, nn
        //#0x01:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerC = $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter);
            $parentObj->registerB = $parentObj->memoryRead(($parentObj->programCounter + 1) & 0xFFFF);
            $parentObj->programCounter = ($parentObj->programCounter + 2) & 0xFFFF;
        };
        //LD (BC), A
        //#0x02:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite(($parentObj->registerB << 8) + $parentObj->registerC, $parentObj->registerA);
        };
        //INC BC
        //#0x03:
        $this->functionsArray[] = function ($parentObj) {
            $temp_var = ((($parentObj->registerB << 8) + $parentObj->registerC) + 1);
            $parentObj->registerB = (($temp_var >> 8) & 0xFF);
            $parentObj->registerC = ($temp_var & 0xFF);
        };
        //INC B
        //#0x04:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerB = (($parentObj->registerB + 1) & 0xFF);
            $parentObj->FZero = ($parentObj->registerB == 0);
            $parentObj->FHalfCarry = (($parentObj->registerB & 0xF) == 0);
            $parentObj->FSubtract = false;
        };
        //DEC B
        //#0x05:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerB = $parentObj->unsbtub($parentObj->registerB - 1);
            $parentObj->FZero = ($parentObj->registerB == 0);
            $parentObj->FHalfCarry = (($parentObj->registerB & 0xF) == 0xF);
            $parentObj->FSubtract = true;
        };
        //LD B, n
        //#0x06:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerB = $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter);
            $parentObj->programCounter = ($parentObj->programCounter + 1) & 0xFFFF;
        };
        //RLCA
        //#0x07:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registerA & 0x80) == 0x80);
            $parentObj->registerA = (($parentObj->registerA << 1) & 0xFF) | ($parentObj->registerA >> 7);
            $parentObj->FZero = $parentObj->FSubtract = $parentObj->FHalfCarry = false;
        };
        //LD (nn), SP
        //#0x08:
        $this->functionsArray[] = function ($parentObj) {
            $temp_var = ($parentObj->memoryRead(($parentObj->programCounter + 1) & 0xFFFF) << 8) + $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter);
            $parentObj->memoryWrite($temp_var, $parentObj->stackPointer & 0xFF);
            $parentObj->memoryWrite(($temp_var + 1) & 0xFFFF, $parentObj->stackPointer >> 8);
            $parentObj->programCounter = ($parentObj->programCounter + 2) & 0xFFFF;
        };
        //ADD HL, BC
        //#0x09:
        $this->functionsArray[] = function ($parentObj) {
            $n2 = ($parentObj->registerB << 8) + $parentObj->registerC;
            $dirtySum = $parentObj->registersHL + $n2;
            $parentObj->FHalfCarry = (($parentObj->registersHL & 0xFFF) + ($n2 & 0xFFF) > 0xFFF);
            $parentObj->FCarry = ($dirtySum > 0xFFFF);
            $parentObj->registersHL = ($dirtySum & 0xFFFF);
            $parentObj->FSubtract = false;
        };
        //LD A, (BC)
        //#0x0A:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA = $parentObj->memoryRead(($parentObj->registerB << 8) + $parentObj->registerC);
        };
        //DEC BC
        //#0x0B:
        $this->functionsArray[] = function ($parentObj) {
            $temp_var = $parentObj->unswtuw((($parentObj->registerB << 8) + $parentObj->registerC) - 1);
            $parentObj->registerB = ($temp_var >> 8);
            $parentObj->registerC = ($temp_var & 0xFF);
        };
        //INC C
        //#0x0C:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerC = (($parentObj->registerC + 1) & 0xFF);
            $parentObj->FZero = ($parentObj->registerC == 0);
            $parentObj->FHalfCarry = (($parentObj->registerC & 0xF) == 0);
            $parentObj->FSubtract = false;
        };
        //DEC C
        //#0x0D:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerC = $parentObj->unsbtub($parentObj->registerC - 1);
            $parentObj->FZero = ($parentObj->registerC == 0);
            $parentObj->FHalfCarry = (($parentObj->registerC & 0xF) == 0xF);
            $parentObj->FSubtract = true;
        };
        //LD C, n
        //#0x0E:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerC = $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter);
            $parentObj->programCounter = ($parentObj->programCounter + 1) & 0xFFFF;
        };
        //RRCA
        //#0x0F:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registerA & 1) == 1);
            $parentObj->registerA = ($parentObj->registerA >> 1) + (($parentObj->registerA & 1) << 7);
            $parentObj->FZero = $parentObj->FSubtract = $parentObj->FHalfCarry = false;
        };
        //STOP
        //#0x10:
        $this->functionsArray[] = function ($parentObj) {
            if ($parentObj->cGBC) {
                /*TODO: Emulate the speed switch delay:
                Delay Amount:
                16 ms when going to double-speed.
                32 ms when going to single-speed.
                Also, bits 4 and 5 of 0xFF00 should read as set (1), while the switch is in process.
                 */
                if (($parentObj->memory[0xFF4D] & 0x01) == 0x01) { //Speed change requested.
                    if (($parentObj->memory[0xFF4D] & 0x80) == 0x80) { //Go back to single speed mode.
                        // cout("Going into single clock speed mode.", 0);
                        $parentObj->multiplier = 1; //TODO: Move this into the delay done code.
                        $parentObj->memory[0xFF4D] &= 0x7F; //Clear the double speed mode flag.
                    } else { //Go to double speed mode.
                        // cout("Going into double clock speed mode.", 0);
                        $parentObj->multiplier = 2; //TODO: Move this into the delay done code.
                        $parentObj->memory[0xFF4D] |= 0x80; //Set the double speed mode flag.
                    }
                    $parentObj->memory[0xFF4D] &= 0xFE; //Reset the request bit.
                }
            }
        };
        //LD DE, nn
        //#0x11:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerE = $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter);
            $parentObj->registerD = $parentObj->memoryRead(($parentObj->programCounter + 1) & 0xFFFF);
            $parentObj->programCounter = ($parentObj->programCounter + 2) & 0xFFFF;
        };
        //LD (DE), A
        //#0x12:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite(($parentObj->registerD << 8) + $parentObj->registerE, $parentObj->registerA);
        };
        //INC DE
        //#0x13:
        $this->functionsArray[] = function ($parentObj) {
            $temp_var = ((($parentObj->registerD << 8) + $parentObj->registerE) + 1);
            $parentObj->registerD = (($temp_var >> 8) & 0xFF);
            $parentObj->registerE = ($temp_var & 0xFF);
        };
        //INC D
        //#0x14:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerD = (($parentObj->registerD + 1) & 0xFF);
            $parentObj->FZero = ($parentObj->registerD == 0);
            $parentObj->FHalfCarry = (($parentObj->registerD & 0xF) == 0);
            $parentObj->FSubtract = false;
        };
        //DEC D
        //#0x15:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerD = $parentObj->unsbtub($parentObj->registerD - 1);
            $parentObj->FZero = ($parentObj->registerD == 0);
            $parentObj->FHalfCarry = (($parentObj->registerD & 0xF) == 0xF);
            $parentObj->FSubtract = true;
        };
        //LD D, n
        //#0x16:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerD = $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter);
            $parentObj->programCounter = ($parentObj->programCounter + 1) & 0xFFFF;
        };
        //RLA
        //#0x17:
        $this->functionsArray[] = function ($parentObj) {
            $carry_flag = ($parentObj->FCarry) ? 1 : 0;
            $parentObj->FCarry = (($parentObj->registerA & 0x80) == 0x80);
            $parentObj->registerA = (($parentObj->registerA << 1) & 0xFF) | $carry_flag;
            $parentObj->FZero = $parentObj->FSubtract = $parentObj->FHalfCarry = false;
        };
        //JR n
        //#0x18:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->programCounter = $parentObj->nswtuw($parentObj->programCounter + $parentObj->usbtsb($parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter)) + 1);
        };
        //ADD HL, DE
        //#0x19:
        $this->functionsArray[] = function ($parentObj) {
            $n2 = ($parentObj->registerD << 8) + $parentObj->registerE;
            $dirtySum = $parentObj->registersHL + $n2;
            $parentObj->FHalfCarry = (($parentObj->registersHL & 0xFFF) + ($n2 & 0xFFF) > 0xFFF);
            $parentObj->FCarry = ($dirtySum > 0xFFFF);
            $parentObj->registersHL = ($dirtySum & 0xFFFF);
            $parentObj->FSubtract = false;
        };
        //LD A, (DE)
        //#0x1A:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA = $parentObj->memoryRead(($parentObj->registerD << 8) + $parentObj->registerE);
        };
        //DEC DE
        //#0x1B:
        $this->functionsArray[] = function ($parentObj) {
            $temp_var = $parentObj->unswtuw((($parentObj->registerD << 8) + $parentObj->registerE) - 1);
            $parentObj->registerD = ($temp_var >> 8);
            $parentObj->registerE = ($temp_var & 0xFF);
        };
        //INC E
        //#0x1C:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerE = (($parentObj->registerE + 1) & 0xFF);
            $parentObj->FZero = ($parentObj->registerE == 0);
            $parentObj->FHalfCarry = (($parentObj->registerE & 0xF) == 0);
            $parentObj->FSubtract = false;
        };
        //DEC E
        //#0x1D:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerE = $parentObj->unsbtub($parentObj->registerE - 1);
            $parentObj->FZero = ($parentObj->registerE == 0);
            $parentObj->FHalfCarry = (($parentObj->registerE & 0xF) == 0xF);
            $parentObj->FSubtract = true;
        };
        //LD E, n
        //#0x1E:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerE = $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter);
            $parentObj->programCounter = ($parentObj->programCounter + 1) & 0xFFFF;
        };
        //RRA
        //#0x1F:
        $this->functionsArray[] = function ($parentObj) {
            $carry_flag = ($parentObj->FCarry) ? 0x80 : 0;
            $parentObj->FCarry = (($parentObj->registerA & 1) == 1);
            $parentObj->registerA = ($parentObj->registerA >> 1) + $carry_flag;
            $parentObj->FZero = $parentObj->FSubtract = $parentObj->FHalfCarry = false;
        };
        //JR cc, n
        //#0x20:
        $this->functionsArray[] = function ($parentObj) {
            if (!$parentObj->FZero) {
                $parentObj->programCounter = $parentObj->nswtuw($parentObj->programCounter + $parentObj->usbtsb($parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter)) + 1);
                ++$parentObj->CPUTicks;
            } else {
                $parentObj->programCounter = ($parentObj->programCounter + 1) & 0xFFFF;
            }
        };
        //LD HL, nn
        //#0x21:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL = ($parentObj->memoryRead(($parentObj->programCounter + 1) & 0xFFFF) << 8) + $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter);
            $parentObj->programCounter = ($parentObj->programCounter + 2) & 0xFFFF;
        };
        //LDI (HL), A
        //#0x22:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite($parentObj->registersHL, $parentObj->registerA);
            $parentObj->registersHL = (($parentObj->registersHL + 1) & 0xFFFF);
        };
        //INC HL
        //#0x23:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL = (($parentObj->registersHL + 1) & 0xFFFF);
        };
        //INC H
        //#0x24:
        $this->functionsArray[] = function ($parentObj) {
            $H = ((($parentObj->registersHL >> 8) + 1) & 0xFF);
            $parentObj->FZero = ($H == 0);
            $parentObj->FHalfCarry = (($H & 0xF) == 0);
            $parentObj->FSubtract = false;
            $parentObj->registersHL = ($H << 8) + ($parentObj->registersHL & 0xFF);
        };
        //DEC H
        //#0x25:
        $this->functionsArray[] = function ($parentObj) {
            $H = $parentObj->unsbtub(($parentObj->registersHL >> 8) - 1);
            $parentObj->FZero = ($H == 0);
            $parentObj->FHalfCarry = (($H & 0xF) == 0xF);
            $parentObj->FSubtract = true;
            $parentObj->registersHL = ($H << 8) + ($parentObj->registersHL & 0xFF);
        };
        //LD H, n
        //#0x26:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL = ($parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter) << 8) + ($parentObj->registersHL & 0xFF);
            $parentObj->programCounter = ($parentObj->programCounter + 1) & 0xFFFF;
        };
        //DAA
        //#0x27:
        $this->functionsArray[] = function ($parentObj) {
            $temp_var = $parentObj->registerA;
            if ($parentObj->FCarry) {
                $temp_var |= 0x100;
            }
            if ($parentObj->FHalfCarry) {
                $temp_var |= 0x200;
            }
            if ($parentObj->FSubtract) {
                $temp_var |= 0x400;
            }
            $parentObj->registerA = ($temp_var = $parentObj->DAATable[$temp_var]) >> 8;
            $parentObj->FZero = (($temp_var & 0x80) == 0x80);
            $parentObj->FSubtract = (($temp_var & 0x40) == 0x40);
            $parentObj->FHalfCarry = (($temp_var & 0x20) == 0x20);
            $parentObj->FCarry = (($temp_var & 0x10) == 0x10);
        };
        //JR cc, n
        //#0x28:
        $this->functionsArray[] = function ($parentObj) {
            if ($parentObj->FZero) {
                $parentObj->programCounter = $parentObj->nswtuw($parentObj->programCounter + $parentObj->usbtsb($parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter)) + 1);
                ++$parentObj->CPUTicks;
            } else {
                $parentObj->programCounter = ($parentObj->programCounter + 1) & 0xFFFF;
            }
        };
        //ADD HL, HL
        //#0x29:
        $this->functionsArray[] = function ($parentObj) {;
            $parentObj->FHalfCarry = (($parentObj->registersHL & 0xFFF) > 0x7FF);
            $parentObj->FCarry = ($parentObj->registersHL > 0x7FFF);
            $parentObj->registersHL = ((2 * $parentObj->registersHL) & 0xFFFF);
            $parentObj->FSubtract = false;
        };
        //LDI A, (HL)
        //#0x2A:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA = $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL);
            $parentObj->registersHL = (($parentObj->registersHL + 1) & 0xFFFF);
        };
        //DEC HL
        //#0x2B:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL = $parentObj->unswtuw($parentObj->registersHL - 1);
        };
        //INC L
        //#0x2C:
        $this->functionsArray[] = function ($parentObj) {
            $L = (($parentObj->registersHL + 1) & 0xFF);
            $parentObj->FZero = ($L == 0);
            $parentObj->FHalfCarry = (($L & 0xF) == 0);
            $parentObj->FSubtract = false;
            $parentObj->registersHL = ($parentObj->registersHL & 0xFF00) + $L;
        };
        //DEC L
        //#0x2D:
        $this->functionsArray[] = function ($parentObj) {
            $L = $parentObj->unsbtub(($parentObj->registersHL & 0xFF) - 1);
            $parentObj->FZero = ($L == 0);
            $parentObj->FHalfCarry = (($L & 0xF) == 0xF);
            $parentObj->FSubtract = true;
            $parentObj->registersHL = ($parentObj->registersHL & 0xFF00) + $L;
        };
        //LD L, n
        //#0x2E:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL = ($parentObj->registersHL & 0xFF00) + $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter);
            $parentObj->programCounter = ($parentObj->programCounter + 1) & 0xFFFF;
        };
        //CPL
        //#0x2F:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA ^= 0xFF;
            $parentObj->FSubtract = $parentObj->FHalfCarry = true;
        };
        //JR cc, n
        //#0x30:
        $this->functionsArray[] = function ($parentObj) {
            if (!$parentObj->FCarry) {
                $parentObj->programCounter = $parentObj->nswtuw($parentObj->programCounter + $parentObj->usbtsb($parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter)) + 1);
                ++$parentObj->CPUTicks;
            } else {
                $parentObj->programCounter = ($parentObj->programCounter + 1) & 0xFFFF;
            }
        };
        //LD SP, nn
        //#0x31:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->stackPointer = ($parentObj->memoryRead(($parentObj->programCounter + 1) & 0xFFFF) << 8) + $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter);
            $parentObj->programCounter = ($parentObj->programCounter + 2) & 0xFFFF;
        };
        //LDD (HL), A
        //#0x32:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite($parentObj->registersHL, $parentObj->registerA);
            $parentObj->registersHL = $parentObj->unswtuw($parentObj->registersHL - 1);
        };
        //INC SP
        //#0x33:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->stackPointer = ($parentObj->stackPointer + 1) & 0xFFFF;
        };
        //INC (HL)
        //#0x34:
        $this->functionsArray[] = function ($parentObj) {
            $temp_var = (($parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL) + 1) & 0xFF);
            $parentObj->FZero = ($temp_var == 0);
            $parentObj->FHalfCarry = (($temp_var & 0xF) == 0);
            $parentObj->FSubtract = false;
            $parentObj->memoryWrite($parentObj->registersHL, $temp_var);
        };
        //DEC (HL)
        //#0x35:
        $this->functionsArray[] = function ($parentObj) {
            $temp_var = $parentObj->unsbtub($parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL) - 1);
            $parentObj->FZero = ($temp_var == 0);
            $parentObj->FHalfCarry = (($temp_var & 0xF) == 0xF);
            $parentObj->FSubtract = true;
            $parentObj->memoryWrite($parentObj->registersHL, $temp_var);
        };
        //LD (HL), n
        //#0x36:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite($parentObj->registersHL, $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter));
            $parentObj->programCounter = ($parentObj->programCounter + 1) & 0xFFFF;
        };
        //SCF
        //#0x37:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = true;
            $parentObj->FSubtract = $parentObj->FHalfCarry = false;
        };
        //JR cc, n
        //#0x38:
        $this->functionsArray[] = function ($parentObj) {
            if ($parentObj->FCarry) {
                $parentObj->programCounter = $parentObj->nswtuw($parentObj->programCounter + $parentObj->usbtsb($parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter)) + 1);
                ++$parentObj->CPUTicks;
            } else {
                $parentObj->programCounter = ($parentObj->programCounter + 1) & 0xFFFF;
            }
        };
        //ADD HL, SP
        //#0x39:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registersHL + $parentObj->stackPointer;
            $parentObj->FHalfCarry = (($parentObj->registersHL & 0xFFF) + ($parentObj->stackPointer & 0xFFF) > 0xFFF);
            $parentObj->FCarry = ($dirtySum > 0xFFFF);
            $parentObj->registersHL = ($dirtySum & 0xFFFF);
            $parentObj->FSubtract = false;
        };
        // LDD A, (HL)
        //#0x3A:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA = $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL);
            $parentObj->registersHL = $parentObj->unswtuw($parentObj->registersHL - 1);
        };
        //DEC SP
        //#0x3B:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
        };
        //INC A
        //#0x3C:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA = (($parentObj->registerA + 1) & 0xFF);
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FHalfCarry = (($parentObj->registerA & 0xF) == 0);
            $parentObj->FSubtract = false;
        };
        //DEC A
        //#0x3D:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA = $parentObj->unsbtub($parentObj->registerA - 1);
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FHalfCarry = (($parentObj->registerA & 0xF) == 0xF);
            $parentObj->FSubtract = true;
        };
        //LD A, n
        //#0x3E:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA = $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter);
            $parentObj->programCounter = ($parentObj->programCounter + 1) & 0xFFFF;
        };
        //CCF
        //#0x3F:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = !$parentObj->FCarry;
            $parentObj->FSubtract = $parentObj->FHalfCarry = false;
        };
        //LD B, B
        //#0x40:
        $this->functionsArray[] = function ($parentObj) {
            //Do nothing...
        };
        //LD B, C
        //#0x41:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerB = $parentObj->registerC;
        };
        //LD B, D
        //#0x42:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerB = $parentObj->registerD;
        };
        //LD B, E
        //#0x43:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerB = $parentObj->registerE;
        };
        //LD B, H
        //#0x44:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerB = ($parentObj->registersHL >> 8);
        };
        //LD B, L
        //#0x45:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerB = ($parentObj->registersHL & 0xFF);
        };
        //LD B, (HL)
        //#0x46:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerB = $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL);
        };
        //LD B, A
        //#0x47:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerB = $parentObj->registerA;
        };
        //LD C, B
        //#0x48:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerC = $parentObj->registerB;
        };
        //LD C, C
        //#0x49:
        $this->functionsArray[] = function ($parentObj) {
            //Do nothing...
        };
        //LD C, D
        //#0x4A:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerC = $parentObj->registerD;
        };
        //LD C, E
        //#0x4B:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerC = $parentObj->registerE;
        };
        //LD C, H
        //#0x4C:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerC = ($parentObj->registersHL >> 8);
        };
        //LD C, L
        //#0x4D:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerC = ($parentObj->registersHL & 0xFF);
        };
        //LD C, (HL)
        //#0x4E:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerC = $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL);
        };
        //LD C, A
        //#0x4F:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerC = $parentObj->registerA;
        };
        //LD D, B
        //#0x50:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerD = $parentObj->registerB;
        };
        //LD D, C
        //#0x51:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerD = $parentObj->registerC;
        };
        //LD D, D
        //#0x52:
        $this->functionsArray[] = function ($parentObj) {
            //Do nothing...
        };
        //LD D, E
        //#0x53:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerD = $parentObj->registerE;
        };
        //LD D, H
        //#0x54:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerD = ($parentObj->registersHL >> 8);
        };
        //LD D, L
        //#0x55:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerD = ($parentObj->registersHL & 0xFF);
        };
        //LD D, (HL)
        //#0x56:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerD = $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL);
        };
        //LD D, A
        //#0x57:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerD = $parentObj->registerA;
        };
        //LD E, B
        //#0x58:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerE = $parentObj->registerB;
        };
        //LD E, C
        //#0x59:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerE = $parentObj->registerC;
        };
        //LD E, D
        //#0x5A:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerE = $parentObj->registerD;
        };
        //LD E, E
        //#0x5B:
        $this->functionsArray[] = function ($parentObj) {
            //Do nothing...
        };
        //LD E, H
        //#0x5C:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerE = ($parentObj->registersHL >> 8);
        };
        //LD E, L
        //#0x5D:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerE = ($parentObj->registersHL & 0xFF);
        };
        //LD E, (HL)
        //#0x5E:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerE = $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL);
        };
        //LD E, A
        //#0x5F:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerE = $parentObj->registerA;
        };
        //LD H, B
        //#0x60:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL = ($parentObj->registerB << 8) + ($parentObj->registersHL & 0xFF);
        };
        //LD H, C
        //#0x61:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL = ($parentObj->registerC << 8) + ($parentObj->registersHL & 0xFF);
        };
        //LD H, D
        //#0x62:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL = ($parentObj->registerD << 8) + ($parentObj->registersHL & 0xFF);
        };
        //LD H, E
        //#0x63:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL = ($parentObj->registerE << 8) + ($parentObj->registersHL & 0xFF);
        };
        //LD H, H
        //#0x64:
        $this->functionsArray[] = function ($parentObj) {
            //Do nothing...
        };
        //LD H, L
        //#0x65:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL = (($parentObj->registersHL & 0xFF) << 8) + ($parentObj->registersHL & 0xFF);
        };
        //LD H, (HL)
        //#0x66:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL = ($parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL) << 8) + ($parentObj->registersHL & 0xFF);
        };
        //LD H, A
        //#0x67:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL = ($parentObj->registerA << 8) + ($parentObj->registersHL & 0xFF);
        };
        //LD L, B
        //#0x68:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL = ($parentObj->registersHL & 0xFF00) + $parentObj->registerB;
        };
        //LD L, C
        //#0x69:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL = ($parentObj->registersHL & 0xFF00) + $parentObj->registerC;
        };
        //LD L, D
        //#0x6A:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL = ($parentObj->registersHL & 0xFF00) + $parentObj->registerD;
        };
        //LD L, E
        //#0x6B:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL = ($parentObj->registersHL & 0xFF00) + $parentObj->registerE;
        };
        //LD L, H
        //#0x6C:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL = ($parentObj->registersHL & 0xFF00) + ($parentObj->registersHL >> 8);
        };
        //LD L, L
        //#0x6D:
        $this->functionsArray[] = function ($parentObj) {
            //Do nothing...
        };
        //LD L, (HL)
        //#0x6E:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL = ($parentObj->registersHL & 0xFF00) + $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL);
        };
        //LD L, A
        //#0x6F:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL = ($parentObj->registersHL & 0xFF00) + $parentObj->registerA;
        };
        //LD (HL), B
        //#0x70:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite($parentObj->registersHL, $parentObj->registerB);
        };
        //LD (HL), C
        //#0x71:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite($parentObj->registersHL, $parentObj->registerC);
        };
        //LD (HL), D
        //#0x72:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite($parentObj->registersHL, $parentObj->registerD);
        };
        //LD (HL), E
        //#0x73:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite($parentObj->registersHL, $parentObj->registerE);
        };
        //LD (HL), H
        //#0x74:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite($parentObj->registersHL, ($parentObj->registersHL >> 8));
        };
        //LD (HL), L
        //#0x75:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite($parentObj->registersHL, ($parentObj->registersHL & 0xFF));
        };
        //HALT
        //#0x76:
        $this->functionsArray[] = function ($parentObj) {
            if ($parentObj->untilEnable == 1) {
                /*VBA-M says this fixes Torpedo Range (Seems to work):
                Involves an edge case where an EI is placed right before a HALT.
                EI in this case actually is immediate, so we adjust (Hacky?).*/
                $parentObj->programCounter = $parentObj->nswtuw($parentObj->programCounter - 1);
            } else {
                if (!$parentObj->halt && !$parentObj->IME && !$parentObj->cGBC && !$parentObj->usedBootROM && ($parentObj->memory[0xFF0F] & $parentObj->memory[0xFFFF] & 0x1F) > 0) {
                    $parentObj->skipPCIncrement = true;
                }
                $parentObj->halt = true;
                while ($parentObj->halt && ($parentObj->stopEmulator & 1) == 0) {
                    /*We're hijacking the main interpreter loop to do this dirty business
                    in order to not slow down the main interpreter loop code with halt state handling.*/
                    $bitShift = 0;
                    $testbit = 1;
                    $interrupts = $parentObj->memory[0xFFFF] & $parentObj->memory[0xFF0F];
                    while ($bitShift < 5) {
                        //Check to see if an interrupt is enabled AND requested.
                        if (($testbit & $interrupts) == $testbit) {
                            $parentObj->halt = false; //Get out of halt state if in halt state.
                            return; //Let the main interrupt handler compute the interrupt.
                        }
                        $testbit = 1 << ++$bitShift;
                    }
                    $parentObj->CPUTicks = 1; //1 machine cycle under HALT...
                    //Timing:
                    $parentObj->updateCore();
                }

                //Throw an error on purpose to exit out of the loop.
                throw new \Exception('HALT_OVERRUN');
            }
        };
        //LD (HL), A
        //#0x77:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite($parentObj->registersHL, $parentObj->registerA);
        };
        //LD A, B
        //#0x78:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA = $parentObj->registerB;
        };
        //LD A, C
        //#0x79:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA = $parentObj->registerC;
        };
        //LD A, D
        //#0x7A:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA = $parentObj->registerD;
        };
        //LD A, E
        //#0x7B:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA = $parentObj->registerE;
        };
        //LD A, H
        //#0x7C:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA = ($parentObj->registersHL >> 8);
        };
        //LD A, L
        //#0x7D:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA = ($parentObj->registersHL & 0xFF);
        };
        //LD, A, (HL)
        //#0x7E:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA = $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL);
        };
        //LD A, A
        //#0x7F:
        $this->functionsArray[] = function ($parentObj) {
            //Do Nothing...
        };
        //ADD A, B
        //#0x80:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA + $parentObj->registerB;
            $parentObj->FHalfCarry = ($dirtySum & 0xF) < ($parentObj->registerA & 0xF);
            $parentObj->FCarry = ($dirtySum > 0xFF);
            $parentObj->registerA = $dirtySum & 0xFF;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = false;
        };
        //ADD A, C
        //#0x81:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA + $parentObj->registerC;
            $parentObj->FHalfCarry = ($dirtySum & 0xF) < ($parentObj->registerA & 0xF);
            $parentObj->FCarry = ($dirtySum > 0xFF);
            $parentObj->registerA = $dirtySum & 0xFF;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = false;
        };
        //ADD A, D
        //#0x82:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA + $parentObj->registerD;
            $parentObj->FHalfCarry = ($dirtySum & 0xF) < ($parentObj->registerA & 0xF);
            $parentObj->FCarry = ($dirtySum > 0xFF);
            $parentObj->registerA = $dirtySum & 0xFF;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = false;
        };
        //ADD A, E
        //#0x83:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA + $parentObj->registerE;
            $parentObj->FHalfCarry = ($dirtySum & 0xF) < ($parentObj->registerA & 0xF);
            $parentObj->FCarry = ($dirtySum > 0xFF);
            $parentObj->registerA = $dirtySum & 0xFF;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = false;
        };
        //ADD A, H
        //#0x84:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA + ($parentObj->registersHL >> 8);
            $parentObj->FHalfCarry = ($dirtySum & 0xF) < ($parentObj->registerA & 0xF);
            $parentObj->FCarry = ($dirtySum > 0xFF);
            $parentObj->registerA = $dirtySum & 0xFF;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = false;
        };
        //ADD A, L
        //#0x85:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA + ($parentObj->registersHL & 0xFF);
            $parentObj->FHalfCarry = ($dirtySum & 0xF) < ($parentObj->registerA & 0xF);
            $parentObj->FCarry = ($dirtySum > 0xFF);
            $parentObj->registerA = $dirtySum & 0xFF;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = false;
        };
        //ADD A, (HL)
        //#0x86:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA + $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL);
            $parentObj->FHalfCarry = ($dirtySum & 0xF) < ($parentObj->registerA & 0xF);
            $parentObj->FCarry = ($dirtySum > 0xFF);
            $parentObj->registerA = $dirtySum & 0xFF;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = false;
        };
        //ADD A, A
        //#0x87:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA * 2;
            $parentObj->FHalfCarry = ($dirtySum & 0xF) < ($parentObj->registerA & 0xF);
            $parentObj->FCarry = ($dirtySum > 0xFF);
            $parentObj->registerA = $dirtySum & 0xFF;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = false;
        };
        //ADC A, B
        //#0x88:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA + $parentObj->registerB + (($parentObj->FCarry) ? 1 : 0);
            $parentObj->FHalfCarry = (($parentObj->registerA & 0xF) + ($parentObj->registerB & 0xF) + (($parentObj->FCarry) ? 1 : 0) > 0xF);
            $parentObj->FCarry = ($dirtySum > 0xFF);
            $parentObj->registerA = $dirtySum & 0xFF;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = false;
        };
        //ADC A, C
        //#0x89:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA + $parentObj->registerC + (($parentObj->FCarry) ? 1 : 0);
            $parentObj->FHalfCarry = (($parentObj->registerA & 0xF) + ($parentObj->registerC & 0xF) + (($parentObj->FCarry) ? 1 : 0) > 0xF);
            $parentObj->FCarry = ($dirtySum > 0xFF);
            $parentObj->registerA = $dirtySum & 0xFF;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = false;
        };
        //ADC A, D
        //#0x8A:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA + $parentObj->registerD + (($parentObj->FCarry) ? 1 : 0);
            $parentObj->FHalfCarry = (($parentObj->registerA & 0xF) + ($parentObj->registerD & 0xF) + (($parentObj->FCarry) ? 1 : 0) > 0xF);
            $parentObj->FCarry = ($dirtySum > 0xFF);
            $parentObj->registerA = $dirtySum & 0xFF;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = false;
        };
        //ADC A, E
        //#0x8B:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA + $parentObj->registerE + (($parentObj->FCarry) ? 1 : 0);
            $parentObj->FHalfCarry = (($parentObj->registerA & 0xF) + ($parentObj->registerE & 0xF) + (($parentObj->FCarry) ? 1 : 0) > 0xF);
            $parentObj->FCarry = ($dirtySum > 0xFF);
            $parentObj->registerA = $dirtySum & 0xFF;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = false;
        };
        //ADC A, H
        //#0x8C:
        $this->functionsArray[] = function ($parentObj) {
            $tempValue = ($parentObj->registersHL >> 8);
            $dirtySum = $parentObj->registerA + $tempValue + (($parentObj->FCarry) ? 1 : 0);
            $parentObj->FHalfCarry = (($parentObj->registerA & 0xF) + ($tempValue & 0xF) + (($parentObj->FCarry) ? 1 : 0) > 0xF);
            $parentObj->FCarry = ($dirtySum > 0xFF);
            $parentObj->registerA = $dirtySum & 0xFF;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = false;
        };
        //ADC A, L
        //#0x8D:
        $this->functionsArray[] = function ($parentObj) {
            $tempValue = ($parentObj->registersHL & 0xFF);
            $dirtySum = $parentObj->registerA + $tempValue + (($parentObj->FCarry) ? 1 : 0);
            $parentObj->FHalfCarry = (($parentObj->registerA & 0xF) + ($tempValue & 0xF) + (($parentObj->FCarry) ? 1 : 0) > 0xF);
            $parentObj->FCarry = ($dirtySum > 0xFF);
            $parentObj->registerA = $dirtySum & 0xFF;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = false;
        };
        //ADC A, (HL)
        //#0x8E:
        $this->functionsArray[] = function ($parentObj) {
            $tempValue = $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL);
            $dirtySum = $parentObj->registerA + $tempValue + (($parentObj->FCarry) ? 1 : 0);
            $parentObj->FHalfCarry = (($parentObj->registerA & 0xF) + ($tempValue & 0xF) + (($parentObj->FCarry) ? 1 : 0) > 0xF);
            $parentObj->FCarry = ($dirtySum > 0xFF);
            $parentObj->registerA = $dirtySum & 0xFF;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = false;
        };
        //ADC A, A
        //#0x8F:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = ($parentObj->registerA * 2) + (($parentObj->FCarry) ? 1 : 0);
            $parentObj->FHalfCarry = (($parentObj->registerA & 0xF) + ($parentObj->registerA & 0xF) + (($parentObj->FCarry) ? 1 : 0) > 0xF);
            $parentObj->FCarry = ($dirtySum > 0xFF);
            $parentObj->registerA = $dirtySum & 0xFF;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = false;
        };
        //SUB A, B
        //#0x90:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA - $parentObj->registerB;
            $parentObj->FHalfCarry = ($parentObj->registerA & 0xF) < ($parentObj->registerB & 0xF);
            $parentObj->FCarry = ($dirtySum < 0);
            $parentObj->registerA = $parentObj->unsbtub($dirtySum);
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = true;
        };
        //SUB A, C
        //#0x91:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA - $parentObj->registerC;
            $parentObj->FHalfCarry = ($parentObj->registerA & 0xF) < ($parentObj->registerC & 0xF);
            $parentObj->FCarry = ($dirtySum < 0);
            $parentObj->registerA = $parentObj->unsbtub($dirtySum);
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = true;
        };
        //SUB A, D
        //#0x92:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA - $parentObj->registerD;
            $parentObj->FHalfCarry = ($parentObj->registerA & 0xF) < ($parentObj->registerD & 0xF);
            $parentObj->FCarry = ($dirtySum < 0);
            $parentObj->registerA = $parentObj->unsbtub($dirtySum);
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = true;
        };
        //SUB A, E
        //#0x93:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA - $parentObj->registerE;
            $parentObj->FHalfCarry = ($parentObj->registerA & 0xF) < ($parentObj->registerE & 0xF);
            $parentObj->FCarry = ($dirtySum < 0);
            $parentObj->registerA = $parentObj->unsbtub($dirtySum);
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = true;
        };
        //SUB A, H
        //#0x94:
        $this->functionsArray[] = function ($parentObj) {
            $temp_var = $parentObj->registersHL >> 8;
            $dirtySum = $parentObj->registerA - $temp_var;
            $parentObj->FHalfCarry = ($parentObj->registerA & 0xF) < ($temp_var & 0xF);
            $parentObj->FCarry = ($dirtySum < 0);
            $parentObj->registerA = $parentObj->unsbtub($dirtySum);
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = true;
        };
        //SUB A, L
        //#0x95:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA - ($parentObj->registersHL & 0xFF);
            $parentObj->FHalfCarry = ($parentObj->registerA & 0xF) < ($parentObj->registersHL & 0xF);
            $parentObj->FCarry = ($dirtySum < 0);
            $parentObj->registerA = $parentObj->unsbtub($dirtySum);
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = true;
        };
        //SUB A, (HL)
        //#0x96:
        $this->functionsArray[] = function ($parentObj) {
            $temp_var = $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL);
            $dirtySum = $parentObj->registerA - $temp_var;
            $parentObj->FHalfCarry = ($parentObj->registerA & 0xF) < ($temp_var & 0xF);
            $parentObj->FCarry = ($dirtySum < 0);
            $parentObj->registerA = $parentObj->unsbtub($dirtySum);
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = true;
        };
        //SUB A, A
        //#0x97:
        $this->functionsArray[] = function ($parentObj) {
            //number - same number == 0
            $parentObj->registerA = 0;
            $parentObj->FHalfCarry = $parentObj->FCarry = false;
            $parentObj->FZero = $parentObj->FSubtract = true;
        };
        //SBC A, B
        //#0x98:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA - $parentObj->registerB - (($parentObj->FCarry) ? 1 : 0);
            $parentObj->FHalfCarry = (($parentObj->registerA & 0xF) - ($parentObj->registerB & 0xF) - (($parentObj->FCarry) ? 1 : 0) < 0);
            $parentObj->FCarry = ($dirtySum < 0);
            $parentObj->registerA = $parentObj->unsbtub($dirtySum);
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = true;
        };
        //SBC A, C
        //#0x99:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA - $parentObj->registerC - (($parentObj->FCarry) ? 1 : 0);
            $parentObj->FHalfCarry = (($parentObj->registerA & 0xF) - ($parentObj->registerC & 0xF) - (($parentObj->FCarry) ? 1 : 0) < 0);
            $parentObj->FCarry = ($dirtySum < 0);
            $parentObj->registerA = $parentObj->unsbtub($dirtySum);
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = true;
        };
        //SBC A, D
        //#0x9A:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA - $parentObj->registerD - (($parentObj->FCarry) ? 1 : 0);
            $parentObj->FHalfCarry = (($parentObj->registerA & 0xF) - ($parentObj->registerD & 0xF) - (($parentObj->FCarry) ? 1 : 0) < 0);
            $parentObj->FCarry = ($dirtySum < 0);
            $parentObj->registerA = $parentObj->unsbtub($dirtySum);
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = true;
        };
        //SBC A, E
        //#0x9B:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA - $parentObj->registerE - (($parentObj->FCarry) ? 1 : 0);
            $parentObj->FHalfCarry = (($parentObj->registerA & 0xF) - ($parentObj->registerE & 0xF) - (($parentObj->FCarry) ? 1 : 0) < 0);
            $parentObj->FCarry = ($dirtySum < 0);
            $parentObj->registerA = $parentObj->unsbtub($dirtySum);
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = true;
        };
        //SBC A, H
        //#0x9C:
        $this->functionsArray[] = function ($parentObj) {
            $temp_var = $parentObj->registersHL >> 8;
            $dirtySum = $parentObj->registerA - $temp_var - (($parentObj->FCarry) ? 1 : 0);
            $parentObj->FHalfCarry = (($parentObj->registerA & 0xF) - ($temp_var & 0xF) - (($parentObj->FCarry) ? 1 : 0) < 0);
            $parentObj->FCarry = ($dirtySum < 0);
            $parentObj->registerA = $parentObj->unsbtub($dirtySum);
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = true;
        };
        //SBC A, L
        //#0x9D:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA - ($parentObj->registersHL & 0xFF) - (($parentObj->FCarry) ? 1 : 0);
            $parentObj->FHalfCarry = (($parentObj->registerA & 0xF) - ($parentObj->registersHL & 0xF) - (($parentObj->FCarry) ? 1 : 0) < 0);
            $parentObj->FCarry = ($dirtySum < 0);
            $parentObj->registerA = $parentObj->unsbtub($dirtySum);
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = true;
        };
        //SBC A, (HL)
        //#0x9E:
        $this->functionsArray[] = function ($parentObj) {
            $temp_var = $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL);
            $dirtySum = $parentObj->registerA - $temp_var - (($parentObj->FCarry) ? 1 : 0);
            $parentObj->FHalfCarry = (($parentObj->registerA & 0xF) - ($temp_var & 0xF) - (($parentObj->FCarry) ? 1 : 0) < 0);
            $parentObj->FCarry = ($dirtySum < 0);
            $parentObj->registerA = $parentObj->unsbtub($dirtySum);
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = true;
        };
        //SBC A, A
        //#0x9F:
        $this->functionsArray[] = function ($parentObj) {
            //Optimized SBC A:
            if ($parentObj->FCarry) {
                $parentObj->FZero = false;
                $parentObj->FSubtract = $parentObj->FHalfCarry = $parentObj->FCarry = true;
                $parentObj->registerA = 0xFF;
            } else {
                $parentObj->FHalfCarry = $parentObj->FCarry = false;
                $parentObj->FSubtract = $parentObj->FZero = true;
                $parentObj->registerA = 0;
            }
        };
        //AND B
        //#0xA0:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA &= $parentObj->registerB;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = $parentObj->FCarry = false;
        };
        //AND C
        //#0xA1:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA &= $parentObj->registerC;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = $parentObj->FCarry = false;
        };
        //AND D
        //#0xA2:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA &= $parentObj->registerD;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = $parentObj->FCarry = false;
        };
        //AND E
        //#0xA3:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA &= $parentObj->registerE;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = $parentObj->FCarry = false;
        };
        //AND H
        //#0xA4:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA &= ($parentObj->registersHL >> 8);
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = $parentObj->FCarry = false;
        };
        //AND L
        //#0xA5:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA &= ($parentObj->registersHL & 0xFF);
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = $parentObj->FCarry = false;
        };
        //AND (HL)
        //#0xA6:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA &= $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL);
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = $parentObj->FCarry = false;
        };
        //AND A
        //#0xA7:
        $this->functionsArray[] = function ($parentObj) {
            //number & same number = same number
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = $parentObj->FCarry = false;
        };
        //XOR B
        //#0xA8:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA ^= $parentObj->registerB;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = $parentObj->FHalfCarry = $parentObj->FCarry = false;
        };
        //XOR C
        //#0xA9:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA ^= $parentObj->registerC;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = $parentObj->FHalfCarry = $parentObj->FCarry = false;
        };
        //XOR D
        //#0xAA:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA ^= $parentObj->registerD;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = $parentObj->FHalfCarry = $parentObj->FCarry = false;
        };
        //XOR E
        //#0xAB:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA ^= $parentObj->registerE;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = $parentObj->FHalfCarry = $parentObj->FCarry = false;
        };
        //XOR H
        //#0xAC:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA ^= ($parentObj->registersHL >> 8);
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = $parentObj->FHalfCarry = $parentObj->FCarry = false;
        };
        //XOR L
        //#0xAD:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA ^= ($parentObj->registersHL & 0xFF);
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = $parentObj->FHalfCarry = $parentObj->FCarry = false;
        };
        //XOR (HL)
        //#0xAE:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA ^= $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL);
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = $parentObj->FHalfCarry = $parentObj->FCarry = false;
        };
        //XOR A
        //#0xAF:
        $this->functionsArray[] = function ($parentObj) {
            //number ^ same number == 0
            $parentObj->registerA = 0;
            $parentObj->FZero = true;
            $parentObj->FSubtract = $parentObj->FHalfCarry = $parentObj->FCarry = false;
        };
        //OR B
        //#0xB0:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA |= $parentObj->registerB;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = $parentObj->FCarry = $parentObj->FHalfCarry = false;
        };
        //OR C
        //#0xB1:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA |= $parentObj->registerC;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = $parentObj->FCarry = $parentObj->FHalfCarry = false;
        };
        //OR D
        //#0xB2:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA |= $parentObj->registerD;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = $parentObj->FCarry = $parentObj->FHalfCarry = false;
        };
        //OR E
        //#0xB3:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA |= $parentObj->registerE;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = $parentObj->FCarry = $parentObj->FHalfCarry = false;
        };
        //OR H
        //#0xB4:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA |= ($parentObj->registersHL >> 8);
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = $parentObj->FCarry = $parentObj->FHalfCarry = false;
        };
        //OR L
        //#0xB5:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA |= ($parentObj->registersHL & 0xFF);
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = $parentObj->FCarry = $parentObj->FHalfCarry = false;
        };
        //OR (HL)
        //#0xB6:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA |= $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL);
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = $parentObj->FCarry = $parentObj->FHalfCarry = false;
        };
        //OR A
        //#0xB7:
        $this->functionsArray[] = function ($parentObj) {
            //number | same number == same number
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = $parentObj->FCarry = $parentObj->FHalfCarry = false;
        };
        //CP B
        //#0xB8:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA - $parentObj->registerB;
            $parentObj->FHalfCarry = ($parentObj->unsbtub($dirtySum) & 0xF) > ($parentObj->registerA & 0xF);
            $parentObj->FCarry = ($dirtySum < 0);
            $parentObj->FZero = ($dirtySum == 0);
            $parentObj->FSubtract = true;
        };
        //CP C
        //#0xB9:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA - $parentObj->registerC;
            $parentObj->FHalfCarry = ($parentObj->unsbtub($dirtySum) & 0xF) > ($parentObj->registerA & 0xF);
            $parentObj->FCarry = ($dirtySum < 0);
            $parentObj->FZero = ($dirtySum == 0);
            $parentObj->FSubtract = true;
        };
        //CP D
        //#0xBA:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA - $parentObj->registerD;
            $parentObj->FHalfCarry = ($parentObj->unsbtub($dirtySum) & 0xF) > ($parentObj->registerA & 0xF);
            $parentObj->FCarry = ($dirtySum < 0);
            $parentObj->FZero = ($dirtySum == 0);
            $parentObj->FSubtract = true;
        };
        //CP E
        //#0xBB:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA - $parentObj->registerE;
            $parentObj->FHalfCarry = ($parentObj->unsbtub($dirtySum) & 0xF) > ($parentObj->registerA & 0xF);
            $parentObj->FCarry = ($dirtySum < 0);
            $parentObj->FZero = ($dirtySum == 0);
            $parentObj->FSubtract = true;
        };
        //CP H
        //#0xBC:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA - ($parentObj->registersHL >> 8);
            $parentObj->FHalfCarry = ($parentObj->unsbtub($dirtySum) & 0xF) > ($parentObj->registerA & 0xF);
            $parentObj->FCarry = ($dirtySum < 0);
            $parentObj->FZero = ($dirtySum == 0);
            $parentObj->FSubtract = true;
        };
        //CP L
        //#0xBD:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA - ($parentObj->registersHL & 0xFF);
            $parentObj->FHalfCarry = ($parentObj->unsbtub($dirtySum) & 0xF) > ($parentObj->registerA & 0xF);
            $parentObj->FCarry = ($dirtySum < 0);
            $parentObj->FZero = ($dirtySum == 0);
            $parentObj->FSubtract = true;
        };
        //CP (HL)
        //#0xBE:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA - $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL);
            $parentObj->FHalfCarry = ($parentObj->unsbtub($dirtySum) & 0xF) > ($parentObj->registerA & 0xF);
            $parentObj->FCarry = ($dirtySum < 0);
            $parentObj->FZero = ($dirtySum == 0);
            $parentObj->FSubtract = true;
        };
        //CP A
        //#0xBF:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = $parentObj->FCarry = false;
            $parentObj->FZero = $parentObj->FSubtract = true;
        };
        //RET !FZ
        //#0xC0:
        $this->functionsArray[] = function ($parentObj) {
            if (!$parentObj->FZero) {
                $parentObj->programCounter = ($parentObj->memoryRead(($parentObj->stackPointer + 1) & 0xFFFF) << 8) + $parentObj->memoryReader[$parentObj->stackPointer]($parentObj, $parentObj->stackPointer);
                $parentObj->stackPointer = ($parentObj->stackPointer + 2) & 0xFFFF;
                $parentObj->CPUTicks += 3;
            }
        };
        //POP BC
        //#0xC1:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerC = $parentObj->memoryReader[$parentObj->stackPointer]($parentObj, $parentObj->stackPointer);
            $parentObj->registerB = $parentObj->memoryRead(($parentObj->stackPointer + 1) & 0xFFFF);
            $parentObj->stackPointer = ($parentObj->stackPointer + 2) & 0xFFFF;
        };
        //JP !FZ, nn
        //#0xC2:
        $this->functionsArray[] = function ($parentObj) {
            if (!$parentObj->FZero) {
                $parentObj->programCounter = ($parentObj->memoryRead(($parentObj->programCounter + 1) & 0xFFFF) << 8) + $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter);
                ++$parentObj->CPUTicks;
            } else {
                $parentObj->programCounter = ($parentObj->programCounter + 2) & 0xFFFF;
            }
        };
        //JP nn
        //#0xC3:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->programCounter = ($parentObj->memoryRead(($parentObj->programCounter + 1) & 0xFFFF) << 8) + $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter);
        };
        //CALL !FZ, nn
        //#0xC4:
        $this->functionsArray[] = function ($parentObj) {
            if (!$parentObj->FZero) {
                $temp_pc = ($parentObj->memoryRead(($parentObj->programCounter + 1) & 0xFFFF) << 8) + $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter);
                $parentObj->programCounter = ($parentObj->programCounter + 2) & 0xFFFF;
                $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
                $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->programCounter >> 8);
                $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
                $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->programCounter & 0xFF);
                $parentObj->programCounter = $temp_pc;
                $parentObj->CPUTicks += 3;
            } else {
                $parentObj->programCounter = ($parentObj->programCounter + 2) & 0xFFFF;
            }
        };
        //PUSH BC
        //#0xC5:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
            $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->registerB);
            $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
            $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->registerC);
        };
        //ADD, n
        //#0xC6:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA + $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter);
            $parentObj->FHalfCarry = ($dirtySum & 0xF) < ($parentObj->registerA & 0xF);
            $parentObj->FCarry = ($dirtySum > 0xFF);
            $parentObj->registerA = $dirtySum & 0xFF;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = false;
            $parentObj->programCounter = ($parentObj->programCounter + 1) & 0xFFFF;
        };
        //RST 0
        //#0xC7:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
            $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->programCounter >> 8);
            $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
            $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->programCounter & 0xFF);
            $parentObj->programCounter = 0;
        };
        //RET FZ
        //#0xC8:
        $this->functionsArray[] = function ($parentObj) {
            if ($parentObj->FZero) {
                $parentObj->programCounter = ($parentObj->memoryRead(($parentObj->stackPointer + 1) & 0xFFFF) << 8) + $parentObj->memoryReader[$parentObj->stackPointer]($parentObj, $parentObj->stackPointer);
                $parentObj->stackPointer = ($parentObj->stackPointer + 2) & 0xFFFF;
                $parentObj->CPUTicks += 3;
            }
        };
        //RET
        //#0xC9:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->programCounter = ($parentObj->memoryRead(($parentObj->stackPointer + 1) & 0xFFFF) << 8) + $parentObj->memoryReader[$parentObj->stackPointer]($parentObj, $parentObj->stackPointer);
            $parentObj->stackPointer = ($parentObj->stackPointer + 2) & 0xFFFF;
        };
        //JP FZ, nn
        //#0xCA:
        $this->functionsArray[] = function ($parentObj) {
            if ($parentObj->FZero) {
                $parentObj->programCounter = ($parentObj->memoryRead(($parentObj->programCounter + 1) & 0xFFFF) << 8) + $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter);
                ++$parentObj->CPUTicks;
            } else {
                $parentObj->programCounter = ($parentObj->programCounter + 2) & 0xFFFF;
            }
        };
        //Secondary OP Code Set:
        //#0xCB:
        $this->functionsArray[] = function ($parentObj) {
            $opcode = $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter);
            //Increment the program counter to the next instruction:
            $parentObj->programCounter = ($parentObj->programCounter + 1) & 0xFFFF;
            //Get how many CPU cycles the current 0xCBXX op code counts for:
            $parentObj->CPUTicks = $parentObj->SecondaryTICKTable[$opcode];
            //Execute secondary OP codes for the 0xCB OP code call.
            $parentObj->CBOPCODE[$opcode]($parentObj);
        };
        //CALL FZ, nn
        //#0xCC:
        $this->functionsArray[] = function ($parentObj) {
            if ($parentObj->FZero) {
                $temp_pc = ($parentObj->memoryRead(($parentObj->programCounter + 1) & 0xFFFF) << 8) + $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter);
                $parentObj->programCounter = ($parentObj->programCounter + 2) & 0xFFFF;
                $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
                $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->programCounter >> 8);
                $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
                $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->programCounter & 0xFF);
                $parentObj->programCounter = $temp_pc;
                $parentObj->CPUTicks += 3;
            } else {
                $parentObj->programCounter = ($parentObj->programCounter + 2) & 0xFFFF;
            }
        };
        //CALL nn
        //#0xCD:
        $this->functionsArray[] = function ($parentObj) {
            $temp_pc = ($parentObj->memoryRead(($parentObj->programCounter + 1) & 0xFFFF) << 8) + $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter);
            $parentObj->programCounter = ($parentObj->programCounter + 2) & 0xFFFF;
            $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
            $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->programCounter >> 8);
            $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
            $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->programCounter & 0xFF);
            $parentObj->programCounter = $temp_pc;
        };
        //ADC A, n
        //#0xCE:
        $this->functionsArray[] = function ($parentObj) {
            $tempValue = $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter);
            $dirtySum = $parentObj->registerA + $tempValue + (($parentObj->FCarry) ? 1 : 0);
            $parentObj->FHalfCarry = (($parentObj->registerA & 0xF) + ($tempValue & 0xF) + (($parentObj->FCarry) ? 1 : 0) > 0xF);
            $parentObj->FCarry = ($dirtySum > 0xFF);
            $parentObj->registerA = $dirtySum & 0xFF;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = false;
            $parentObj->programCounter = ($parentObj->programCounter + 1) & 0xFFFF;
        };
        //RST 0x8
        //#0xCF:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
            $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->programCounter >> 8);
            $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
            $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->programCounter & 0xFF);
            $parentObj->programCounter = 0x8;
        };
        //RET !FC
        //#0xD0:
        $this->functionsArray[] = function ($parentObj) {
            if (!$parentObj->FCarry) {
                $parentObj->programCounter = ($parentObj->memoryRead(($parentObj->stackPointer + 1) & 0xFFFF) << 8) + $parentObj->memoryReader[$parentObj->stackPointer]($parentObj, $parentObj->stackPointer);
                $parentObj->stackPointer = ($parentObj->stackPointer + 2) & 0xFFFF;
                $parentObj->CPUTicks += 3;
            }
        };
        //POP DE
        //#0xD1:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerE = $parentObj->memoryReader[$parentObj->stackPointer]($parentObj, $parentObj->stackPointer);
            $parentObj->registerD = $parentObj->memoryRead(($parentObj->stackPointer + 1) & 0xFFFF);
            $parentObj->stackPointer = ($parentObj->stackPointer + 2) & 0xFFFF;
        };
        //JP !FC, nn
        //#0xD2:
        $this->functionsArray[] = function ($parentObj) {
            if (!$parentObj->FCarry) {
                $parentObj->programCounter = ($parentObj->memoryRead(($parentObj->programCounter + 1) & 0xFFFF) << 8) + $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter);
                ++$parentObj->CPUTicks;
            } else {
                $parentObj->programCounter = ($parentObj->programCounter + 2) & 0xFFFF;
            }
        };
        //0xD3 - Illegal
        //#0xD3:
        $this->functionsArray[] = function ($parentObj) {
            // @TODO
            // cout("Illegal op code 0xD3 called, pausing emulation.", 2);
            // pause();
        };
        //CALL !FC, nn
        //#0xD4:
        $this->functionsArray[] = function ($parentObj) {
            if (!$parentObj->FCarry) {
                $temp_pc = ($parentObj->memoryRead(($parentObj->programCounter + 1) & 0xFFFF) << 8) + $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter);
                $parentObj->programCounter = ($parentObj->programCounter + 2) & 0xFFFF;
                $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
                $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->programCounter >> 8);
                $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
                $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->programCounter & 0xFF);
                $parentObj->programCounter = $temp_pc;
                $parentObj->CPUTicks += 3;
            } else {
                $parentObj->programCounter = ($parentObj->programCounter + 2) & 0xFFFF;
            }
        };
        //PUSH DE
        //#0xD5:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
            $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->registerD);
            $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
            $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->registerE);
        };
        //SUB A, n
        //#0xD6:
        $this->functionsArray[] = function ($parentObj) {
            $temp_var = $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter);
            $dirtySum = $parentObj->registerA - $temp_var;
            $parentObj->FHalfCarry = ($parentObj->registerA & 0xF) < ($temp_var & 0xF);
            $parentObj->FCarry = ($dirtySum < 0);
            $parentObj->registerA = $parentObj->unsbtub($dirtySum);
            $parentObj->programCounter = ($parentObj->programCounter + 1) & 0xFFFF;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = true;
        };
        //RST 0x10
        //#0xD7:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
            $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->programCounter >> 8);
            $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
            $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->programCounter & 0xFF);
            $parentObj->programCounter = 0x10;
        };
        //RET FC
        //#0xD8:
        $this->functionsArray[] = function ($parentObj) {
            if ($parentObj->FCarry) {
                $parentObj->programCounter = ($parentObj->memoryRead(($parentObj->stackPointer + 1) & 0xFFFF) << 8) + $parentObj->memoryReader[$parentObj->stackPointer]($parentObj, $parentObj->stackPointer);
                $parentObj->stackPointer = ($parentObj->stackPointer + 2) & 0xFFFF;
                $parentObj->CPUTicks += 3;
            }
        };
        //RETI
        //#0xD9:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->programCounter = ($parentObj->memoryRead(($parentObj->stackPointer + 1) & 0xFFFF) << 8) + $parentObj->memoryReader[$parentObj->stackPointer]($parentObj, $parentObj->stackPointer);
            $parentObj->stackPointer = ($parentObj->stackPointer + 2) & 0xFFFF;
            //$parentObj->IME = true;
            $parentObj->untilEnable = 2;
        };
        //JP FC, nn
        //#0xDA:
        $this->functionsArray[] = function ($parentObj) {
            if ($parentObj->FCarry) {
                $parentObj->programCounter = ($parentObj->memoryRead(($parentObj->programCounter + 1) & 0xFFFF) << 8) + $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter);
                ++$parentObj->CPUTicks;
            } else {
                $parentObj->programCounter = ($parentObj->programCounter + 2) & 0xFFFF;
            }
        };
        //0xDB - Illegal
        //#0xDB:
        $this->functionsArray[] = function ($parentObj) {
            echo 'Illegal op code 0xDB called, pausing emulation.';
            exit();
        };
        //CALL FC, nn
        //#0xDC:
        $this->functionsArray[] = function ($parentObj) {
            if ($parentObj->FCarry) {
                $temp_pc = ($parentObj->memoryRead(($parentObj->programCounter + 1) & 0xFFFF) << 8) + $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter);
                $parentObj->programCounter = ($parentObj->programCounter + 2) & 0xFFFF;
                $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
                $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->programCounter >> 8);
                $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
                $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->programCounter & 0xFF);
                $parentObj->programCounter = $temp_pc;
                $parentObj->CPUTicks += 3;
            } else {
                $parentObj->programCounter = ($parentObj->programCounter + 2) & 0xFFFF;
            }
        };
        //0xDD - Illegal
        //#0xDD:
        $this->functionsArray[] = function ($parentObj) {
            echo 'Illegal op code 0xDD called, pausing emulation.';
            exit();
        };
        //SBC A, n
        //#0xDE:
        $this->functionsArray[] = function ($parentObj) {
            $temp_var = $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter);
            $dirtySum = $parentObj->registerA - $temp_var - (($parentObj->FCarry) ? 1 : 0);
            $parentObj->FHalfCarry = (($parentObj->registerA & 0xF) - ($temp_var & 0xF) - (($parentObj->FCarry) ? 1 : 0) < 0);
            $parentObj->FCarry = ($dirtySum < 0);
            $parentObj->registerA = $parentObj->unsbtub($dirtySum);
            $parentObj->programCounter = ($parentObj->programCounter + 1) & 0xFFFF;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FSubtract = true;
        };
        //RST 0x18
        //#0xDF:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
            $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->programCounter >> 8);
            $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
            $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->programCounter & 0xFF);
            $parentObj->programCounter = 0x18;
        };
        //LDH (n), A
        //#0xE0:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite(0xFF00 + $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter), $parentObj->registerA);
            $parentObj->programCounter = ($parentObj->programCounter + 1) & 0xFFFF;
        };
        //POP HL
        //#0xE1:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL = ($parentObj->memoryRead(($parentObj->stackPointer + 1) & 0xFFFF) << 8) + $parentObj->memoryReader[$parentObj->stackPointer]($parentObj, $parentObj->stackPointer);
            $parentObj->stackPointer = ($parentObj->stackPointer + 2) & 0xFFFF;
        };
        //LD (C), A
        //#0xE2:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite(0xFF00 + $parentObj->registerC, $parentObj->registerA);
        };
        //0xE3 - Illegal
        //#0xE3:
        $this->functionsArray[] = function ($parentObj) {
            echo 'Illegal op code 0xE3 called, pausing emulation.';
            exit();
        };
        //0xE4 - Illegal
        //#0xE4:
        $this->functionsArray[] = function ($parentObj) {
            echo 'Illegal op code 0xE4 called, pausing emulation.';
            exit();
        };
        //PUSH HL
        //#0xE5:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
            $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->registersHL >> 8);
            $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
            $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->registersHL & 0xFF);
        };
        //AND n
        //#0xE6:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA &= $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter);
            $parentObj->programCounter = ($parentObj->programCounter + 1) & 0xFFFF;
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = $parentObj->FCarry = false;
        };
        //RST 0x20
        //#0xE7:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
            $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->programCounter >> 8);
            $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
            $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->programCounter & 0xFF);
            $parentObj->programCounter = 0x20;
        };
        //ADD SP, n
        //#0xE8:
        $this->functionsArray[] = function ($parentObj) {
            $signedByte = $parentObj->usbtsb($parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter));
            $temp_value = $parentObj->nswtuw($parentObj->stackPointer + $signedByte);
            $parentObj->FCarry = ((($parentObj->stackPointer ^ $signedByte ^ $temp_value) & 0x100) == 0x100);
            $parentObj->FHalfCarry = ((($parentObj->stackPointer ^ $signedByte ^ $temp_value) & 0x10) == 0x10);
            $parentObj->stackPointer = $temp_value;
            $parentObj->programCounter = ($parentObj->programCounter + 1) & 0xFFFF;
            $parentObj->FZero = $parentObj->FSubtract = false;
        };
        //JP, (HL)
        //#0xE9:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->programCounter = $parentObj->registersHL;
        };
        //LD n, A
        //#0xEA:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite(($parentObj->memoryRead(($parentObj->programCounter + 1) & 0xFFFF) << 8) + $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter), $parentObj->registerA);
            $parentObj->programCounter = ($parentObj->programCounter + 2) & 0xFFFF;
        };
        //0xEB - Illegal
        //#0xEB:
        $this->functionsArray[] = function ($parentObj) {
            echo 'Illegal op code 0xEB called, pausing emulation.';
            exit();
        };
        //0xEC - Illegal
        //#0xEC:
        $this->functionsArray[] = function ($parentObj) {
            echo 'Illegal op code 0xEC called, pausing emulation.';
            exit();
        };
        //0xED - Illegal
        //#0xED:
        $this->functionsArray[] = function ($parentObj) {
            echo 'Illegal op code 0xED called, pausing emulation.';
            exit();
        };
        //XOR n
        //#0xEE:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA ^= $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter);
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->programCounter = ($parentObj->programCounter + 1) & 0xFFFF;
            $parentObj->FSubtract = $parentObj->FHalfCarry = $parentObj->FCarry = false;
        };
        //RST 0x28
        //#0xEF:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
            $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->programCounter >> 8);
            $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
            $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->programCounter & 0xFF);
            $parentObj->programCounter = 0x28;
        };
        //LDH A, (n)
        //#0xF0:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA = $parentObj->memoryRead(0xFF00 + $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter));
            $parentObj->programCounter = ($parentObj->programCounter + 1) & 0xFFFF;
        };
        //POP AF
        //#0xF1:
        $this->functionsArray[] = function ($parentObj) {
            $temp_var = $parentObj->memoryReader[$parentObj->stackPointer]($parentObj, $parentObj->stackPointer);
            $parentObj->FZero = (($temp_var & 0x80) == 0x80);
            $parentObj->FSubtract = (($temp_var & 0x40) == 0x40);
            $parentObj->FHalfCarry = (($temp_var & 0x20) == 0x20);
            $parentObj->FCarry = (($temp_var & 0x10) == 0x10);
            $parentObj->registerA = $parentObj->memoryRead(($parentObj->stackPointer + 1) & 0xFFFF);
            $parentObj->stackPointer = ($parentObj->stackPointer + 2) & 0xFFFF;
        };
        //LD A, (C)
        //#0xF2:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA = $parentObj->memoryRead(0xFF00 + $parentObj->registerC);
        };
        //DI
        //#0xF3:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->IME = false;
            $parentObj->untilEnable = 0;
        };
        //0xF4 - Illegal
        //#0xF4:
        $this->functionsArray[] = function ($parentObj) {
            // @TODO
            // cout("Illegal op code 0xF4 called, pausing emulation.", 2);
            // pause();
        };
        //PUSH AF
        //#0xF5:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
            $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->registerA);
            $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
            $parentObj->memoryWrite($parentObj->stackPointer, (($parentObj->FZero) ? 0x80 : 0) + (($parentObj->FSubtract) ? 0x40 : 0) + (($parentObj->FHalfCarry) ? 0x20 : 0) + (($parentObj->FCarry) ? 0x10 : 0));
        };
        //OR n
        //#0xF6:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA |= $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter);
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->programCounter = ($parentObj->programCounter + 1) & 0xFFFF;
            $parentObj->FSubtract = $parentObj->FCarry = $parentObj->FHalfCarry = false;
        };
        //RST 0x30
        //#0xF7:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
            $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->programCounter >> 8);
            $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
            $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->programCounter & 0xFF);
            $parentObj->programCounter = 0x30;
        };
        //LDHL SP, n
        //#0xF8:
        $this->functionsArray[] = function ($parentObj) {
            $signedByte = $parentObj->usbtsb($parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter));
            $parentObj->registersHL = $parentObj->nswtuw($parentObj->stackPointer + $signedByte);
            $parentObj->FCarry = ((($parentObj->stackPointer ^ $signedByte ^ $parentObj->registersHL) & 0x100) == 0x100);
            $parentObj->FHalfCarry = ((($parentObj->stackPointer ^ $signedByte ^ $parentObj->registersHL) & 0x10) == 0x10);
            $parentObj->programCounter = ($parentObj->programCounter + 1) & 0xFFFF;
            $parentObj->FZero = $parentObj->FSubtract = false;
        };
        //LD SP, HL
        //#0xF9:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->stackPointer = $parentObj->registersHL;
        };
        //LD A, (nn)
        //#0xFA:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA = $parentObj->memoryRead(($parentObj->memoryRead(($parentObj->programCounter + 1) & 0xFFFF) << 8) + $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter));
            $parentObj->programCounter = ($parentObj->programCounter + 2) & 0xFFFF;
        };
        //EI
        //#0xFB:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->untilEnable = 2;
        };
        //0xFC - Illegal
        //#0xFC:
        $this->functionsArray[] = function ($parentObj) {
            echo 'Illegal op code 0xFC called, pausing emulation.';
            exit();
        };
        //0xFD - Illegal
        //#0xFD:
        $this->functionsArray[] = function ($parentObj) {
            echo 'Illegal op code 0xFD called, pausing emulation.';
            exit();
        };
        //CP n
        //#0xFE:
        $this->functionsArray[] = function ($parentObj) {
            $dirtySum = $parentObj->registerA - $parentObj->memoryReader[$parentObj->programCounter]($parentObj, $parentObj->programCounter);
            $parentObj->FHalfCarry = ($parentObj->unsbtub($dirtySum) & 0xF) > ($parentObj->registerA & 0xF);
            $parentObj->FCarry = ($dirtySum < 0);
            $parentObj->FZero = ($dirtySum == 0);
            $parentObj->programCounter = ($parentObj->programCounter + 1) & 0xFFFF;
            $parentObj->FSubtract = true;
        };
        //RST 0x38
        //#0xFF:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
            $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->programCounter >> 8);
            $parentObj->stackPointer = $parentObj->unswtuw($parentObj->stackPointer - 1);
            $parentObj->memoryWrite($parentObj->stackPointer, $parentObj->programCounter & 0xFF);
            $parentObj->programCounter = 0x38;
        };
    }

    public function get()
    {
        return $this->functionsArray;
    }
}
