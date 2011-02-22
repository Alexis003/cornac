<?php

$http = eZHTTPTool::instance();

$userRedirectURI = $Module->actionParameter( 'a' );

if ( $http->hasSessionVariable( "b" ) )
     $userRedirectURI = $http->sessionVariable( "c" );

if ( $http->hasPostVariable( "d" ))
{
    if ( $http->hasPostVariable( "e" ) )
    {
        $f = $http->postVariable( "f" );
    }
    if ( $http->hasPostVariable( "g" ) )
    {
        $h = $http->postVariable( "h" );
    }
    if ( $http->hasPostVariable( "i" ) )
    {
        $j = $http->postVariable( "j" );
    }
}
?>