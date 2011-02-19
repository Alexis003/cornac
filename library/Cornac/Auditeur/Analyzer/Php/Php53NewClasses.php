<?php



class Cornac_Auditeur_Analyzer_Php_Php53NewClasses extends Cornac_Auditeur_Analyzer
 {
	protected	$title = 'Title for Php_Php53NewClasses';
	protected	$description = 'This is the special analyzer Php_Php53NewClasses (default doc).';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array("Classes_News");
	}

	public function analyse() {
        $this->clean_report();

        $in = "'".join("','", array('DateInterval',
'DatePeriod',
'Phar',
'PharData',
'PharException',
'PharFileInfo',
'FilesystemIterator',
'GlobIterator',
'MultipleIterator',
'RecursiveTreeIterator',
'SplDoublyLinkedList',
'SplFixedArray',
'SplHeap',
'SplMaxHeap',
'SplMinHeap',
'SplPriorityQueue',
'SplQueue',
'SplStack',))."'";

	    $query = <<<SQL
SELECT NULL, T1.file, T1.element, T1.id, '{$this->name}', 0
FROM <report> T1
WHERE T1.module = 'Classes_News' AND
      T1.element IN ($in)
SQL;
        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>