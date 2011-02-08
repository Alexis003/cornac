<?php

class Cornac_Dir_InvertedRegexIterator extends RegexIterator {
    // @todo watch out for the default values. Couldn't find them in the docs. 
    public function __construct(Iterator $iterator , $regex , $mode = 0, $flags = 0, $preg_flags = 0 ) {
        parent::__construct($iterator , $regex , $mode, $flags, $preg_flags );
    }

    public function accept() {
        return !parent::accept();
    }
}

?>