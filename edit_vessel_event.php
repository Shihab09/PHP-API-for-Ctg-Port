<?php
			
	$rotation=$_POST['rotation'];
	$vvd_gkey=$_POST['vvd_gkey'];
	$addition_pilot=$_POST['addition_pilot'];
	$tug=$_POST['tug'];
	$remarks=$_POST['remarks'];
	$pob=$_POST['pob'];
	$berth=$_POST['berth'];
	$first_line=$_POST['first_line'];
	$lastline=$_POST['lastline'];
	$dop=$_POST['dop'];
	$request_for=$_POST['request_for'];
	$final_submit=$_POST['final_submit'];
	
	if($rotation !=""){
		require_once 'dbconfig.php';
		
		$str_query="";
		if($request_for =="incoming"){
			$str_query = "UPDATE doc_vsl_arrival SET pilot_on_board='$pob',mooring_frm_time='$first_line',
			pilot_off_board='$dop',aditional_pilot='$addition_pilot',aditional_tug='$tug',remarks='$remarks',tug_name='$tug_name',berth='$berth',final_submit='$final_submit' WHERE vvd_gkey='$vvd_gkey'";
		}else if($request_for =="shifting"){
			$str_query = "UPDATE doc_vsl_shift  SET pilot_on_board='$pob',mooring_to_time='$lastline',mooring_frm_time='$first_line',
			pilot_off_board='$dop',aditional_pilot='$addition_pilot',aditional_tug='$tug',remarks='$remarks',tug_name='$tug_name',berth='$berth',final_submit='$final_submit' WHERE vvd_gkey='$vvd_gkey'";
		}else{
			$str_query = "UPDATE doc_vsl_depart  SET pilot_on_board='$pob',mooring_to_time='$lastline',pilot_off_board='$dop',
			aditional_pilot='$addition_pilot',aditional_tug='$tug',remarks='$remarks',tug_name='$tug_name',berth='$berth',final_submit='$final_submit' WHERE vvd_gkey='$vvd_gkey'";
		}
		//echo($str_query);
		
		if(mysqli_query($conn, $str_query)){
			$result["success"] = "1";
			$result["message"] = "Data insertation success";
			mysqli_close($conn);
			echo json_encode($result);
			
		}else{
			$result["success"] = "0";
			$result["message"] = "Please Try Again";
			mysqli_close($conn);
			echo json_encode($result);
			
		} 																																			
	

	}else{
		$result["success"] = "0";
		$result["message"] = "Please Try Again!!";
		echo json_encode($result);
	
	}

?>