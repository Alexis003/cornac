<?php

interface i_used {
    public function b(); 
}

class c implements i_used {
    public function b() {}
    public function d() {}
    
}

interface i_unused {
    public function e(); 
}

?>