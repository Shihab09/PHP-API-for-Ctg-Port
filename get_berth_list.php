<?php


	$request_for=$_POST['request_for'];
	$vvd_gkey=$_POST['vvd_gkey'];
	
	if($request_for !="" && $vvd_gkey !=""){
		
		
		require_once 'dbconfig.php';


		
		$sql ="SELECT DISTINCT id AS berth_name,gkey FROM argo_quay ORDER BY id";
		
		$result = oci_parse($con_sparcsn4_oracle, $sql);
		oci_execute($result);
		
		$dataList=array();
		$status = false;
		while(($row= oci_fetch_array($result)) != false)
		{
			$status = true;
			$gkey=$row['GKEY'];
			$berth_name=$row['BERTH_NAME'];
			$dataList[] = array( 'gkey' => $gkey,'berth_name' => $berth_name);
			
		}
		$berth = "";
		if($request_for =="incoming"){
				$table = "doc_vsl_arrival";
				$berth= "SEA";
		}else if($request_for =="shifting"){
				$table = "doc_vsl_arrival";
		}else{
				$table = "doc_vsl_shift";
		}
              
	   $BerthQuery ="SELECT berth FROM $table WHERE vvd_gkey='$vvd_gkey' LIMIT 1";
  	   $BerthQuery_doc_vsl_arrival ="SELECT berth FROM doc_vsl_arrival WHERE vvd_gkey='$vvd_gkey' LIMIT 1";
	  
	   
		$berthQueryRes=mysqli_query($conn,$BerthQuery);
		$BerthQuery_doc_vsl_arrivalRes=mysqli_query($conn,$BerthQuery_doc_vsl_arrival);
		
		if(mysqli_num_rows($berthQueryRes)>0){
			while($berthrow=mysqli_fetch_array($berthQueryRes)){
				$berth = $berthrow['berth'];
			}
		}else if(mysqli_num_rows($BerthQuery_doc_vsl_arrivalRes)>0){
			while($berthrow=mysqli_fetch_array($BerthQuery_doc_vsl_arrivalRes)){
				$berth = $berthrow['berth'];
			}
		
		}else{
			$BerthQuery21 = "SELECT id FROM 	vsl_vessel_berthings INNER JOIN  argo_quay ON 
			argo_quay.gkey = vsl_vessel_berthings.quay WHERE vsl_vessel_berthings.vvd_gkey='$vvd_gkey' 
			ORDER BY vsl_vessel_berthings.gkey DESC FETCH FIRST ROW ONLY";
			$result2 = oci_parse($con_sparcsn4_oracle, $BerthQuery21);
			oci_execute($result2);
			while(($row2= oci_fetch_array($result2)) != false)
			{
				$berth = $row2['ID'];
			}
			
			
		}
	
		
		if(!$status){
			
			$response["success"] = "0";
			$response["message"]="Data Not Found";
			mysqli_close($conn);
			echo json_encode($response);
			
		}else{
			$response["success"]="1";
			$response["message"]="Data Found";
			$response['berthFrom']=$berth;
			$response['data']=$dataList;
			mysqli_close($conn);
			echo json_encode($response);
			
		}
	}else{
		$result["success"] = "0";
		$result["message"] = "Please Try Again";
		echo json_encode($result);
	}
?>