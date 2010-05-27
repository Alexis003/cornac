<?php

class functioncall extends instruction {
    protected $function = null;
    protected $args = null;
    
    function __construct($entree) {
        parent::__construct(array());
        
        if ($entree[0]->checkCode('=')) {
            $entree[0]->code = 'echo';
        }
        
        if ($entree[0]->checkClass('Token')) {
            $this->function = $this->make_token_traite($entree[0]);
        } else {
            $this->function = $entree[0];
        }

        if (isset($entree[1])) {
            $this->args = $entree[1];
        } else {
            $this->args = new arglist(array( null ));
        }
    }

    function __toString() {
        return __CLASS__." ".$this->function." ( ".$this->args. " ) ";
    }

    function getFunction() {
        return $this->function;
    }

    function getArgs() {
        return $this->args;
    }

    function neutralise() {
        $this->function->detach();
        $this->args->detach();
    }
    
   function getRegex() {
        return array(
    'functioncall_simple_regex',
    'functioncall_sansparentheses_regex',
    'functioncall_echosansparentheses_regex',
    'functioncall_sansarglist_regex',
    'functioncall_variable_regex',
    'functioncall_variableempty_regex',
    'functioncall_list_regex',
    'functioncall_shorttag_regex',
                    );
    }    
}

?>