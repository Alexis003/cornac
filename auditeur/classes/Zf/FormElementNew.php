<?php 


class Zf_FormElementNew extends modules {
	protected	$title = 'Zend_FormElement instanciated';
	protected	$description = 'List elements instantiated. ';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array('Classes_News', 'Zf_FormElement');
	}

	public function analyse() {
        $this->clean_report();

// @doc classes instantiated from herited classes
	    $query = <<<SQL
SELECT NULL, TR1.file, TR1.element, TR1.token_id, '{$this->name}', 0
FROM <report> TR1
JOIN <report> TR2
    ON TR1.module = 'Classes_News' AND
       TR2.module = 'Zf_FormElement' AND
       TR1.element = TR2.element
SQL;
        $this->exec_query_insert('report', $query);

// @doc classes directly instantiated from Zend
	    $query = <<<SQL
SELECT NULL, TR1.file, TR1.element, TR1.token_id, '{$this->name}', 0
FROM <report> TR1
WHERE TR1.module = 'Classes_News' AND
      TR1.element LIKE "Zend_Form_Element%"
SQL;
        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>