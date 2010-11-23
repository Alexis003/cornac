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

class affectations_direct_gpc extends modules {
	protected	$title = 'GPC assignation';
	protected	$description = 'Assigning directly GPC variables';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

        $gpc_regexp = '(\\\\'.join('|\\\\',modules::getPHPGPC()).')';
// @note variables, not whole arrays
        $query = <<<SQL
SELECT NULL, T1.fichier, TC.code, T1.id,'{$this->name}'  , 0
FROM <tokens> T1  
JOIN <tokens_tags> TT
    ON T1.id = TT.token_id AND TT.type='right'
JOIN <tokens> T2
    ON T2.fichier = T1.fichier AND TT.token_sub_id = T2.id
JOIN <tokens> T3
    ON T3.fichier = T1.fichier AND 
       T3.type='variable' AND 
       T3.droite between T2.droite AND T2.gauche 
JOIN <tokens_cache> TC
    ON TC.id = T3.id
WHERE T1.type = 'affectation' AND
      BINARY TC.code REGEXP '^$gpc_regexp'
SQL;
        $this->exec_query_insert('rapport', $query);

        return true; 
        // @todo finish this one
// @note full arrays,  not just variables
        $query = <<<SQL
SELECT NULL, T1.fichier, TC.code, T1.id,'{$this->name}'  , 0
FROM <tokens> T1
JOIN <tokens_tags> TT
    ON T1.id = TT.token_id AND 
       TT.type='right'
JOIN <tokens> T2
    ON T2.fichier = T1.fichier AND 
       TT.token_sub_id = T2.id
JOIN <tokens> T3
    ON T3.fichier = T1.fichier AND 
       T3.type='variable' AND 
       T3.droite between T2.droite AND T2.gauche 
LEFT JOIN <tokens> T4
    ON T4.fichier = T1.fichier AND 
       T4.droite=T3.droite -1 
JOIN <tokens_cache> TC
  ON TC.id = T3.id
WHERE T1.fichier like "%affectations_gpc%" AND
      T1.type = 'affectation' AND
      (T4.type IS NULL OR T4.type != '_array') AND 
      BINARY TC.code REGEXP '^$gpc_regexp'
SQL;
        $this->exec_query_insert('rapport', $query);
    }
}

?>