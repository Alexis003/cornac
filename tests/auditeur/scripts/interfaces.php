<?php

interface interface_a {
    function a(); 
}

class x implements interface_a, ArrayObject {
    function interface_b() {}
}

?>