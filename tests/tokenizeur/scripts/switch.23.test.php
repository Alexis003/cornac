<?php
        switch ($a) {
        case 'a' . ':' . 'b':
            $a = 1;
            $b = 2;
            break;
        case 'c' . ':' . 'd' . 'e'. 'f';
            $a = 1;
            $b = 2;
            $c = 3;
            break;
        case 'e' . ':' . 'f';
            $a = 1;
            $b = 2;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
?>
