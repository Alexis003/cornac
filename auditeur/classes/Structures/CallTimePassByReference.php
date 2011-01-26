<?php 


class Structures_CallTimePassByReference extends modules {
	protected	$title = 'Title for Structures_CallTimePassByReference';
	protected	$description = 'This is the special analyzer Structures_CallTimePassByReference (default doc).';

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
SELECT NULL, T1.file, T1.code, T1.id, '{$this->name}', 0
    FROM <tokens> T1
    WHERE type = 'variable'
SQL;
        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>