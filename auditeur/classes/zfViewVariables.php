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

class zfViewVariables extends modules {
	protected	$title = 'Title for zfViewVariables';
	protected	$description = 'This is the special analyzer zfViewVariables (default doc).';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array();
	}

	public function analyse() {
        $this->clean_rapport();

// @todo of course, update this useless query. :)
	    $query = <<<SQL
SELECT NULL, T1.file, T2.code, T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens_cache> TC
ON TC.id = T1.id
JOIN <tokens_tags> TT
ON TT.token_sub_id = T1.id
JOIN jaguar_tags TT2
ON TT2.token_id = TT.token_id AND
   TT2.type = 'property'
JOIN <tokens> T2
ON T2.file = T1.file AND
   T2.id = TT2.token_sub_id
WHERE T1.type = 'property' AND
      TC.code LIKE "\$this->view"
SQL;
        $this->exec_query_insert('rapport', $query);

        return true;
	}
}

?>