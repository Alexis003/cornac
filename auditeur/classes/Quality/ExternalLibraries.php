<?php 


class Quality_ExternalLibraries extends modules {
	protected	$title = 'Title for Quality_ExternalStructures';
	protected	$description = 'This is the special analyzer Quality_ExternalStructures (default doc).';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array('Quality_ExternalStructures');
	}

	public function analyse() {
        $this->clean_report();

        $list = modules::getPopLib();
        
        foreach($list as $ext => $characteristics) {
            $in = "'".join("', '", $characteristics['classes'])."'";

            // @doc search for usage as class extensions
            $query = <<<SQL
SELECT NULL, T1.file, '$ext', T1.id, '{$this->name}', 0
FROM <report> T1
WHERE T1.module = 'Quality_ExternalStructures' AND
      T1.element IN ($in)
GROUP BY '$ext'
SQL;
            $this->exec_query_insert('report', $query);
        }

        return true;
	}
}

?>