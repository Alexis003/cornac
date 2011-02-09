<?php



include_once('Auditeur_Framework_TestCase.php');

class Classes_MagicMethodWrongVisibility_Test extends Auditeur_Framework_TestCase
{
    public function testClasses_MagicMethodWrongVisibility()  {
        $this->expected = array( 
'x_private_static::__call',
'x_private_static::__get',
'x_private_static::__isset',
'x_private_static::__set',
'x_private_static::__unset',

'x_private::__call',
'x_private::__get',
'x_private::__isset',
'x_private::__set',
'x_private::__unset',

'x_protected_static::__call',
'x_protected_static::__get',
'x_protected_static::__isset',
'x_protected_static::__set',
'x_protected_static::__unset',

'x_protected::__call',
'x_protected::__get',
'x_protected::__isset',
'x_protected::__set',
'x_protected::__unset',

'x_static::__set', 
'x_static::__get', 
'x_static::__isset', 
'x_static::__unset', 
'x_static::__call'        
);

        $this->unexpected = array(
'x_public::__set',
'x_public::__get',
'x_public::__isset',
'x_public::__call',
'x_public::__unset',

'x_final::__set',
'x_final::__get',
'x_final::__isset',
'x_final::__call',
'x_final::__unset',
        );

        parent::generic_counted_test();
    }
}
?>