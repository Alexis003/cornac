<?php 


class Functions_CodeAfterThrow extends modules {
	protected	$title = 'Dead code after throw';
	protected	$description = 'Spot dead code after throw. ';

	function __construct($mid) {
        parent::__construct($mid);
	}

	public function analyse() {
        $this->clean_report();

	    $query = <<<SQL
SELECT NULL, T1.file, CONCAT(T1.class, "::",T1.scope), T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens> T2
    ON T1.file = T2.file AND
       T1.left BETWEEN T2.left AND T2.right AND
       T2.type='_function'
WHERE T1.type='_throw' AND
      T2.right != T1.right + 2 AND 
      T2.level = T1.level - 2
SQL;
        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>