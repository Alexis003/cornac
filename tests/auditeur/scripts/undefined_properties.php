<?php

class myclasse2 {
    public $public_defined_inited = 1;
    protected $protected_defined_inited = 2;
    private $private_defined_inited = 3;
    var $var_defined_inited = 4;

    function methode($arg_for_methode) {
        $this->public_defined_inited++;
        $this->protected_defined_inited++;
        $this->private_defined_inited++;
        $this->var_defined_inited++;

        $this->undefined = 5;
    }
}

?>