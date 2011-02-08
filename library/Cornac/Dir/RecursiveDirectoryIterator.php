<?php

class Cornac_Dir_RecursiveDirectoryIterator {
    function list_files($directory) {
        $files = new RecursiveDirectoryIterator($directory, 
                                                FilesystemIterator::KEY_AS_PATHNAME | 
                                                FilesystemIterator::CURRENT_AS_FILEINFO );
        $iterator = new RecursiveIteratorIterator($files);
        // @doc ignore some file extensions
        $regex = new Cornac_Dir_IgnoreFileExtensionFilter($iterator);
        // @doc ignore files without extension
        $regex2 = new Cornac_Dir_IgnoreFileNoExtensionFilter($regex);
        // @doc ignore files starting with .
        $regex3 = new Cornac_Dir_InvertedRegexIterator($regex2, '#/\.#', RecursiveRegexIterator::GET_MATCH);
        // @doc ignore some file prefix
        $regex4 = new Cornac_Dir_IgnoreFilePrefixFilter($regex3);
        // @doc ignore some directories
        $regex5 = new Cornac_Dir_IgnoreDirsFilter($regex4);

        $list = array();
        foreach($regex5 as $filename => $current) {
            $list[] = $filename;
        }
        
        return $list;
    }
}

?>