<?php

$Module = array( 'name' => 'Forum Topic Management',
                 'variable_params' => true );

$ViewList = array();
$ViewList['new'] = array(
    'script' => 'new.php',
    'params' => array( 'ForumID' ) );
$ViewList['view'] = array(
    'script' => 'view.php',
    'params' => array( 'TopicID' ) );

?>
