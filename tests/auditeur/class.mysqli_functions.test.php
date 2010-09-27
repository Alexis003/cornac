<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 Alter Way Solutions (France)                      |
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
 */include_once('Auditeur_Framework_TestCase.php');

class mysqli_functions_Test extends Auditeur_Framework_TestCase
{
    public function testmysqli_functions()  {
        $this->expected = array( 
'mysqli_affected_rows',
'mysqli_autocommit',
'mysqli_bind_param',
'mysqli_bind_result',
'mysqli_change_user',
'mysqli_character_set_name',
'mysqli_client_encoding',
'mysqli_close',
'mysqli_commit',
'mysqli_connect',
'mysqli_connect_errno',
'mysqli_connect_error',
'mysqli_data_seek',
'mysqli_debug',
'mysqli_dump_debug_info',
'mysqli_errno',
'mysqli_error',
'mysqli_escape_string',
'mysqli_execute',
'mysqli_fetch',
'mysqli_fetch_all',
'mysqli_fetch_array',
'mysqli_fetch_assoc',
'mysqli_fetch_field',
'mysqli_fetch_field_direct',
'mysqli_fetch_fields',
'mysqli_fetch_lengths',
'mysqli_fetch_object',
'mysqli_fetch_row',
'mysqli_field_count',
'mysqli_field_seek',
'mysqli_field_tell',
'mysqli_free_result',
'mysqli_get_cache_stats',
'mysqli_get_charset',
'mysqli_get_client_info',
'mysqli_get_client_stats',
'mysqli_get_client_version',
'mysqli_get_connection_stats',
'mysqli_get_host_info',
'mysqli_get_metadata',
'mysqli_get_proto_info',
'mysqli_get_server_info',
'mysqli_get_server_version',
'mysqli_get_warnings',
'mysqli_info',
'mysqli_init',
'mysqli_insert_id',
'mysqli_kill',
'mysqli_more_results',
'mysqli_multi_query',
'mysqli_next_result',
'mysqli_num_fields',
'mysqli_num_rows',
'mysqli_options',
'mysqli_param_count',
'mysqli_ping',
'mysqli_poll',
'mysqli_prepare',
'mysqli_query',
'mysqli_real_connect',
'mysqli_real_escape_string',
'mysqli_real_query',
'mysqli_reap_async_query',
'mysqli_refresh',
'mysqli_report',
'mysqli_rollback',
'mysqli_select_db',
'mysqli_send_long_data',
'mysqli_set_charset',
'mysqli_set_opt',
'mysqli_sqlstate',
'mysqli_stat',
'mysqli_stmt_affected_rows',
'mysqli_stmt_attr_get',
'mysqli_stmt_attr_set',
'mysqli_stmt_bind_param',
'mysqli_stmt_bind_result',
'mysqli_stmt_close',
'mysqli_stmt_data_seek',
'mysqli_stmt_errno',
'mysqli_stmt_error',
'mysqli_stmt_execute',
'mysqli_stmt_fetch',
'mysqli_stmt_field_count',
'mysqli_stmt_free_result',
'mysqli_stmt_get_result',
'mysqli_stmt_get_warnings',
'mysqli_stmt_init',
'mysqli_stmt_insert_id',
'mysqli_stmt_more_results',
'mysqli_stmt_next_result',
'mysqli_stmt_num_rows',
'mysqli_stmt_param_count',
'mysqli_stmt_prepare',
'mysqli_stmt_reset',
'mysqli_stmt_result_metadata',
'mysqli_stmt_send_long_data',
'mysqli_stmt_sqlstate',
'mysqli_stmt_store_result',
'mysqli_store_result',
'mysqli_thread_id',
'mysqli_thread_safe',
'mysqli_use_result',
'mysqli_warning_count',

        );
        $this->unexpeted = array(/*'',*/);

        parent::generic_test();
    }
}
?>