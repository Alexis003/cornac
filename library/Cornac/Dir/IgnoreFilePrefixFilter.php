<?php

class Cornac_Dir_IgnoreFilePrefixFilter extends FilterIterator {
    public function accept() {
        global $INI;
        
        if (isset($INI['tokenizeur']['ignore_prefixe']) && !empty($INI['tokenizeur']['ignore_prefixe'])) {
            $regex_prefix = str_replace(',','|',  preg_quote($INI['tokenizeur']['ignore_prefixe']));
            $regex_prefix = '/('.$regex_prefix.')$/';
        } else {
        // @doc no default values;
            return true; 
        }        
        
        return !preg_match($regex_prefix, $this->getInnerIterator()->key());
    }
}

?>