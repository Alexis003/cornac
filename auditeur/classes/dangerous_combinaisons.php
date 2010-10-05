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

class dangerous_combinaisons extends modules {
	protected	$title = 'Combinaisons dangereuses';
	protected	$description = 'Liste de fichiers ayant des combinaisons dangereuses d\'elements (ex. $_POST et shell_exec).';

	function __construct($mid) {
        parent::__construct($mid);
	}

	function dependsOn() {
	    return array('affectations_variables');
	}

	public function analyse() {
        $this->clean_rapport();
        
        $combinaisons = parse_ini_file('../dict/combinaisons.ini', true);

        foreach ($combinaisons as $nom => $combinaison) {
            $in = "'".join("','", $combinaison['combinaison'])."'";
            $count = count($combinaison['combinaison']);
            // @todo : this shouldn't be sufficient. One must work on distinct occurences... may be a sub query will do

// @note : some token duplicate code from other tokens (like functioncall, which have no code by itself, but get a copy of their name for easy reference)
// @note so, we need to ignore some types. 
            $query = <<<SQL
SELECT NULL, T1.fichier, '$nom', T1.code, '{$this->name}', 0
FROM <tokens> T1
WHERE T1.type NOT IN ('functioncall','method')
GROUP BY fichier
HAVING SUM(IF (code IN ($in), 1, 0)) >= $count
SQL;
            $this->exec_query_insert('rapport', $query);
        }
        return true;
	}
}

?>