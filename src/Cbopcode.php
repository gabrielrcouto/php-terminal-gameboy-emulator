<?php
namespace GameBoy;

class Cbopcode
{
    public $functionsArray = [];

    public function __construct()
    {
        //#0x00:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registerB & 0x80) == 0x80);
            $parentObj->registerB = (($parentObj->registerB << 1) & 0xFF) + (($parentObj->FCarry) ? 1 : 0);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerB == 0);
        };

        //#0x01:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registerC & 0x80) == 0x80);
            $parentObj->registerC = (($parentObj->registerC << 1) & 0xFF) + (($parentObj->FCarry) ? 1 : 0);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerC == 0);
        };
        //#0x02:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registerD & 0x80) == 0x80);
            $parentObj->registerD = (($parentObj->registerD << 1) & 0xFF) + (($parentObj->FCarry) ? 1 : 0);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerD == 0);
        };
        //#0x03:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registerE & 0x80) == 0x80);
            $parentObj->registerE = (($parentObj->registerE << 1) & 0xFF) + (($parentObj->FCarry) ? 1 : 0);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerE == 0);
        };
        //#0x04:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registersHL & 0x8000) == 0x8000);
            $parentObj->registersHL = (($parentObj->registersHL << 1) & 0xFE00) + (($parentObj->FCarry) ? 0x100 : 0) + ($parentObj->registersHL & 0xFF);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registersHL <= 0xFF);
        };
        //#0x05:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registersHL & 0x80) == 0x80);
            $parentObj->registersHL = ($parentObj->registersHL & 0xFF00) + (($parentObj->registersHL << 1) & 0xFF) + (($parentObj->FCarry) ? 1 : 0);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registersHL & 0xFF) == 0x00);
        };
        //#0x06:
        $this->functionsArray[] = function ($parentObj) {
            $temp_var = $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL);
            $parentObj->FCarry = (($temp_var & 0x80) == 0x80);
            $temp_var = (($temp_var << 1) & 0xFF) + (($parentObj->FCarry) ? 1 : 0);
            $parentObj->memoryWrite($parentObj->registersHL, $temp_var);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($temp_var == 0x00);
        };
        //#0x07:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registerA & 0x80) == 0x80);
            $parentObj->registerA = (($parentObj->registerA << 1) & 0xFF) + (($parentObj->FCarry) ? 1 : 0);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerA == 0x00);
        };
        //#0x08:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registerB & 0x01) == 0x01);
            $parentObj->registerB = (($parentObj->FCarry) ? 0x80 : 0) + ($parentObj->registerB >> 1);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerB == 0);
        };
        //#0x09:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registerC & 0x01) == 0x01);
            $parentObj->registerC = (($parentObj->FCarry) ? 0x80 : 0) + ($parentObj->registerC >> 1);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerC == 0);
        };
        //#0x0A:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registerD & 0x01) == 0x01);
            $parentObj->registerD = (($parentObj->FCarry) ? 0x80 : 0) + ($parentObj->registerD >> 1);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerD == 0);
        };
        //#0x0B:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registerE & 0x01) == 0x01);
            $parentObj->registerE = (($parentObj->FCarry) ? 0x80 : 0) + ($parentObj->registerE >> 1);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerE == 0);
        };
        //#0x0C:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registersHL & 0x0100) == 0x0100);
            $parentObj->registersHL = (($parentObj->FCarry) ? 0x8000 : 0) + (($parentObj->registersHL >> 1) & 0xFF00) + ($parentObj->registersHL & 0xFF);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registersHL <= 0xFF);
        };
        //#0x0D:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registersHL & 0x01) == 0x01);
            $parentObj->registersHL = ($parentObj->registersHL & 0xFF00) + (($parentObj->FCarry) ? 0x80 : 0) + (($parentObj->registersHL & 0xFF) >> 1);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registersHL & 0xFF) == 0x00);
        };
        //#0x0E:
        $this->functionsArray[] = function ($parentObj) {
            $temp_var = $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL);
            $parentObj->FCarry = (($temp_var & 0x01) == 0x01);
            $temp_var = (($parentObj->FCarry) ? 0x80 : 0) + ($temp_var >> 1);
            $parentObj->memoryWrite($parentObj->registersHL, $temp_var);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($temp_var == 0x00);
        };
        //#0x0F:
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registerA & 0x01) == 0x01);
            $parentObj->registerA = (($parentObj->FCarry) ? 0x80 : 0) + ($parentObj->registerA >> 1);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerA == 0x00);
        };
        //#0x10:
        $this->functionsArray[] = function ($parentObj) {
            $newFCarry = (($parentObj->registerB & 0x80) == 0x80);
            $parentObj->registerB = (($parentObj->registerB << 1) & 0xFF) + (($parentObj->FCarry) ? 1 : 0);
            $parentObj->FCarry = $newFCarry;
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerB == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $newFCarry = (($parentObj->registerC & 0x80) == 0x80);
            $parentObj->registerC = (($parentObj->registerC << 1) & 0xFF) + (($parentObj->FCarry) ? 1 : 0);
            $parentObj->FCarry = $newFCarry;
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerC == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $newFCarry = (($parentObj->registerD & 0x80) == 0x80);
            $parentObj->registerD = (($parentObj->registerD << 1) & 0xFF) + (($parentObj->FCarry) ? 1 : 0);
            $parentObj->FCarry = $newFCarry;
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerD == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $newFCarry = (($parentObj->registerE & 0x80) == 0x80);
            $parentObj->registerE = (($parentObj->registerE << 1) & 0xFF) + (($parentObj->FCarry) ? 1 : 0);
            $parentObj->FCarry = $newFCarry;
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerE == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $newFCarry = (($parentObj->registersHL & 0x8000) == 0x8000);
            $parentObj->registersHL = (($parentObj->registersHL << 1) & 0xFE00) + (($parentObj->FCarry) ? 0x100 : 0) + ($parentObj->registersHL & 0xFF);
            $parentObj->FCarry = $newFCarry;
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registersHL <= 0xFF);
        };
        $this->functionsArray[] = function ($parentObj) {
            $newFCarry = (($parentObj->registersHL & 0x80) == 0x80);
            $parentObj->registersHL = ($parentObj->registersHL & 0xFF00) + (($parentObj->registersHL << 1) & 0xFF) + (($parentObj->FCarry) ? 1 : 0);
            $parentObj->FCarry = $newFCarry;
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registersHL & 0xFF) == 0x00);
        };
        $this->functionsArray[] = function ($parentObj) {
            $temp_var = $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL);
            $newFCarry = (($temp_var & 0x80) == 0x80);
            $temp_var = (($temp_var << 1) & 0xFF) + (($parentObj->FCarry) ? 1 : 0);
            $parentObj->FCarry = $newFCarry;
            $parentObj->memoryWrite($parentObj->registersHL, $temp_var);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($temp_var == 0x00);
        };
        $this->functionsArray[] = function ($parentObj) {
            $newFCarry = (($parentObj->registerA & 0x80) == 0x80);
            $parentObj->registerA = (($parentObj->registerA << 1) & 0xFF) + (($parentObj->FCarry) ? 1 : 0);
            $parentObj->FCarry = $newFCarry;
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerA == 0x00);
        };
        $this->functionsArray[] = function ($parentObj) {
            $newFCarry = (($parentObj->registerB & 0x01) == 0x01);
            $parentObj->registerB = (($parentObj->FCarry) ? 0x80 : 0) + ($parentObj->registerB >> 1);
            $parentObj->FCarry = $newFCarry;
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerB == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $newFCarry = (($parentObj->registerC & 0x01) == 0x01);
            $parentObj->registerC = (($parentObj->FCarry) ? 0x80 : 0) + ($parentObj->registerC >> 1);
            $parentObj->FCarry = $newFCarry;
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerC == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $newFCarry = (($parentObj->registerD & 0x01) == 0x01);
            $parentObj->registerD = (($parentObj->FCarry) ? 0x80 : 0) + ($parentObj->registerD >> 1);
            $parentObj->FCarry = $newFCarry;
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerD == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $newFCarry = (($parentObj->registerE & 0x01) == 0x01);
            $parentObj->registerE = (($parentObj->FCarry) ? 0x80 : 0) + ($parentObj->registerE >> 1);
            $parentObj->FCarry = $newFCarry;
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerE == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $newFCarry = (($parentObj->registersHL & 0x0100) == 0x0100);
            $parentObj->registersHL = (($parentObj->FCarry) ? 0x8000 : 0) + (($parentObj->registersHL >> 1) & 0xFF00) + ($parentObj->registersHL & 0xFF);
            $parentObj->FCarry = $newFCarry;
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registersHL <= 0xFF);
        };
        $this->functionsArray[] = function ($parentObj) {
            $newFCarry = (($parentObj->registersHL & 0x01) == 0x01);
            $parentObj->registersHL = ($parentObj->registersHL & 0xFF00) + (($parentObj->FCarry) ? 0x80 : 0) + (($parentObj->registersHL & 0xFF) >> 1);
            $parentObj->FCarry = $newFCarry;
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registersHL & 0xFF) == 0x00);
        };
        $this->functionsArray[] = function ($parentObj) {
            $temp_var = $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL);
            $newFCarry = (($temp_var & 0x01) == 0x01);
            $temp_var = (($parentObj->FCarry) ? 0x80 : 0) + ($temp_var >> 1);
            $parentObj->FCarry = $newFCarry;
            $parentObj->memoryWrite($parentObj->registersHL, $temp_var);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($temp_var == 0x00);
        };
        $this->functionsArray[] = function ($parentObj) {
            $newFCarry = (($parentObj->registerA & 0x01) == 0x01);
            $parentObj->registerA = (($parentObj->FCarry) ? 0x80 : 0) + ($parentObj->registerA >> 1);
            $parentObj->FCarry = $newFCarry;
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerA == 0x00);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registerB & 0x80) == 0x80);
            $parentObj->registerB = ($parentObj->registerB << 1) & 0xFF;
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerB == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registerC & 0x80) == 0x80);
            $parentObj->registerC = ($parentObj->registerC << 1) & 0xFF;
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerC == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registerD & 0x80) == 0x80);
            $parentObj->registerD = ($parentObj->registerD << 1) & 0xFF;
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerD == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registerE & 0x80) == 0x80);
            $parentObj->registerE = ($parentObj->registerE << 1) & 0xFF;
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerE == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registersHL & 0x8000) == 0x8000);
            $parentObj->registersHL = (($parentObj->registersHL << 1) & 0xFE00) + ($parentObj->registersHL & 0xFF);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registersHL <= 0xFF);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registersHL & 0x0080) == 0x0080);
            $parentObj->registersHL = ($parentObj->registersHL & 0xFF00) + (($parentObj->registersHL << 1) & 0xFF);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registersHL & 0xFF) == 0x00);
        };
        $this->functionsArray[] = function ($parentObj) {
            $temp_var = $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL);
            $parentObj->FCarry = (($temp_var & 0x80) == 0x80);
            $temp_var = ($temp_var << 1) & 0xFF;
            $parentObj->memoryWrite($parentObj->registersHL, $temp_var);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($temp_var == 0x00);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registerA & 0x80) == 0x80);
            $parentObj->registerA = ($parentObj->registerA << 1) & 0xFF;
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerA == 0x00);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registerB & 0x01) == 0x01);
            $parentObj->registerB = ($parentObj->registerB & 0x80) + ($parentObj->registerB >> 1);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerB == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registerC & 0x01) == 0x01);
            $parentObj->registerC = ($parentObj->registerC & 0x80) + ($parentObj->registerC >> 1);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerC == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registerD & 0x01) == 0x01);
            $parentObj->registerD = ($parentObj->registerD & 0x80) + ($parentObj->registerD >> 1);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerD == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registerE & 0x01) == 0x01);
            $parentObj->registerE = ($parentObj->registerE & 0x80) + ($parentObj->registerE >> 1);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerE == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registersHL & 0x0100) == 0x0100);
            $parentObj->registersHL = (($parentObj->registersHL >> 1) & 0xFF00) + ($parentObj->registersHL & 0x80FF);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registersHL <= 0xFF);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registersHL & 0x0001) == 0x0001);
            $parentObj->registersHL = ($parentObj->registersHL & 0xFF80) + (($parentObj->registersHL & 0xFF) >> 1);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registersHL & 0xFF) == 0x00);
        };
        $this->functionsArray[] = function ($parentObj) {
            $temp_var = $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL);
            $parentObj->FCarry = (($temp_var & 0x01) == 0x01);
            $temp_var = ($temp_var & 0x80) + ($temp_var >> 1);
            $parentObj->memoryWrite($parentObj->registersHL, $temp_var);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($temp_var == 0x00);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registerA & 0x01) == 0x01);
            $parentObj->registerA = ($parentObj->registerA & 0x80) + ($parentObj->registerA >> 1);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerA == 0x00);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerB = (($parentObj->registerB & 0xF) << 4) + ($parentObj->registerB >> 4);
            $parentObj->FZero = ($parentObj->registerB == 0);
            $parentObj->FCarry = $parentObj->FHalfCarry = $parentObj->FSubtract = false;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerC = (($parentObj->registerC & 0xF) << 4) + ($parentObj->registerC >> 4);
            $parentObj->FZero = ($parentObj->registerC == 0);
            $parentObj->FCarry = $parentObj->FHalfCarry = $parentObj->FSubtract = false;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerD = (($parentObj->registerD & 0xF) << 4) + ($parentObj->registerD >> 4);
            $parentObj->FZero = ($parentObj->registerD == 0);
            $parentObj->FCarry = $parentObj->FHalfCarry = $parentObj->FSubtract = false;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerE = (($parentObj->registerE & 0xF) << 4) + ($parentObj->registerE >> 4);
            $parentObj->FZero = ($parentObj->registerE == 0);
            $parentObj->FCarry = $parentObj->FHalfCarry = $parentObj->FSubtract = false;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL = (($parentObj->registersHL & 0xF00) << 4) + (($parentObj->registersHL & 0xF000) >> 4) + ($parentObj->registersHL & 0xFF);
            $parentObj->FZero = ($parentObj->registersHL <= 0xFF);
            $parentObj->FCarry = $parentObj->FHalfCarry = $parentObj->FSubtract = false;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL = ($parentObj->registersHL & 0xFF00) + (($parentObj->registersHL & 0xF) << 4) + (($parentObj->registersHL & 0xF0) >> 4);
            $parentObj->FZero = (($parentObj->registersHL & 0xFF) == 0);
            $parentObj->FCarry = $parentObj->FHalfCarry = $parentObj->FSubtract = false;
        };
        $this->functionsArray[] = function ($parentObj) {
            $temp_var = $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL);
            $temp_var = (($temp_var & 0xF) << 4) + ($temp_var >> 4);
            $parentObj->memoryWrite($parentObj->registersHL, $temp_var);
            $parentObj->FZero = ($temp_var == 0);
            $parentObj->FCarry = $parentObj->FHalfCarry = $parentObj->FSubtract = false;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA = (($parentObj->registerA & 0xF) << 4) + ($parentObj->registerA >> 4);
            $parentObj->FZero = ($parentObj->registerA == 0);
            $parentObj->FCarry = $parentObj->FHalfCarry = $parentObj->FSubtract = false;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registerB & 0x01) == 0x01);
            $parentObj->registerB >>= 1;
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerB == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registerC & 0x01) == 0x01);
            $parentObj->registerC >>= 1;
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerC == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registerD & 0x01) == 0x01);
            $parentObj->registerD >>= 1;
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerD == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registerE & 0x01) == 0x01);
            $parentObj->registerE >>= 1;
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerE == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registersHL & 0x0100) == 0x0100);
            $parentObj->registersHL = (($parentObj->registersHL >> 1) & 0xFF00) + ($parentObj->registersHL & 0xFF);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registersHL <= 0xFF);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registersHL & 0x0001) == 0x0001);
            $parentObj->registersHL = ($parentObj->registersHL & 0xFF00) + (($parentObj->registersHL & 0xFF) >> 1);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registersHL & 0xFF) == 0x00);
        };
        $this->functionsArray[] = function ($parentObj) {
            $temp_var = $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL);
            $parentObj->FCarry = (($temp_var & 0x01) == 0x01);
            $parentObj->memoryWrite($parentObj->registersHL, $temp_var >>= 1);
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($temp_var == 0x00);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FCarry = (($parentObj->registerA & 0x01) == 0x01);
            $parentObj->registerA >>= 1;
            $parentObj->FHalfCarry = $parentObj->FSubtract = false;
            $parentObj->FZero = ($parentObj->registerA == 0x00);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerB & 0x01) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerC & 0x01) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerD & 0x01) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerE & 0x01) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registersHL & 0x0100) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registersHL & 0x0001) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL) & 0x01) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerA & 0x01) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerB & 0x02) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerC & 0x02) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerD & 0x02) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerE & 0x02) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registersHL & 0x0200) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registersHL & 0x0002) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL) & 0x02) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerA & 0x02) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerB & 0x04) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerC & 0x04) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerD & 0x04) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerE & 0x04) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registersHL & 0x0400) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registersHL & 0x0004) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL) & 0x04) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerA & 0x04) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerB & 0x08) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerC & 0x08) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerD & 0x08) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerE & 0x08) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registersHL & 0x0800) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registersHL & 0x0008) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL) & 0x08) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerA & 0x08) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerB & 0x10) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerC & 0x10) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerD & 0x10) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerE & 0x10) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registersHL & 0x1000) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registersHL & 0x0010) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL) & 0x10) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerA & 0x10) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerB & 0x20) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerC & 0x20) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerD & 0x20) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerE & 0x20) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registersHL & 0x2000) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registersHL & 0x0020) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL) & 0x20) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerA & 0x20) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerB & 0x40) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerC & 0x40) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerD & 0x40) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerE & 0x40) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registersHL & 0x4000) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registersHL & 0x0040) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL) & 0x40) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerA & 0x40) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerB & 0x80) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerC & 0x80) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerD & 0x80) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerE & 0x80) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registersHL & 0x8000) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registersHL & 0x0080) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL) & 0x80) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->FHalfCarry = true;
            $parentObj->FSubtract = false;
            $parentObj->FZero = (($parentObj->registerA & 0x80) == 0);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerB &= 0xFE;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerC &= 0xFE;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerD &= 0xFE;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerE &= 0xFE;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL &= 0xFEFF;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL &= 0xFFFE;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite($parentObj->registersHL, $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL) & 0xFE);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA &= 0xFE;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerB &= 0xFD;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerC &= 0xFD;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerD &= 0xFD;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerE &= 0xFD;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL &= 0xFDFF;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL &= 0xFFFD;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite($parentObj->registersHL, $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL) & 0xFD);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA &= 0xFD;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerB &= 0xFB;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerC &= 0xFB;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerD &= 0xFB;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerE &= 0xFB;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL &= 0xFBFF;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL &= 0xFFFB;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite($parentObj->registersHL, $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL) & 0xFB);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA &= 0xFB;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerB &= 0xF7;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerC &= 0xF7;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerD &= 0xF7;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerE &= 0xF7;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL &= 0xF7FF;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL &= 0xFFF7;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite($parentObj->registersHL, $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL) & 0xF7);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA &= 0xF7;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerB &= 0xEF;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerC &= 0xEF;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerD &= 0xEF;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerE &= 0xEF;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL &= 0xEFFF;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL &= 0xFFEF;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite($parentObj->registersHL, $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL) & 0xEF);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA &= 0xEF;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerB &= 0xDF;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerC &= 0xDF;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerD &= 0xDF;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerE &= 0xDF;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL &= 0xDFFF;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL &= 0xFFDF;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite($parentObj->registersHL, $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL) & 0xDF);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA &= 0xDF;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerB &= 0xBF;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerC &= 0xBF;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerD &= 0xBF;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerE &= 0xBF;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL &= 0xBFFF;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL &= 0xFFBF;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite($parentObj->registersHL, $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL) & 0xBF);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA &= 0xBF;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerB &= 0x7F;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerC &= 0x7F;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerD &= 0x7F;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerE &= 0x7F;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL &= 0x7FFF;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL &= 0xFF7F;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite($parentObj->registersHL, $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL) & 0x7F);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA &= 0x7F;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerB |= 0x01;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerC |= 0x01;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerD |= 0x01;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerE |= 0x01;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL |= 0x0100;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL |= 0x01;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite($parentObj->registersHL, $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL) | 0x01);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA |= 0x01;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerB |= 0x02;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerC |= 0x02;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerD |= 0x02;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerE |= 0x02;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL |= 0x0200;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL |= 0x02;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite($parentObj->registersHL, $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL) | 0x02);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA |= 0x02;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerB |= 0x04;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerC |= 0x04;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerD |= 0x04;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerE |= 0x04;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL |= 0x0400;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL |= 0x04;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite($parentObj->registersHL, $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL) | 0x04);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA |= 0x04;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerB |= 0x08;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerC |= 0x08;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerD |= 0x08;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerE |= 0x08;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL |= 0x0800;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL |= 0x08;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite($parentObj->registersHL, $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL) | 0x08);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA |= 0x08;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerB |= 0x10;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerC |= 0x10;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerD |= 0x10;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerE |= 0x10;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL |= 0x1000;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL |= 0x10;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite($parentObj->registersHL, $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL) | 0x10);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA |= 0x10;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerB |= 0x20;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerC |= 0x20;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerD |= 0x20;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerE |= 0x20;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL |= 0x2000;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL |= 0x20;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite($parentObj->registersHL, $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL) | 0x20);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA |= 0x20;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerB |= 0x40;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerC |= 0x40;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerD |= 0x40;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerE |= 0x40;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL |= 0x4000;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL |= 0x40;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite($parentObj->registersHL, $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL) | 0x40);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA |= 0x40;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerB |= 0x80;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerC |= 0x80;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerD |= 0x80;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerE |= 0x80;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL |= 0x8000;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registersHL |= 0x80;
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->memoryWrite($parentObj->registersHL, $parentObj->memoryReader[$parentObj->registersHL]($parentObj, $parentObj->registersHL) | 0x80);
        };
        $this->functionsArray[] = function ($parentObj) {
            $parentObj->registerA |= 0x80;
        };
    }

    public function get()
    {
        return $this->functionsArray;
    }
}
