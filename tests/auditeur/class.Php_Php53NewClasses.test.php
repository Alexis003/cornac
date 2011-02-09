<?php



include_once('Auditeur_Framework_TestCase.php');

class Php_Php53NewClasses_Test extends Auditeur_Framework_TestCase
{
    public function testPhp_Php53NewClasses()  {
        $this->expected = array( 'DatePeriod',
'Phar',
'PharData',
'PharException',
'PharFileInfo',
'FilesystemIterator',
'GlobIterator',
'MultipleIterator',
'RecursiveTreeIterator',
'SplDoublyLinkedList',
'SplFixedArray',
'SplHeap',
'SplMaxHeap',
'SplMinHeap',
'SplPriorityQueue',
'SplQueue',
'SplStack',);
        $this->unexpected = array(/*'',*/);

        parent::generic_test();
    }
}
?>