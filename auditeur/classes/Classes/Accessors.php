<?php 


class Classes_Accessors extends modules {
	protected	$title = 'Title for Classes_Accessors';
	protected	$description = 'This is the special analyzer Classes_Accessors (default doc).';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array();
	}

	public function analyse() {
        $this->clean_report('Classes_Properties','Classes_Hierarchy','Classes_MethodsDefinition');

	    $query = <<<SQL
SELECT NULL, T1.file, T2.element, T1.id, '{$this->name}' , 0
FROM <report> T1
JOIN <report> T2
    ON T2.file = T1.file AND
       (replace(T1.element, '$', 'get') = T2.element OR 
        replace(T1.element, '$', 'set') = T2.element ) AND
       T2.module = 'Classes_MethodsDefinition'
WHERE T1.module = 'Classes_Properties'
SQL;
        $this->exec_query_insert('report', $query);

	    $query = <<<SQL
SELECT NULL, T1.file, T2.element, T1.id, '{$this->name}' , 0
FROM <report> T1
JOIN <report_dot> TD1
    ON left(T1.element, locate('->',T1.element) - 1) = TD1.a AND
       TD1.module = 'Classes_Hierarchy'
JOIN <report> T2
    ON T2.file = T1.file AND
       (concat(TD1.b,'->get',right(T1.element, length(T1.element) - 3 - length(TD1.b))) = T2.element OR 
        concat(TD1.b,'->set',right(T1.element, length(T1.element) - 3 - length(TD1.b))) = T2.element   ) AND
       T2.module = 'Classes_MethodsDefinition'
WHERE T1.module = 'Classes_Properties'
SQL;
        $this->exec_query_insert('report', $query);

	    $query = <<<SQL
SELECT NULL, T1.file, T2.element, T1.id, '{$this->name}' , 0
FROM <report> T1
JOIN <report_dot> TD1
    ON left(T1.element, locate('->',T1.element) - 1) = TD1.a AND
       TD1.module = 'Classes_Hierarchy'
JOIN <report_dot> TD2
    ON TD2.a = TD1.b AND
       TD2.module = 'Classes_Hierarchy'
JOIN <report> T2
    ON T2.file = T1.file AND
       (concat(TD2.b,'->get',right(T1.element, length(T1.element) - 3 - length(TD1.b))) = T2.element OR 
        concat(TD2.b,'->set',right(T1.element, length(T1.element) - 3 - length(TD1.b))) = T2.element   ) AND
       T2.module = 'Classes_MethodsDefinition'
WHERE T1.module = 'Classes_Properties'
SQL;
        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>