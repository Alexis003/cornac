<?php

class instruction extends token {
    
    function __construct($array) {
        parent::__construct($array);
    }

    function __toString() {
        return __CLASS__." ";
    }


}

?>