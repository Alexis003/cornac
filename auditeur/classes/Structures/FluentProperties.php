<?php 


class Structures_FluentProperties extends modules {
	protected	$title = 'Fluent interfaces with properties';
	protected	$description = 'Spot long chaining of properties call ($x->y->z->a->b->c).';

	function __construct($mid) {
        parent::__construct($mid);
	}

	public function analyse() {
        $this->clean_report();

	    $query = <<<SQL
SELECT NULL, T1.file, CONCAT(beginning.code, '->', GROUP_CONCAT(T3.code ORDER BY T3.left  SEPARATOR '->')) , T1.id, '{$this->name}',0
FROM <tokens> T1
INNER JOIN (
    SELECT T3.id, T1.file, T3.left, T3.right, T3.code
    FROM <tokens> T1
    JOIN <tokens> T2
    ON T1.file = T2.file AND
       T2.type = 'property' AND
       T2.left = T1.left - 1
    JOIN <tokens> T3
    ON T1.file = T3.file AND
       T3.type = 'variable' AND
       T3.left = T1.left + 1
    WHERE T1.type = 'property'
) beginning
ON T1.file = beginning.file AND
   type='property' AND 
   T1.left < beginning.left AND 
   T1.right > beginning.right
JOIN <tokens_tags> TT
    ON TT.token_id = T1.id AND
       TT.type='property'
JOIN <tokens> T3
    ON T3.file = beginning.file AND
       T3.id = TT.token_sub_id
GROUP BY beginning.id
HAVING COUNT(*) > 1
SQL;
        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>