<?php

namespace GameBoy;

class Cbopcode
{
    /**
     * Run the given cbopcode.
     *
     * @param Core $core
     * @param int $address
     * @return mixed
     */
    public static function run(Core $core, $address)
    {
        $function = 'cbopcode'.$address;
        return Cbopcode::$function($core);
    }

    /**
     * Cbopcode #0x00.
     *
     * @param Core $core
     */
    private static function cbopcode0(Core $core)
    {
        $core->FCarry = (($core->registerB & 0x80) == 0x80);
        $core->registerB = (($core->registerB << 1) & 0xFF) + (($core->FCarry) ? 1 : 0);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerB == 0);
    }

    /**
     * Cbopcode #0x01.
     *
     * @param Core $core
     */
    private static function cbopcode1(Core $core)
    {
        $core->FCarry = (($core->registerC & 0x80) == 0x80);
        $core->registerC = (($core->registerC << 1) & 0xFF) + (($core->FCarry) ? 1 : 0);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerC == 0);
    }

    /**
     * Cbopcode #0x02.
     *
     * @param Core $core
     */
    private static function cbopcode2(Core $core)
    {
        $core->FCarry = (($core->registerD & 0x80) == 0x80);
        $core->registerD = (($core->registerD << 1) & 0xFF) + (($core->FCarry) ? 1 : 0);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerD == 0);
    }

    /**
     * Cbopcode #0x03.
     *
     * @param Core $core
     */
    private static function cbopcode3(Core $core)
    {
        $core->FCarry = (($core->registerE & 0x80) == 0x80);
        $core->registerE = (($core->registerE << 1) & 0xFF) + (($core->FCarry) ? 1 : 0);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerE == 0);
    }

    /**
     * Cbopcode #0x04.
     *
     * @param Core $core
     */
    private static function cbopcode4(Core $core)
    {
        $core->FCarry = (($core->registersHL & 0x8000) == 0x8000);
        $core->registersHL = (($core->registersHL << 1) & 0xFE00) + (($core->FCarry) ? 0x100 : 0) + ($core->registersHL & 0xFF);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registersHL <= 0xFF);
    }

    /**
     * Cbopcode #0x05.
     *
     * @param Core $core
     */
    private static function cbopcode5(Core $core)
    {
        $core->FCarry = (($core->registersHL & 0x80) == 0x80);
        $core->registersHL = ($core->registersHL & 0xFF00) + (($core->registersHL << 1) & 0xFF) + (($core->FCarry) ? 1 : 0);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = (($core->registersHL & 0xFF) == 0x00);
    }

    /**
     * Cbopcode #0x06.
     *
     * @param Core $core
     */
    private static function cbopcode6(Core $core)
    {
        $temp_var = $core->memoryReader[$core->registersHL]($core, $core->registersHL);
        $core->FCarry = (($temp_var & 0x80) == 0x80);
        $temp_var = (($temp_var << 1) & 0xFF) + (($core->FCarry) ? 1 : 0);
        $core->memoryWrite($core->registersHL, $temp_var);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($temp_var == 0x00);
    }

    /**
     * Cbopcode #0x07.
     *
     * @param Core $core
     */
    private static function cbopcode7(Core $core)
    {
        $core->FCarry = (($core->registerA & 0x80) == 0x80);
        $core->registerA = (($core->registerA << 1) & 0xFF) + (($core->FCarry) ? 1 : 0);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerA == 0x00);
    }

    /**
     * Cbopcode #0x08.
     *
     * @param Core $core
     */
    private static function cbopcode8(Core $core)
    {
        $core->FCarry = (($core->registerB & 0x01) == 0x01);
        $core->registerB = (($core->FCarry) ? 0x80 : 0) + ($core->registerB >> 1);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerB == 0);
    }

    /**
     * Cbopcode #0x09.
     *
     * @param Core $core
     */
    private static function cbopcode9(Core $core)
    {
        $core->FCarry = (($core->registerC & 0x01) == 0x01);
        $core->registerC = (($core->FCarry) ? 0x80 : 0) + ($core->registerC >> 1);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerC == 0);
    }

    /**
     * Cbopcode #0x0A.
     *
     * @param Core $core
     */
    private static function cbopcode10(Core $core)
    {
        $core->FCarry = (($core->registerD & 0x01) == 0x01);
        $core->registerD = (($core->FCarry) ? 0x80 : 0) + ($core->registerD >> 1);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerD == 0);
    }

    /**
     * Cbopcode #0x0B.
     *
     * @param Core $core
     */
    private static function cbopcode11(Core $core)
    {
        $core->FCarry = (($core->registerE & 0x01) == 0x01);
        $core->registerE = (($core->FCarry) ? 0x80 : 0) + ($core->registerE >> 1);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerE == 0);
    }

    /**
     * Cbopcode #0x0C.
     *
     * @param Core $core
     */
    private static function cbopcode12(Core $core)
    {
        $core->FCarry = (($core->registersHL & 0x0100) == 0x0100);
        $core->registersHL = (($core->FCarry) ? 0x8000 : 0) + (($core->registersHL >> 1) & 0xFF00) + ($core->registersHL & 0xFF);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registersHL <= 0xFF);
    }

    /**
     * Cbopcode #0x0D.
     *
     * @param Core $core
     */
    private static function cbopcode13(Core $core)
    {
        $core->FCarry = (($core->registersHL & 0x01) == 0x01);
        $core->registersHL = ($core->registersHL & 0xFF00) + (($core->FCarry) ? 0x80 : 0) + (($core->registersHL & 0xFF) >> 1);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = (($core->registersHL & 0xFF) == 0x00);
    }

    /**
     * Cbopcode #0x0E.
     *
     * @param Core $core
     */
    private static function cbopcode14(Core $core)
    {
        $temp_var = $core->memoryReader[$core->registersHL]($core, $core->registersHL);
        $core->FCarry = (($temp_var & 0x01) == 0x01);
        $temp_var = (($core->FCarry) ? 0x80 : 0) + ($temp_var >> 1);
        $core->memoryWrite($core->registersHL, $temp_var);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($temp_var == 0x00);
    }

    /**
     * Cbopcode #0x0F.
     *
     * @param Core $core
     */
    private static function cbopcode15(Core $core)
    {
        $core->FCarry = (($core->registerA & 0x01) == 0x01);
        $core->registerA = (($core->FCarry) ? 0x80 : 0) + ($core->registerA >> 1);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerA == 0x00);
    }

    /**
     * Cbopcode #0x10.
     *
     * @param Core $core
     */
    private static function cbopcode16(Core $core)
    {
        $newFCarry = (($core->registerB & 0x80) == 0x80);
        $core->registerB = (($core->registerB << 1) & 0xFF) + (($core->FCarry) ? 1 : 0);
        $core->FCarry = $newFCarry;
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerB == 0);
    }

    /**
     * Cbopcode #0x11.
     *
     * @param Core $core
     */
    private static function cbopcode17(Core $core)
    {
        $newFCarry = (($core->registerC & 0x80) == 0x80);
        $core->registerC = (($core->registerC << 1) & 0xFF) + (($core->FCarry) ? 1 : 0);
        $core->FCarry = $newFCarry;
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerC == 0);
    }

    /**
     * Cbopcode #0x12.
     *
     * @param Core $core
     */
    private static function cbopcode18(Core $core)
    {
        $newFCarry = (($core->registerD & 0x80) == 0x80);
        $core->registerD = (($core->registerD << 1) & 0xFF) + (($core->FCarry) ? 1 : 0);
        $core->FCarry = $newFCarry;
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerD == 0);
    }

    /**
     * Cbopcode #0x13.
     *
     * @param Core $core
     */
    private static function cbopcode19(Core $core)
    {
        $newFCarry = (($core->registerE & 0x80) == 0x80);
        $core->registerE = (($core->registerE << 1) & 0xFF) + (($core->FCarry) ? 1 : 0);
        $core->FCarry = $newFCarry;
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerE == 0);
    }

    /**
     * Cbopcode #0x14.
     *
     * @param Core $core
     */
    private static function cbopcode20(Core $core)
    {
        $newFCarry = (($core->registersHL & 0x8000) == 0x8000);
        $core->registersHL = (($core->registersHL << 1) & 0xFE00) + (($core->FCarry) ? 0x100 : 0) + ($core->registersHL & 0xFF);
        $core->FCarry = $newFCarry;
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registersHL <= 0xFF);
    }

    /**
     * Cbopcode #0x15.
     *
     * @param Core $core
     */
    private static function cbopcode21(Core $core)
    {
        $newFCarry = (($core->registersHL & 0x80) == 0x80);
        $core->registersHL = ($core->registersHL & 0xFF00) + (($core->registersHL << 1) & 0xFF) + (($core->FCarry) ? 1 : 0);
        $core->FCarry = $newFCarry;
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = (($core->registersHL & 0xFF) == 0x00);
    }

    /**
     * Cbopcode #0x16.
     *
     * @param Core $core
     */
    private static function cbopcode22(Core $core)
    {
        $temp_var = $core->memoryReader[$core->registersHL]($core, $core->registersHL);
        $newFCarry = (($temp_var & 0x80) == 0x80);
        $temp_var = (($temp_var << 1) & 0xFF) + (($core->FCarry) ? 1 : 0);
        $core->FCarry = $newFCarry;
        $core->memoryWrite($core->registersHL, $temp_var);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($temp_var == 0x00);
    }

    /**
     * Cbopcode #0x17.
     *
     * @param Core $core
     */
    private static function cbopcode23(Core $core)
    {
        $newFCarry = (($core->registerA & 0x80) == 0x80);
        $core->registerA = (($core->registerA << 1) & 0xFF) + (($core->FCarry) ? 1 : 0);
        $core->FCarry = $newFCarry;
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerA == 0x00);
    }

    /**
     * Cbopcode #0x18.
     *
     * @param Core $core
     */
    private static function cbopcode24(Core $core)
    {
        $newFCarry = (($core->registerB & 0x01) == 0x01);
        $core->registerB = (($core->FCarry) ? 0x80 : 0) + ($core->registerB >> 1);
        $core->FCarry = $newFCarry;
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerB == 0);
    }

    /**
     * Cbopcode #0x19.
     *
     * @param Core $core
     */
    private static function cbopcode25(Core $core)
    {
        $newFCarry = (($core->registerC & 0x01) == 0x01);
        $core->registerC = (($core->FCarry) ? 0x80 : 0) + ($core->registerC >> 1);
        $core->FCarry = $newFCarry;
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerC == 0);
    }

    /**
     * Cbopcode #0x1A.
     *
     * @param Core $core
     */
    private static function cbopcode26(Core $core)
    {
        $newFCarry = (($core->registerD & 0x01) == 0x01);
        $core->registerD = (($core->FCarry) ? 0x80 : 0) + ($core->registerD >> 1);
        $core->FCarry = $newFCarry;
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerD == 0);
    }

    /**
     * Cbopcode #0x1B.
     *
     * @param Core $core
     */
    private static function cbopcode27(Core $core)
    {
        $newFCarry = (($core->registerE & 0x01) == 0x01);
        $core->registerE = (($core->FCarry) ? 0x80 : 0) + ($core->registerE >> 1);
        $core->FCarry = $newFCarry;
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerE == 0);
    }

    /**
     * Cbopcode #0x1C.
     *
     * @param Core $core
     */
    private static function cbopcode28(Core $core)
    {
        $newFCarry = (($core->registersHL & 0x0100) == 0x0100);
        $core->registersHL = (($core->FCarry) ? 0x8000 : 0) + (($core->registersHL >> 1) & 0xFF00) + ($core->registersHL & 0xFF);
        $core->FCarry = $newFCarry;
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registersHL <= 0xFF);
    }

    /**
     * Cbopcode #0x1D.
     *
     * @param Core $core
     */
    private static function cbopcode29(Core $core)
    {
        $newFCarry = (($core->registersHL & 0x01) == 0x01);
        $core->registersHL = ($core->registersHL & 0xFF00) + (($core->FCarry) ? 0x80 : 0) + (($core->registersHL & 0xFF) >> 1);
        $core->FCarry = $newFCarry;
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = (($core->registersHL & 0xFF) == 0x00);
    }

    /**
     * Cbopcode #0x1E.
     *
     * @param Core $core
     */
    private static function cbopcode30(Core $core)
    {
        $temp_var = $core->memoryReader[$core->registersHL]($core, $core->registersHL);
        $newFCarry = (($temp_var & 0x01) == 0x01);
        $temp_var = (($core->FCarry) ? 0x80 : 0) + ($temp_var >> 1);
        $core->FCarry = $newFCarry;
        $core->memoryWrite($core->registersHL, $temp_var);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($temp_var == 0x00);
    }

    /**
     * Cbopcode #0x1F.
     *
     * @param Core $core
     */
    private static function cbopcode31(Core $core)
    {
        $newFCarry = (($core->registerA & 0x01) == 0x01);
        $core->registerA = (($core->FCarry) ? 0x80 : 0) + ($core->registerA >> 1);
        $core->FCarry = $newFCarry;
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerA == 0x00);
    }

    /**
     * Cbopcode #0x20.
     *
     * @param Core $core
     */
    private static function cbopcode32(Core $core)
    {
        $core->FCarry = (($core->registerB & 0x80) == 0x80);
        $core->registerB = ($core->registerB << 1) & 0xFF;
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerB == 0);
    }

    /**
     * Cbopcode #0x21.
     *
     * @param Core $core
     */
    private static function cbopcode33(Core $core)
    {
        $core->FCarry = (($core->registerC & 0x80) == 0x80);
        $core->registerC = ($core->registerC << 1) & 0xFF;
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerC == 0);
    }

    /**
     * Cbopcode #0x22.
     *
     * @param Core $core
     */
    private static function cbopcode34(Core $core)
    {
        $core->FCarry = (($core->registerD & 0x80) == 0x80);
        $core->registerD = ($core->registerD << 1) & 0xFF;
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerD == 0);
    }

    /**
     * Cbopcode #0x23.
     *
     * @param Core $core
     */
    private static function cbopcode35(Core $core)
    {
        $core->FCarry = (($core->registerE & 0x80) == 0x80);
        $core->registerE = ($core->registerE << 1) & 0xFF;
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerE == 0);
    }

    /**
     * Cbopcode #0x24.
     *
     * @param Core $core
     */
    private static function cbopcode36(Core $core)
    {
        $core->FCarry = (($core->registersHL & 0x8000) == 0x8000);
        $core->registersHL = (($core->registersHL << 1) & 0xFE00) + ($core->registersHL & 0xFF);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registersHL <= 0xFF);
    }

    /**
     * Cbopcode #0x25.
     *
     * @param Core $core
     */
    private static function cbopcode37(Core $core)
    {
        $core->FCarry = (($core->registersHL & 0x0080) == 0x0080);
        $core->registersHL = ($core->registersHL & 0xFF00) + (($core->registersHL << 1) & 0xFF);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = (($core->registersHL & 0xFF) == 0x00);
    }

    /**
     * Cbopcode #0x26.
     *
     * @param Core $core
     */
    private static function cbopcode38(Core $core)
    {
        $temp_var = $core->memoryReader[$core->registersHL]($core, $core->registersHL);
        $core->FCarry = (($temp_var & 0x80) == 0x80);
        $temp_var = ($temp_var << 1) & 0xFF;
        $core->memoryWrite($core->registersHL, $temp_var);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($temp_var == 0x00);
    }

    /**
     * Cbopcode #0x27.
     *
     * @param Core $core
     */
    private static function cbopcode39(Core $core)
    {
        $core->FCarry = (($core->registerA & 0x80) == 0x80);
        $core->registerA = ($core->registerA << 1) & 0xFF;
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerA == 0x00);
    }

    /**
     * Cbopcode #0x28.
     *
     * @param Core $core
     */
    private static function cbopcode40(Core $core)
    {
        $core->FCarry = (($core->registerB & 0x01) == 0x01);
        $core->registerB = ($core->registerB & 0x80) + ($core->registerB >> 1);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerB == 0);
    }

    /**
     * Cbopcode #0x29.
     *
     * @param Core $core
     */
    private static function cbopcode41(Core $core)
    {
        $core->FCarry = (($core->registerC & 0x01) == 0x01);
        $core->registerC = ($core->registerC & 0x80) + ($core->registerC >> 1);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerC == 0);
    }

    /**
     * Cbopcode #0x2A.
     *
     * @param Core $core
     */
    private static function cbopcode42(Core $core)
    {
        $core->FCarry = (($core->registerD & 0x01) == 0x01);
        $core->registerD = ($core->registerD & 0x80) + ($core->registerD >> 1);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerD == 0);
    }

    /**
     * Cbopcode #0x2B.
     *
     * @param Core $core
     */
    private static function cbopcode43(Core $core)
    {
        $core->FCarry = (($core->registerE & 0x01) == 0x01);
        $core->registerE = ($core->registerE & 0x80) + ($core->registerE >> 1);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerE == 0);
    }

    /**
     *
     * Cbopcode #0x2C.
     *
     * @param Core $core
     */
    private static function cbopcode44(Core $core)
    {
        $core->FCarry = (($core->registersHL & 0x0100) == 0x0100);
        $core->registersHL = (($core->registersHL >> 1) & 0xFF00) + ($core->registersHL & 0x80FF);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registersHL <= 0xFF);
    }

    /**
     * Cbopcode #0x2D.
     *
     * @param Core $core
     */
    private static function cbopcode45(Core $core)
    {
        $core->FCarry = (($core->registersHL & 0x0001) == 0x0001);
        $core->registersHL = ($core->registersHL & 0xFF80) + (($core->registersHL & 0xFF) >> 1);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = (($core->registersHL & 0xFF) == 0x00);
    }

    /**
     * Cbopcode #0x2E.
     *
     * @param Core $core
     */
    private static function cbopcode46(Core $core)
    {
        $temp_var = $core->memoryReader[$core->registersHL]($core, $core->registersHL);
        $core->FCarry = (($temp_var & 0x01) == 0x01);
        $temp_var = ($temp_var & 0x80) + ($temp_var >> 1);
        $core->memoryWrite($core->registersHL, $temp_var);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($temp_var == 0x00);
    }

    /**
     * Cbopcode #0x2F.
     *
     * @param Core $core
     */
    private static function cbopcode47(Core $core)
    {
        $core->FCarry = (($core->registerA & 0x01) == 0x01);
        $core->registerA = ($core->registerA & 0x80) + ($core->registerA >> 1);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerA == 0x00);
    }

    /**
     * Cbopcode #0x30.
     *
     * @param Core $core
     */
    private static function cbopcode48(Core $core)
    {
        $core->registerB = (($core->registerB & 0xF) << 4) + ($core->registerB >> 4);
        $core->FZero = ($core->registerB == 0);
        $core->FCarry = $core->FHalfCarry = $core->FSubtract = false;
    }

    /**
     * Cbopcode #0x31.
     *
     * @param Core $core
     */
    private static function cbopcode49(Core $core)
    {
        $core->registerC = (($core->registerC & 0xF) << 4) + ($core->registerC >> 4);
        $core->FZero = ($core->registerC == 0);
        $core->FCarry = $core->FHalfCarry = $core->FSubtract = false;
    }

    /**
     * Cbopcode #0x32.
     *
     * @param Core $core
     */
    private static function cbopcode50(Core $core)
    {
        $core->registerD = (($core->registerD & 0xF) << 4) + ($core->registerD >> 4);
        $core->FZero = ($core->registerD == 0);
        $core->FCarry = $core->FHalfCarry = $core->FSubtract = false;
    }

    /**
     * Cbopcode #0x33.
     *
     * @param Core $core
     */
    private static function cbopcode51(Core $core)
    {
        $core->registerE = (($core->registerE & 0xF) << 4) + ($core->registerE >> 4);
        $core->FZero = ($core->registerE == 0);
        $core->FCarry = $core->FHalfCarry = $core->FSubtract = false;
    }

    /**
     * Cbopcode #0x34.
     *
     * @param Core $core
     */
    private static function cbopcode52(Core $core)
    {
        $core->registersHL = (($core->registersHL & 0xF00) << 4) + (($core->registersHL & 0xF000) >> 4) + ($core->registersHL & 0xFF);
        $core->FZero = ($core->registersHL <= 0xFF);
        $core->FCarry = $core->FHalfCarry = $core->FSubtract = false;
    }

    /**
     * Cbopcode #0x35.
     *
     * @param Core $core
     */
    private static function cbopcode53(Core $core)
    {
        $core->registersHL = ($core->registersHL & 0xFF00) + (($core->registersHL & 0xF) << 4) + (($core->registersHL & 0xF0) >> 4);
        $core->FZero = (($core->registersHL & 0xFF) == 0);
        $core->FCarry = $core->FHalfCarry = $core->FSubtract = false;
    }

    /**
     * Cbopcode #0x36.
     *
     * @param Core $core
     */
    private static function cbopcode54(Core $core)
    {
        $temp_var = $core->memoryReader[$core->registersHL]($core, $core->registersHL);
        $temp_var = (($temp_var & 0xF) << 4) + ($temp_var >> 4);
        $core->memoryWrite($core->registersHL, $temp_var);
        $core->FZero = ($temp_var == 0);
        $core->FCarry = $core->FHalfCarry = $core->FSubtract = false;
    }

    /**
     * Cbopcode #0x37.
     *
     * @param Core $core
     */
    private static function cbopcode55(Core $core)
    {
        $core->registerA = (($core->registerA & 0xF) << 4) + ($core->registerA >> 4);
        $core->FZero = ($core->registerA == 0);
        $core->FCarry = $core->FHalfCarry = $core->FSubtract = false;
    }

    /**
     * Cbopcode #0x38.
     *
     * @param Core $core
     */
    private static function cbopcode56(Core $core)
    {
        $core->FCarry = (($core->registerB & 0x01) == 0x01);
        $core->registerB >>= 1;
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerB == 0);
    }

    /**
     * Cbopcode #0x39.
     *
     * @param Core $core
     */
    private static function cbopcode57(Core $core)
    {
        $core->FCarry = (($core->registerC & 0x01) == 0x01);
        $core->registerC >>= 1;
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerC == 0);
    }

    /**
     * Cbopcode #0x3A.
     *
     * @param Core $core
     */
    private static function cbopcode58(Core $core)
    {
        $core->FCarry = (($core->registerD & 0x01) == 0x01);
        $core->registerD >>= 1;
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerD == 0);
    }

    /**
     * Cbopcode #0x3B.
     *
     * @param Core $core
     */
    private static function cbopcode59(Core $core)
    {
        $core->FCarry = (($core->registerE & 0x01) == 0x01);
        $core->registerE >>= 1;
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerE == 0);
    }

    /**
     * Cbopcode #0x3C.
     *
     * @param Core $core
     */
    private static function cbopcode60(Core $core)
    {
        $core->FCarry = (($core->registersHL & 0x0100) == 0x0100);
        $core->registersHL = (($core->registersHL >> 1) & 0xFF00) + ($core->registersHL & 0xFF);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registersHL <= 0xFF);
    }

    /**
     * Cbopcode #0x3D.
     *
     * @param Core $core
     */
    private static function cbopcode61(Core $core)
    {
        $core->FCarry = (($core->registersHL & 0x0001) == 0x0001);
        $core->registersHL = ($core->registersHL & 0xFF00) + (($core->registersHL & 0xFF) >> 1);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = (($core->registersHL & 0xFF) == 0x00);
    }

    /**
     * Cbopcode #0x3E.
     *
     * @param Core $core
     */
    private static function cbopcode62(Core $core)
    {
        $temp_var = $core->memoryReader[$core->registersHL]($core, $core->registersHL);
        $core->FCarry = (($temp_var & 0x01) == 0x01);
        $core->memoryWrite($core->registersHL, $temp_var >>= 1);
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($temp_var == 0x00);
    }

    /**
     * Cbopcode #0x3F.
     *
     * @param Core $core
     */
    private static function cbopcode63(Core $core)
    {
        $core->FCarry = (($core->registerA & 0x01) == 0x01);
        $core->registerA >>= 1;
        $core->FHalfCarry = $core->FSubtract = false;
        $core->FZero = ($core->registerA == 0x00);
    }

    /**
     * Cbopcode #0x40.
     *
     * @param Core $core
     */
    private static function cbopcode64(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerB & 0x01) == 0);
    }

    /**
     * Cbopcode #0x41.
     *
     * @param Core $core
     */
    private static function cbopcode65(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerC & 0x01) == 0);
    }

    /**
     * Cbopcode #0x42.
     *
     * @param Core $core
     */
    private static function cbopcode66(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerD & 0x01) == 0);
    }

    /**
     * Cbopcode #0x43.
     *
     * @param Core $core
     */
    private static function cbopcode67(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerE & 0x01) == 0);
    }

    /**
     * Cbopcode #0x44.
     *
     * @param Core $core
     */
    private static function cbopcode68(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registersHL & 0x0100) == 0);
    }

    /**
     * Cbopcode #0x45.
     *
     * @param Core $core
     */
    private static function cbopcode69(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registersHL & 0x0001) == 0);
    }

    /**
     * Cbopcode #0x46.
     *
     * @param Core $core
     */
    private static function cbopcode70(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->memoryReader[$core->registersHL]($core, $core->registersHL) & 0x01) == 0);
    }

    /**
     * Cbopcode #0x47.
     *
     * @param Core $core
     */
    private static function cbopcode71(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerA & 0x01) == 0);
    }

    /**
     * Cbopcode #0x48.
     *
     * @param Core $core
     */
    private static function cbopcode72(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerB & 0x02) == 0);
    }

    /**
     * Cbopcode #0x49.
     *
     * @param Core $core
     */
    private static function cbopcode73(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerC & 0x02) == 0);
    }

    /**
     * Cbopcode #0x4A.
     *
     * @param Core $core
     */
    private static function cbopcode74(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerD & 0x02) == 0);
    }

    /**
     * Cbopcode #0x4B.
     *
     * @param Core $core
     */
    private static function cbopcode75(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerE & 0x02) == 0);
    }

    /**
     * Cbopcode #0x4C.
     *
     * @param Core $core
     */
    private static function cbopcode76(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registersHL & 0x0200) == 0);
    }

    /**
     * Cbopcode #0x4D.
     *
     * @param Core $core
     */
    private static function cbopcode77(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registersHL & 0x0002) == 0);
    }

    /**
     * Cbopcode #0x4E.
     *
     * @param Core $core
     */
    private static function cbopcode78(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->memoryReader[$core->registersHL]($core, $core->registersHL) & 0x02) == 0);
    }

    /**
     * Cbopcode #0x4F.
     *
     * @param Core $core
     */
    private static function cbopcode79(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerA & 0x02) == 0);
    }

    /**
     * Cbopcode #0x50.
     *
     * @param Core $core
     */
    private static function cbopcode80(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerB & 0x04) == 0);
    }

    /**
     * Cbopcode #0x51.
     *
     * @param Core $core
     */
    private static function cbopcode81(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerC & 0x04) == 0);
    }

    /**
     * Cbopcode #0x52.
     *
     * @param Core $core
     */
    private static function cbopcode82(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerD & 0x04) == 0);
    }

    /**
     * Cbopcode #0x53.
     *
     * @param Core $core
     */
    private static function cbopcode83(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerE & 0x04) == 0);
    }

    /**
     * Cbopcode #0x54.
     *
     * @param Core $core
     */
    private static function cbopcode84(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registersHL & 0x0400) == 0);
    }

    /**
     * Cbopcode #0x55.
     *
     * @param Core $core
     */
    private static function cbopcode85(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registersHL & 0x0004) == 0);
    }

    /**
     * Cbopcode #0x56.
     *
     * @param Core $core
     */
    private static function cbopcode86(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->memoryReader[$core->registersHL]($core, $core->registersHL) & 0x04) == 0);
    }

    /**
     * Cbopcode #0x57.
     *
     * @param Core $core
     */
    private static function cbopcode87(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerA & 0x04) == 0);
    }

    /**
     * Cbopcode #0x58.
     *
     * @param Core $core
     */
    private static function cbopcode88(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerB & 0x08) == 0);
    }

    /**
     * Cbopcode #0x59.
     *
     * @param Core $core
     */
    private static function cbopcode89(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerC & 0x08) == 0);
    }

    /**
     * Cbopcode #0x5A.
     *
     * @param Core $core
     */
    private static function cbopcode90(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerD & 0x08) == 0);
    }

    /**
     * Cbopcode #0x5B.
     *
     * @param Core $core
     */
    private static function cbopcode91(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerE & 0x08) == 0);
    }

    /**
     * Cbopcode #0x5C.
     *
     * @param Core $core
     */
    private static function cbopcode92(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registersHL & 0x0800) == 0);
    }

    /**
     * Cbopcode #0x5D.
     *
     * @param Core $core
     */
    private static function cbopcode93(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registersHL & 0x0008) == 0);
    }

    /**
     * Cbopcode #0x5E.
     *
     * @param Core $core
     */
    private static function cbopcode94(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->memoryReader[$core->registersHL]($core, $core->registersHL) & 0x08) == 0);
    }

    /**
     * Cbopcode #0x5F.
     *
     * @param Core $core
     */
    private static function cbopcode95(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerA & 0x08) == 0);
    }

    /**
     * Cbopcode #0x60.
     *
     * @param Core $core
     */
    private static function cbopcode96(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerB & 0x10) == 0);
    }

    /**
     * Cbopcode #0x61.
     *
     * @param Core $core
     */
    private static function cbopcode97(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerC & 0x10) == 0);
    }

    /**
     * Cbopcode #0x62.
     *
     * @param Core $core
     */
    private static function cbopcode98(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerD & 0x10) == 0);
    }

    /**
     * Cbopcode #0x63.
     *
     * @param Core $core
     */
    private static function cbopcode99(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerE & 0x10) == 0);
    }

    /**
     * Cbopcode #0x64.
     *
     * @param Core $core
     */
    private static function cbopcode100(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registersHL & 0x1000) == 0);
    }

    /**
     * Cbopcode #0x65.
     *
     * @param Core $core
     */
    private static function cbopcode101(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registersHL & 0x0010) == 0);
    }

    /**
     * Cbopcode #0x66.
     *
     * @param Core $core
     */
    private static function cbopcode102(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->memoryReader[$core->registersHL]($core, $core->registersHL) & 0x10) == 0);
    }

    /**
     * Cbopcode #0x67.
     *
     * @param Core $core
     */
    private static function cbopcode103(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerA & 0x10) == 0);
    }

    /**
     * Cbopcode #0x68.
     *
     * @param Core $core
     */
    private static function cbopcode104(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerB & 0x20) == 0);
    }

    /**
     * Cbopcode #0x69.
     *
     * @param Core $core
     */
    private static function cbopcode105(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerC & 0x20) == 0);
    }

    /**
     * Cbopcode #0x6A.
     *
     * @param Core $core
     */
    private static function cbopcode106(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerD & 0x20) == 0);
    }

    /**
     * Cbopcode #0x6B.
     *
     * @param Core $core
     */
    private static function cbopcode107(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerE & 0x20) == 0);
    }

    /**
     * Cbopcode #0x6C.
     *
     * @param Core $core
     */
    private static function cbopcode108(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registersHL & 0x2000) == 0);
    }

    /**
     * Cbopcode #0x6D.
     *
     * @param Core $core
     */
    private static function cbopcode109(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registersHL & 0x0020) == 0);
    }

    /**
     * Cbopcode #0x6E.
     *
     * @param Core $core
     */
    private static function cbopcode110(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->memoryReader[$core->registersHL]($core, $core->registersHL) & 0x20) == 0);
    }

    /**
     * Cbopcode #0x6F.
     *
     * @param Core $core
     */
    private static function cbopcode111(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerA & 0x20) == 0);
    }

    /**
     * Cbopcode #0x70.
     *
     * @param Core $core
     */
    private static function cbopcode112(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerB & 0x40) == 0);
    }

    /**
     * Cbopcode #0x71.
     *
     * @param Core $core
     */
    private static function cbopcode113(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerC & 0x40) == 0);
    }

    /**
     * Cbopcode #0x72.
     *
     * @param Core $core
     */
    private static function cbopcode114(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerD & 0x40) == 0);
    }

    /**
     * Cbopcode #0x73.
     *
     * @param Core $core
     */
    private static function cbopcode115(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerE & 0x40) == 0);
    }

    /**
     * Cbopcode #0x74.
     *
     * @param Core $core
     */
    private static function cbopcode116(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registersHL & 0x4000) == 0);
    }

    /**
     * Cbopcode #0x75.
     *
     * @param Core $core
     */
    private static function cbopcode117(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registersHL & 0x0040) == 0);
    }

    /**
     * Cbopcode #0x76.
     *
     * @param Core $core
     */
    private static function cbopcode118(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->memoryReader[$core->registersHL]($core, $core->registersHL) & 0x40) == 0);
    }

    /**
     * Cbopcode #0x77.
     *
     * @param Core $core
     */
    private static function cbopcode119(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerA & 0x40) == 0);
    }

    /**
     * Cbopcode #0x78.
     *
     * @param Core $core
     */
    private static function cbopcode120(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerB & 0x80) == 0);
    }

    /**
     * Cbopcode #0x79.
     *
     * @param Core $core
     */
    private static function cbopcode121(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerC & 0x80) == 0);
    }

    /**
     * Cbopcode #0x7A.
     *
     * @param Core $core
     */
    private static function cbopcode122(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerD & 0x80) == 0);
    }

    /**
     * Cbopcode #0x7B.
     *
     * @param Core $core
     */
    private static function cbopcode123(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerE & 0x80) == 0);
    }

    /**
     * Cbopcode #0x7C.
     *
     * @param Core $core
     */
    private static function cbopcode124(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registersHL & 0x8000) == 0);
    }

    /**
     * Cbopcode #0x7D.
     *
     * @param Core $core
     */
    private static function cbopcode125(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registersHL & 0x0080) == 0);
    }

    /**
     * Cbopcode #0x7E.
     *
     * @param Core $core
     */
    private static function cbopcode126(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->memoryReader[$core->registersHL]($core, $core->registersHL) & 0x80) == 0);
    }

    /**
     * Cbopcode #0x7F.
     *
     * @param Core $core
     */
    private static function cbopcode127(Core $core)
    {
        $core->FHalfCarry = true;
        $core->FSubtract = false;
        $core->FZero = (($core->registerA & 0x80) == 0);
    }

    /**
     * Cbopcode #0x80.
     *
     * @param Core $core
     */
    private static function cbopcode128(Core $core)
    {
        $core->registerB &= 0xFE;
    }

    /**
     * Cbopcode #0x81.
     *
     * @param Core $core
     */
    private static function cbopcode129(Core $core)
    {
        $core->registerC &= 0xFE;
    }

    /**
     * Cbopcode #0x82.
     *
     * @param Core $core
     */
    private static function cbopcode130(Core $core)
    {
        $core->registerD &= 0xFE;
    }

    /**
     * Cbopcode #0x83.
     *
     * @param Core $core
     */
    private static function cbopcode131(Core $core)
    {
        $core->registerE &= 0xFE;
    }

    /**
     * Cbopcode #0x84.
     *
     * @param Core $core
     */
    private static function cbopcode132(Core $core)
    {
        $core->registersHL &= 0xFEFF;
    }

    /**
     * Cbopcode #0x85.
     *
     * @param Core $core
     */
    private static function cbopcode133(Core $core)
    {
        $core->registersHL &= 0xFFFE;
    }

    /**
     * Cbopcode #0x86.
     *
     * @param Core $core
     */
    private static function cbopcode134(Core $core)
    {
        $core->memoryWrite($core->registersHL, $core->memoryReader[$core->registersHL]($core, $core->registersHL) & 0xFE);
    }

    /**
     * Cbopcode #0x87.
     *
     * @param Core $core
     */
    private static function cbopcode135(Core $core)
    {
        $core->registerA &= 0xFE;
    }

    /**
     * Cbopcode #0x88.
     *
     * @param Core $core
     */
    private static function cbopcode136(Core $core)
    {
        $core->registerB &= 0xFD;
    }

    /**
     * Cbopcode #0x89.
     *
     * @param Core $core
     */
    private static function cbopcode137(Core $core)
    {
        $core->registerC &= 0xFD;
    }

    /**
     * Cbopcode #0x8A.
     *
     * @param Core $core
     */
    private static function cbopcode138(Core $core)
    {
        $core->registerD &= 0xFD;
    }

    /**
     * Cbopcode #0x8B.
     *
     * @param Core $core
     */
    private static function cbopcode139(Core $core)
    {
        $core->registerE &= 0xFD;
    }

    /**
     * Cbopcode #0x8C.
     *
     * @param Core $core
     */
    private static function cbopcode140(Core $core)
    {
        $core->registersHL &= 0xFDFF;
    }

    /**
     * Cbopcode #0x8D.
     *
     * @param Core $core
     */
    private static function cbopcode141(Core $core)
    {
        $core->registersHL &= 0xFFFD;
    }

    /**
     * Cbopcode #0x8E.
     *
     * @param Core $core
     */
    private static function cbopcode142(Core $core)
    {
        $core->memoryWrite($core->registersHL, $core->memoryReader[$core->registersHL]($core, $core->registersHL) & 0xFD);
    }

    /**
     * Cbopcode #0x8F.
     *
     * @param Core $core
     */
    private static function cbopcode143(Core $core)
    {
        $core->registerA &= 0xFD;
    }

    /**
     * Cbopcode #0x90.
     *
     * @param Core $core
     */
    private static function cbopcode144(Core $core)
    {
        $core->registerB &= 0xFB;
    }

    /**
     * Cbopcode #0x91.
     *
     * @param Core $core
     */
    private static function cbopcode145(Core $core)
    {
        $core->registerC &= 0xFB;
    }

    /**
     * Cbopcode #0x92.
     *
     * @param Core $core
     */
    private static function cbopcode146(Core $core)
    {
        $core->registerD &= 0xFB;
    }

    /**
     * Cbopcode #0x93.
     *
     * @param Core $core
     */
    private static function cbopcode147(Core $core)
    {
        $core->registerE &= 0xFB;
    }

    /**
     * Cbopcode #0x94.
     *
     * @param Core $core
     */
    private static function cbopcode148(Core $core)
    {
        $core->registersHL &= 0xFBFF;
    }

    /**
     * Cbopcode #0x95.
     *
     * @param Core $core
     */
    private static function cbopcode149(Core $core)
    {
        $core->registersHL &= 0xFFFB;
    }

    /**
     * Cbopcode #0x96.
     *
     * @param Core $core
     */
    private static function cbopcode150(Core $core)
    {
        $core->memoryWrite($core->registersHL, $core->memoryReader[$core->registersHL]($core, $core->registersHL) & 0xFB);
    }

    /**
     * Cbopcode #0x97.
     *
     * @param Core $core
     */
    private static function cbopcode151(Core $core)
    {
        $core->registerA &= 0xFB;
    }

    /**
     * Cbopcode #0x98.
     *
     * @param Core $core
     */
    private static function cbopcode152(Core $core)
    {
        $core->registerB &= 0xF7;
    }

    /**
     * Cbopcode #0x99.
     *
     * @param Core $core
     */
    private static function cbopcode153(Core $core)
    {
        $core->registerC &= 0xF7;
    }

    /**
     * Cbopcode #0x9A.
     *
     * @param Core $core
     */
    private static function cbopcode154(Core $core)
    {
        $core->registerD &= 0xF7;
    }

    /**
     * Cbopcode #0x9B.
     *
     * @param Core $core
     */
    private static function cbopcode155(Core $core)
    {
        $core->registerE &= 0xF7;
    }

    /**
     * Cbopcode #0x9C.
     *
     * @param Core $core
     */
    private static function cbopcode156(Core $core)
    {
        $core->registersHL &= 0xF7FF;
    }

    /**
     * Cbopcode #0x9D.
     *
     * @param Core $core
     */
    private static function cbopcode157(Core $core)
    {
        $core->registersHL &= 0xFFF7;
    }

    /**
     * Cbopcode #0x9E.
     *
     * @param Core $core
     */
    private static function cbopcode158(Core $core)
    {
        $core->memoryWrite($core->registersHL, $core->memoryReader[$core->registersHL]($core, $core->registersHL) & 0xF7);
    }

    /**
     * Cbopcode #0x9F.
     *
     * @param Core $core
     */
    private static function cbopcode159(Core $core)
    {
        $core->registerA &= 0xF7;
    }

    /**
     * Cbopcode #0xA0.
     *
     * @param Core $core
     */
    private static function cbopcode160(Core $core)
    {
        $core->registerB &= 0xEF;
    }

    /**
     * Cbopcode #0xA1.
     *
     * @param Core $core
     */
    private static function cbopcode161(Core $core)
    {
        $core->registerC &= 0xEF;
    }

    /**
     * Cbopcode #0xA2.
     *
     * @param Core $core
     */
    private static function cbopcode162(Core $core)
    {
        $core->registerD &= 0xEF;
    }

    /**
     * Cbopcode #0xA3.
     *
     * @param Core $core
     */
    private static function cbopcode163(Core $core)
    {
        $core->registerE &= 0xEF;
    }

    /**
     * Cbopcode #0xA4.
     *
     * @param Core $core
     */
    private static function cbopcode164(Core $core)
    {
        $core->registersHL &= 0xEFFF;
    }

    /**
     * Cbopcode #0xA5.
     *
     * @param Core $core
     */
    private static function cbopcode165(Core $core)
    {
        $core->registersHL &= 0xFFEF;
    }

    /**
     * Cbopcode #0xA6.
     *
     * @param Core $core
     */
    private static function cbopcode166(Core $core)
    {
        $core->memoryWrite($core->registersHL, $core->memoryReader[$core->registersHL]($core, $core->registersHL) & 0xEF);
    }

    /**
     * Cbopcode #0xA7.
     *
     * @param Core $core
     */
    private static function cbopcode167(Core $core)
    {
        $core->registerA &= 0xEF;
    }

    /**
     * Cbopcode #0xA8.
     *
     * @param Core $core
     */
    private static function cbopcode168(Core $core)
    {
        $core->registerB &= 0xDF;
    }

    /**
     * Cbopcode #0xA9.
     *
     * @param Core $core
     */
    private static function cbopcode169(Core $core)
    {
        $core->registerC &= 0xDF;
    }

    /**
     * Cbopcode #0xAA.
     *
     * @param Core $core
     */
    private static function cbopcode170(Core $core)
    {
        $core->registerD &= 0xDF;
    }

    /**
     * Cbopcode #0xAB.
     *
     * @param Core $core
     */
    private static function cbopcode171(Core $core)
    {
        $core->registerE &= 0xDF;
    }

    /**
     * Cbopcode #0xAC.
     *
     * @param Core $core
     */
    private static function cbopcode172(Core $core)
    {
        $core->registersHL &= 0xDFFF;
    }

    /**
     * Cbopcode #0xAD.
     *
     * @param Core $core
     */
    private static function cbopcode173(Core $core)
    {
        $core->registersHL &= 0xFFDF;
    }

    /**
     * Cbopcode #0xAE.
     *
     * @param Core $core
     */
    private static function cbopcode174(Core $core)
    {
        $core->memoryWrite($core->registersHL, $core->memoryReader[$core->registersHL]($core, $core->registersHL) & 0xDF);
    }

    /**
     * Cbopcode #0xAF.
     *
     * @param Core $core
     */
    private static function cbopcode175(Core $core)
    {
        $core->registerA &= 0xDF;
    }

    /**
     * Cbopcode #0xB0.
     *
     * @param Core $core
     */
    private static function cbopcode176(Core $core)
    {
        $core->registerB &= 0xBF;
    }

    /**
     * Cbopcode #0xB1.
     *
     * @param Core $core
     */
    private static function cbopcode177(Core $core)
    {
        $core->registerC &= 0xBF;
    }

    /**
     * Cbopcode #0xB2.
     *
     * @param Core $core
     */
    private static function cbopcode178(Core $core)
    {
        $core->registerD &= 0xBF;
    }

    /**
     * Cbopcode #0xB3.
     *
     * @param Core $core
     */
    private static function cbopcode179(Core $core)
    {
        $core->registerE &= 0xBF;
    }

    /**
     * Cbopcode #0xB4.
     *
     * @param Core $core
     */
    private static function cbopcode180(Core $core)
    {
        $core->registersHL &= 0xBFFF;
    }

    /**
     * Cbopcode #0xB5.
     *
     * @param Core $core
     */
    private static function cbopcode181(Core $core)
    {
        $core->registersHL &= 0xFFBF;
    }

    /**
     * Cbopcode #0xB6.
     *
     * @param Core $core
     */
    private static function cbopcode182(Core $core)
    {
        $core->memoryWrite($core->registersHL, $core->memoryReader[$core->registersHL]($core, $core->registersHL) & 0xBF);
    }

    /**
     * Cbopcode #0xB7.
     *
     * @param Core $core
     */
    private static function cbopcode183(Core $core)
    {
        $core->registerA &= 0xBF;
    }

    /**
     * Cbopcode #0xB8.
     *
     * @param Core $core
     */
    private static function cbopcode184(Core $core)
    {
        $core->registerB &= 0x7F;
    }

    /**
     * Cbopcode #0xB9.
     *
     * @param Core $core
     */
    private static function cbopcode185(Core $core)
    {
        $core->registerC &= 0x7F;
    }

    /**
     * Cbopcode #0xBA.
     *
     * @param Core $core
     */
    private static function cbopcode186(Core $core)
    {
        $core->registerD &= 0x7F;
    }

    /**
     * Cbopcode #0xBB.
     *
     * @param Core $core
     */
    private static function cbopcode187(Core $core)
    {
        $core->registerE &= 0x7F;
    }

    /**
     * Cbopcode #0xBC.
     *
     * @param Core $core
     */
    private static function cbopcode188(Core $core)
    {
        $core->registersHL &= 0x7FFF;
    }

    /**
     * Cbopcode #0xBD.
     *
     * @param Core $core
     */
    private static function cbopcode189(Core $core)
    {
        $core->registersHL &= 0xFF7F;
    }

    /**
     * Cbopcode #0xBE.
     *
     * @param Core $core
     */
    private static function cbopcode190(Core $core)
    {
        $core->memoryWrite($core->registersHL, $core->memoryReader[$core->registersHL]($core, $core->registersHL) & 0x7F);
    }

    /**
     * Cbopcode #0xBF.
     *
     * @param Core $core
     */
    private static function cbopcode191(Core $core)
    {
        $core->registerA &= 0x7F;
    }

    /**
     * Cbopcode #0xC0.
     *
     * @param Core $core
     */
    private static function cbopcode192(Core $core)
    {
        $core->registerB |= 0x01;
    }

    /**
     * Cbopcode #0xC1.
     *
     * @param Core $core
     */
    private static function cbopcode193(Core $core)
    {
        $core->registerC |= 0x01;
    }

    /**
     * Cbopcode #0xC2.
     *
     * @param Core $core
     */
    private static function cbopcode194(Core $core)
    {
        $core->registerD |= 0x01;
    }

    /**
     * Cbopcode #0xC3.
     *
     * @param Core $core
     */
    private static function cbopcode195(Core $core)
    {
        $core->registerE |= 0x01;
    }

    /**
     * Cbopcode #0xC4.
     *
     * @param Core $core
     */
    private static function cbopcode196(Core $core)
    {
        $core->registersHL |= 0x0100;
    }

    /**
     * Cbopcode #0xC5.
     *
     * @param Core $core
     */
    private static function cbopcode197(Core $core)
    {
        $core->registersHL |= 0x01;
    }

    /**
     * Cbopcode #0xC6.
     *
     * @param Core $core
     */
    private static function cbopcode198(Core $core)
    {
        $core->memoryWrite($core->registersHL, $core->memoryReader[$core->registersHL]($core, $core->registersHL) | 0x01);
    }

    /**
     * Cbopcode #0xC7.
     *
     * @param Core $core
     */
    private static function cbopcode199(Core $core)
    {
        $core->registerA |= 0x01;
    }

    /**
     * Cbopcode #0xC8.
     *
     * @param Core $core
     */
    private static function cbopcode200(Core $core)
    {
        $core->registerB |= 0x02;
    }

    /**
     * Cbopcode #0xC9.
     *
     * @param Core $core
     */
    private static function cbopcode201(Core $core)
    {
        $core->registerC |= 0x02;
    }

    /**
     * Cbopcode #0xCA.
     *
     * @param Core $core
     */
    private static function cbopcode202(Core $core)
    {
        $core->registerD |= 0x02;
    }

    /**
     * Cbopcode #0xCB.
     *
     * @param Core $core
     */
    private static function cbopcode203(Core $core)
    {
        $core->registerE |= 0x02;
    }

    /**
     * Cbopcode #0xCC.
     *
     * @param Core $core
     */
    private static function cbopcode204(Core $core)
    {
        $core->registersHL |= 0x0200;
    }

    /**
     * Cbopcode #0xCD.
     *
     * @param Core $core
     */
    private static function cbopcode205(Core $core)
    {
        $core->registersHL |= 0x02;
    }

    /**
     * Cbopcode #0xCE.
     *
     * @param Core $core
     */
    private static function cbopcode206(Core $core)
    {
        $core->memoryWrite($core->registersHL, $core->memoryReader[$core->registersHL]($core, $core->registersHL) | 0x02);
    }

    /**
     * Cbopcode #0xCF.
     *
     * @param Core $core
     */
    private static function cbopcode207(Core $core)
    {
        $core->registerA |= 0x02;
    }

    /**
     * Cbopcode #0xD0.
     *
     * @param Core $core
     */
    private static function cbopcode208(Core $core)
    {
        $core->registerB |= 0x04;
    }

    /**
     * Cbopcode #0xD1.
     *
     * @param Core $core
     */
    private static function cbopcode209(Core $core)
    {
        $core->registerC |= 0x04;
    }

    /**
     * Cbopcode #0xD2.
     *
     * @param Core $core
     */
    private static function cbopcode210(Core $core)
    {
        $core->registerD |= 0x04;
    }

    /**
     * Cbopcode #0xD3.
     *
     * @param Core $core
     */
    private static function cbopcode211(Core $core)
    {
        $core->registerE |= 0x04;
    }

    /**
     * Cbopcode #0xD4.
     *
     * @param Core $core
     */
    private static function cbopcode212(Core $core)
    {
        $core->registersHL |= 0x0400;
    }

    /**
     * Cbopcode #0xD5.
     *
     * @param Core $core
     */
    private static function cbopcode213(Core $core)
    {
        $core->registersHL |= 0x04;
    }

    /**
     * Cbopcode #0xD6.
     *
     * @param Core $core
     */
    private static function cbopcode214(Core $core)
    {
        $core->memoryWrite($core->registersHL, $core->memoryReader[$core->registersHL]($core, $core->registersHL) | 0x04);
    }

    /**
     * Cbopcode #0xD7.
     *
     * @param Core $core
     */
    private static function cbopcode215(Core $core)
    {
        $core->registerA |= 0x04;
    }

    /**
     * Cbopcode #0xD8.
     *
     * @param Core $core
     */
    private static function cbopcode216(Core $core)
    {
        $core->registerB |= 0x08;
    }

    /**
     * Cbopcode #0xD9.
     *
     * @param Core $core
     */
    private static function cbopcode217(Core $core)
    {
        $core->registerC |= 0x08;
    }

    /**
     * Cbopcode #0xDA.
     *
     * @param Core $core
     */
    private static function cbopcode218(Core $core)
    {
        $core->registerD |= 0x08;
    }

    /**
     * Cbopcode #0xDB.
     *
     * @param Core $core
     */
    private static function cbopcode219(Core $core)
    {
        $core->registerE |= 0x08;
    }

    /**
     * Cbopcode #0xDC.
     *
     * @param Core $core
     */
    private static function cbopcode220(Core $core)
    {
        $core->registersHL |= 0x0800;
    }

    /**
     * Cbopcode #0xDD.
     *
     * @param Core $core
     */
    private static function cbopcode221(Core $core)
    {
        $core->registersHL |= 0x08;
    }

    /**
     * Cbopcode #0xDE.
     *
     * @param Core $core
     */
    private static function cbopcode222(Core $core)
    {
        $core->memoryWrite($core->registersHL, $core->memoryReader[$core->registersHL]($core, $core->registersHL) | 0x08);
    }

    /**
     * Cbopcode #0xDF.
     *
     * @param Core $core
     */
    private static function cbopcode223(Core $core)
    {
        $core->registerA |= 0x08;
    }

    /**
     * Cbopcode #0xE0.
     *
     * @param Core $core
     */
    private static function cbopcode224(Core $core)
    {
        $core->registerB |= 0x10;
    }

    /**
     * Cbopcode #0xE1.
     *
     * @param Core $core
     */
    private static function cbopcode225(Core $core)
    {
        $core->registerC |= 0x10;
    }

    /**
     * Cbopcode #0xE2.
     *
     * @param Core $core
     */
    private static function cbopcode226(Core $core)
    {
        $core->registerD |= 0x10;
    }

    /**
     * Cbopcode #0xE3.
     *
     * @param Core $core
     */
    private static function cbopcode227(Core $core)
    {
        $core->registerE |= 0x10;
    }

    /**
     * Cbopcode #0xE4.
     *
     * @param Core $core
     */
    private static function cbopcode228(Core $core)
    {
        $core->registersHL |= 0x1000;
    }

    /**
     * Cbopcode #0xE5.
     *
     * @param Core $core
     */
    private static function cbopcode229(Core $core)
    {
        $core->registersHL |= 0x10;
    }

    /**
     * Cbopcode #0xE6.
     *
     * @param Core $core
     */
    private static function cbopcode230(Core $core)
    {
        $core->memoryWrite($core->registersHL, $core->memoryReader[$core->registersHL]($core, $core->registersHL) | 0x10);
    }

    /**
     * Cbopcode #0xE7.
     *
     * @param Core $core
     */
    private static function cbopcode231(Core $core)
    {
        $core->registerA |= 0x10;
    }

    /**
     * Cbopcode #0xE8.
     *
     * @param Core $core
     */
    private static function cbopcode232(Core $core)
    {
        $core->registerB |= 0x20;
    }

    /**
     * Cbopcode #0xE9.
     *
     * @param Core $core
     */
    private static function cbopcode233(Core $core)
    {
        $core->registerC |= 0x20;
    }

    /**
     * Cbopcode #0xEA.
     *
     * @param Core $core
     */
    private static function cbopcode234(Core $core)
    {
        $core->registerD |= 0x20;
    }

    /**
     * Cbopcode #0xEB.
     *
     * @param Core $core
     */
    private static function cbopcode235(Core $core)
    {
        $core->registerE |= 0x20;
    }

    /**
     * Cbopcode #0xEC.
     *
     * @param Core $core
     */
    private static function cbopcode236(Core $core)
    {
        $core->registersHL |= 0x2000;
    }

    /**
     * Cbopcode #0xED.
     *
     * @param Core $core
     */
    private static function cbopcode237(Core $core)
    {
        $core->registersHL |= 0x20;
    }

    /**
     * Cbopcode #0xEE.
     *
     * @param Core $core
     */
    private static function cbopcode238(Core $core)
    {
        $core->memoryWrite($core->registersHL, $core->memoryReader[$core->registersHL]($core, $core->registersHL) | 0x20);
    }

    /**
     * Cbopcode #0xEF.
     *
     * @param Core $core
     */
    private static function cbopcode239(Core $core)
    {
        $core->registerA |= 0x20;
    }

    /**
     * Cbopcode #0xF0.
     *
     * @param Core $core
     */
    private static function cbopcode240(Core $core)
    {
        $core->registerB |= 0x40;
    }

    /**
     * Cbopcode #0xF1.
     *
     * @param Core $core
     */
    private static function cbopcode241(Core $core)
    {
        $core->registerC |= 0x40;
    }

    /**
     * Cbopcode #0xF2.
     *
     * @param Core $core
     */
    private static function cbopcode242(Core $core)
    {
        $core->registerD |= 0x40;
    }

    /**
     * Cbopcode #0xF3.
     *
     * @param Core $core
     */
    private static function cbopcode243(Core $core)
    {
        $core->registerE |= 0x40;
    }

    /**
     * Cbopcode #0xF4.
     *
     * @param Core $core
     */
    private static function cbopcode244(Core $core)
    {
        $core->registersHL |= 0x4000;
    }

    /**
     * Cbopcode #0xF5.
     *
     * @param Core $core
     */
    private static function cbopcode245(Core $core)
    {
        $core->registersHL |= 0x40;
    }

    /**
     * Cbopcode #0xF6.
     *
     * @param Core $core
     */
    private static function cbopcode246(Core $core)
    {
        $core->memoryWrite($core->registersHL, $core->memoryReader[$core->registersHL]($core, $core->registersHL) | 0x40);
    }

    /**
     * Cbopcode #0xF7.
     *
     * @param Core $core
     */
    private static function cbopcode247(Core $core)
    {
        $core->registerA |= 0x40;
    }

    /**
     * Cbopcode #0xF8.
     *
     * @param Core $core
     */
    private static function cbopcode248(Core $core)
    {
        $core->registerB |= 0x80;
    }

    /**
     * Cbopcode #0xF9.
     *
     * @param Core $core
     */
    private static function cbopcode249(Core $core)
    {
        $core->registerC |= 0x80;
    }

    /**
     * Cbopcode #0xFA.
     *
     * @param Core $core
     */
    private static function cbopcode250(Core $core)
    {
        $core->registerD |= 0x80;
    }

    /**
     * Cbopcode #0xFB.
     *
     * @param Core $core
     */
    private static function cbopcode251(Core $core)
    {
        $core->registerE |= 0x80;
    }

    /**
     * Cbopcode #0xFC.
     *
     * @param Core $core
     */
    private static function cbopcode252(Core $core)
    {
        $core->registersHL |= 0x8000;
    }

    /**
     * Cbopcode #0xFD.
     *
     * @param Core $core
     */
    private static function cbopcode253(Core $core)
    {
        $core->registersHL |= 0x80;
    }

    /**
     * Cbopcode #0xFE.
     *
     * @param Core $core
     */
    private static function cbopcode254(Core $core)
    {
        $core->memoryWrite($core->registersHL, $core->memoryReader[$core->registersHL]($core, $core->registersHL) | 0x80);
    }

    /**
     * Cbopcode #0xFF.
     *
     * @param Core $core
     */
    private static function cbopcode255(Core $core)
    {
        $core->registerA |= 0x80;
    }
}
