<?php

class mysql_functions extends functioncalls {
	protected	$title = 'Fonctions de mysql';
	protected	$description = 'Liste des fonctions de l\'extension de mysql de PHP';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
	    $this->functions = array('mysql_connect', 
	                             'mysql_pconnect', 
	                             'mysql_close', 
	                             'mysql_select_db', 
	                             'mysql_query', 
	                             'mysql_unbuffered_query', 
	                             'mysql_db_query', 
	                             'mysql_list_dbs', 
	                             'mysql_list_tables', 
	                             'mysql_list_fields', 
	                             'mysql_list_processes',
	                             'mysql_error', 
	                             'mysql_errno', 
	                             'mysql_affected_rows',
	                             'mysql_insert_id', 
	                             'mysql_result', 
	                             'mysql_num_rows', 
	                             'mysql_num_fields',
	                             'mysql_fetch_row',
	                             'mysql_fetch_array', 
	                             'mysql_fetch_assoc', 
	                             'mysql_fetch_object',
	                             'mysql_data_seek', 
	                             'mysql_fetch_lengths',
	                             'mysql_fetch_field',
	                             'mysql_field_seek',
	                             'mysql_free_result',
	                             'mysql_field_name', 
	                             'mysql_field_table',
	                             'mysql_field_len', 
	                             'mysql_field_type', 
	                             'mysql_field_flags',
	                             'mysql_escape_string',
	                             'mysql_real_escape_string', 
	                             'mysql_stat',
	                             'mysql_thread_id', 
	                             'mysql_client_encoding', 
	                             'mysql_ping', 
	                             'mysql_get_client_info', 
	                             'mysql_get_host_info', 
	                             'mysql_get_proto_info', 
	                             'mysql_get_server_info',
	                             'mysql_info', 
	                             'mysql_set_charset', 
	                             'mysql_fieldname', 
	                             'mysql_fieldtable', 
	                             'mysql_fieldlen', 
	                             'mysql_fieldtype',
	                             'mysql_fieldflags', 
	                             'mysql_selectdb', 
	                             'mysql_freeresult', 
	                             'mysql_numfields', 
	                             'mysql_numrows', 
	                             'mysql_listdbs', 
	                             'mysql_listtables',
	                             'mysql_listfields', 
	                             'mysql_db_name', 
	                             'mysql_dbname', 
	                             'mysql_tablename', 
	                             'mysql_table_name');
	    parent::analyse();
	    
	    return true;
	}
}

?>