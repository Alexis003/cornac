<?php 


class Structures_Constants extends modules {
	protected	$title = 'Constant structures';
	protected	$description = 'Spot structures that are basically constant : they are based on constant, literals. ';

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
SELECT T1.id
FROM <tokens> T1
WHERE type IN ('_constant','literals','class_constant','rawtext')
SQL;
        $this->exec_query_attributes($this->name, $query);

// @todo also support functioncall, array(), etc. 
// @todo : optimize this loop of 3. How? 
for ($i =0 ; $i < 5; $i++) {
	    $query = <<<SQL
SELECT T1.id 
FROM <tokens> T1
JOIN <tokens> T2
    ON T2.left BETWEEN T1.left AND T1.right AND
       T1.file = T2.file AND
       T1.level + 1 = T2.level
JOIN <report_attributes> TA
    ON TA.id = T2.id 
WHERE T1.type IN ('operation',
'comparison',
'logical',
'parenthesis',
'arglist',
'functioncall',
'keyvalue',
'concatenation',
'_new',
'sequence',
'block',
'_nsname',
'inclusion',
'noscream',
'ternaryop')
GROUP BY T1.id
HAVING SUM(IF(TA.Structures_Constants = 'Yes', 1, 0)) = COUNT(*)
SQL;
//        print $this->prepare_query($query);
        $this->exec_query_attributes($this->name, $query);
}

        return true;
	}
}

?>