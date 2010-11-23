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

class variables_unaffected extends modules {
	protected	$title = 'Never initialised variables';
	protected	$description = 'List of variables being used without initialisation';

    function __construct($mid) {
        parent::__construct($mid);
    }
    
    function dependsOn() {
        return array('variables', 'affectations_variables','keyval');
    }

	public function analyse() {
	    // @question : isn't TR1.file = TR2.file too restrictive? ç
	    // @todo take scope/class into account
	    // @todo take foreach into account
	    // @todo speed improvement here. 
        $query = <<<SQL
CREATE TEMPORARY TABLE tmp_variables_unaffected
SELECT element, file
FROM <rapport> TR1
WHERE TR1.module = 'affectations_variables'
SQL;
    	$this->exec_query($query);

        $query = <<<SQL
ALTER TABLE tmp_variables_unaffected ADD INDEX(element)
SQL;
    	$this->exec_query($query);

        $query = <<<SQL
ALTER TABLE tmp_variables_unaffected ADD INDEX(file)
SQL;
    	$this->exec_query($query);
    	
        $query = <<<SQL
SELECT NULL, TR1.file, TR1.element AS code, TR1.token_id, '{$this->name}', 0
FROM <rapport> TR1
LEFT JOIN tmp_variables_unaffected TR2
    ON TR1.element = TR2.element AND 
       TR1.file = TR2.file
WHERE TR1.module='variables' AND 
      TR2.element IS NULL
SQL;
    	$this->exec_query_insert('rapport',$query);

        $query = <<<SQL
DELETE FROM <rapport> 
WHERE element IN ('\$GLOBALS','\$_SESSION','\$_REQUEST',
                      '\$_GET','\$_POST','\$this','\$_FILES') AND
      module='{$this->name}'
SQL;
    	$this->exec_query($query);

        $query = <<<SQL
DELETE FROM CR1 
    USING <rapport> CR1, <rapport> CR2
WHERE CR1.module='{$this->name}' AND
      CR2.module='keyval' AND
      CR1.element = CR2.element AND
      CR1.file = CR2.file
SQL;
    	$this->exec_query($query);

        $query = <<<SQL
DROP TABLE tmp_variables_unaffected;
SQL;
    	$this->exec_query($query);

	    return true;
	}
}

?>