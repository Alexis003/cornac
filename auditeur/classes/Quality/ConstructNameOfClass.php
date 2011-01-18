<?php 


class Quality_ConstructNameOfClass extends modules {
	protected	$title = 'Class constructors';
	protected	$description = 'Spot classes constructor (__construct, NameOfClasse)';

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
SELECT NULL, T1.file, concat(class, '::', T1.code), T1.id, '{$this->name}', 0
FROM <tokens> T1
    WHERE type = '_function' AND
          (code = class        OR
           code = '__construct'  )
SQL;
        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>