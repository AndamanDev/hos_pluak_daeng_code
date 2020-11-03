<?php

namespace inspinia\utils;

use yii\helpers\VarDumper;

class CoreUtility
{

    public static function array2String($arry)
    {
        $str = '';

        if (is_array($arry)) {
            $str = @serialize($arry);
        }

        return $str;
    }

    public static function strArray2String($arry)
    {
        $str = '';
        if ($arry !== '') {
            $value = eval("return $arry;");

            if (is_array($value)) {
                $str = @serialize($value);
            } else {
                $str = '';
            }
        }
        return $str;
    }

    public static function string2strArray($str)
    {
        $arry = @unserialize($str);
        if (is_array($arry)) {
            return VarDumper::export($arry);
        }
        return null;
    }

    public static function string2Array($str)
    {
        $arry = @unserialize($str);
        if (is_array($arry)) {
            return $arry;
        }
        return [];
    }

}
