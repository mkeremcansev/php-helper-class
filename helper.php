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
     * @param int $length
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
     * @param string $value
     * @param string|null $encoding
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
     * @param string $value
     * @param int $limit
     * @param string $end
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
     * String append word & words.
     * @param $value
     * @param ...$values
     * @return string
     */
    public static function append($value, ...$values): string
    {
        return $value . implode('', $values);
    }

    /**
     * Get client IP information.
     * @return mixed
     */
    public static function getIpAddress()
    {
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote = @$_SERVER['REMOTE_ADDR'];
        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }
        return $ip;
    }
}
