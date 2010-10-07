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

class literals_as_argref extends modules {
	protected	$title = 'Litéraux passés comme arguments';
	protected	$description = 'Literal values passed as argument of function, when the former expect';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array();
	}

	public function analyse() {
        $this->clean_rapport();

	    $query = <<<SQL
DROP TABLES IF EXISTS literals_as_argref_calls, literals_as_argref_definitions
SQL;
        $this->exec_query($query);
// @todo drop the above

	    $query = <<<SQL
SELECT @i := 0;
SQL;
        $this->exec_query($query);

	    $query = <<<SQL
SELECT @id := 0;
SQL;
        $this->exec_query($query);

// @todo make temporary
	    $query = <<<SQL
CREATE TABLE literals_as_argref_definitions
SELECT  T1.fichier AS file, 
        T4.class AS class, 
        T4.scope AS scope, 
        T3.type,
        T3.code,
       if (@id = T2.id, @i := @i + 1, LEAST(@id := T2.id , @i := 0 )) AS rank
FROM <tokens> T1
JOIN <tokens> T2
    ON T2.fichier = T1.fichier AND 
       T2.droite = T1.droite + 3 AND
       T2.type = 'arglist'
JOIN <tokens> T3
    ON T3.fichier = T1.fichier AND
       T3.level = T2.level + 1 AND
       T3.droite BETWEEN T2.droite AND T2.gauche
JOIN <tokens> T4
    ON T4.fichier = T1.fichier AND
       T4.droite = T1.droite + 1
WHERE T1.type='_function'
SQL;
        $this->exec_query($query);

// @note process only functions (not methods yet)
	    $query = <<<SQL
SELECT GROUP_CONCAT(distinct scope SEPARATOR "','") AS list 
FROM literals_as_argref_definitions 
WHERE class=''
SQL;
        $res = $this->exec_query($query);
        $rows = $res->fetch(PDO::FETCH_ASSOC);
        $in = "'".$rows['list']."'";

	    $query = <<<SQL
SELECT @i := 0;
SQL;
        $this->exec_query($query);

	    $query = <<<SQL
SELECT @id := 0;
SQL;
        $this->exec_query($query);

// @todo make TEMPORARY
	    $query = <<<SQL
CREATE  TABLE literals_as_argref_calls
SELECT T3.fichier, 
       T1.code, 
       T2.id, 
       T4.type, 
       if (@id = T3.id, @i := @i + 1, LEAST(@id := T3.id , @i := 0 )) AS rank
FROM <tokens> T1
JOIN <tokens> T2
    ON T2.fichier = T1.fichier AND
       T2.droite = T1.droite - 1 AND
       T2.type = 'functioncall'
JOIN <tokens> T3
    ON T3.fichier = T1.fichier AND
       T3.droite = T1.gauche + 1 AND
       T3.type = 'arglist'
JOIN <tokens> T4
    ON T4.fichier = T1.fichier AND
       T4.level = T3.level + 1 AND
       T4.droite BETWEEN T3.droite AND T3.gauche
WHERE T1.code IN ($in);

SQL;
        $this->exec_query($query);

	    $query = <<<SQL
DROP TABLES literals_as_argref_calls, literals_as_argref_definitions
SQL;
        $this->exec_query($query);
// @todo activate this

        return true;
	}
}

?>