<?php
	$pilot_id=$_POST['user_id'];
	$report_type=$_POST['report_type'];
	$start_date=$_POST['start_date'];
	$end_date=$_POST['end_date'];
		
	if($pilot_id !="" or $report_type !="" or $start_date !="" or $end_date !="" ){
		require_once 'dbconfig.php';

		$sql="";
		
		if($report_type=="Incoming"){
			$sql="SELECT rotation AS Import_Rotation_No,vsl_name AS Vessel_Name,master_name AS Name_of_Master,doc_vsl_arrival.vvd_gkey,'incoming' AS input_type FROM doc_vsl_arrival INNER JOIN 
			doc_vsl_info ON doc_vsl_arrival.vvd_gkey=doc_vsl_info.vvd_gkey
			WHERE pilot_name='$pilot_id' AND DATE(pilot_off_board) BETWEEN '$start_date' AND '$end_date'";
		}else if($report_type=="Shifting"){
			$sql="SELECT rotation AS Import_Rotation_No,vsl_name AS Vessel_Name,master_name AS Name_of_Master,doc_vsl_shift.vvd_gkey,'shifting' AS input_type FROM doc_vsl_shift INNER JOIN 
			doc_vsl_info ON doc_vsl_info.vvd_gkey=doc_vsl_shift.vvd_gkey
			WHERE pilot_name='$pilot_id' AND DATE(pilot_off_board) BETWEEN '$start_date' AND '$end_date'";
			
		}else if($report_type=="Outgoing"){
			$sql="SELECT rotation AS Import_Rotation_No,vsl_name AS Vessel_Name,master_name AS Name_of_Master,doc_vsl_depart.vvd_gkey,'outgoing' AS input_type FROM doc_vsl_depart INNER JOIN 
			doc_vsl_info ON doc_vsl_info.vvd_gkey=doc_vsl_depart.vvd_gkey
			WHERE pilot_name='$pilot_id' AND DATE(pilot_off_board) BETWEEN '$start_date' AND '$end_date'";
			
		}else if($report_type=="Cancel"){
			$sql="SELECT rotation AS Import_Rotation_No,vsl_name AS Vessel_Name,master_name AS Name_of_Master,doc_vsl_cancel.vvd_gkey,'cancel' AS input_type FROM doc_vsl_cancel INNER JOIN 
			doc_vsl_info ON doc_vsl_info.vvd_gkey=doc_vsl_cancel.vvd_gkey
			WHERE pilot_name='$pilot_id' AND DATE(pilot_off_board) BETWEEN '$start_date' AND '$end_date'";
			
		}else{
			
			$sql="SELECT Import_Rotation_No,Vessel_Name,Name_of_Master,vvd_gkey,input_type FROM (
			SELECT rotation AS Import_Rotation_No,vsl_name AS Vessel_Name,master_name AS Name_of_Master,doc_vsl_arrival.vvd_gkey,'incoming' AS input_type FROM doc_vsl_arrival INNER JOIN 
			doc_vsl_info ON doc_vsl_arrival.vvd_gkey=doc_vsl_info.vvd_gkey
			WHERE pilot_name='$pilot_id' AND DATE(pilot_off_board) BETWEEN '$start_date' AND '$end_date'
			UNION
			SELECT rotation AS Import_Rotation_No,vsl_name AS Vessel_Name,master_name AS Name_of_Master,doc_vsl_shift.vvd_gkey,'shifting' AS input_type FROM doc_vsl_shift INNER JOIN 
			doc_vsl_info ON doc_vsl_info.vvd_gkey=doc_vsl_shift.vvd_gkey
			WHERE pilot_name='$pilot_id' AND DATE(pilot_off_board) BETWEEN '$start_date' AND '$end_date'
			UNION
			SELECT rotation AS Import_Rotation_No,vsl_name AS Vessel_Name,master_name AS Name_of_Master,doc_vsl_depart.vvd_gkey,'outgoing' AS input_type FROM doc_vsl_depart INNER JOIN 
			doc_vsl_info ON doc_vsl_info.vvd_gkey=doc_vsl_depart.vvd_gkey
			WHERE pilot_name='$pilot_id' AND DATE(pilot_off_board) BETWEEN '$start_date' AND '$end_date'
			UNION
			SELECT rotation AS Import_Rotation_No,vsl_name AS Vessel_Name,master_name AS Name_of_Master,doc_vsl_cancel.vvd_gkey,'cancel' AS input_type FROM doc_vsl_cancel INNER JOIN 
			doc_vsl_info ON doc_vsl_info.vvd_gkey=doc_vsl_cancel.vvd_gkey
			WHERE pilot_name='$pilot_id' AND DATE(pilot_off_board) BETWEEN '$start_date' AND '$end_date') AS temp";
		}
		$result=mysqli_query($conn,$sql);
		$dataList=array();
		if(mysqli_num_rows($result)>0){
			while($row=mysqli_fetch_array($result)){
				$vvd_gkey=$row['vvd_gkey'];
				$Import_Rotation_No=$row['Import_Rotation_No'];
				$Vessel_Name=$row['Vessel_Name'];
				$Name_of_Master=$row['Name_of_Master'];
				$input_type=$row['input_type'];
				$dataList[] = array( 'vvd_gkey' => $vvd_gkey,'Import_Rotation_No' => $Import_Rotation_No,'Vessel_Name' => $Vessel_Name,'Name_of_Master' => $Name_of_Master,'input_type' => $input_type);
			}
		}
		if(mysqli_num_rows($result)==0){
			$response["success"] = "0";
			$response["message"]="Data Not Found";
			mysqli_close($conn);
			echo json_encode($response);
		}else{
			$response["success"]="1";
			$response["message"]="Data Found";
			$response['data']=$dataList;
			mysqli_close($conn);
			echo json_encode($response);
		} 
	}else{
		$response["success"]="0";
		$response["message"]="Data Not Found";
		echo json_encode($response);
		
	}	

?>