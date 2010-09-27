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

class keyval_outside extends modules {
	protected	$title = 'Clé/valeurs hors de la boucle';
	protected	$description = 'Identifie les clé/valeur qui sont hors de la boucle de leur initialisation.';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array('keyval');
	}
	
	public function analyse() {
        $this->clean_rapport();

// @rfu SELECT TR1.element, T1.droite, T1.gauche, T2.droite, T1.fichier, T1.ligne, T2.ligne

	    $query = <<<SQL
SELECT NULL, T1.fichier, T2.code, T2.id, '{$this->name}',0
FROM <rapport> TR1
JOIN <tokens> T1 
    ON T1.id = TR1.token_id
JOIN  <tokens> T2
    ON T1.fichier = T2.fichier AND
       T1.class = T2.class AND
       T1.scope = T2.scope AND
       T2.code = TR1.element AND
       T2.droite > T1.gauche
WHERE TR1.module='keyval';
SQL;
        $this->exec_query_insert('rapport', $query);
        
        return true;
	}
}

?>