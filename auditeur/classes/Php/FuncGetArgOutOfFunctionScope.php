<?php



class Php_FuncGetArgOutOfFunctionScope extends modules {
	protected	$title = 'func_get_arg out of function scope';
	protected	$description = 'func_get_arg out of function scope. This was OK before 5.3, and not anymore.';

	function __construct($mid) {
        parent::__construct($mid);
	}

	public function analyse() {
        $this->clean_report();

// @todo of course, update this useless query. :)
	    $query = <<<SQL
SELECT NULL, T1.file, T1.code, T1.id, '{$this->name}', 0
FROM <tokens> T1
WHERE T1.type = '_functionname_' AND
      T1.code IN ('func_get_arg','func_get_args','func_num_args') AND
      T1.scope = 'global' AND
      T1.class = ''
SQL;
        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>