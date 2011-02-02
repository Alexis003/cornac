<?php 


class Quality_GpcAsArgument extends modules {
	protected	$title = 'Gpc passed as argument';
	protected	$description = 'Spot GPC variables used as argument of other functions (this is a good way to hide Gpc usage, but leads to security problems).';

	function __construct($mid) {
        parent::__construct($mid);
	}

	public function analyse() {
        $this->clean_report();

// @todo of course, update this useless query. :)
	    $query = <<<SQL
SELECT NULL, T1.file, TC.code, T1.id, '{$this->name}', 0
FROM <tokens> T1
JOIN <tokens> T2
    ON T1.file = T2.file AND
       T2.left BETWEEN T1.left AND T2.right AND
       T2.level = T1.level + 1 AND
       T2.type = 'variable' AND
       T2.code LIKE "$\_%"
JOIN <tokens_tags> TT
    ON TT.token_sub_id = T1.id AND
       TT.type = 'args'
JOIN <tokens> T3
    ON TT.token_id = T3.id
JOIN <tokens_cache> TC
    ON TC.id = T3.id
WHERE T1.type = 'arglist'
SQL;
        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>