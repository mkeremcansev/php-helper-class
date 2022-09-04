<?php

class Helper
{
    /**
     * Turkish language slug builder.
     * @param string $string
     * @return array|string|string[]|null
     */
    public static function slug(string $string): string
    {
        $string = str_replace('ü', 'u', $string);
        $string = str_replace('Ü', 'U', $string);
        $string = str_replace('ğ', 'g', $string);
        $string = str_replace('Ğ', 'G', $string);
        $string = str_replace('ş', 's', $string);
        $string = str_replace('Ş', 'S', $string);
        $string = str_replace('ç', 'c', $string);
        $string = str_replace('Ç', 'C', $string);
        $string = str_replace('ö', 'o', $string);
        $string = str_replace('Ö', 'O', $string);
        $string = str_replace('ı', 'i', $string);
        $string = str_replace('İ', 'I', $string);

        return preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
    }

    /**
     * Random code builder.
     * @param $length
     * @return string
     * @throws Exception
     */
    public static function random(int $length = 16): string
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }

    /**
     * Return the length of the given string.
     * @param $value
     * @param $encoding
     * @return false|int
     */
    public static function length(string $value, string $encoding = null): string
    {
        if ($encoding) {
            return mb_strlen($value, $encoding);
        }

        return mb_strlen($value);
    }

    /**
     * Limit the number of characters in a string.
     * @param $value
     * @param $limit
     * @param $end
     * @return mixed|string
     */
    public static function limit(string $value, int $limit = 100, string $end = '...'): string
    {
        if (mb_strwidth($value, 'UTF-8') <= $limit) {
            return $value;
        }

        return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')) . $end;
    }

    /**
     * Convert the given string to upper-case.
     * @param string $value
     * @return string
     */
    public static function upper(string $value): string
    {
        return mb_strtoupper($value, 'UTF-8');
    }

    /**
     * Convert the given string to lower-case.
     * @param string $value
     * @return string
     */
    public static function lower(string $value): string
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /**
     * @param $value
     * @param ...$values
     * @return string
     */
    public static function append($value, ...$values): string
    {
        return $value . implode('', $values);
    }
}