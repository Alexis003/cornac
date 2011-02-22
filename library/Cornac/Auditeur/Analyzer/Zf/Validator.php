<?php



class Cornac_Auditeur_Analyzer_Zf_Validator extends Cornac_Auditeur_Analyzer
 {
	protected	$title = 'Zend Validator classes';
	protected	$description = 'Spot classes that extends Zend_Validate ';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array('Classes_Hierarchy');
	}

	public function analyse() {
        $this->clean_report();

// @todo searching for herited classes from a framework is a common task. Make this a generic class 

// @doc herited usage of Zend Framework element (one heritage)
	    $query = <<<SQL
SELECT NULL, T1.file, T1.code, T1.id, '{$this->name}', 0
FROM <report_dot> TD
JOIN <tokens> T1
    ON T1.code = TD.b AND
       T1.type = '_classname_'
WHERE TD.a LIKE "Zend_Validate%" AND
      TD.module = 'Classes_Hierarchy'
SQL;
        $this->exec_query_insert('report', $query);

// @doc herited usage of Zend Framework element (2nd level heritage)
	    $query = <<<SQL
SELECT NULL, T1.file, T1.code, T1.id, '{$this->name}', 0
FROM <report_dot> TD
JOIN <report_dot> TD2
    ON TD.b = TD2.a AND
       TD2.module = 'Classes_Hierarchy'
JOIN <tokens> T1
    ON T1.code = TD2.b AND
       T1.type = '_classname_'
WHERE TD.a LIKE "Zend_Validate%" AND
      TD.module = 'Classes_Hierarchy'
SQL;
        $this->exec_query_insert('report', $query);

// @doc herited usage of Zend Framework element (3rd level heritage)
	    $query = <<<SQL
SELECT NULL, T1.file, T1.code, T1.id, '{$this->name}', 0
FROM <report_dot> TD
JOIN <report_dot> TD2
    ON TD.b = TD2.a AND
       TD2.module = 'Classes_Hierarchy'
JOIN <report_dot> TD3
    ON TD2.b = TD3.a AND
       TD3.module = 'Classes_Hierarchy'
JOIN <tokens> T1
    ON T1.code = TD3.b AND
       T1.type = '_classname_'
WHERE TD.a LIKE "Zend_Validate%" AND
      TD.module = 'Classes_Hierarchy'
SQL;
        $this->exec_query_insert('report', $query);
 
// @doc herited usage of Zend Framework element (4th level heritage)
	    $query = <<<SQL
SELECT NULL, T1.file, T1.code, T1.id, '{$this->name}', 0
FROM <report_dot> TD
JOIN <report_dot> TD2
    ON TD.b = TD2.a AND
       TD2.module = 'Classes_Hierarchy'
JOIN <report_dot> TD3
    ON TD2.b = TD3.a AND
       TD3.module = 'Classes_Hierarchy'
JOIN <report_dot> TD4
    ON TD3.b = TD4.a AND
       TD4.module = 'Classes_Hierarchy'
JOIN <tokens> T1
    ON T1.code = TD3.b AND
       T1.type = '_classname_'
WHERE TD.a LIKE "Zend_Validate%" AND
      TD.module = 'Classes_Hierarchy'
SQL;
        $this->exec_query_insert('report', $query);

// @todo 5th level heritage ? 

        return true;
	}
}

?>