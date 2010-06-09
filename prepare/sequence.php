<?php

class sequence extends instruction {
    protected $elements = array();
    
    function __construct($elements) {
        parent::__construct(array());
        
        foreach($elements as $l) {
            if (get_class($l) == 'sequence') {
                $this->elements = array_merge($this->elements, $l->getElements());
            } elseif (get_class($l) == 'block') {
                $this->elements = array_merge($this->elements, $l->getList());
            } elseif (get_class($l) == 'codephp') {
                $code = $l->getphp_code();
                if (get_class($code) == 'sequence') {
                    $this->elements = array_merge($this->elements, $code->getElements());
                } else {
                    $this->elements[] = $code;
                }
            } else {
                $this->elements[] = $l;
            }
         }
    }

    function __toString() {
        $retour = __CLASS__;
        if (count($this->elements) == 0) {
            $retour .= "Sequence vide\n";
        } else {
            foreach($this->elements as $e) {
                $retour .= $e."\n";
            }
        }
        return $retour;
    }

    function getCode() {
        return __CLASS__;
    }
    
    function getElements() {
        return $this->elements;
    }

    function neutralise() {
        foreach($this->elements as &$e) {
            $e->detach();
        }
    }

    private function mange($sequence) {
        $this->elements = array_merge($this->elements, $sequence->getElements());
        $this->neutralise();
    }
    
    public function getRegex() {
        return array(
          'sequence_regex',
          'sequence_suite_regex',
          'sequence_class_regex',
          'sequence_empty_regex',
          'sequence_cdr_regex',
                    );
    }    
}

?>