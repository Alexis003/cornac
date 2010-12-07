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

class Structures_CaseWithoutBreak extends modules {
	protected	$title = 'case without break';
	protected	$description = 'Check that all case structure has a break case. It should be checked then, even if this may be valid.';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array();
	}

	public function analyse() {
        $this->clean_report();

// @todo of course, update this useless query. :)
	    $query = <<<SQL
SELECT NULL, T1.file, TC.code, T1.id, '{$this->name}', 0
FROM <tokens> T1
LEFT JOIN <tokens> T2 
    ON T2.left BETWEEN T1.left AND T1.right AND
       T1.file = T2.file AND
       T2.type = '_break'
JOIN <tokens_cache> TC
    ON TC.id = T1.id
WHERE T1.type = '_case' AND
      T2.id IS NULL
SQL;
        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>