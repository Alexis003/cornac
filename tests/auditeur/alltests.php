<?php
//ini_set('include_path', ".:".dirname(__FILE__));

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
'class.functions_undefined.test.php',
'class.functions_unused.test.php',
'class.classes.test.php',
'class.arobases.test.php',
'class.properties_defined.test.php',
'class.properties_used.test.php',
//'class.array_duplication.test.php',
'class.classes_undefined.test.php',
'class.classes_unused.test.php',
'class.php_modules.test.php',
'class.php_functions.test.php',
'class.xml_functions.test.php',
'class.functions_frequency.test.php',
'class.emptyfunctions.test.php',

// Prochain tests
);

foreach($tests as $i => $test ) {
    $fichier = $test;
    if (!file_exists($fichier)) {
        unset($tests[$i]); 
        print "Tests $test introuvable (pas de fichier $fichier) : omis\n";
        continue;
    }
    require (dirname(__FILE__)."/".$fichier);
    
    $code = file_get_contents(dirname(__FILE__)."/".$fichier);
    if (!preg_match('$class (.*?_Test) $', $code, $r)) {
        print "Impossible de trouver la classe de test dans '$fichier'\n";
        die();
    }
    
    /*
    $class = $r[1];
    $methods = get_class_methods($class);
    $methods = preg_grep('$^test$', $methods);

    preg_match('$test(.*)(\d+)$', $methods[0], $r);
    $nom = strtolower($r[1]);
    
    foreach($methods as $id => $method) {
        $methods[$id] = preg_replace('$\D+$', '', $method);
    }
    
    $lestests = glob('scripts/'.$nom.'.*');
    
    foreach($lestests as $id => $test) {
        $script = preg_replace('$\D+$', '', $test);
        
        if (!in_array($script, $methods)) {
            print "Il manque une méthode pour le script $script de $nom\n";
        }
    }

    $lestests = glob('exp/'.$nom.'.*');
    
    foreach($lestests as $id => $test) {
        $script = preg_replace('$\D+$', '', $test);
        
        if (!in_array($script, $methods)) {
            print "Il manque une méthode pour l'attendu $script de $nom\n";
        }
    }
    */
}
 
class Framework_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PHPUnit Framework');
 
         global $tests;
         
         foreach($tests as $test) {
             $test = substr($test, 6); // exist le class.
             $test = substr($test, 0, -4); // exist le .php
             $test = str_replace('.','_', $test); // exit le .
             $test = ucwords($test);
             $test = str_replace('_test','_Test', $test);
             
            $suite->addTestSuite($test);
         }
 
        return $suite;
    }
}
?>