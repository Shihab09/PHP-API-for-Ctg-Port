<?php
	$rotation=$_POST['rotation'];
	if($rotation !=""){
		$vvd_gkey =$_POST['vvd_gkey'];
		$vsl_name =$_POST['vsl_name'];
		$master_name =$_POST['master_name'];
		$flag =$_POST['flag'];
		$grt =$_POST['grt'];
		$nrt =$_POST['nrt'];
		$deck_cargo =$_POST['deck_cargo'];
		$loa =$_POST['loa'];
		$local_agent =$_POST['local_agent'];
		$last_port =$_POST['last_port'];
		$next_port=$_POST['next_port'];	
		$entry_by=$_POST['entry_by'];
		$channel=$_POST['channel'];
	
		////
		require_once 'dbconfig.php';

		$igm_mst_id='';
		$query="SELECT id  FROM doc_vsl_info WHERE rotation='$rotation'";
		$qresult=mysqli_query($conn,$query);
		$queryBuilder = "";
		if(mysqli_num_rows($qresult)>0){
			$queryBuilder="UPDATE doc_vsl_info SET vvd_gkey= '$vvd_gkey',vsl_name='$vsl_name',master_name='$master_name',flag='$flag',grt='$grt',nrt='$nrt',deck_cargo='$deck_cargo',
			channel='$channel',loa='$loa',local_agent='$local_agent',last_port='$last_port',next_port='$next_port',rotation='$rotation',entry_by='$entry_by',
			entry_time=NOW() WHERE rotation='$rotation'";
		}else{
			$queryBuilder = "INSERT INTO doc_vsl_info(vvd_gkey,vsl_name,master_name,flag,grt,nrt,deck_cargo,
			channel,loa,local_agent,last_port,next_port,rotation,entry_by,entry_time) VALUES('$vvd_gkey','$vsl_name','$master_name','$flag','$grt','$nrt','$deck_cargo',
			'$channel','$loa','$local_agent','$last_port','$next_port','$rotation','$entry_by',NOW())";
		}
		
		if(mysqli_query($conn, $queryBuilder)){
			$result["success"] = "1";
			$result["message"] = "Successful";
			mysqli_close($conn);
			echo json_encode($result);
				
		}else{
			$result["success"] = "0";
			$result["message"] = "Please Try again" ;
			mysqli_close($conn);
			echo json_encode($result);
		} 	

	
	}else{
		$result["success"] = "0";
		$result["message"] = "Please Try Again";
		echo json_encode($result);
	
	}

?>