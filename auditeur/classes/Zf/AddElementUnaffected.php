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

class Zf_AddElementUnaffected extends modules {
	protected	$title = 'addElement non affectés ';
	protected	$description = 'Recherche les utilisations de la méthode addElement qui ne sont pas affectés à une variable';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array('addElement');
	}
	
	public function analyse() {
        $this->clean_report();

	    $query = <<<SQL
SELECT NULL, T1.file, concat('line ',T1.line), T1.id, '{$this->name}', 0
FROM <report> TR
JOIN <tokens> T1
    ON T1.id = TR.token_id
LEFT JOIN <tokens> T2
    ON T1.file = T2.file AND
       T1.left BETWEEN T2.left AND T2.right AND
       T2.type = 'affectation'
LEFT JOIN <tokens_tags> TT
    ON TT.token_id=  T2.id AND
       TT.type = 'left'
LEFT JOIN <tokens> T3
    ON T1.file = T3.file AND
       T3.id = TT.token_sub_id
WHERE TR.module='addElement' AND
      T3.id IS NULL
SQL;
        $this->exec_query_insert('report', $query);
        
        return true;
	}
}

?>