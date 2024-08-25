<?php
    
	$strVslName =$_REQUEST['strVslName'];
	$strMasterName=$_REQUEST['strMasterName'];
	$strFlag=$_REQUEST['strFlag'];  
	$strGrt=$_REQUEST['strGrt'];  
	$strNrt=$_REQUEST['strNrt'];  
	$strDeckCargo=$_REQUEST['strDeckCargo'];  
	$strLoa=$_REQUEST['strLoa'];
    	$strLocalAgent=$_REQUEST['strLocalAgent'];  
	$strLastPort=$_REQUEST['strLastPort'];  
	$strNextPort=$_REQUEST['strNextPort'];  
	$strRotationNumber=$_REQUEST['strRotationNumber'];
	$entry_by=$_REQUEST['entry_by'];
	$entry_ip=$_REQUEST['entry_ip'];
     

	require_once 'dbconfig.php';
	

    $sql ="INSERT INTO doc_vsl_info (vsl_name,master_name,flag,grt,nrt,deck_cargo,loa,local_agent,last_port,next_port,rotation,entry_by,entry_time,entry_ip) 
					VALUES ('$strVslName','$strMasterName','$strFlag','$strGrt','$strNrt','$strDeckCargo','$strLoa','$strLocalAgent','$strLastPort','$strNextPort',
					'$strRotationNumber','$entry_by',now(),'$entry_ip')";


//echo $sql;

    if ( mysqli_query($conn, $sql) ) {
        $result["success"] = "1";
        $result["message"] = "Data insertatio success";
        echo json_encode($result);
        mysqli_close($conn);
    } else {
        $result["success"] = "0";
        $result["message"] = "error in Data insertatio";
        echo json_encode($result);
        mysqli_close($conn);
    } 
	
?>