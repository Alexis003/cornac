<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 - 2011                                            |
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

class Cornac_Auditeur_Analyzer_Structures_ForeachKeyValue extends Cornac_Auditeur_Analyzer
 {
	protected	$title = 'Foreach variables';
	protected	$description = 'List of blind variables used in a foreach';

	function __construct($mid) {
        parent::__construct($mid);
	}

	public function analyse() {
        $this->clean_report();

// @doc values
	    $query = <<<SQL
SELECT NULL, T1.file, T2.code, T1.id, '{$this->name}',0
FROM <tokens> T1
JOIN <tokens_tags> TT
    ON TT.token_id = T1.id AND
       TT.type='value'
JOIN <tokens> T2
    ON T1.file = T2.file AND
       TT.token_sub_id = T2.id AND
       T2.type = 'variable'
WHERE T1.type='_foreach'
SQL;
        $this->exec_query_insert('report',$query);

// @doc values as references
	    $query = <<<SQL
SELECT NULL, T1.file, T3.code, T1.id, '{$this->name}',0
FROM <tokens> T1
JOIN <tokens_tags> TT
    ON TT.token_id = T1.id AND
       TT.type='value'
JOIN <tokens> T2
    ON T1.file = T2.file AND
       TT.token_sub_id = T2.id
JOIN <tokens> T3
    ON T1.file = T3.file   AND
       T2.left + 1 = T3.left AND
       T3.type = 'variable'
WHERE T1.type='_foreach'
SQL;
        $this->exec_query_insert('report',$query);

// @doc keys
	    $query = <<<SQL
SELECT NULL, T1.file, T2.code, T1.id, '{$this->name}',0
FROM <tokens> T1
JOIN <tokens_tags> TT
    ON TT.token_id = T1.id AND
       TT.type='key'
JOIN <tokens> T2
    ON T1.file = T2.file AND
       TT.token_sub_id = T2.id
WHERE T1.type='_foreach'
SQL;
        $this->exec_query_insert('report',$query);
        
        return true;
	}
}

?>