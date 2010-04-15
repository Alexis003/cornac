<?php

class ldap_functions extends functioncalls {
	protected	$description = 'Liste des fonctions de dossier';
	protected	$description_en = 'usage of directory functions';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $this->functions = array('ldap_connect', 'ldap_close', 'ldap_bind', 'ldap_sasl_bind', 'ldap_unbind', 'ldap_read', 'ldap_list', 'ldap_search', 'ldap_free_result', 'ldap_count_entries', 'ldap_first_entry', 'ldap_next_entry', 'ldap_get_entries', 'ldap_first_attribute', 'ldap_next_attribute', 'ldap_get_attributes', 'ldap_get_values', 'ldap_get_values_len', 'ldap_get_dn', 'ldap_explode_dn', 'ldap_dn2ufn', 'ldap_add', 'ldap_delete', 'ldap_modify', 'ldap_mod_add', 'ldap_mod_replace', 'ldap_mod_del', 'ldap_errno', 'ldap_err2str', 'ldap_error', 'ldap_compare', 'ldap_sort', 'ldap_rename', 'ldap_get_option', 'ldap_set_option', 'ldap_first_reference', 'ldap_next_reference', 'ldap_parse_reference', 'ldap_parse_result', 'ldap_start_tls', 'ldap_set_rebind_proc');
	    parent::analyse();
	}
}

?>