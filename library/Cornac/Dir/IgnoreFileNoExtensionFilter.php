<?php

class Cornac_Dir_IgnoreFileNoExtensionFilter extends FilterIterator {
    public function accept() {
        return strpos(basename($this->getInnerIterator()->current()), '.') !== false;
    }
}

?>