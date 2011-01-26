<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 - 2011 Alter Way Solutions (France)               |
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
//ini_set('include_path', ".:".dirname(__FILE__));

//require_once 'PHPUnit/Framework.php'; 

$tests = array( 
'class.commentaire.test.php',
'class.affectation.test.php',
'class.ternaryop.test.php',
'class.codephp.test.php',
'class.constant.test.php',
'class.functioncall.test.php',
'class.inclusion.test.php',
'class.literals.test.php',
'class.operation.test.php',
'class.sequence.test.php',
'class.variable.test.php',
'class.concatenation.test.php',
'class.noscream.test.php',
'class.ifthen.test.php',
'class.property.test.php',
'class.logical.test.php',
'class.not.test.php',
'class.array.test.php',
'class.method.test.php',
'class.plusplus.test.php',
'class.block.test.php',
'class.foreach.test.php',
'class.comparison.test.php',
'class.for.test.php',
'class.opappend.test.php',
'class.break.test.php',
'class.new.test.php',
'class.rawtext.test.php',
'class.arrayfct.test.php',
'class.switch.test.php',
'class.global.test.php',
'class.return.test.php',
'class.function.test.php',
'class.keyvalue.test.php',
'class.class.test.php',
'class.while.test.php',
'class.reference.test.php',
'class.sign.test.php',
'class.cast.test.php',
'class.method_static.test.php',
'class.static.test.php',
'class.try.test.php',
'class.bitshift.test.php',
'class.throw.test.php',
'class.constant_static.test.php',
'class.dowhile.test.php',
'class.invert.test.php',
'class.property_static.test.php',
'class.constant_class.test.php',
'class.continue.test.php',
'class.clone.test.php',
'class.typehint.test.php',
'class.interface.test.php',
'class.declare.test.php',
'class.shell.test.php',
'class.halt_compiler.test.php',
'class.namespace.test.php',
'class.use.test.php',
'class.closure.test.php',
'class.goto.test.php',
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