<?php

session_start();

require_once('othelloLogic.php');

$_SESSION['aiMsg'] = '';

$results = aiPlay($_SESSION['depth']);



if ($_SESSION['DEBUGFLAG'])
{
$_SESSION['aiMsg'] = "Value is: {$results[0]}<br>Move is: " . getPlacement($results[1][0]-1, $results[1][1]-1). '<br>' . $_SESSION['aiMsg'];
$_SESSION['aiMsg'] = $_SESSION['miniMaxNum'] . "<br>" . $_SESSION['aiMsg'];
}


echo "<script>placePiece({$results[1][0]}, {$results[1][1]});</script>";

?>
