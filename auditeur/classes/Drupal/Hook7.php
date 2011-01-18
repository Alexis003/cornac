<?php 


class Drupal_Hook7 extends modules {
	protected	$title = 'Spot Drupal hooks';
	protected	$description = 'Spot function with Drupal7 hook suffixes. The more there are, the more likely the file will be a Drupal 7 module';

	function __construct($mid) {
        parent::__construct($mid);
        $this->hook_regexp = '_('.join('|',modules::getDrupal7Hooks()).')$';
	}

	function dependsOn() {
	    return array('Functions_Definitions');
	}

	public function analyse() {
        $this->clean_report();

	    $query = <<<SQL
SELECT NULL, T1.file, T1.element, T1.id, '{$this->name}', 0
    FROM <report> T1
    WHERE T1.module = 'Functions_Definitions' AND
          T1.element REGEXP '{$this->hook_regexp}'
SQL;
        $this->exec_query_insert('report', $query);

        return true;
	}
}

?>