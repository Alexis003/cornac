<?php 


class Variables_StrangeChars extends modules {
	protected	$title = 'Variables with strange chars';
	protected	$description = 'List variables whose name contains caracters that are no letter, figure or _ (and $). This is usually a bad idea';

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
WHERE T1.type='variable' AND
      T1.code REGEXP '[^a-z\$_0-9]'
SQL;
        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>