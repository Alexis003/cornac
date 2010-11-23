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

class inclusions2 extends modules {
	protected	$title = 'Inclusion network';
	protected	$description = 'Relations between files, based on inclusions';

	function __construct($mid) {
        parent::__construct($mid);
        
        $this->format = modules::FORMAT_DOT;
	}
	
	public function analyse() {
        $this->clean_rapport();
        
        $query = <<<SQL
INSERT INTO <rapport_dot> 
SELECT distinct T1.file, T3.code,T1.file, '{$this->name}'
FROM <tokens> T1
JOIN <tokens> T2
    ON T2.left  = T1.left + 1 AND
       T2.file = T1.file
JOIN <tokens_cache> T3
    ON T3.id = T2.id
      AND T3.file = T2.file
WHERE T1.type='inclusion'
SQL;
        $res = $this->exec_query($query);
        
       $query = <<<SQL
INSERT INTO <rapport_dot> 
SELECT distinct T1.file, T2.code, T1.file, '{$this->name}'
FROM <tokens> T1
JOIN <tokens> T2
    ON T2.left  = T1.left + 1 AND
       T2.file = T1.file
WHERE T1.type='inclusion' AND
      T2.type in ('literals','variable')
SQL;
        $res = $this->exec_query($query);

        $concat = $this->concat('"inc/"','T4.code',"'/'",'T4.code',"'.inc'");
        
       $query = <<<SQL
INSERT INTO <rapport_dot> 
SELECT T1.file, REPLACE($concat,'"', ''), T1.file, '{$this->name}' 
FROM <tokens> T1
JOIN <tokens_tags> TT 
    ON TT.token_id = T1.id
JOIN <tokens> T2
    ON TT.token_sub_id = T2.id AND T1.file = T2.file and TT.type='function' and T2.code='loadLibrary'
JOIN <tokens_tags> TT2 
    ON TT2.token_id = T1.id
JOIN <tokens> T3
    ON TT2.token_sub_id = T3.id AND T1.file = T3.file and TT2.type='args'
JOIN <tokens> T4
    ON T1.file = T4.file and T4.type='literals' AND T4.left between T3.left and T3.right
WHERE T1.type='functioncall'
SQL;
        $res = $this->exec_query($query);

       include_once('../libs/path_normaliser.php');
       $query = <<<SQL
SELECT * FROM <rapport_dot> WHERE module='{$this->name}'
SQL;
        $res = $this->exec_query($query);

    while($row = $res->fetch()) {
        $row['b'] = str_replace( array("\"", "'"), array('',''), $row['b']);
        
        // @todo need a mechanism to translate path into absolute value 
        $variables = array(
            '$name.' => 'ModuleManager',
            '$cache_name.' =>  'cache', 
            '$u_module.' => 'Cache', 
            '$u_path.' => 'Cache',
            
        );
        
        $row['b'] = str_replace(array_keys($variables), array_values($variables), $row['b']);
        $row['b'] = path_normaliser(dirname($row['a']).'/', $row['b']);
        $row['b'] = addslashes($row['b']);
        $row[1] = addslashes($row[1]);

       $query = <<<SQL
UPDATE <rapport_dot>
   SET b = '{$row['b']}'
WHERE module='{$this->name}' AND 
      b = '{$row[1]}' AND
      a = '{$row['a']}'
SQL;
        $this->exec_query($query);
        }
	}
}

?>