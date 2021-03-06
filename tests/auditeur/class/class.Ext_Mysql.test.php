<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 - 2011                                            |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.01 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.php.net/license/3_01.txt                                  |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Author: Damien Seguy <damien.seguy@gmail.com>                        |
   +----------------------------------------------------------------------+
 */
include_once('../../library/Cornac/Autoload.php');
spl_autoload_register('Cornac_Autoload::autoload');

class Ext_Mysql_Test extends Cornac_Tests_Auditeur
{
    public function testmysql_functions()  {
        $this->expected = array( 
'mysql',
'mysql_affected_rows',
'mysql_client_encoding',
'mysql_close',
'mysql_connect',
'mysql_data_seek',
'mysql_db_name',
'mysql_db_query',
'mysql_dbname',
'mysql_errno',
'mysql_error',
'mysql_escape_string',
'mysql_fetch_array',
'mysql_fetch_assoc',
'mysql_fetch_field',
'mysql_fetch_lengths',
'mysql_fetch_object',
'mysql_fetch_row',
'mysql_field_flags',
'mysql_field_len',
'mysql_field_name',
'mysql_field_seek',
'mysql_field_table',
'mysql_field_type',
'mysql_fieldflags',
'mysql_fieldlen',
'mysql_fieldname',
'mysql_fieldtable',
'mysql_fieldtype',
'mysql_free_result',
'mysql_freeresult',
'mysql_get_client_info',
'mysql_get_host_info',
'mysql_get_proto_info',
'mysql_get_server_info',
'mysql_info',
'mysql_insert_id',
'mysql_list_dbs',
'mysql_list_fields',
'mysql_list_processes',
'mysql_list_tables',
'mysql_listdbs',
'mysql_listfields',
'mysql_listtables',
'mysql_num_fields',
'mysql_num_rows',
'mysql_numfields',
'mysql_numrows',
'mysql_pconnect',
'mysql_ping',
'mysql_query',
'mysql_real_escape_string',
'mysql_result',
'mysql_select_db',
'mysql_selectdb',
'mysql_set_charset',
'mysql_stat',
'mysql_table_name',
'mysql_tablename',
'mysql_thread_id',
'mysql_unbuffered_query',

);
        $this->unexpected = array(/*'',*/);

        parent::generic_test();
    }
}
?>