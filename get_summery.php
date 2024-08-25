<?php
	$pilot_id=$_POST['user_id'];
		
	if($pilot_id !=""){
		require_once 'dbconfig.php';

		$sql = "SELECT * FROM((SELECT COUNT(id) AS total_vsl_arrival  FROM doc_vsl_arrival WHERE pilot_name='$pilot_id') AS a,
		(SELECT COUNT(id) AS total_vsl_depart  FROM doc_vsl_depart WHERE pilot_name='$pilot_id') AS d,
		(SELECT COUNT(id) AS total_vsl_shift  FROM doc_vsl_shift WHERE pilot_name='$pilot_id') AS s,
		(SELECT COUNT(id) AS total_vsl_cancel  FROM doc_vsl_cancel WHERE pilot_name='$pilot_id') AS c)";
		$result=mysqli_query($conn,$sql);
		
		$dataList=array();
		$row=mysqli_fetch_array($result);
		
		
		if($row[0]>0 or $row[1]>0 or $row[2]>0 or $row[3]>0){
			$total_vsl_arrival=$row[0];
			$total_vsl_depart=$row[1];
			$total_vsl_shift=$row[2];
			$total_vsl_cancel =$row[3];
			$dataList[] = array( 'total_vsl_arrival' => $total_vsl_arrival,'total_vsl_depart' => $total_vsl_depart,'total_vsl_shift' => $total_vsl_shift,'total_vsl_cancel' => $total_vsl_cancel);
		}
		
		if($row[0]==0 and $row[1]==0 and $row[2]==0 and $row[3]==0){
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