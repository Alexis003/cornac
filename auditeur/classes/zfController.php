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

class zfController extends modules {
	protected	$title = 'Controleurs ZF';
	protected	$description = 'Liste des fonctions méthodes de contrôleur pour le ZF (*Action)';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

        $concat = $this->concat("T3.class", "'->'","T3.code");
	    $query = <<<SQL
SELECT NULL, T1.fichier, $concat AS code, T3.id, 'zfController', 0
FROM <tokens> T1
JOIN <tokens_tags> TT 
ON T1.id = TT.token_id AND
   TT.type='extends'
JOIN <tokens> T2
ON T2.id = TT.token_sub_id AND
   T2.fichier=T1.fichier
JOIN <tokens> T3
ON T3.fichier = T2.fichier AND 
   T3.droite BETWEEN T1.droite AND T1.gauche AND
   T3.type = '_function'
WHERE T1.type = '_class' AND
T2.code IN ( "Application_Zend_Controller","Zend_Controller") AND
T3.code like "%Action"
SQL;
        $this->exec_query_insert('rapport', $query);
        
        return true;
	}
}

?>