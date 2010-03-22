<?php
        $number =  (ord($data[++$index]) << 16) |
                  (ord($data[++$index]) << 8) | ord($data[++$index]);
        return $number;


?>