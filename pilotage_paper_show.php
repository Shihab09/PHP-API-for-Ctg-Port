<?php

$rotaiton=$_GET['rotaiton'];
$vvd_gkey=$_GET['vvd_gkey'];
$activity_for=$_GET['activity_for']; 
$myParam="http://cpatos.gov.bd/pcs/index.php/report/PilotageReportOfVessel_TosApp/R/".$rotaiton."/".$vvd_gkey."/".$activity_for;


//include($myParam);
require($myParam);	

//redirect($myParam,'refresh');
//header('Location:'.$myParam);  
//require_once($myParam); 
//render($myParam);
//exec('php '.$myParam);
//require($myParam);
?>