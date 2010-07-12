<?php

// by freamer89 at gmail dot com
// from PHP docs comments on http://php.net/manual/en/function.parse-ini-file.php
// renamed
function write_ini_file($array, $file)
{
    $res = array();
    foreach($array as $key => $val)
    {
        if(is_array($val))
        {
            $res[] = "[$key]";
            foreach($val as $skey => $sval) $res[] = "$skey = ".(is_numeric($sval) ? $sval : '"'.$sval.'"');
        }
        else $res[] = "$key = ".(is_numeric($val) ? $val : '"'.$val.'"');
    }
    file_put_contents($file, implode("\r\n", $res));
}
?>