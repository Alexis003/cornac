<?php



class Php_SetlocaleWithString extends modules {
	protected	$title = 'Setlocale() with LC_ string';
	protected	$description = 'Spot usage of setlocale with string, and not constants. This is an incompatibility for PHP 5.3.';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array();
	}

	public function analyse() {
        $this->clean_report();

	    $query = <<<SQL
SELECT NULL, T1.file, T3.code, T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens> T2
    ON T2.file = T1.file AND
       T2.left = T1.left + 3
JOIN <tokens> T3
    ON T3.file = T1.file AND
       T3.left = T2.left + 1
WHERE T1.type='functioncall' AND 
      T1.code = 'setlocale' AND
      T3.type = "literals" AND
      T3.code LIKE "LC_%"
SQL;
        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>