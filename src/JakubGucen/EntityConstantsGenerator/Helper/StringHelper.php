<?php

namespace JakubGucen\EntityConstantsGenerator\Helper;

class StringHelper
{
    /**
     * https://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
     */
    public static function checkStringStartsWith(
        string $string,
        string $startsWith
    ): bool {
        $length = mb_strlen($startsWith);
        return mb_substr($string, 0, $length) === $startsWith;
    }

    /**
     * https://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
     */
    public static function checkStringEndsWith(
        string $string,
        string $endsWith
    ): bool {
        $length = mb_strlen($endsWith);
        if (!$length) {
            return true;
        }

        return mb_substr($string, -$length) === $endsWith;
    }

    public static function generateConstantName(string $propertyName): string
    {
        $constantName = '';

        if ($propertyName === mb_strtoupper($propertyName)) {
            return $propertyName;
        }

        $chars = mb_str_split($propertyName);
        foreach ($chars as $key => $char) {
            $charUpper = mb_strtoupper($char);

            if ($key > 0 && $char !== '_' && $char === $charUpper) {
                $constantName .= '_';
            }

            $constantName .= $charUpper;
        }

        return $constantName;
    }
}
