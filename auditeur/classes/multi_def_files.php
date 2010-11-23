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

class multi_def_files extends modules {
	protected	$title = 'Multi declaration files';
	protected	$description = 'Files that declare several structures (classes, functions and global code).';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

	    $query = <<<SQL
CREATE TEMPORARY TABLE multi_def_files
SELECT DISTINCT T1.file AS file,  if (class= '', scope, class) AS context
FROM <tokens> T1
WHERE T1.type NOT IN ('codephp','sequence')
SQL;
        $res = $this->exec_query($query);

	    $query = <<<SQL
INSERT INTO <rapport> 
    SELECT NULL, T1.file, T1.context, 0, '{$this->name}', 0
    FROM multi_def_files T1
SQL;
        $res = $this->exec_query($query);

	    $query = <<<SQL
DROP TABLE multi_def_files
SQL;
        $res = $this->exec_query($query);

        return true;
    }
}

?>