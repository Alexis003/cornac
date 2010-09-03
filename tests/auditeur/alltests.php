<?php

require_once 'PHPUnit/Framework.php'; 

$tests = array( 
'class.variables.test.php',
'class.constantes.test.php',
'class._new.test.php',
'class.affectations_variables.test.php',
'class.headers.test.php',
'class.method_special.test.php',
'class.globals.test.php',
'class.iffectations.test.php',
'class.functions_unused.test.php',
'class.classes.test.php',
'class.arobases.test.php',
'class.properties_defined.test.php',
'class.properties_used.test.php',
'class.classes_undefined.test.php',
'class.classes_unused.test.php',
'class.php_modules.test.php',
'class.php_functions.test.php',
'class.xml_functions.test.php',
'class.functions_frequency.test.php',
'class.emptyfunctions.test.php',
'class.doubledeffunctions.test.php',
'class.doubledefclass.test.php',
'class.inclusions.test.php',
'class.statiques.test.php',
'class.html_tags.test.php',
'class.undeffunctions.test.php',
'class.classes_nb_methods.test.php',
'class.variablesvariables.test.php',
'class.unused_properties.test.php',
'class.undefined_properties.test.php',
'class.block_of_call.test.php',
'class.arglist_call.test.php',
'class.arglist_def.test.php',
'class.arglist_disc.test.php',
'class.multi_def_files.test.php',
'class.php_classes.test.php',
'class.affectations_direct_gpc.test.php',
'class.affectations_literals.test.php',
'class.concatenation_gpc.test.php',
'class.upload_functions.test.php',
'class.variables_unaffected.test.php',
'class.dangerous_combinaisons.test.php',
'class.gpc_affectations.test.php',
'class.variables_one_letter.test.php',
'class.interfaces.test.php',
'class.functions_without_returns.test.php',
'class.session_variables.test.php',
'class.gpc_variables.test.php',
// 'class.inclusions2.test.php',  @todo tests with dot format will come later
'class.inclusions_path.test.php',
'class.literals_long.test.php',
'class.literals_reused.test.php',
'class.tableaux.test.php',
'class.vardump.test.php',
'class.defarray.test.php',
//'class.globals_link.test.php',
'class.multidimarray.test.php',
'class.thrown.test.php',

// Prochain tests
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