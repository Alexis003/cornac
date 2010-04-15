<?php

class arobases extends typecalls {
    protected    $description = 'Utilisation des arobases';
    protected    $description_en = 'Usage of @';

    function __construct($mid) {
        parent::__construct($mid);
        
        $this->name = __CLASS__;
    }
    
    public function analyse() {
	    $this->type = 'noscream';
	    parent::analyse();
        return ;
    }
}

?>