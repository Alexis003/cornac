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
 */

require_once 'PHPUnit/Autoload.php'; 

$tests = array( 
'class.Variables_Names.test.php',
'class.Classes_News.test.php',
'class.affectations_variables.test.php',
'class.Ext_Headers.test.php',
'class.method_special.test.php',
'class.Php_Globals.test.php',
'class.Structures_Iffectations.test.php',
'class.Functions_Unused.test.php',
'class.Classes_Definitions.test.php',
'class.Php_Arobases.test.php',
'class.Classes_Properties.test.php',
'class.Classes_PropertiesUsed.test.php',
'class.Classes_Undefined.test.php',
'class.Classes_Unused.test.php',
'class.Php_Modules.test.php',
'class.Functions_Php.test.php',
'class.Ext_Xml.test.php',
'class.Functions_Occurrences.test.php',
'class.Functions_Emptys.test.php',
'class.Functions_DoubleDeclaration.test.php',
'class.Classes_DoubleDeclaration.test.php',
'class.Functions_Inclusions.test.php',
'class.statiques.test.php',
'class.html_tags.test.php',
'class.Functions_Undefined.test.php',
'class.Classes_MethodsCount.test.php',
'class.Variables_Variables.test.php',
'class.Classes_PropertiesUnused.test.php',
'class.Classes_PropertiesUndefined.test.php',
'class.block_of_call.test.php',
'class.Functions_ArglistCalled.test.php',
'class.Functions_ArglistDefined.test.php',
'class.Functions_ArglistDiscrepencies.test.php',
'class.multi_def_files.test.php',
'class.Classes_Php.test.php',
'class.affectations_direct_gpc.test.php',
'class.affectations_literals.test.php',
'class.concatenation_gpc.test.php',
'class.Ext_Upload.test.php',
'class.Variables_Unaffected.test.php',
'class.dangerous_combinaisons.test.php',
'class.gpc_affectations.test.php',
'class.Variables_OneLetter.test.php',
'class.Classes_Interfaces.test.php',
'class.Functions_WithoutReturns.test.php',
'class.session_variables.test.php',
'class.gpc_variables.test.php',
// 'class.Php_InclusionLinks.test.php',  @todo tests with dot format will come later
'class.inclusions_path.test.php',
'class.Literals_Long.test.php',
'class.Literals_Reused.test.php',
'class.tableaux.test.php',
'class.Ext_VarDump.test.php',
'class.defarray.test.php',
//'class.globals_link.test.php',
'class.multidimarray.test.php',
'class.Php_Throws.test.php',
'class.Zf_Classes.test.php',
'class.popular_libraries.test.php',
'class.Constants_Definitions.test.php',
'class.Structures_ForeachUnused.test.php',
'class.Classes_This.test.php',
'class.Php_References.test.php',
'class.Functions_CallingBack.test.php',
'class.functions_with_callback.test.php',
'class.Variables_LongNames.test.php',
'class.Php_RegexStrings.test.php',
'class.fluid_interface.test.php',
'class.Ext_Ereg.test.php',
'class.Ext_Errors.test.php',
'class.Ext_Image.test.php',
'class.Classes_PropertiesPublic.test.php',
'class.Ext_DieExit.test.php',
'class.keyval.test.php',
'class.Ext_Ldap.test.php',
'class.Functions_Handlers.test.php',
'class.Functions_ArglistUnused.test.php',
'class.Php_FunctionsConflict.test.php',
'class.Php_ConstantConflict.test.php',
'class.Php_ClassesConflict.test.php',
'class.Ext_Xdebug.test.php',
'class.Ext_Session.test.php',
'class.Ext_Dir.test.php',
'class.Ext_Mssql.test.php',
'class.Ext_Mysql.test.php',
'class.Ext_Mysqli.test.php',
'class.Ext_Execs.test.php',
'class.Ext_Evals.test.php',
'class.Ext_Filter.test.php',
'class.Ext_File.test.php',
'class.keyval_outside.test.php',
'class.Zf_Controller.test.php',
'class.Zf_Dependencies.test.php',
'class.Classes_Abstracts.test.php',
'class.Classes_Finals.test.php',
'class.Functions_ArglistReferences.test.php',
'class.Php_Keywords.test.php',
'class.Literals_InArglist.test.php',
'class.Classes_ToStringNoArg.test.php',
'class.Functions_UnusedReturn.test.php',
'class.method_without_ppp.test.php',
'class.Zf_Action.test.php',
'class.Php_Returns.test.php',
'class.Functions_CodeAfterReturn.test.php',
'class.Functions_Security.test.php',
'class.Functions_Recursive.test.php',
'class.Structures_LinesLoaded.test.php',
'class.Literals_RawtextWhitespace.test.php',
'class.Structures_LoopsInfinite.test.php',
'class.Structures_ComparisonConstants.test.php',
'class.Structures_LoopsNested.test.php',
'class.Ext_Random.test.php',
'class.Structures_IfWithoutComparison.test.php',
'class.Zf_Redirect.test.php',
'class.Zf_Session.test.php',
'class.Zf_SQL.test.php',
'class.Structures_SwitchWithoutDefault.test.php',
'class.Structures_CaseWithoutBreak.test.php',
'class.Structures_LoopsLong.test.php',
'class.Structures_LoopsOneLiner.test.php',
// new tests
);

foreach($tests as $i => $test ) {
    $file = trim($test); // @note precaution. I happened to leave some white space 
    if (!file_exists($file)) {
        unset($tests[$i]); 
        print "Test file '$test' not available : omitted\n";
        continue;
    }
    require (dirname(__FILE__)."/".$file);
    
    $code = file_get_contents(dirname(__FILE__)."/".$file);
    if (!preg_match('$class (.*?_Test) $', $code, $r)) {
        print "Couldn't find the test class in file '$file'\n";
        die();
    }
    
    $script = substr($file, 6, -9); 
    if (!file_exists("scripts/$script.php")) {
        print "Couldn't find the script file $script for the test in file '$file'\n";
        die();
    };
}
 
class Framework_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PHPUnit Framework');
 
         global $tests;
         
         foreach($tests as $test) {
             $test = substr($test, 6); // @doc remove class.
             $test = substr($test, 0, -4); // @doc remove .php
             $test = str_replace('.','_', $test); // @doc remove .
             $test = ucwords($test);
             $test = str_replace('_test','_Test', $test);
             
            $suite->addTestSuite($test);
         }
 
        return $suite;
    }
}
?>
