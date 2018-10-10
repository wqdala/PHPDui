<?php

class MyTools
{
    public static function GetColorR($color)
    {
        return ($color & 0xFF0000) >> 16;
    }

    public static function GetColorG($color)
    {
        return ($color & 0x00FF00) >> 8;
    }

    public static function GetColorB($color)
    {
        return $color & 0x0000FF;
    }
}