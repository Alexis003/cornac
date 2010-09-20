<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 Alter Way Solutions (France)                      |
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

class classes_hierarchie extends modules {
	protected	$title = 'Hiérarchie des classes';
	protected	$description = 'Hierarchie de classe';

	function __construct($mid) {
        parent::__construct($mid);
        
        $this->format = modules::FORMAT_DOT;
	}
	
	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
INSERT INTO <rapport_dot> 
    SELECT distinct T2.code, T2.class,'', '{$this->name}'
    FROM <tokens_tags> TT
    JOIN <tokens> T2
       ON TT.token_sub_id = T2.id
    WHERE TT.type = 'extends';
SQL;
    
        $this->exec_query($query);
    }
}

?>