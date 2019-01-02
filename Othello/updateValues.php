<?php

	if (!isset($_POST['whiteAI'], $_POST['blackAI'], $_POST['depth']))
	{
		exit();	
	}

	if (!isset($_POST['debugflag']))
		$_POST['debugflag'] = false;
	if (!isset($_POST['pruneflag']))
		$_POST['pruneflag'] = false;
	if (!isset($_POST['hintflag']))
		$_POST['hintflag'] = false;
	
	session_start();
	
	$_SESSION['whiteAI'] = filter_var($_POST['whiteAI'], FILTER_VALIDATE_BOOLEAN);
	$_SESSION['blackAI'] = filter_var($_POST['blackAI'], FILTER_VALIDATE_BOOLEAN);
	$_SESSION['DEBUGFLAG'] = filter_var($_POST['debugflag'], FILTER_VALIDATE_BOOLEAN);
	$_SESSION['PRUNEFLAG'] = filter_var($_POST['pruneflag'], FILTER_VALIDATE_BOOLEAN);
	$_SESSION['HINTFLAG'] = filter_var($_POST['hintflag'], FILTER_VALIDATE_BOOLEAN);
	$_SESSION['depth'] = $_POST['depth'];
	
?>