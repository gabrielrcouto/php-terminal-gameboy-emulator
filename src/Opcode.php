<?php

namespace GameBoy;

use Exception;

class Opcode
{
    /**
     * Run the given opcode.
     *
     * @param Core $core
     * @param int $address
     * @return mixed
     */
    public static function run(Core $core, $address)
    {
        $function = 'opcode'.$address;
        return Opcode::$function($core);
    }

    /**
     * Opcode #0x00.
     *
     * NOP
     *
     * @param Core $core
     */
    private static function opcode0(Core $core)
    {
        // Do Nothing...
    }

    /**
     * Opcode #0x01.
     *
     * LD BC, nn
     *
     * @param Core $core
     */
    private static function opcode1(Core $core)
    {
        $core->registerC = $core->memoryReader[$core->programCounter]($core, $core->programCounter);
        $core->registerB = $core->memoryRead(($core->programCounter + 1) & 0xFFFF);
        $core->programCounter = ($core->programCounter + 2) & 0xFFFF;
    }

    /**
     * Opcode #0x02.
     *
     * LD (BC), A
     *
     * @param Core $core
     */
    private static function opcode2(Core $core)
    {
        $core->memoryWrite(($core->registerB << 8) + $core->registerC, $core->registerA);
    }

    /**
     * Opcode #0x03.
     *
     * INC BC
     *
     * @param Core $core
     */
    private static function opcode3(Core $core)
    {
        $temp_var = ((($core->registerB << 8) + $core->registerC) + 1);
        $core->registerB = (($temp_var >> 8) & 0xFF);
        $core->registerC = ($temp_var & 0xFF);
    }

    /**
     * Opcode #0x04.
     *
     * INC B
     *
     * @param Core $core
     */
    private static function opcode4(Core $core)
    {
        $core->registerB = (($core->registerB + 1) & 0xFF);
        $core->FZero = ($core->registerB == 0);
        $core->FHalfCarry = (($core->registerB & 0xF) == 0);
        $core->FSubtract = false;
    }

    /**
     * Opcode #0x05.
     *
     * DEC B
     *
     * @param Core $core
     */
    private static function opcode5(Core $core)
    {
        $core->registerB = $core->unsbtub($core->registerB - 1);
        $core->FZero = ($core->registerB == 0);
        $core->FHalfCarry = (($core->registerB & 0xF) == 0xF);
        $core->FSubtract = true;
    }

    /**
     * Opcode #0x06.
     *
     * LD B, n
     *
     * @param Core $core
     */
    private static function opcode6(Core $core)
    {
        $core->registerB = $core->memoryReader[$core->programCounter]($core, $core->programCounter);
        $core->programCounter = ($core->programCounter + 1) & 0xFFFF;
    }

    /**
     * Opcode #0x07.
     *
     * RCLA
     *
     * @param Core $core
     */
    private static function opcode7(Core $core)
    {
        $core->FCarry = (($core->registerA & 0x80) == 0x80);
        $core->registerA = (($core->registerA << 1) & 0xFF) | ($core->registerA >> 7);
        $core->FZero = $core->FSubtract = $core->FHalfCarry = false;
    }

    /**
     * Opcode #0x08
     *
     * LD (nn), SP
     *
     * @param Core $core
     */
    private static function opcode8(Core $core)
    {
        $temp_var = ($core->memoryRead(($core->programCounter + 1) & 0xFFFF) << 8) + $core->memoryReader[$core->programCounter]($core, $core->programCounter);
        $core->memoryWrite($temp_var, $core->stackPointer & 0xFF);
        $core->memoryWrite(($temp_var + 1) & 0xFFFF, $core->stackPointer >> 8);
        $core->programCounter = ($core->programCounter + 2) & 0xFFFF;
    }

    /**
     * Opcode #0x09.
     *
     * ADD HL, BC
     *
     * @param Core $core
     */
    private static function opcode9(Core $core)
    {
        $n2 = ($core->registerB << 8) + $core->registerC;
        $dirtySum = $core->registersHL + $n2;
        $core->FHalfCarry = (($core->registersHL & 0xFFF) + ($n2 & 0xFFF) > 0xFFF);
        $core->FCarry = ($dirtySum > 0xFFFF);
        $core->registersHL = ($dirtySum & 0xFFFF);
        $core->FSubtract = false;
    }

    /**
     * Opcode #0x0A.
     *
     * LD A, (BC)
     *
     * @param Core $core
     */
    private static function opcode10(Core $core)
    {
        $core->registerA = $core->memoryRead(($core->registerB << 8) + $core->registerC);
    }

    /**
     * Opcode #0x0B.
     *
     * DEC BC
     *
     * @param Core $core
     */
    private static function opcode11(Core $core)
    {
        $temp_var = $core->unswtuw((($core->registerB << 8) + $core->registerC) - 1);
        $core->registerB = ($temp_var >> 8);
        $core->registerC = ($temp_var & 0xFF);
    }

    /**
     * Opcode #0x0C
     *
     * INC C
     *
     * @param Core $core
     */
    private static function opcode12(Core $core)
    {
        $core->registerC = (($core->registerC + 1) & 0xFF);
        $core->FZero = ($core->registerC == 0);
        $core->FHalfCarry = (($core->registerC & 0xF) == 0);
        $core->FSubtract = false;
    }

    /**
     * Opcode #0x0D.
     *
     * DEC C
     *
     * @param Core $core
     */
    private static function opcode13(Core $core)
    {
        $core->registerC = $core->unsbtub($core->registerC - 1);
        $core->FZero = ($core->registerC == 0);
        $core->FHalfCarry = (($core->registerC & 0xF) == 0xF);
        $core->FSubtract = true;
    }

    /**
     * Opcode #0x0E.
     *
     * LD C, n
     *
     * @param Core $core
     */
    private static function opcode14(Core $core)
    {
        $core->registerC = $core->memoryReader[$core->programCounter]($core, $core->programCounter);
        $core->programCounter = ($core->programCounter + 1) & 0xFFFF;
    }

    /**
     * Opcode #0x0F.
     *
     * RRCA
     *
     * @param Core $core
     */
    private static function opcode15(Core $core)
    {
        $core->FCarry = (($core->registerA & 1) == 1);
        $core->registerA = ($core->registerA >> 1) + (($core->registerA & 1) << 7);
        $core->FZero = $core->FSubtract = $core->FHalfCarry = false;
    }

    /**
     * Opcode #0x10.
     *
     * STOP
     *
     * @param Core $core
     */
    private static function opcode16(Core $core)
    {
        if ($core->cGBC) {
            /*TODO: Emulate the speed switch delay:
            Delay Amount:
            16 ms when going to double-speed.
            32 ms when going to single-speed.
            Also, bits 4 and 5 of 0xFF00 should read as set (1), while the switch is in process.
             */

            // Speed change requested.
            if (($core->memory[0xFF4D] & 0x01) == 0x01) {
                //Go back to single speed mode.
                if (($core->memory[0xFF4D] & 0x80) == 0x80) {
                    // cout("Going into single clock speed mode.", 0);
                    $core->multiplier = 1; //TODO: Move this into the delay done code.
                    $core->memory[0xFF4D] &= 0x7F; //Clear the double speed mode flag.
                    //Go to double speed mode.
                } else {
                    // cout("Going into double clock speed mode.", 0);
                    $core->multiplier = 2; //TODO: Move this into the delay done code.
                    $core->memory[0xFF4D] |= 0x80; //Set the double speed mode flag.
                }
                $core->memory[0xFF4D] &= 0xFE; //Reset the request bit.
            }
        }
    }

    /**
     * Opcode #0x11.
     *
     * LD DE, nn
     *
     * @param Core $core
     */
    private static function opcode17(Core $core)
    {
        $core->registerE = $core->memoryReader[$core->programCounter]($core, $core->programCounter);
        $core->registerD = $core->memoryRead(($core->programCounter + 1) & 0xFFFF);
        $core->programCounter = ($core->programCounter + 2) & 0xFFFF;
    }

    /**
     * Opcode #0x12.
     *
     * LD (DE), A
     *
     * @param Core $core
     */
    private static function opcode18(Core $core)
    {
        $core->memoryWrite(($core->registerD << 8) + $core->registerE, $core->registerA);
    }

    /**
     * Opcode #0x13.
     *
     * INC DE
     *
     * @param Core $core
     */
    private static function opcode19(Core $core)
    {
        $temp_var = ((($core->registerD << 8) + $core->registerE) + 1);
        $core->registerD = (($temp_var >> 8) & 0xFF);
        $core->registerE = ($temp_var & 0xFF);
    }

    /**
     * Opcode #0x14.
     *
     * INC D
     *
     * @param Core $core
     */
    private static function opcode20(Core $core)
    {
        $core->registerD = (($core->registerD + 1) & 0xFF);
        $core->FZero = ($core->registerD == 0);
        $core->FHalfCarry = (($core->registerD & 0xF) == 0);
        $core->FSubtract = false;
    }

    /**
     * Opcode #0x15.
     *
     * DEC D
     *
     * @param Core $core
     */
    private static function opcode21(Core $core)
    {
        $core->registerD = $core->unsbtub($core->registerD - 1);
        $core->FZero = ($core->registerD == 0);
        $core->FHalfCarry = (($core->registerD & 0xF) == 0xF);
        $core->FSubtract = true;
    }

    /**
     * Opcode #0x16.
     *
     * LD D, n
     *
     * @param Core $core
     */
    private static function opcode22(Core $core)
    {
        $core->registerD = $core->memoryReader[$core->programCounter]($core, $core->programCounter);
        $core->programCounter = ($core->programCounter + 1) & 0xFFFF;
    }

    /**
     * Opcode #0x17.
     *
     * RLA
     *
     * @param Core $core
     */
    private static function opcode23(Core $core)
    {
        $carry_flag = ($core->FCarry) ? 1 : 0;
        $core->FCarry = (($core->registerA & 0x80) == 0x80);
        $core->registerA = (($core->registerA << 1) & 0xFF) | $carry_flag;
        $core->FZero = $core->FSubtract = $core->FHalfCarry = false;
    }

    /**
     * Opcode #0x18.
     *
     * JR n
     *
     * @param Core $core
     */
    private static function opcode24(Core $core)
    {
        $core->programCounter = $core->nswtuw($core->programCounter + $core->usbtsb($core->memoryReader[$core->programCounter]($core, $core->programCounter)) + 1);
    }

    /**
     * Opcode #0x19.
     *
     * ADD HL, DE
     *
     * @param Core $core
     */
    private static function opcode25(Core $core)
    {
        $n2 = ($core->registerD << 8) + $core->registerE;
        $dirtySum = $core->registersHL + $n2;
        $core->FHalfCarry = (($core->registersHL & 0xFFF) + ($n2 & 0xFFF) > 0xFFF);
        $core->FCarry = ($dirtySum > 0xFFFF);
        $core->registersHL = ($dirtySum & 0xFFFF);
        $core->FSubtract = false;
    }

    /**
     * Opcode #0x1A.
     *
     * LD A, (DE)
     *
     * @param Core $core
     */
    private static function opcode26(Core $core)
    {
        $core->registerA = $core->memoryRead(($core->registerD << 8) + $core->registerE);
    }

    /**
     * Opcode #0x1B.
     *
     * DEC DE
     *
     * @param Core $core
     */
    private static function opcode27(Core $core)
    {
        $temp_var = $core->unswtuw((($core->registerD << 8) + $core->registerE) - 1);
        $core->registerD = ($temp_var >> 8);
        $core->registerE = ($temp_var & 0xFF);
    }

    /**
     * Opcode #0x1C.
     *
     * INC E
     *
     * @param Core $core
     */
    private static function opcode28(Core $core)
    {
        $core->registerE = (($core->registerE + 1) & 0xFF);
        $core->FZero = ($core->registerE == 0);
        $core->FHalfCarry = (($core->registerE & 0xF) == 0);
        $core->FSubtract = false;
    }

    /**
     * Opcode #0x1D.
     *
     * DEC E
     *
     * @param Core $core
     */
    private static function opcode29(Core $core)
    {
        $core->registerE = $core->unsbtub($core->registerE - 1);
        $core->FZero = ($core->registerE == 0);
        $core->FHalfCarry = (($core->registerE & 0xF) == 0xF);
        $core->FSubtract = true;
    }

    /**
     * Opcode #0x1E.
     *
     * LD E, n
     *
     * @param Core $core
     */
    private static function opcode30(Core $core)
    {
        $core->registerE = $core->memoryReader[$core->programCounter]($core, $core->programCounter);
        $core->programCounter = ($core->programCounter + 1) & 0xFFFF;
    }

    /**
     * Opcode #0x1F.
     *
     * RRA
     *
     * @param Core $core
     */
    private static function opcode31(Core $core)
    {
        $carry_flag = ($core->FCarry) ? 0x80 : 0;
        $core->FCarry = (($core->registerA & 1) == 1);
        $core->registerA = ($core->registerA >> 1) + $carry_flag;
        $core->FZero = $core->FSubtract = $core->FHalfCarry = false;
    }

    /**
     * Opcode #0x20.
     *
     * JR cc, n
     *
     * @param Core $core
     */
    private static function opcode32(Core $core)
    {
        if (!$core->FZero) {
            $core->programCounter = $core->nswtuw($core->programCounter + $core->usbtsb($core->memoryReader[$core->programCounter]($core, $core->programCounter)) + 1);
            ++$core->CPUTicks;
        } else {
            $core->programCounter = ($core->programCounter + 1) & 0xFFFF;
        }
    }

    /**
     * Opcode #0x21.
     *
     * LD HL, nn
     *
     * @param Core $core
     */
    private static function opcode33(Core $core)
    {
        $core->registersHL = ($core->memoryRead(($core->programCounter + 1) & 0xFFFF) << 8) + $core->memoryReader[$core->programCounter]($core, $core->programCounter);
        $core->programCounter = ($core->programCounter + 2) & 0xFFFF;
    }

    /**
     * Opcode #0x22.
     *
     * LDI (HL), A
     *
     * @param Core $core
     */
    private static function opcode34(Core $core)
    {
        $core->memoryWrite($core->registersHL, $core->registerA);
        $core->registersHL = (($core->registersHL + 1) & 0xFFFF);
    }

    /**
     * Opcode #0x23.
     *
     * INC HL
     *
     * @param Core $core
     */
    private static function opcode35(Core $core)
    {
        $core->registersHL = (($core->registersHL + 1) & 0xFFFF);
    }

    /**
     * Opcode #0x24.
     *
     * INC H
     *
     * @param Core $core
     */
    private static function opcode36(Core $core)
    {
        $H = ((($core->registersHL >> 8) + 1) & 0xFF);
        $core->FZero = ($H == 0);
        $core->FHalfCarry = (($H & 0xF) == 0);
        $core->FSubtract = false;
        $core->registersHL = ($H << 8) + ($core->registersHL & 0xFF);
    }

    /**
     * Opcode #0x25.
     *
     * DEC H
     *
     * @param Core $core
     */
    private static function opcode37(Core $core)
    {
        $H = $core->unsbtub(($core->registersHL >> 8) - 1);
        $core->FZero = ($H == 0);
        $core->FHalfCarry = (($H & 0xF) == 0xF);
        $core->FSubtract = true;
        $core->registersHL = ($H << 8) + ($core->registersHL & 0xFF);
    }

    /**
     * Opcode #0x26.
     *
     * LD H, n
     *
     * @param Core $core
     */
    private static function opcode38(Core $core)
    {
        $core->registersHL = ($core->memoryReader[$core->programCounter]($core, $core->programCounter) << 8) + ($core->registersHL & 0xFF);
        $core->programCounter = ($core->programCounter + 1) & 0xFFFF;
    }

    /**
     * Opcode #0x27.
     *
     * DAA
     *
     * @param Core $core
     */
    private static function opcode39(Core $core)
    {
        $temp_var = $core->registerA;
        if ($core->FCarry) {
            $temp_var |= 0x100;
        }
        if ($core->FHalfCarry) {
            $temp_var |= 0x200;
        }
        if ($core->FSubtract) {
            $temp_var |= 0x400;
        }
        $core->registerA = ($temp_var = $core->DAATable[$temp_var]) >> 8;
        $core->FZero = (($temp_var & 0x80) == 0x80);
        $core->FSubtract = (($temp_var & 0x40) == 0x40);
        $core->FHalfCarry = (($temp_var & 0x20) == 0x20);
        $core->FCarry = (($temp_var & 0x10) == 0x10);
    }

    /**
     * Opcode #0x28.
     *
     * JR cc, n
     *
     * @param Core $core
     */
    private static function opcode40(Core $core)
    {
        if ($core->FZero) {
            $core->programCounter = $core->nswtuw($core->programCounter + $core->usbtsb($core->memoryReader[$core->programCounter]($core, $core->programCounter)) + 1);
            ++$core->CPUTicks;
        } else {
            $core->programCounter = ($core->programCounter + 1) & 0xFFFF;
        }
    }

    /**
     * Opcode #0x29.
     *
     * ADD HL, HL
     *
     * @param Core $core
     */
    private static function opcode41(Core $core)
    {
        ;
        $core->FHalfCarry = (($core->registersHL & 0xFFF) > 0x7FF);
        $core->FCarry = ($core->registersHL > 0x7FFF);
        $core->registersHL = ((2 * $core->registersHL) & 0xFFFF);
        $core->FSubtract = false;
    }

    /**
     * Opcode #0x2A.
     *
     * LDI A, (HL)
     *
     * @param Core $core
     */
    private static function opcode42(Core $core)
    {
        $core->registerA = $core->memoryReader[$core->registersHL]($core, $core->registersHL);
        $core->registersHL = (($core->registersHL + 1) & 0xFFFF);
    }

    /**
     * Opcode #0x2B.
     *
     * DEC HL
     *
     * @param Core $core
     */
    private static function opcode43(Core $core)
    {
        $core->registersHL = $core->unswtuw($core->registersHL - 1);
    }

    /**
     * Opcode #0x2C.
     *
     * INC L
     *
     * @param Core $core
     */
    private static function opcode44(Core $core)
    {
        $L = (($core->registersHL + 1) & 0xFF);
        $core->FZero = ($L == 0);
        $core->FHalfCarry = (($L & 0xF) == 0);
        $core->FSubtract = false;
        $core->registersHL = ($core->registersHL & 0xFF00) + $L;
    }

    /**
     * Opcode #0x2D.
     *
     * DEC L
     *
     * @param Core $core
     */
    private static function opcode45(Core $core)
    {
        $L = $core->unsbtub(($core->registersHL & 0xFF) - 1);
        $core->FZero = ($L == 0);
        $core->FHalfCarry = (($L & 0xF) == 0xF);
        $core->FSubtract = true;
        $core->registersHL = ($core->registersHL & 0xFF00) + $L;
    }

    /**
     * Opcode #0x2E.
     *
     * LD L, n
     *
     * @param Core $core
     */
    private static function opcode46(Core $core)
    {
        $core->registersHL = ($core->registersHL & 0xFF00) + $core->memoryReader[$core->programCounter]($core, $core->programCounter);
        $core->programCounter = ($core->programCounter + 1) & 0xFFFF;
    }

    /**
     * Opcode #0x2F.
     *
     * CPL
     *
     * @param Core $core
     */
    private static function opcode47(Core $core)
    {
        $core->registerA ^= 0xFF;
        $core->FSubtract = $core->FHalfCarry = true;
    }

    /**
     * Opcode #0x30.
     *
     * JR cc, n
     *
     * @param Core $core
     */
    private static function opcode48(Core $core)
    {
        if (!$core->FCarry) {
            $core->programCounter = $core->nswtuw($core->programCounter + $core->usbtsb($core->memoryReader[$core->programCounter]($core, $core->programCounter)) + 1);
            ++$core->CPUTicks;
        } else {
            $core->programCounter = ($core->programCounter + 1) & 0xFFFF;
        }
    }

    /**
     * Opcode #0x31.
     *
     * LD SP, nn
     *
     * @param Core $core
     */
    private static function opcode49(Core $core)
    {
        $core->stackPointer = ($core->memoryRead(($core->programCounter + 1) & 0xFFFF) << 8) + $core->memoryReader[$core->programCounter]($core, $core->programCounter);
        $core->programCounter = ($core->programCounter + 2) & 0xFFFF;
    }

    /**
     * Opcode #0x32.
     *
     * LDD (HL), A
     *
     * @param Core $core
     */
    private static function opcode50(Core $core)
    {
        $core->memoryWrite($core->registersHL, $core->registerA);
        $core->registersHL = $core->unswtuw($core->registersHL - 1);
    }

    /**
     * Opcode #0x33.
     *
     * INC SP
     *
     * @param Core $core
     */
    private static function opcode51(Core $core)
    {
        $core->stackPointer = ($core->stackPointer + 1) & 0xFFFF;
    }

    /**
     * Opcode #0x34.
     *
     * INC (HL)
     *
     * @param Core $core
     */
    private static function opcode52(Core $core)
    {
        $temp_var = (($core->memoryReader[$core->registersHL]($core, $core->registersHL) + 1) & 0xFF);
        $core->FZero = ($temp_var == 0);
        $core->FHalfCarry = (($temp_var & 0xF) == 0);
        $core->FSubtract = false;
        $core->memoryWrite($core->registersHL, $temp_var);
    }

    /**
     * Opcode #0x35.
     *
     * DEC (HL)
     *
     * @param Core $core
     */
    private static function opcode53(Core $core)
    {
        $temp_var = $core->unsbtub($core->memoryReader[$core->registersHL]($core, $core->registersHL) - 1);
        $core->FZero = ($temp_var == 0);
        $core->FHalfCarry = (($temp_var & 0xF) == 0xF);
        $core->FSubtract = true;
        $core->memoryWrite($core->registersHL, $temp_var);
    }

    /**
     * Opcode #0x36.
     *
     * LD (HL), n
     *
     * @param Core $core
     */
    private static function opcode54(Core $core)
    {
        $core->memoryWrite($core->registersHL, $core->memoryReader[$core->programCounter]($core, $core->programCounter));
        $core->programCounter = ($core->programCounter + 1) & 0xFFFF;
    }

    /**
     * Opcode #0x37.
     *
     * SCF
     *
     * @param Core $core
     */
    private static function opcode55(Core $core)
    {
        $core->FCarry = true;
        $core->FSubtract = $core->FHalfCarry = false;
    }

    /**
     * Opcode #0x38.
     *
     * JR cc, n
     *
     * @param Core $core
     */
    private static function opcode56(Core $core)
    {
        if ($core->FCarry) {
            $core->programCounter = $core->nswtuw($core->programCounter + $core->usbtsb($core->memoryReader[$core->programCounter]($core, $core->programCounter)) + 1);
            ++$core->CPUTicks;
        } else {
            $core->programCounter = ($core->programCounter + 1) & 0xFFFF;
        }
    }

    /**
     * Opcode #0x39.
     *
     * ADD HL, SP
     *
     * @param Core $core
     */
    private static function opcode57(Core $core)
    {
        $dirtySum = $core->registersHL + $core->stackPointer;
        $core->FHalfCarry = (($core->registersHL & 0xFFF) + ($core->stackPointer & 0xFFF) > 0xFFF);
        $core->FCarry = ($dirtySum > 0xFFFF);
        $core->registersHL = ($dirtySum & 0xFFFF);
        $core->FSubtract = false;
    }

    /**
     * Opcode #0x3A.
     *
     *  LDD A, (HL)
     *
     * @param Core $core
     */
    private static function opcode58(Core $core)
    {
        $core->registerA = $core->memoryReader[$core->registersHL]($core, $core->registersHL);
        $core->registersHL = $core->unswtuw($core->registersHL - 1);
    }

    /**
     * Opcode #0x3B.
     *
     * DEC SP
     *
     * @param Core $core
     */
    private static function opcode59(Core $core)
    {
        $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
    }

    /**
     * Opcode #0x3C.
     *
     * INC A
     *
     * @param Core $core
     */
    private static function opcode60(Core $core)
    {
        $core->registerA = (($core->registerA + 1) & 0xFF);
        $core->FZero = ($core->registerA == 0);
        $core->FHalfCarry = (($core->registerA & 0xF) == 0);
        $core->FSubtract = false;
    }

    /**
     * Opcode #0x3D.
     *
     * DEC A
     *
     * @param Core $core
     */
    private static function opcode61(Core $core)
    {
        $core->registerA = $core->unsbtub($core->registerA - 1);
        $core->FZero = ($core->registerA == 0);
        $core->FHalfCarry = (($core->registerA & 0xF) == 0xF);
        $core->FSubtract = true;
    }

    /**
     * Opcode #0x3E.
     *
     * LD A, n
     *
     * @param Core $core
     */
    private static function opcode62(Core $core)
    {
        $core->registerA = $core->memoryReader[$core->programCounter]($core, $core->programCounter);
        $core->programCounter = ($core->programCounter + 1) & 0xFFFF;
    }

    /**
     * Opcode #0x3F.
     *
     * CCF
     *
     * @param Core $core
     */
    private static function opcode63(Core $core)
    {
        $core->FCarry = !$core->FCarry;
        $core->FSubtract = $core->FHalfCarry = false;
    }

    /**
     * Opcode #0x40.
     *
     * LD B, B
     *
     * @param Core $core
     */
    private static function opcode64(Core $core)
    {
        //Do nothing...
    }

    /**
     * Opcode #0x41.
     *
     * LD B, C
     *
     * @param Core $core
     */
    private static function opcode65(Core $core)
    {
        $core->registerB = $core->registerC;
    }

    /**
     * Opcode #0x42.
     *
     * LD B, D
     *
     * @param Core $core
     */
    private static function opcode66(Core $core)
    {
        $core->registerB = $core->registerD;
    }

    /**
     * Opcode #0x43.
     *
     * LD B, E
     *
     * @param Core $core
     */
    private static function opcode67(Core $core)
    {
        $core->registerB = $core->registerE;
    }

    /**
     * Opcode #0x44.
     *
     * LD B, H
     *
     * @param Core $core
     */
    private static function opcode68(Core $core)
    {
        $core->registerB = ($core->registersHL >> 8);
    }

    /**
     * Opcode #0x45.
     *
     * LD B, L
     *
     * @param Core $core
     */
    private static function opcode69(Core $core)
    {
        $core->registerB = ($core->registersHL & 0xFF);
    }

    /**
     * Opcode #0x46.
     *
     * LD B, (HL)
     *
     * @param Core $core
     */
    private static function opcode70(Core $core)
    {
        $core->registerB = $core->memoryReader[$core->registersHL]($core, $core->registersHL);
    }

    /**
     * Opcode #0x47.
     *
     * LD B, A
     *
     * @param Core $core
     */
    private static function opcode71(Core $core)
    {
        $core->registerB = $core->registerA;
    }

    /**
     * Opcode #0x48.
     *
     * LD C, B
     *
     * @param Core $core
     */
    private static function opcode72(Core $core)
    {
        $core->registerC = $core->registerB;
    }

    /**
     * Opcode #0x49.
     *
     * LD C, C
     *
     * @param Core $core
     */
    private static function opcode73(Core $core)
    {
        //Do nothing...
    }

    /**
     * Opcode #0x4A.
     *
     * LD C, D
     *
     * @param Core $core
     */
    private static function opcode74(Core $core)
    {
        $core->registerC = $core->registerD;
    }

    /**
     * Opcode #0x4B.
     *
     * LD C, E
     *
     * @param Core $core
     */
    private static function opcode75(Core $core)
    {
        $core->registerC = $core->registerE;
    }

    /**
     * Opcode #0x4C.
     *
     * LD C, H
     *
     * @param Core $core
     */
    private static function opcode76(Core $core)
    {
        $core->registerC = ($core->registersHL >> 8);
    }

    /**
     * Opcode #0x4D.
     *
     * LD C, L
     *
     * @param Core $core
     */
    private static function opcode77(Core $core)
    {
        $core->registerC = ($core->registersHL & 0xFF);
    }

    /**
     * Opcode #0x4E.
     *
     * LD C, (HL)
     *
     * @param Core $core
     */
    private static function opcode78(Core $core)
    {
        $core->registerC = $core->memoryReader[$core->registersHL]($core, $core->registersHL);
    }

    /**
     * Opcode #0x4F.
     *
     * LD C, A
     *
     * @param Core $core
     */
    private static function opcode79(Core $core)
    {
        $core->registerC = $core->registerA;
    }

    /**
     * Opcode #0x50.
     *
     * LD D, B
     *
     * @param Core $core
     */
    private static function opcode80(Core $core)
    {
        $core->registerD = $core->registerB;
    }

    /**
     * Opcode #0x51.
     *
     * LD D, C
     *
     * @param Core $core
     */
    private static function opcode81(Core $core)
    {
        $core->registerD = $core->registerC;
    }

    /**
     * Opcode #0x52.
     *
     * LD D, D
     *
     * @param Core $core
     */
    private static function opcode82(Core $core)
    {
        //Do nothing...
    }

    /**
     * Opcode #0x53.
     *
     * LD D, E
     *
     * @param Core $core
     */
    private static function opcode83(Core $core)
    {
        $core->registerD = $core->registerE;
    }

    /**
     * Opcode #0x54.
     *
     * LD D, H
     *
     * @param Core $core
     */
    private static function opcode84(Core $core)
    {
        $core->registerD = ($core->registersHL >> 8);
    }

    /**
     * Opcode #0x55.
     *
     * LD D, L
     *
     * @param Core $core
     */
    private static function opcode85(Core $core)
    {
        $core->registerD = ($core->registersHL & 0xFF);
    }

    /**
     * Opcode #0x56.
     *
     * LD D, (HL)
     *
     * @param Core $core
     */
    private static function opcode86(Core $core)
    {
        $core->registerD = $core->memoryReader[$core->registersHL]($core, $core->registersHL);
    }

    /**
     * Opcode #0x57.
     *
     * LD D, A
     *
     * @param Core $core
     */
    private static function opcode87(Core $core)
    {
        $core->registerD = $core->registerA;
    }

    /**
     * Opcode #0x58.
     *
     * LD E, B
     *
     * @param Core $core
     */
    private static function opcode88(Core $core)
    {
        $core->registerE = $core->registerB;
    }

    /**
     * Opcode #0x59.
     *
     * LD E, C
     *
     * @param Core $core
     */
    private static function opcode89(Core $core)
    {
        $core->registerE = $core->registerC;
    }

    /**
     * Opcode #0x5A.
     *
     * LD E, D
     *
     * @param Core $core
     */
    private static function opcode90(Core $core)
    {
        $core->registerE = $core->registerD;
    }

    /**
     * Opcode #0x5B.
     *
     * LD E, E
     *
     * @param Core $core
     */
    private static function opcode91(Core $core)
    {
        //Do nothing...
    }

    /**
     * Opcode #0x5C.
     *
     * LD E, H
     *
     * @param Core $core
     */
    private static function opcode92(Core $core)
    {
        $core->registerE = ($core->registersHL >> 8);
    }

    /**
     * Opcode #0x5D.
     *
     * LD E, L
     *
     * @param Core $core
     */
    private static function opcode93(Core $core)
    {
        $core->registerE = ($core->registersHL & 0xFF);
    }

    /**
     * Opcode #0x5E.
     *
     * LD E, (HL)
     *
     * @param Core $core
     */
    private static function opcode94(Core $core)
    {
        $core->registerE = $core->memoryReader[$core->registersHL]($core, $core->registersHL);
    }

    /**
     * Opcode #0x5F.
     *
     * LD E, A
     *
     * @param Core $core
     */
    private static function opcode95(Core $core)
    {
        $core->registerE = $core->registerA;
    }

    /**
     * Opcode #0x60.
     *
     * LD H, B
     *
     * @param Core $core
     */
    private static function opcode96(Core $core)
    {
        $core->registersHL = ($core->registerB << 8) + ($core->registersHL & 0xFF);
    }

    /**
     * Opcode #0x61.
     *
     * LD H, C
     *
     * @param Core $core
     */
    private static function opcode97(Core $core)
    {
        $core->registersHL = ($core->registerC << 8) + ($core->registersHL & 0xFF);
    }

    /**
     * Opcode #0x62.
     *
     * LD H, D
     *
     * @param Core $core
     */
    private static function opcode98(Core $core)
    {
        $core->registersHL = ($core->registerD << 8) + ($core->registersHL & 0xFF);
    }

    /**
     * Opcode #0x63.
     *
     * LD H, E
     *
     * @param Core $core
     */
    private static function opcode99(Core $core)
    {
        $core->registersHL = ($core->registerE << 8) + ($core->registersHL & 0xFF);
    }

    /**
     * Opcode #0x64.
     *
     * LD H, H
     *
     * @param Core $core
     */
    private static function opcode100(Core $core)
    {
        //Do nothing...
    }

    /**
     * Opcode #0x65.
     *
     * LD H, L
     *
     * @param Core $core
     */
    private static function opcode101(Core $core)
    {
        $core->registersHL = (($core->registersHL & 0xFF) << 8) + ($core->registersHL & 0xFF);
    }

    /**
     * Opcode #0x66.
     *
     * LD H, (HL)
     *
     * @param Core $core
     */
    private static function opcode102(Core $core)
    {
        $core->registersHL = ($core->memoryReader[$core->registersHL]($core, $core->registersHL) << 8) + ($core->registersHL & 0xFF);
    }

    /**
     * Opcode #0x67.
     *
     * LD H, A
     *
     * @param Core $core
     */
    private static function opcode103(Core $core)
    {
        $core->registersHL = ($core->registerA << 8) + ($core->registersHL & 0xFF);
    }

    /**
     * Opcode #0x68.
     *
     * LD L, B
     *
     * @param Core $core
     */
    private static function opcode104(Core $core)
    {
        $core->registersHL = ($core->registersHL & 0xFF00) + $core->registerB;
    }

    /**
     * Opcode #0x69.
     *
     * LD L, C
     *
     * @param Core $core
     */
    private static function opcode105(Core $core)
    {
        $core->registersHL = ($core->registersHL & 0xFF00) + $core->registerC;
    }

    /**
     * Opcode #0x6A.
     *
     * LD L, D
     *
     * @param Core $core
     */
    private static function opcode106(Core $core)
    {
        $core->registersHL = ($core->registersHL & 0xFF00) + $core->registerD;
    }

    /**
     * Opcode #0x6B.
     *
     * LD L, E
     *
     * @param Core $core
     */
    private static function opcode107(Core $core)
    {
        $core->registersHL = ($core->registersHL & 0xFF00) + $core->registerE;
    }

    /**
     * Opcode #0x6C.
     *
     * LD L, H
     *
     * @param Core $core
     */
    private static function opcode108(Core $core)
    {
        $core->registersHL = ($core->registersHL & 0xFF00) + ($core->registersHL >> 8);
    }

    /**
     * Opcode #0x6D.
     *
     * LD L, L
     *
     * @param Core $core
     */
    private static function opcode109(Core $core)
    {
        //Do nothing...
    }

    /**
     * Opcode #0x6E.
     *
     * LD L, (HL)
     *
     * @param Core $core
     */
    private static function opcode110(Core $core)
    {
        $core->registersHL = ($core->registersHL & 0xFF00) + $core->memoryReader[$core->registersHL]($core, $core->registersHL);
    }

    /**
     * Opcode #0x6F.
     *
     * LD L, A
     *
     * @param Core $core
     */
    private static function opcode111(Core $core)
    {
        $core->registersHL = ($core->registersHL & 0xFF00) + $core->registerA;
    }

    /**
     * Opcode #0x70.
     *
     * LD (HL), B
     *
     * @param Core $core
     */
    private static function opcode112(Core $core)
    {
        $core->memoryWrite($core->registersHL, $core->registerB);
    }

    /**
     * Opcode #0x71.
     *
     * LD (HL), C
     *
     * @param Core $core
     */
    private static function opcode113(Core $core)
    {
        $core->memoryWrite($core->registersHL, $core->registerC);
    }

    /**
     * Opcode #0x72.
     *
     * LD (HL), D
     *
     * @param Core $core
     */
    private static function opcode114(Core $core)
    {
        $core->memoryWrite($core->registersHL, $core->registerD);
    }

    /**
     * Opcode #0x73.
     *
     * LD (HL), E
     *
     * @param Core $core
     */
    private static function opcode115(Core $core)
    {
        $core->memoryWrite($core->registersHL, $core->registerE);
    }

    /**
     * Opcode #0x74.
     *
     * LD (HL), H
     *
     * @param Core $core
     */
    private static function opcode116(Core $core)
    {
        $core->memoryWrite($core->registersHL, ($core->registersHL >> 8));
    }

    /**
     * Opcode #0x75.
     *
     * LD (HL), L
     *
     * @param Core $core
     */
    private static function opcode117(Core $core)
    {
        $core->memoryWrite($core->registersHL, ($core->registersHL & 0xFF));
    }

    /**
     * Opcode #0x76.
     *
     * HALT
     *
     * @param \GameBoy\Core $core
     * @throws Exception
     */
    private static function opcode118(Core $core)
    {
        if ($core->untilEnable == 1) {
            /*VBA-M says this fixes Torpedo Range (Seems to work):
            Involves an edge case where an EI is placed right before a HALT.
            EI in this case actually is immediate, so we adjust (Hacky?).*/
            $core->programCounter = $core->nswtuw($core->programCounter - 1);
        } else {
            if (!$core->halt && !$core->IME && !$core->cGBC && !$core->usedBootROM && ($core->memory[0xFF0F] & $core->memory[0xFFFF] & 0x1F) > 0) {
                $core->skipPCIncrement = true;
            }
            $core->halt = true;
            while ($core->halt && ($core->stopEmulator & 1) == 0) {
                /*We're hijacking the main interpreter loop to do this dirty business
                in order to not slow down the main interpreter loop code with halt state handling.*/
                $bitShift = 0;
                $testbit = 1;
                $interrupts = $core->memory[0xFFFF] & $core->memory[0xFF0F];
                while ($bitShift < 5) {
                    //Check to see if an interrupt is enabled AND requested.
                    if (($testbit & $interrupts) == $testbit) {
                        $core->halt = false; //Get out of halt state if in halt state.
                        return; //Let the main interrupt handler compute the interrupt.
                    }
                    $testbit = 1 << ++$bitShift;
                }
                $core->CPUTicks = 1; //1 machine cycle under HALT...
                //Timing:
                $core->updateCore();
            }

            //Throw an error on purpose to exit out of the loop.
            throw new Exception('HALT_OVERRUN');
        }
    }

    /**
     * Opcode #0x77.
     *
     * LD (HL), A
     *
     * @param Core $core
     */
    private static function opcode119(Core $core)
    {
        $core->memoryWrite($core->registersHL, $core->registerA);
    }

    /**
     * Opcode #0x78.
     *
     * LD A, B
     *
     * @param Core $core
     */
    private static function opcode120(Core $core)
    {
        $core->registerA = $core->registerB;
    }

    /**
     * Opcode #0x79.
     *
     * LD A, C
     *
     * @param Core $core
     */
    private static function opcode121(Core $core)
    {
        $core->registerA = $core->registerC;
    }

    /**
     * Opcode #0x7A.
     *
     * LD A, D
     *
     * @param Core $core
     */
    private static function opcode122(Core $core)
    {
        $core->registerA = $core->registerD;
    }

    /**
     * Opcode #0x7B.
     *
     * LD A, E
     *
     * @param Core $core
     */
    private static function opcode123(Core $core)
    {
        $core->registerA = $core->registerE;
    }

    /**
     * Opcode #0x7C.
     *
     * LD A, H
     *
     * @param Core $core
     */
    private static function opcode124(Core $core)
    {
        $core->registerA = ($core->registersHL >> 8);
    }

    /**
     * Opcode #0x7D.
     *
     * LD A, L
     *
     * @param Core $core
     */
    private static function opcode125(Core $core)
    {
        $core->registerA = ($core->registersHL & 0xFF);
    }

    /**
     * Opcode #0x7E.
     *
     * LD, A, (HL)
     *
     * @param Core $core
     */
    private static function opcode126(Core $core)
    {
        $core->registerA = $core->memoryReader[$core->registersHL]($core, $core->registersHL);
    }

    /**
     * Opcode #0x7F.
     *
     * LD A, A
     *
     * @param Core $core
     */
    private static function opcode127(Core $core)
    {
        //Do Nothing...
    }

    /**
     * Opcode #0x80.
     *
     * ADD A, B
     *
     * @param Core $core
     */
    private static function opcode128(Core $core)
    {
        $dirtySum = $core->registerA + $core->registerB;
        $core->FHalfCarry = ($dirtySum & 0xF) < ($core->registerA & 0xF);
        $core->FCarry = ($dirtySum > 0xFF);
        $core->registerA = $dirtySum & 0xFF;
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = false;
    }

    /**
     * Opcode #0x81.
     *
     * ADD A, C
     *
     * @param Core $core
     */
    private static function opcode129(Core $core)
    {
        $dirtySum = $core->registerA + $core->registerC;
        $core->FHalfCarry = ($dirtySum & 0xF) < ($core->registerA & 0xF);
        $core->FCarry = ($dirtySum > 0xFF);
        $core->registerA = $dirtySum & 0xFF;
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = false;
    }

    /**
     * Opcode #0x82.
     *
     * ADD A, D
     *
     * @param Core $core
     */
    private static function opcode130(Core $core)
    {
        $dirtySum = $core->registerA + $core->registerD;
        $core->FHalfCarry = ($dirtySum & 0xF) < ($core->registerA & 0xF);
        $core->FCarry = ($dirtySum > 0xFF);
        $core->registerA = $dirtySum & 0xFF;
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = false;
    }

    /**
     * Opcode #0x83.
     *
     * ADD A, E
     *
     * @param Core $core
     */
    private static function opcode131(Core $core)
    {
        $dirtySum = $core->registerA + $core->registerE;
        $core->FHalfCarry = ($dirtySum & 0xF) < ($core->registerA & 0xF);
        $core->FCarry = ($dirtySum > 0xFF);
        $core->registerA = $dirtySum & 0xFF;
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = false;
    }

    /**
     * Opcode #0x84.
     *
     * ADD A, H
     *
     * @param Core $core
     */
    private static function opcode132(Core $core)
    {
        $dirtySum = $core->registerA + ($core->registersHL >> 8);
        $core->FHalfCarry = ($dirtySum & 0xF) < ($core->registerA & 0xF);
        $core->FCarry = ($dirtySum > 0xFF);
        $core->registerA = $dirtySum & 0xFF;
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = false;
    }

    /**
     * Opcode #0x85.
     *
     * ADD A, L
     *
     * @param Core $core
     */
    private static function opcode133(Core $core)
    {
        $dirtySum = $core->registerA + ($core->registersHL & 0xFF);
        $core->FHalfCarry = ($dirtySum & 0xF) < ($core->registerA & 0xF);
        $core->FCarry = ($dirtySum > 0xFF);
        $core->registerA = $dirtySum & 0xFF;
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = false;
    }

    /**
     * Opcode #0x86.
     *
     * ADD A, (HL)
     *
     * @param Core $core
     */
    private static function opcode134(Core $core)
    {
        $dirtySum = $core->registerA + $core->memoryReader[$core->registersHL]($core, $core->registersHL);
        $core->FHalfCarry = ($dirtySum & 0xF) < ($core->registerA & 0xF);
        $core->FCarry = ($dirtySum > 0xFF);
        $core->registerA = $dirtySum & 0xFF;
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = false;
    }

    /**
     * Opcode #0x87.
     *
     * ADD A, A
     *
     * @param Core $core
     */
    private static function opcode135(Core $core)
    {
        $dirtySum = $core->registerA * 2;
        $core->FHalfCarry = ($dirtySum & 0xF) < ($core->registerA & 0xF);
        $core->FCarry = ($dirtySum > 0xFF);
        $core->registerA = $dirtySum & 0xFF;
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = false;
    }

    /**
     * Opcode #0x88.
     *
     * ADC A, B
     *
     * @param Core $core
     */
    private static function opcode136(Core $core)
    {
        $dirtySum = $core->registerA + $core->registerB + (($core->FCarry) ? 1 : 0);
        $core->FHalfCarry = (($core->registerA & 0xF) + ($core->registerB & 0xF) + (($core->FCarry) ? 1 : 0) > 0xF);
        $core->FCarry = ($dirtySum > 0xFF);
        $core->registerA = $dirtySum & 0xFF;
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = false;
    }

    /**
     * Opcode #0x89.
     *
     * ADC A, C
     *
     * @param Core $core
     */
    private static function opcode137(Core $core)
    {
        $dirtySum = $core->registerA + $core->registerC + (($core->FCarry) ? 1 : 0);
        $core->FHalfCarry = (($core->registerA & 0xF) + ($core->registerC & 0xF) + (($core->FCarry) ? 1 : 0) > 0xF);
        $core->FCarry = ($dirtySum > 0xFF);
        $core->registerA = $dirtySum & 0xFF;
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = false;
    }

    /**
     * Opcode #0x8A.
     *
     * ADC A, D
     *
     * @param Core $core
     */
    private static function opcode138(Core $core)
    {
        $dirtySum = $core->registerA + $core->registerD + (($core->FCarry) ? 1 : 0);
        $core->FHalfCarry = (($core->registerA & 0xF) + ($core->registerD & 0xF) + (($core->FCarry) ? 1 : 0) > 0xF);
        $core->FCarry = ($dirtySum > 0xFF);
        $core->registerA = $dirtySum & 0xFF;
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = false;
    }

    /**
     * Opcode #0x8B.
     *
     * ADC A, E
     *
     * @param Core $core
     */
    private static function opcode139(Core $core)
    {
        $dirtySum = $core->registerA + $core->registerE + (($core->FCarry) ? 1 : 0);
        $core->FHalfCarry = (($core->registerA & 0xF) + ($core->registerE & 0xF) + (($core->FCarry) ? 1 : 0) > 0xF);
        $core->FCarry = ($dirtySum > 0xFF);
        $core->registerA = $dirtySum & 0xFF;
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = false;
    }

    /**
     * Opcode #0x8C.
     *
     * ADC A, H
     *
     * @param Core $core
     */
    private static function opcode140(Core $core)
    {
        $tempValue = ($core->registersHL >> 8);
        $dirtySum = $core->registerA + $tempValue + (($core->FCarry) ? 1 : 0);
        $core->FHalfCarry = (($core->registerA & 0xF) + ($tempValue & 0xF) + (($core->FCarry) ? 1 : 0) > 0xF);
        $core->FCarry = ($dirtySum > 0xFF);
        $core->registerA = $dirtySum & 0xFF;
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = false;
    }

    /**
     * Opcode #0x8D.
     *
     * ADC A, L
     *
     * @param Core $core
     */
    private static function opcode141(Core $core)
    {
        $tempValue = ($core->registersHL & 0xFF);
        $dirtySum = $core->registerA + $tempValue + (($core->FCarry) ? 1 : 0);
        $core->FHalfCarry = (($core->registerA & 0xF) + ($tempValue & 0xF) + (($core->FCarry) ? 1 : 0) > 0xF);
        $core->FCarry = ($dirtySum > 0xFF);
        $core->registerA = $dirtySum & 0xFF;
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = false;
    }

    /**
     * Opcode #0x8E.
     *
     * ADC A, (HL)
     *
     * @param Core $core
     */
    private static function opcode142(Core $core)
    {
        $tempValue = $core->memoryReader[$core->registersHL]($core, $core->registersHL);
        $dirtySum = $core->registerA + $tempValue + (($core->FCarry) ? 1 : 0);
        $core->FHalfCarry = (($core->registerA & 0xF) + ($tempValue & 0xF) + (($core->FCarry) ? 1 : 0) > 0xF);
        $core->FCarry = ($dirtySum > 0xFF);
        $core->registerA = $dirtySum & 0xFF;
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = false;
    }

    /**
     * Opcode #0x8F.
     *
     * ADC A, A
     *
     * @param Core $core
     */
    private static function opcode143(Core $core)
    {
        $dirtySum = ($core->registerA * 2) + (($core->FCarry) ? 1 : 0);
        $core->FHalfCarry = (($core->registerA & 0xF) + ($core->registerA & 0xF) + (($core->FCarry) ? 1 : 0) > 0xF);
        $core->FCarry = ($dirtySum > 0xFF);
        $core->registerA = $dirtySum & 0xFF;
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = false;
    }

    /**
     * Opcode #0x90.
     *
     * SUB A, B
     *
     * @param Core $core
     */
    private static function opcode144(Core $core)
    {
        $dirtySum = $core->registerA - $core->registerB;
        $core->FHalfCarry = ($core->registerA & 0xF) < ($core->registerB & 0xF);
        $core->FCarry = ($dirtySum < 0);
        $core->registerA = $core->unsbtub($dirtySum);
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = true;
    }

    /**
     * Opcode #0x91.
     *
     * SUB A, C
     *
     * @param Core $core
     */
    private static function opcode145(Core $core)
    {
        $dirtySum = $core->registerA - $core->registerC;
        $core->FHalfCarry = ($core->registerA & 0xF) < ($core->registerC & 0xF);
        $core->FCarry = ($dirtySum < 0);
        $core->registerA = $core->unsbtub($dirtySum);
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = true;
    }

    /**
     * Opcode #0x92.
     *
     * SUB A, D
     *
     * @param Core $core
     */
    private static function opcode146(Core $core)
    {
        $dirtySum = $core->registerA - $core->registerD;
        $core->FHalfCarry = ($core->registerA & 0xF) < ($core->registerD & 0xF);
        $core->FCarry = ($dirtySum < 0);
        $core->registerA = $core->unsbtub($dirtySum);
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = true;
    }

    /**
     * Opcode #0x93.
     *
     * SUB A, E
     *
     * @param Core $core
     */
    private static function opcode147(Core $core)
    {
        $dirtySum = $core->registerA - $core->registerE;
        $core->FHalfCarry = ($core->registerA & 0xF) < ($core->registerE & 0xF);
        $core->FCarry = ($dirtySum < 0);
        $core->registerA = $core->unsbtub($dirtySum);
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = true;
    }

    /**
     * Opcode #0x94.
     *
     * SUB A, H
     *
     * @param Core $core
     */
    private static function opcode148(Core $core)
    {
        $temp_var = $core->registersHL >> 8;
        $dirtySum = $core->registerA - $temp_var;
        $core->FHalfCarry = ($core->registerA & 0xF) < ($temp_var & 0xF);
        $core->FCarry = ($dirtySum < 0);
        $core->registerA = $core->unsbtub($dirtySum);
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = true;
    }

    /**
     * Opcode #0x95.
     *
     * SUB A, L
     *
     * @param Core $core
     */
    private static function opcode149(Core $core)
    {
        $dirtySum = $core->registerA - ($core->registersHL & 0xFF);
        $core->FHalfCarry = ($core->registerA & 0xF) < ($core->registersHL & 0xF);
        $core->FCarry = ($dirtySum < 0);
        $core->registerA = $core->unsbtub($dirtySum);
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = true;
    }

    /**
     * Opcode #0x96.
     *
     * SUB A, (HL)
     *
     * @param Core $core
     */
    private static function opcode150(Core $core)
    {
        $temp_var = $core->memoryReader[$core->registersHL]($core, $core->registersHL);
        $dirtySum = $core->registerA - $temp_var;
        $core->FHalfCarry = ($core->registerA & 0xF) < ($temp_var & 0xF);
        $core->FCarry = ($dirtySum < 0);
        $core->registerA = $core->unsbtub($dirtySum);
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = true;
    }

    /**
     * Opcode #0x97.
     *
     * SUB A, A
     *
     * @param Core $core
     */
    private static function opcode151(Core $core)
    {
        //number - same number == 0
        $core->registerA = 0;
        $core->FHalfCarry = $core->FCarry = false;
        $core->FZero = $core->FSubtract = true;
    }

    /**
     * Opcode #0x98.
     *
     * SBC A, B
     *
     * @param Core $core
     */
    private static function opcode152(Core $core)
    {
        $dirtySum = $core->registerA - $core->registerB - (($core->FCarry) ? 1 : 0);
        $core->FHalfCarry = (($core->registerA & 0xF) - ($core->registerB & 0xF) - (($core->FCarry) ? 1 : 0) < 0);
        $core->FCarry = ($dirtySum < 0);
        $core->registerA = $core->unsbtub($dirtySum);
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = true;
    }

    /**
     * Opcode #0x99.
     *
     * SBC A, C
     *
     * @param Core $core
     */
    private static function opcode153(Core $core)
    {
        $dirtySum = $core->registerA - $core->registerC - (($core->FCarry) ? 1 : 0);
        $core->FHalfCarry = (($core->registerA & 0xF) - ($core->registerC & 0xF) - (($core->FCarry) ? 1 : 0) < 0);
        $core->FCarry = ($dirtySum < 0);
        $core->registerA = $core->unsbtub($dirtySum);
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = true;
    }

    /**
     * Opcode #0x9A.
     *
     * SBC A, D
     *
     * @param Core $core
     */
    private static function opcode154(Core $core)
    {
        $dirtySum = $core->registerA - $core->registerD - (($core->FCarry) ? 1 : 0);
        $core->FHalfCarry = (($core->registerA & 0xF) - ($core->registerD & 0xF) - (($core->FCarry) ? 1 : 0) < 0);
        $core->FCarry = ($dirtySum < 0);
        $core->registerA = $core->unsbtub($dirtySum);
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = true;
    }

    /**
     * Opcode #0x9B.
     *
     * SBC A, E
     *
     * @param Core $core
     */
    private static function opcode155(Core $core)
    {
        $dirtySum = $core->registerA - $core->registerE - (($core->FCarry) ? 1 : 0);
        $core->FHalfCarry = (($core->registerA & 0xF) - ($core->registerE & 0xF) - (($core->FCarry) ? 1 : 0) < 0);
        $core->FCarry = ($dirtySum < 0);
        $core->registerA = $core->unsbtub($dirtySum);
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = true;
    }

    /**
     * Opcode #0x9C.
     *
     * SBC A, H
     *
     * @param Core $core
     */
    private static function opcode156(Core $core)
    {
        $temp_var = $core->registersHL >> 8;
        $dirtySum = $core->registerA - $temp_var - (($core->FCarry) ? 1 : 0);
        $core->FHalfCarry = (($core->registerA & 0xF) - ($temp_var & 0xF) - (($core->FCarry) ? 1 : 0) < 0);
        $core->FCarry = ($dirtySum < 0);
        $core->registerA = $core->unsbtub($dirtySum);
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = true;
    }

    /**
     * Opcode #0x9D.
     *
     * SBC A, L
     *
     * @param Core $core
     */
    private static function opcode157(Core $core)
    {
        $dirtySum = $core->registerA - ($core->registersHL & 0xFF) - (($core->FCarry) ? 1 : 0);
        $core->FHalfCarry = (($core->registerA & 0xF) - ($core->registersHL & 0xF) - (($core->FCarry) ? 1 : 0) < 0);
        $core->FCarry = ($dirtySum < 0);
        $core->registerA = $core->unsbtub($dirtySum);
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = true;
    }

    /**
     * Opcode #0x9E.
     *
     * SBC A, (HL)
     *
     * @param Core $core
     */
    private static function opcode158(Core $core)
    {
        $temp_var = $core->memoryReader[$core->registersHL]($core, $core->registersHL);
        $dirtySum = $core->registerA - $temp_var - (($core->FCarry) ? 1 : 0);
        $core->FHalfCarry = (($core->registerA & 0xF) - ($temp_var & 0xF) - (($core->FCarry) ? 1 : 0) < 0);
        $core->FCarry = ($dirtySum < 0);
        $core->registerA = $core->unsbtub($dirtySum);
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = true;
    }

    /**
     * Opcode #0x9F.
     *
     * SBC A, A
     *
     * @param Core $core
     */
    private static function opcode159(Core $core)
    {
        //Optimized SBC A:
        if ($core->FCarry) {
            $core->FZero = false;
            $core->FSubtract = $core->FHalfCarry = $core->FCarry = true;
            $core->registerA = 0xFF;
        } else {
            $core->FHalfCarry = $core->FCarry = false;
            $core->FSubtract = $core->FZero = true;
            $core->registerA = 0;
        }
    }

    /**
     * Opcode #0xA0.
     *
     * AND B
     *
     * @param Core $core
     */
    private static function opcode160(Core $core)
    {
        $core->registerA &= $core->registerB;
        $core->FZero = ($core->registerA == 0);
        $core->FHalfCarry = true;
        $core->FSubtract = $core->FCarry = false;
    }

    /**
     * Opcode #0xA1.
     *
     * AND C
     *
     * @param Core $core
     */
    private static function opcode161(Core $core)
    {
        $core->registerA &= $core->registerC;
        $core->FZero = ($core->registerA == 0);
        $core->FHalfCarry = true;
        $core->FSubtract = $core->FCarry = false;
    }

    /**
     * Opcode #0xA2.
     *
     * AND D
     *
     * @param Core $core
     */
    private static function opcode162(Core $core)
    {
        $core->registerA &= $core->registerD;
        $core->FZero = ($core->registerA == 0);
        $core->FHalfCarry = true;
        $core->FSubtract = $core->FCarry = false;
    }

    /**
     * Opcode #0xA3.
     *
     * AND E
     *
     * @param Core $core
     */
    private static function opcode163(Core $core)
    {
        $core->registerA &= $core->registerE;
        $core->FZero = ($core->registerA == 0);
        $core->FHalfCarry = true;
        $core->FSubtract = $core->FCarry = false;
    }

    /**
     * Opcode #0xA4.
     *
     * AND H
     *
     * @param Core $core
     */
    private static function opcode164(Core $core)
    {
        $core->registerA &= ($core->registersHL >> 8);
        $core->FZero = ($core->registerA == 0);
        $core->FHalfCarry = true;
        $core->FSubtract = $core->FCarry = false;
    }

    /**
     * Opcode #0xA5.
     *
     * AND L
     *
     * @param Core $core
     */
    private static function opcode165(Core $core)
    {
        $core->registerA &= ($core->registersHL & 0xFF);
        $core->FZero = ($core->registerA == 0);
        $core->FHalfCarry = true;
        $core->FSubtract = $core->FCarry = false;
    }

    /**
     * Opcode #0xA6.
     *
     * AND (HL)
     *
     * @param Core $core
     */
    private static function opcode166(Core $core)
    {
        $core->registerA &= $core->memoryReader[$core->registersHL]($core, $core->registersHL);
        $core->FZero = ($core->registerA == 0);
        $core->FHalfCarry = true;
        $core->FSubtract = $core->FCarry = false;
    }

    /**
     * Opcode #0xA7.
     *
     * AND A
     *
     * @param Core $core
     */
    private static function opcode167(Core $core)
    {
        //number & same number = same number
        $core->FZero = ($core->registerA == 0);
        $core->FHalfCarry = true;
        $core->FSubtract = $core->FCarry = false;
    }

    /**
     * Opcode #0xA8.
     *
     * XOR B
     *
     * @param Core $core
     */
    private static function opcode168(Core $core)
    {
        $core->registerA ^= $core->registerB;
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = $core->FHalfCarry = $core->FCarry = false;
    }

    /**
     * Opcode #0xA9.
     *
     * XOR C
     *
     * @param Core $core
     */
    private static function opcode169(Core $core)
    {
        $core->registerA ^= $core->registerC;
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = $core->FHalfCarry = $core->FCarry = false;
    }

    /**
     * Opcode #0xAA.
     *
     * XOR D
     *
     * @param Core $core
     */
    private static function opcode170(Core $core)
    {
        $core->registerA ^= $core->registerD;
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = $core->FHalfCarry = $core->FCarry = false;
    }

    /**
     * Opcode #0xAB.
     *
     * XOR E
     *
     * @param Core $core
     */
    private static function opcode171(Core $core)
    {
        $core->registerA ^= $core->registerE;
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = $core->FHalfCarry = $core->FCarry = false;
    }

    /**
     * Opcode #0xAC.
     *
     * XOR H
     *
     * @param Core $core
     */
    private static function opcode172(Core $core)
    {
        $core->registerA ^= ($core->registersHL >> 8);
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = $core->FHalfCarry = $core->FCarry = false;
    }

    /**
     * Opcode #0xAD.
     *
     * XOR L
     *
     * @param Core $core
     */
    private static function opcode173(Core $core)
    {
        $core->registerA ^= ($core->registersHL & 0xFF);
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = $core->FHalfCarry = $core->FCarry = false;
    }

    /**
     * Opcode #0xAE.
     *
     * XOR (HL)
     *
     * @param Core $core
     */
    private static function opcode174(Core $core)
    {
        $core->registerA ^= $core->memoryReader[$core->registersHL]($core, $core->registersHL);
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = $core->FHalfCarry = $core->FCarry = false;
    }

    /**
     * Opcode #0xAF.
     *
     * XOR A
     *
     * @param Core $core
     */
    private static function opcode175(Core $core)
    {
        //number ^ same number == 0
        $core->registerA = 0;
        $core->FZero = true;
        $core->FSubtract = $core->FHalfCarry = $core->FCarry = false;
    }

    /**
     * Opcode #0xB0.
     *
     * OR B
     *
     * @param Core $core
     */
    private static function opcode176(Core $core)
    {
        $core->registerA |= $core->registerB;
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = $core->FCarry = $core->FHalfCarry = false;
    }

    /**
     * Opcode #0xB1.
     *
     * OR C
     *
     * @param Core $core
     */
    private static function opcode177(Core $core)
    {
        $core->registerA |= $core->registerC;
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = $core->FCarry = $core->FHalfCarry = false;
    }

    /**
     * Opcode #0xB2.
     *
     * OR D
     *
     * @param Core $core
     */
    private static function opcode178(Core $core)
    {
        $core->registerA |= $core->registerD;
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = $core->FCarry = $core->FHalfCarry = false;
    }

    /**
     * Opcode #0xB3.
     *
     * OR E
     *
     * @param Core $core
     */
    private static function opcode179(Core $core)
    {
        $core->registerA |= $core->registerE;
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = $core->FCarry = $core->FHalfCarry = false;
    }

    /**
     * Opcode #0xB4.
     *
     * OR H
     *
     * @param Core $core
     */
    private static function opcode180(Core $core)
    {
        $core->registerA |= ($core->registersHL >> 8);
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = $core->FCarry = $core->FHalfCarry = false;
    }

    /**
     * Opcode #0xB5.
     *
     * OR L
     *
     * @param Core $core
     */
    private static function opcode181(Core $core)
    {
        $core->registerA |= ($core->registersHL & 0xFF);
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = $core->FCarry = $core->FHalfCarry = false;
    }

    /**
     * Opcode #0xB6.
     *
     * OR (HL)
     *
     * @param Core $core
     */
    private static function opcode182(Core $core)
    {
        $core->registerA |= $core->memoryReader[$core->registersHL]($core, $core->registersHL);
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = $core->FCarry = $core->FHalfCarry = false;
    }

    /**
     * Opcode #0xB7.
     *
     * OR A
     *
     * @param Core $core
     */
    private static function opcode183(Core $core)
    {
        //number | same number == same number
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = $core->FCarry = $core->FHalfCarry = false;
    }

    /**
     * Opcode #0xB8.
     *
     * CP B
     *
     * @param Core $core
     */
    private static function opcode184(Core $core)
    {
        $dirtySum = $core->registerA - $core->registerB;
        $core->FHalfCarry = ($core->unsbtub($dirtySum) & 0xF) > ($core->registerA & 0xF);
        $core->FCarry = ($dirtySum < 0);
        $core->FZero = ($dirtySum == 0);
        $core->FSubtract = true;
    }

    /**
     * Opcode #0xB9.
     *
     * CP C
     *
     * @param Core $core
     */
    private static function opcode185(Core $core)
    {
        $dirtySum = $core->registerA - $core->registerC;
        $core->FHalfCarry = ($core->unsbtub($dirtySum) & 0xF) > ($core->registerA & 0xF);
        $core->FCarry = ($dirtySum < 0);
        $core->FZero = ($dirtySum == 0);
        $core->FSubtract = true;
    }

    /**
     * Opcode #0xBA.
     *
     * CP D
     *
     * @param Core $core
     */
    private static function opcode186(Core $core)
    {
        $dirtySum = $core->registerA - $core->registerD;
        $core->FHalfCarry = ($core->unsbtub($dirtySum) & 0xF) > ($core->registerA & 0xF);
        $core->FCarry = ($dirtySum < 0);
        $core->FZero = ($dirtySum == 0);
        $core->FSubtract = true;
    }

    /**
     * Opcode #0xBB.
     *
     * CP E
     *
     * @param Core $core
     */
    private static function opcode187(Core $core)
    {
        $dirtySum = $core->registerA - $core->registerE;
        $core->FHalfCarry = ($core->unsbtub($dirtySum) & 0xF) > ($core->registerA & 0xF);
        $core->FCarry = ($dirtySum < 0);
        $core->FZero = ($dirtySum == 0);
        $core->FSubtract = true;
    }

    /**
     * Opcode #0xBC.
     *
     * CP H
     *
     * @param Core $core
     */
    private static function opcode188(Core $core)
    {
        $dirtySum = $core->registerA - ($core->registersHL >> 8);
        $core->FHalfCarry = ($core->unsbtub($dirtySum) & 0xF) > ($core->registerA & 0xF);
        $core->FCarry = ($dirtySum < 0);
        $core->FZero = ($dirtySum == 0);
        $core->FSubtract = true;
    }

    /**
     * Opcode #0xBD.
     *
     * CP L
     *
     * @param Core $core
     */
    private static function opcode189(Core $core)
    {
        $dirtySum = $core->registerA - ($core->registersHL & 0xFF);
        $core->FHalfCarry = ($core->unsbtub($dirtySum) & 0xF) > ($core->registerA & 0xF);
        $core->FCarry = ($dirtySum < 0);
        $core->FZero = ($dirtySum == 0);
        $core->FSubtract = true;
    }

    /**
     * Opcode #0xBE.
     *
     * CP (HL)
     *
     * @param Core $core
     */
    private static function opcode190(Core $core)
    {
        $dirtySum = $core->registerA - $core->memoryReader[$core->registersHL]($core, $core->registersHL);
        $core->FHalfCarry = ($core->unsbtub($dirtySum) & 0xF) > ($core->registerA & 0xF);
        $core->FCarry = ($dirtySum < 0);
        $core->FZero = ($dirtySum == 0);
        $core->FSubtract = true;
    }

    /**
     * Opcode #0xBF.
     *
     * CP A
     *
     * @param Core $core
     */
    private static function opcode191(Core $core)
    {
        $core->FHalfCarry = $core->FCarry = false;
        $core->FZero = $core->FSubtract = true;
    }

    /**
     * Opcode #0xC0.
     *
     * RET !FZ
     *
     * @param Core $core
     */
    private static function opcode192(Core $core)
    {
        if (!$core->FZero) {
            $core->programCounter = ($core->memoryRead(($core->stackPointer + 1) & 0xFFFF) << 8) + $core->memoryReader[$core->stackPointer]($core, $core->stackPointer);
            $core->stackPointer = ($core->stackPointer + 2) & 0xFFFF;
            $core->CPUTicks += 3;
        }
    }

    /**
     * Opcode #0xC1.
     *
     * POP BC
     *
     * @param Core $core
     */
    private static function opcode193(Core $core)
    {
        $core->registerC = $core->memoryReader[$core->stackPointer]($core, $core->stackPointer);
        $core->registerB = $core->memoryRead(($core->stackPointer + 1) & 0xFFFF);
        $core->stackPointer = ($core->stackPointer + 2) & 0xFFFF;
    }

    /**
     * Opcode #0xC2.
     *
     * JP !FZ, nn
     *
     * @param Core $core
     */
    private static function opcode194(Core $core)
    {
        if (!$core->FZero) {
            $core->programCounter = ($core->memoryRead(($core->programCounter + 1) & 0xFFFF) << 8) + $core->memoryReader[$core->programCounter]($core, $core->programCounter);
            ++$core->CPUTicks;
        } else {
            $core->programCounter = ($core->programCounter + 2) & 0xFFFF;
        }
    }

    /**
     * Opcode #0xC3.
     *
     * JP nn
     *
     * @param Core $core
     */
    private static function opcode195(Core $core)
    {
        $core->programCounter = ($core->memoryRead(($core->programCounter + 1) & 0xFFFF) << 8) + $core->memoryReader[$core->programCounter]($core, $core->programCounter);
    }

    /**
     * Opcode #0xC4.
     *
     * CALL !FZ, nn
     *
     * @param Core $core
     */
    private static function opcode196(Core $core)
    {
        if (!$core->FZero) {
            $temp_pc = ($core->memoryRead(($core->programCounter + 1) & 0xFFFF) << 8) + $core->memoryReader[$core->programCounter]($core, $core->programCounter);
            $core->programCounter = ($core->programCounter + 2) & 0xFFFF;
            $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
            $core->memoryWrite($core->stackPointer, $core->programCounter >> 8);
            $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
            $core->memoryWrite($core->stackPointer, $core->programCounter & 0xFF);
            $core->programCounter = $temp_pc;
            $core->CPUTicks += 3;
        } else {
            $core->programCounter = ($core->programCounter + 2) & 0xFFFF;
        }
    }

    /**
     * Opcode #0xC5.
     *
     * PUSH BC
     *
     * @param Core $core
     */
    private static function opcode197(Core $core)
    {
        $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
        $core->memoryWrite($core->stackPointer, $core->registerB);
        $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
        $core->memoryWrite($core->stackPointer, $core->registerC);
    }

    /**
     * Opcode #0xC6.
     *
     * ADD, n
     *
     * @param Core $core
     */
    private static function opcode198(Core $core)
    {
        $dirtySum = $core->registerA + $core->memoryReader[$core->programCounter]($core, $core->programCounter);
        $core->FHalfCarry = ($dirtySum & 0xF) < ($core->registerA & 0xF);
        $core->FCarry = ($dirtySum > 0xFF);
        $core->registerA = $dirtySum & 0xFF;
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = false;
        $core->programCounter = ($core->programCounter + 1) & 0xFFFF;
    }

    /**
     * Opcode #0xC7.
     *
     * RST 0
     *
     * @param Core $core
     */
    private static function opcode199(Core $core)
    {
        $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
        $core->memoryWrite($core->stackPointer, $core->programCounter >> 8);
        $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
        $core->memoryWrite($core->stackPointer, $core->programCounter & 0xFF);
        $core->programCounter = 0;
    }

    /**
     * Opcode #0xC8.
     *
     * RET FZ
     *
     * @param Core $core
     */
    private static function opcode200(Core $core)
    {
        if ($core->FZero) {
            $core->programCounter = ($core->memoryRead(($core->stackPointer + 1) & 0xFFFF) << 8) + $core->memoryReader[$core->stackPointer]($core, $core->stackPointer);
            $core->stackPointer = ($core->stackPointer + 2) & 0xFFFF;
            $core->CPUTicks += 3;
        }
    }

    /**
     * Opcode #0xC9.
     *
     * RET
     *
     * @param Core $core
     */
    private static function opcode201(Core $core)
    {
        $core->programCounter = ($core->memoryRead(($core->stackPointer + 1) & 0xFFFF) << 8) + $core->memoryReader[$core->stackPointer]($core, $core->stackPointer);
        $core->stackPointer = ($core->stackPointer + 2) & 0xFFFF;
    }

    /**
     * Opcode #0xCA.
     *
     * JP FZ, nn
     *
     * @param Core $core
     */
    private static function opcode202(Core $core)
    {
        if ($core->FZero) {
            $core->programCounter = ($core->memoryRead(($core->programCounter + 1) & 0xFFFF) << 8) + $core->memoryReader[$core->programCounter]($core, $core->programCounter);
            ++$core->CPUTicks;
        } else {
            $core->programCounter = ($core->programCounter + 2) & 0xFFFF;
        }
    }

    /**
     * Opcode #0xCB.
     *
     * Secondary OP Code Set:
     *
     * @param Core $core
     */
    private static function opcode203(Core $core)
    {
        $opcode = $core->memoryReader[$core->programCounter]($core, $core->programCounter);
        //Increment the program counter to the next instruction:
        $core->programCounter = ($core->programCounter + 1) & 0xFFFF;
        //Get how many CPU cycles the current 0xCBXX op code counts for:
        $core->CPUTicks = $core->SecondaryTICKTable[$opcode];
        //Execute secondary OP codes for the 0xCB OP code call.
        Cbopcode::run($core, $opcode);
    }

    /**
     * Opcode #0xCC.
     *
     * CALL FZ, nn
     *
     * @param Core $core
     */
    private static function opcode204(Core $core)
    {
        if ($core->FZero) {
            $temp_pc = ($core->memoryRead(($core->programCounter + 1) & 0xFFFF) << 8) + $core->memoryReader[$core->programCounter]($core, $core->programCounter);
            $core->programCounter = ($core->programCounter + 2) & 0xFFFF;
            $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
            $core->memoryWrite($core->stackPointer, $core->programCounter >> 8);
            $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
            $core->memoryWrite($core->stackPointer, $core->programCounter & 0xFF);
            $core->programCounter = $temp_pc;
            $core->CPUTicks += 3;
        } else {
            $core->programCounter = ($core->programCounter + 2) & 0xFFFF;
        }
    }

    /**
     * Opcode #0xCD.
     *
     * CALL nn
     *
     * @param Core $core
     */
    private static function opcode205(Core $core)
    {
        $temp_pc = ($core->memoryRead(($core->programCounter + 1) & 0xFFFF) << 8) + $core->memoryReader[$core->programCounter]($core, $core->programCounter);
        $core->programCounter = ($core->programCounter + 2) & 0xFFFF;
        $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
        $core->memoryWrite($core->stackPointer, $core->programCounter >> 8);
        $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
        $core->memoryWrite($core->stackPointer, $core->programCounter & 0xFF);
        $core->programCounter = $temp_pc;
    }

    /**
     * Opcode #0xCE.
     *
     * ADC A, n
     *
     * @param Core $core
     */
    private static function opcode206(Core $core)
    {
        $tempValue = $core->memoryReader[$core->programCounter]($core, $core->programCounter);
        $dirtySum = $core->registerA + $tempValue + (($core->FCarry) ? 1 : 0);
        $core->FHalfCarry = (($core->registerA & 0xF) + ($tempValue & 0xF) + (($core->FCarry) ? 1 : 0) > 0xF);
        $core->FCarry = ($dirtySum > 0xFF);
        $core->registerA = $dirtySum & 0xFF;
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = false;
        $core->programCounter = ($core->programCounter + 1) & 0xFFFF;
    }

    /**
     * Opcode #0xCF.
     *
     * RST 0x8
     *
     * @param Core $core
     */
    private static function opcode207(Core $core)
    {
        $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
        $core->memoryWrite($core->stackPointer, $core->programCounter >> 8);
        $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
        $core->memoryWrite($core->stackPointer, $core->programCounter & 0xFF);
        $core->programCounter = 0x8;
    }

    /**
     * Opcode #0xD0.
     *
     * RET !FC
     *
     * @param Core $core
     */
    private static function opcode208(Core $core)
    {
        if (!$core->FCarry) {
            $core->programCounter = ($core->memoryRead(($core->stackPointer + 1) & 0xFFFF) << 8) + $core->memoryReader[$core->stackPointer]($core, $core->stackPointer);
            $core->stackPointer = ($core->stackPointer + 2) & 0xFFFF;
            $core->CPUTicks += 3;
        }
    }

    /**
     * Opcode #0xD1.
     *
     * POP DE
     *
     * @param Core $core
     */
    private static function opcode209(Core $core)
    {
        $core->registerE = $core->memoryReader[$core->stackPointer]($core, $core->stackPointer);
        $core->registerD = $core->memoryRead(($core->stackPointer + 1) & 0xFFFF);
        $core->stackPointer = ($core->stackPointer + 2) & 0xFFFF;
    }

    /**
     * Opcode #0xD2.
     *
     * JP !FC, nn
     *
     * @param Core $core
     */
    private static function opcode210(Core $core)
    {
        if (!$core->FCarry) {
            $core->programCounter = ($core->memoryRead(($core->programCounter + 1) & 0xFFFF) << 8) + $core->memoryReader[$core->programCounter]($core, $core->programCounter);
            ++$core->CPUTicks;
        } else {
            $core->programCounter = ($core->programCounter + 2) & 0xFFFF;
        }
    }

    /**
     * Opcode #0xD3.
     *
     * 0xD3 - Illegal
     *
     * @param Core $core
     */
    private static function opcode211(Core $core)
    {
        // @TODO
        // cout("Illegal op code 0xD3 called, pausing emulation.", 2);
        // pause();
    }

    /**
     * Opcode #0xD4.
     *
     * CALL !FC, nn
     *
     * @param Core $core
     */
    private static function opcode212(Core $core)
    {
        if (!$core->FCarry) {
            $temp_pc = ($core->memoryRead(($core->programCounter + 1) & 0xFFFF) << 8) + $core->memoryReader[$core->programCounter]($core, $core->programCounter);
            $core->programCounter = ($core->programCounter + 2) & 0xFFFF;
            $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
            $core->memoryWrite($core->stackPointer, $core->programCounter >> 8);
            $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
            $core->memoryWrite($core->stackPointer, $core->programCounter & 0xFF);
            $core->programCounter = $temp_pc;
            $core->CPUTicks += 3;
        } else {
            $core->programCounter = ($core->programCounter + 2) & 0xFFFF;
        }
    }

    /**
     * Opcode #0xD5.
     *
     * PUSH DE
     *
     * @param Core $core
     */
    private static function opcode213(Core $core)
    {
        $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
        $core->memoryWrite($core->stackPointer, $core->registerD);
        $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
        $core->memoryWrite($core->stackPointer, $core->registerE);
    }

    /**
     * Opcode #0xD6.
     *
     * SUB A, n
     *
     * @param Core $core
     */
    private static function opcode214(Core $core)
    {
        $temp_var = $core->memoryReader[$core->programCounter]($core, $core->programCounter);
        $dirtySum = $core->registerA - $temp_var;
        $core->FHalfCarry = ($core->registerA & 0xF) < ($temp_var & 0xF);
        $core->FCarry = ($dirtySum < 0);
        $core->registerA = $core->unsbtub($dirtySum);
        $core->programCounter = ($core->programCounter + 1) & 0xFFFF;
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = true;
    }

    /**
     * Opcode #0xD7.
     *
     * RST 0x10
     *
     * @param Core $core
     */
    private static function opcode215(Core $core)
    {
        $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
        $core->memoryWrite($core->stackPointer, $core->programCounter >> 8);
        $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
        $core->memoryWrite($core->stackPointer, $core->programCounter & 0xFF);
        $core->programCounter = 0x10;
    }

    /**
     * Opcode #0xD8.
     *
     * RET FC
     *
     * @param Core $core
     */
    private static function opcode216(Core $core)
    {
        if ($core->FCarry) {
            $core->programCounter = ($core->memoryRead(($core->stackPointer + 1) & 0xFFFF) << 8) + $core->memoryReader[$core->stackPointer]($core, $core->stackPointer);
            $core->stackPointer = ($core->stackPointer + 2) & 0xFFFF;
            $core->CPUTicks += 3;
        }
    }

    /**
     * Opcode #0xD9.
     *
     * RETI
     *
     * @param Core $core
     */
    private static function opcode217(Core $core)
    {
        $core->programCounter = ($core->memoryRead(($core->stackPointer + 1) & 0xFFFF) << 8) + $core->memoryReader[$core->stackPointer]($core, $core->stackPointer);
        $core->stackPointer = ($core->stackPointer + 2) & 0xFFFF;
        //$core->IME = true;
        $core->untilEnable = 2;
    }

    /**
     * Opcode #0xDA.
     *
     * JP FC, nn
     *
     * @param Core $core
     */
    private static function opcode218(Core $core)
    {
        if ($core->FCarry) {
            $core->programCounter = ($core->memoryRead(($core->programCounter + 1) & 0xFFFF) << 8) + $core->memoryReader[$core->programCounter]($core, $core->programCounter);
            ++$core->CPUTicks;
        } else {
            $core->programCounter = ($core->programCounter + 2) & 0xFFFF;
        }
    }

    /**
     * Opcode #0xDB.
     *
     * 0xDB - Illegal
     *
     * @param Core $core
     */
    private static function opcode219(Core $core)
    {
        echo 'Illegal op code 0xDB called, pausing emulation.';
        exit();
    }

    /**
     * Opcode #0xDC.
     *
     * CALL FC, nn
     *
     * @param Core $core
     */
    private static function opcode220(Core $core)
    {
        if ($core->FCarry) {
            $temp_pc = ($core->memoryRead(($core->programCounter + 1) & 0xFFFF) << 8) + $core->memoryReader[$core->programCounter]($core, $core->programCounter);
            $core->programCounter = ($core->programCounter + 2) & 0xFFFF;
            $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
            $core->memoryWrite($core->stackPointer, $core->programCounter >> 8);
            $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
            $core->memoryWrite($core->stackPointer, $core->programCounter & 0xFF);
            $core->programCounter = $temp_pc;
            $core->CPUTicks += 3;
        } else {
            $core->programCounter = ($core->programCounter + 2) & 0xFFFF;
        }
    }

    /**
     * Opcode #0xDD.
     *
     * 0xDD - Illegal
     *
     * @param Core $core
     */
    private static function opcode221(Core $core)
    {
        echo 'Illegal op code 0xDD called, pausing emulation.';
        exit();
    }

    /**
     * Opcode #0xDE.
     *
     * SBC A, n
     *
     * @param Core $core
     */
    private static function opcode222(Core $core)
    {
        $temp_var = $core->memoryReader[$core->programCounter]($core, $core->programCounter);
        $dirtySum = $core->registerA - $temp_var - (($core->FCarry) ? 1 : 0);
        $core->FHalfCarry = (($core->registerA & 0xF) - ($temp_var & 0xF) - (($core->FCarry) ? 1 : 0) < 0);
        $core->FCarry = ($dirtySum < 0);
        $core->registerA = $core->unsbtub($dirtySum);
        $core->programCounter = ($core->programCounter + 1) & 0xFFFF;
        $core->FZero = ($core->registerA == 0);
        $core->FSubtract = true;
    }

    /**
     * Opcode #0xDF.
     *
     * RST 0x18
     *
     * @param Core $core
     */
    private static function opcode223(Core $core)
    {
        $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
        $core->memoryWrite($core->stackPointer, $core->programCounter >> 8);
        $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
        $core->memoryWrite($core->stackPointer, $core->programCounter & 0xFF);
        $core->programCounter = 0x18;
    }

    /**
     * Opcode #0xE0.
     *
     * LDH (n), A
     *
     * @param Core $core
     */
    private static function opcode224(Core $core)
    {
        $core->memoryWrite(0xFF00 + $core->memoryReader[$core->programCounter]($core, $core->programCounter), $core->registerA);
        $core->programCounter = ($core->programCounter + 1) & 0xFFFF;
    }

    /**
     * Opcode #0xE1.
     *
     * POP HL
     *
     * @param Core $core
     */
    private static function opcode225(Core $core)
    {
        $core->registersHL = ($core->memoryRead(($core->stackPointer + 1) & 0xFFFF) << 8) + $core->memoryReader[$core->stackPointer]($core, $core->stackPointer);
        $core->stackPointer = ($core->stackPointer + 2) & 0xFFFF;
    }

    /**
     * Opcode #0xE2.
     *
     * LD (C), A
     *
     * @param Core $core
     */
    private static function opcode226(Core $core)
    {
        $core->memoryWrite(0xFF00 + $core->registerC, $core->registerA);
    }

    /**
     * Opcode #0xE3.
     *
     * 0xE3 - Illegal
     *
     * @param Core $core
     */
    private static function opcode227(Core $core)
    {
        echo 'Illegal op code 0xE3 called, pausing emulation.';
        exit();
    }

    /**
     * Opcode #0xE4.
     *
     * 0xE4 - Illegal
     *
     * @param Core $core
     */
    private static function opcode228(Core $core)
    {
        echo 'Illegal op code 0xE4 called, pausing emulation.';
        exit();
    }

    /**
     * Opcode #0xE5.
     *
     * PUSH HL
     *
     * @param Core $core
     */
    private static function opcode229(Core $core)
    {
        $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
        $core->memoryWrite($core->stackPointer, $core->registersHL >> 8);
        $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
        $core->memoryWrite($core->stackPointer, $core->registersHL & 0xFF);
    }

    /**
     * Opcode #0xE6.
     *
     * AND n
     *
     * @param Core $core
     */
    private static function opcode230(Core $core)
    {
        $core->registerA &= $core->memoryReader[$core->programCounter]($core, $core->programCounter);
        $core->programCounter = ($core->programCounter + 1) & 0xFFFF;
        $core->FZero = ($core->registerA == 0);
        $core->FHalfCarry = true;
        $core->FSubtract = $core->FCarry = false;
    }

    /**
     * Opcode #0xE7.
     *
     * RST 0x20
     *
     * @param Core $core
     */
    private static function opcode231(Core $core)
    {
        $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
        $core->memoryWrite($core->stackPointer, $core->programCounter >> 8);
        $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
        $core->memoryWrite($core->stackPointer, $core->programCounter & 0xFF);
        $core->programCounter = 0x20;
    }

    /**
     * Opcode #0xE8.
     *
     * ADD SP, n
     *
     * @param Core $core
     */
    private static function opcode232(Core $core)
    {
        $signedByte = $core->usbtsb($core->memoryReader[$core->programCounter]($core, $core->programCounter));
        $temp_value = $core->nswtuw($core->stackPointer + $signedByte);
        $core->FCarry = ((($core->stackPointer ^ $signedByte ^ $temp_value) & 0x100) == 0x100);
        $core->FHalfCarry = ((($core->stackPointer ^ $signedByte ^ $temp_value) & 0x10) == 0x10);
        $core->stackPointer = $temp_value;
        $core->programCounter = ($core->programCounter + 1) & 0xFFFF;
        $core->FZero = $core->FSubtract = false;
    }

    /**
     * Opcode #0xE9.
     *
     * JP, (HL)
     *
     * @param Core $core
     */
    private static function opcode233(Core $core)
    {
        $core->programCounter = $core->registersHL;
    }

    /**
     * Opcode #0xEA.
     *
     * LD n, A
     *
     * @param Core $core
     */
    private static function opcode234(Core $core)
    {
        $core->memoryWrite(($core->memoryRead(($core->programCounter + 1) & 0xFFFF) << 8) + $core->memoryReader[$core->programCounter]($core, $core->programCounter), $core->registerA);
        $core->programCounter = ($core->programCounter + 2) & 0xFFFF;
    }

    /**
     * Opcode #0xEB.
     *
     * 0xEB - Illegal
     *
     * @param Core $core
     */
    private static function opcode235(Core $core)
    {
        echo 'Illegal op code 0xEB called, pausing emulation.';
        exit();
    }

    /**
     * Opcode #0xEC.
     *
     * 0xEC - Illegal
     *
     * @param Core $core
     */
    private static function opcode236(Core $core)
    {
        echo 'Illegal op code 0xEC called, pausing emulation.';
        exit();
    }

    /**
     * Opcode #0xED.
     *
     * 0xED - Illegal
     *
     * @param Core $core
     */
    private static function opcode237(Core $core)
    {
        echo 'Illegal op code 0xED called, pausing emulation.';
        exit();
    }

    /**
     * Opcode #0xEE.
     *
     * XOR n
     *
     * @param Core $core
     */
    private static function opcode238(Core $core)
    {
        $core->registerA ^= $core->memoryReader[$core->programCounter]($core, $core->programCounter);
        $core->FZero = ($core->registerA == 0);
        $core->programCounter = ($core->programCounter + 1) & 0xFFFF;
        $core->FSubtract = $core->FHalfCarry = $core->FCarry = false;
    }

    /**
     * Opcode #0xEF.
     *
     * RST 0x28
     *
     * @param Core $core
     */
    private static function opcode239(Core $core)
    {
        $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
        $core->memoryWrite($core->stackPointer, $core->programCounter >> 8);
        $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
        $core->memoryWrite($core->stackPointer, $core->programCounter & 0xFF);
        $core->programCounter = 0x28;
    }

    /**
     * Opcode #0xF0.
     *
     * LDH A, (n)
     *
     * @param Core $core
     */
    private static function opcode240(Core $core)
    {
        $core->registerA = $core->memoryRead(0xFF00 + $core->memoryReader[$core->programCounter]($core, $core->programCounter));
        $core->programCounter = ($core->programCounter + 1) & 0xFFFF;
    }

    /**
     * Opcode #0xF1.
     *
     * POP AF
     *
     * @param Core $core
     */
    private static function opcode241(Core $core)
    {
        $temp_var = $core->memoryReader[$core->stackPointer]($core, $core->stackPointer);
        $core->FZero = (($temp_var & 0x80) == 0x80);
        $core->FSubtract = (($temp_var & 0x40) == 0x40);
        $core->FHalfCarry = (($temp_var & 0x20) == 0x20);
        $core->FCarry = (($temp_var & 0x10) == 0x10);
        $core->registerA = $core->memoryRead(($core->stackPointer + 1) & 0xFFFF);
        $core->stackPointer = ($core->stackPointer + 2) & 0xFFFF;
    }

    /**
     * Opcode #0xF2.
     *
     * LD A, (C)
     *
     * @param Core $core
     */
    private static function opcode242(Core $core)
    {
        $core->registerA = $core->memoryRead(0xFF00 + $core->registerC);
    }

    /**
     * Opcode #0xF3.
     *
     * DI
     *
     * @param Core $core
     */
    private static function opcode243(Core $core)
    {
        $core->IME = false;
        $core->untilEnable = 0;
    }

    /**
     * Opcode #0xF4.
     *
     * 0xF4 - Illegal
     *
     * @param Core $core
     */
    private static function opcode244(Core $core)
    {
        // @TODO
        // cout("Illegal op code 0xF4 called, pausing emulation.", 2);
        // pause();
    }

    /**
     * Opcode #0xF5.
     *
     * PUSH AF
     *
     * @param Core $core
     */
    private static function opcode245(Core $core)
    {
        $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
        $core->memoryWrite($core->stackPointer, $core->registerA);
        $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
        $core->memoryWrite($core->stackPointer, (($core->FZero) ? 0x80 : 0) + (($core->FSubtract) ? 0x40 : 0) + (($core->FHalfCarry) ? 0x20 : 0) + (($core->FCarry) ? 0x10 : 0));
    }

    /**
     * Opcode #0xF6.
     *
     * OR n
     *
     * @param Core $core
     */
    private static function opcode246(Core $core)
    {
        $core->registerA |= $core->memoryReader[$core->programCounter]($core, $core->programCounter);
        $core->FZero = ($core->registerA == 0);
        $core->programCounter = ($core->programCounter + 1) & 0xFFFF;
        $core->FSubtract = $core->FCarry = $core->FHalfCarry = false;
    }

    /**
     * Opcode #0xF7.
     *
     * RST 0x30
     *
     * @param Core $core
     */
    private static function opcode247(Core $core)
    {
        $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
        $core->memoryWrite($core->stackPointer, $core->programCounter >> 8);
        $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
        $core->memoryWrite($core->stackPointer, $core->programCounter & 0xFF);
        $core->programCounter = 0x30;
    }

    /**
     * Opcode #0xF8.
     *
     * LDHL SP, n
     *
     * @param Core $core
     */
    private static function opcode248(Core $core)
    {
        $signedByte = $core->usbtsb($core->memoryReader[$core->programCounter]($core, $core->programCounter));
        $core->registersHL = $core->nswtuw($core->stackPointer + $signedByte);
        $core->FCarry = ((($core->stackPointer ^ $signedByte ^ $core->registersHL) & 0x100) == 0x100);
        $core->FHalfCarry = ((($core->stackPointer ^ $signedByte ^ $core->registersHL) & 0x10) == 0x10);
        $core->programCounter = ($core->programCounter + 1) & 0xFFFF;
        $core->FZero = $core->FSubtract = false;
    }

    /**
     * Opcode #0xF9.
     *
     * LD SP, HL
     *
     * @param Core $core
     */
    private static function opcode249(Core $core)
    {
        $core->stackPointer = $core->registersHL;
    }

    /**
     * Opcode #0xFA.
     *
     * LD A, (nn)
     *
     * @param Core $core
     */
    private static function opcode250(Core $core)
    {
        $core->registerA = $core->memoryRead(($core->memoryRead(($core->programCounter + 1) & 0xFFFF) << 8) + $core->memoryReader[$core->programCounter]($core, $core->programCounter));
        $core->programCounter = ($core->programCounter + 2) & 0xFFFF;
    }

    /**
     * Opcode #0xFB.
     *
     * EI
     *
     * @param Core $core
     */
    private static function opcode251(Core $core)
    {
        $core->untilEnable = 2;
    }

    /**
     * Opcode #0xFC.
     *
     * 0xFC - Illegal
     *
     * @param Core $core
     */
    private static function opcode252(Core $core)
    {
        echo 'Illegal op code 0xFC called, pausing emulation.';
        exit();
    }

    /**
     * Opcode #0xFD.
     *
     * 0xFD - Illegal
     *
     * @param Core $core
     */
    private static function opcode253(Core $core)
    {
        echo 'Illegal op code 0xFD called, pausing emulation.';
        exit();
    }

    /**
     * Opcode #0xFE.
     *
     * CP n
     *
     * @param Core $core
     */
    private static function opcode254(Core $core)
    {
        $dirtySum = $core->registerA - $core->memoryReader[$core->programCounter]($core, $core->programCounter);
        $core->FHalfCarry = ($core->unsbtub($dirtySum) & 0xF) > ($core->registerA & 0xF);
        $core->FCarry = ($dirtySum < 0);
        $core->FZero = ($dirtySum == 0);
        $core->programCounter = ($core->programCounter + 1) & 0xFFFF;
        $core->FSubtract = true;
    }

    /**
     * Opcode #0xFF.
     *
     * RST 0x38
     *
     * @param Core $core
     */
    private static function opcode255(Core $core)
    {
        $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
        $core->memoryWrite($core->stackPointer, $core->programCounter >> 8);
        $core->stackPointer = $core->unswtuw($core->stackPointer - 1);
        $core->memoryWrite($core->stackPointer, $core->programCounter & 0xFF);
        $core->programCounter = 0x38;
    }
}
