<?php 


class Functions_Arginit extends modules {
	protected	$title = 'Arguments initialisations';
	protected	$description = 'Arguments initialisations';

	function __construct($mid) {
        parent::__construct($mid);
	}

	public function analyse() {
        $this->clean_report();

	    $query = <<<SQL
SELECT T3.id 
FROM <tokens> T1
JOIN <tokens> T2
    ON T2.file = T1.file AND
       T2.left = T1.left + 5
JOIN <tokens> T3
    ON T3.file = T1.file AND
       T3.left BETWEEN T2.left AND T2.right AND
       T3.level = T2.level + 1 AND
       T3.type = 'affectation'
WHERE T2.type = 'arglist' AND
      T1.type = '_function'
SQL;
        $this->exec_query_attributes($this->name, $query);

        return true;
	}
}

?>