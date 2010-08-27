<?php

class html_tags extends noms {
	protected	$title = 'Balises HTML';
	protected	$description = 'Liste des literaux contenant du code HTML';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
INSERT INTO <rapport>
    SELECT NULL, T1.fichier, T1.code AS code, T1.id, '{$this->name}'
    FROM <tokens> T1
    WHERE type='literals' AND code like "%<%>%"
SQL;
        $this->exec_query($query);
        
        // @doc liste des tags!DOCTYPE|a|abbr|acronym|address|applet|area|b|base|basefont|bdo|big|blockquote|body|br|button|caption|center|cite|code|col|colgroup|dd|del|dfn|dir|div|dl|dt|em|fieldset|font|form|frame|frameset|h1|head|hr|html|i|iframe|img|input|ins|isindex|kbd|label|legend|li|link|map|menu|meta|noframes|noscript|object|ol|optgroup|option|p|param|pre|q|s|samp|script|select|small|span|strike|strong|style|sub|sup|table|tbody|td|textarea|tfoot|th|thead|title|tr|tt|u|ul|var|xmp|
        // @question why not make a xhtml/html analyzer?
        return true;
	}
}

?>