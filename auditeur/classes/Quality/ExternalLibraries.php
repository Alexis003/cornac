<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 - 2011 Alter Way Solutions (France)               |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.01 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.php.net/license/3_01.txt                                  |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Author: Damien Seguy <damien.seguy@gmail.com>                        |
   +----------------------------------------------------------------------+
 */

class Quality_ExternalLibraries extends modules {
	protected	$title = 'Common libraries';
	protected	$description = 'Spot commonly used libraries.';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array('classes');
	}
	
	public function analyse() {
        $this->clean_report();
// @todo use also constantes
// @todo use also functions
// @todo spot versions? 

        $list = parse_ini_file('../dict/poplib.ini', true);
        
        foreach($list as $ext => $characteristics) {
            $in = "'".join("', '", $characteristics['classes'])."'";

            // @doc search for usage as class extensions
            $query = <<<SQL
SELECT NULL, T1.file, '$ext', T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens_tags> TT 
    ON TT.token_id = T1.id AND
       TT.type = 'extends'
JOIN <tokens> T2
    ON TT.token_sub_id = T2.id AND
       T1.file = T2.file AND 
       T2.code IN ($in)
WHERE T1.type='_class'
SQL;
            $this->exec_query_insert('report', $query);

            // @doc search for usage as instanciation
            $query = <<<SQL
SELECT NULL, TR.file, '$ext', TR.id, '{$this->name}', 0
FROM <report> TR
WHERE TR.element IN ($in)
SQL;
            $this->exec_query_insert('report', $query);
        }

        
        return true;
	}
}

?>