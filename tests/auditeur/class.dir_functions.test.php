<?php 
include_once('Auditeur_Framework_TestCase.php');

class dir_functions_Test extends Auditeur_Framework_TestCase
{
    public function testdir_functions()  {
        $this->expected = array( 
'chdir',
'chroot',
'dir',
'closedir',
'getcwd',
'opendir',
'readdir',
'rewinddir',
'scandir',
);
        $this->unexpeted = array(/*'',*/);

        parent::generic_test();
    }
}
?>