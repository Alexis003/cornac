<?php

class Cornac_Dir_IgnoreFileExtensionFilter extends FilterIterator {
    public function accept() {
        global $INI;
        
        if (isset($INI['tokenizeur']['ignore_suffixe']) && !empty($INI['tokenizeur']['ignore_suffixe'])) {
            $regex_suffix = str_replace(',','|',  preg_quote($INI['tokenizeur']['ignore_suffixe']));
            $regex_suffix = '/('.$regex_suffix.')$/i';
        } else {
            $regex_suffix = array('.gif','.jpg','.jpeg','.xsl','.css','.js','.png');
            $regex_suffix = '/('.join('|', $regex_suffix).')$/i';
        }
        
        return !preg_match($regex_suffix, $this->getInnerIterator()->key());
    }
}

?>