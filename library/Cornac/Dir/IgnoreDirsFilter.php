<?php

class Cornac_Dir_IgnoreDirsFilter extends FilterIterator {
    public function accept() {
        global $INI;
        
       $ignore_dirs = array( 'cgi-bin', '.', '..',
                             'CVS','.svn','.git','.hg', '.bzr', // @todo : mercurial? other vcs's special folder : please add 
                             'adodb','fpdf','fckeditor','incutio','lightbox','nusoap','odtphp','pear','phpthumb','phputf8','scriptaculous','simpletest','smarty','spyc','tiny_mce','tinymce'); 

        if (isset($INI['tokenizeur']['ignore_dirs']) && !empty($INI['tokenizeur']['ignore_dirs'])) {
            $ignore_dirs = array_merge($ignore_dirs, explode(',',$INI['tokenizeur']['ignore_dirs']));
        } else {
        // @emptyelse
        }      

        $ignore_dirs = array_map('preg_quote', $ignore_dirs);
        $regex = '#/('.join('|',$ignore_dirs).')/#';

// @todo use splinfo!
        return !preg_match($regex, $this->getInnerIterator()->key());
    }
}

?>