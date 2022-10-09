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

        return self::lower(preg_replace('/[^A-Za-z0-9-]+/', '-', $string));
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

    /**
     * Dump data
     * @param $data
     * @return void
     */
    public static function dd($data)
    {
        $debug = debug_backtrace();
        $callingFile = $debug[0]['file'];
        $callingFileLine = $debug[0]['line'];

        ob_start();
        var_dump($data);
        $content = ob_get_contents();
        ob_end_clean();

        $content = preg_replace("/\r\n|\r/", "\n", $content);
        $content = str_replace("]=>\n", '] = ', $content);
        $content = preg_replace('/= {2,}/', '= ', $content);
        $content = preg_replace("/\[\"(.*?)\"\] = /i", "[$1] = ", $content);
        $content = preg_replace('/  /', "    ", $content);
        $content = preg_replace("/\"\"(.*?)\"/i", "\"$1\"", $content);
        $content = preg_replace("/(int|float)\(([0-9\.]+)\)/i", "$1() <span class=\"number\">$2</span>", $content);
        $content = preg_replace("/(\[[\w ]+\] = string\([0-9]+\) )\"(.*?)/sim", "$1<span class=\"string\">\"", $content);
        $content = preg_replace("/(\"\n{1,})( {0,}\})/sim", "$1</span>$2", $content);
        $content = preg_replace("/(\"\n{1,})( {0,}\[)/sim", "$1</span>$2", $content);
        $content = preg_replace("/(string\([0-9]+\) )\"(.*?)\"\n/sim", "$1<span class=\"string\">\"$2\"</span>\n", $content);
        $regex = array(
            'numbers' => array('/(^|] = )(array|float|int|string|resource|object\(.*\)|\&amp;object\(.*\))\(([0-9\.]+)\)/i', '$1$2(<span class="number">$3</span>)'),
            'null' => array('/(^|] = )(null)/i', '$1<span class="keyword">$2</span>'),
            'bool' => array('/(bool)\((true|false)\)/i', '$1(<span class="keyword">$2</span>)'),
            'types' => array('/(of type )\((.*)\)/i', '$1(<span class="type">$2</span>)'),
            'object' => array('/(object|\&amp;object)\(([\w]+)\)/i', '$1(<span class="object">$2</span>)'),
            'function' => array('/(^|] = )(array|string|int|float|bool|resource|object|\&amp;object)\(/i', '$1<span class="function">$2</span>('),
        );

        foreach ($regex as $x) {
            $content = preg_replace($x[0], $x[1], $content);
        }

        $style = "
    .dumpr {
        margin: 2px;
        padding: 2px;
        background-color: #fbfbfb;
        float: left;
        clear: both;
    }
    .dumpr pre {
    background-color: #2d2d2d;
        color: white;
        font-weight:bold !important;
        font-size: 9pt;
        border-radius: 10px;
        font-family: 'Rubik';
        margin: 0px;
        padding-top: 5px;
        padding-bottom: 7px;
        padding-left: 9px;
        padding-right: 9px;
        width: 100% !important;
    }
    .dumpr div {
        background-color: #fcfcfc;
      
        float: left;
        clear: both;
    }
    .dumpr span.string {color: #FF8400; font-weight:bold;}
    .dumpr span.number {color: #FF8400; font-weight:bold;}
    .dumpr span.keyword {color: #FF8400; font-weight:bold;}
    .dumpr span.function {color: #1299DA; font-weight:bold;}
    .dumpr span.object {color: #ac00ac;}
    .dumpr span.type {color: #0072c4;}
    ";

        $style = preg_replace("/ {2,}/", "", $style);
        $style = preg_replace("/\t|\r\n|\r|\n/", "", $style);
        $style = preg_replace("/\/\*.*?\*\//i", '', $style);
        $style = str_replace('}', '} ', $style);
        $style = str_replace(' {', '{', $style);
        $style = trim($style);

        $content = trim($content);
        $content = preg_replace("/\n<\/span>/", "</span>\n", $content);

        $out = "\n\n" .
            "<style type=\"text/css\">" . $style . "</style>\n" .
            "<div class=\"dumpr\">
        <div><pre>$callingFile : $callingFileLine \n$content\n</pre></div></div><div style=\"clear:both;\">&nbsp;</div>" .
            "\n\n";
        echo $out . '<link rel="preconnect" href="https://fonts.googleapis.com">
                     <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                     <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@600&display=swap" rel="stylesheet">
                    ';
    }
}