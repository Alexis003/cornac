<?php 


class Functions_Arguments extends modules {
	protected	$title = 'Spot Function arguments in definitions';
	protected	$description = 'Spot Function arguments in defintitions.';

	function __construct($mid) {
        parent::__construct($mid);
	}

	public function analyse() {
        $this->clean_report();

	    $query = <<<SQL
SELECT T2.id 
FROM webaixia T1
JOIN webaixia T2
    ON T2.file = T1.file AND
       T2.left = T1.left + 5
WHERE T2.type = 'arglist' AND
      T1.type = '_function'
SQL;
        $this->exec_query_attributes($this->name, $query);

        return true;
	}
}

?>