<?php
	$rotation=$_POST['rotation'];	
	
	
	if($rotation !=""){
		 
		
		
		require_once 'dbconfig.php';
		$dataList=array();
		$vvd_gkey="";
		$name="";
		$Name_of_Master="";
		$radio_call_sign="";
		$loa_cm="";
		$gross_registered_ton="";
		$net_registered_ton="";
		$localagent="";
		$flag="";
		$beam_cm="";
		$last_port="";
		$next_port="";
				
		$sql="SELECT vsl_vessels.name,vsl_vessel_visit_details.vvd_gkey,vsl_vessels.radio_call_sign,vsl_vessel_classes.loa_cm,vsl_vessel_classes.gross_registered_ton, 
		vsl_vessel_classes.net_registered_ton,ref_bizunit_scoped.id AS localagent,ref_country.cntry_name AS flag, vsl_vessel_classes.beam_cm 
		,NVL((SELECT NAME FROM argo_facility WHERE gkey=argo_carrier_visit.fcy_gkey) ,'') AS last_port,NVL((SELECT NAME FROM argo_facility WHERE gkey=argo_carrier_visit.next_fcy_gkey),'') AS next_port
		FROM vsl_vessels 
		INNER JOIN vsl_vessel_visit_details ON vsl_vessels.gkey=vsl_vessel_visit_details.vessel_gkey 
		INNER JOIN vsl_vessel_classes ON vsl_vessel_classes.gkey=vsl_vessels.vesclass_gkey 
		INNER JOIN ref_bizunit_scoped ON ref_bizunit_scoped.gkey=vsl_vessel_visit_details.bizu_gkey 
		INNER JOIN ref_country ON ref_country.cntry_code=vsl_vessels.country_code 
		INNER JOIN argo_carrier_visit ON argo_carrier_visit.cvcvd_gkey =vsl_vessel_visit_details.vvd_gkey
		WHERE vsl_vessel_visit_details.ib_vyg='$rotation' FETCH FIRST ROW ONLY";	
			
		$sqlIgmMaster="SELECT Name_of_Master FROM igm_masters WHERE Import_Rotation_No='$rotation' LIMIT 1";	
			
		
		$resultIgmMaster=mysqli_query($conn,$sqlIgmMaster);
		if(mysqli_num_rows($resultIgmMaster)>0){
			while($rowIgmMaster=mysqli_fetch_array($resultIgmMaster)){
				$Name_of_Master=$rowIgmMaster['Name_of_Master'];
			}
		}
		
		
		$result2 = oci_parse($con_sparcsn4_oracle, $sql);
		oci_execute($result2);
		$status =false;
		while(($row= oci_fetch_array($result2)) != false){
			$status =true;
			$vvd_gkey=$row['VVD_GKEY'];
			$name=$row['NAME'];
			$radio_call_sign=$row['RADIO_CALL_SIGN'];
			$loa_cm=$row['LOA_CM'];
			$gross_registered_ton=$row["GROSS_REGISTERED_TON"];
			$net_registered_ton=$row['NET_REGISTERED_TON'];
			$localagent=$row['LOCALAGENT'];
			$flag=$row['FLAG'];
			$beam_cm=$row['BEAM_CM'];
			$last_port=$row['LAST_PORT'];
			$next_port=$row['NEXT_PORT'];
			$dataList[] = array( 'vvd_gkey' => $vvd_gkey,'name' => $name,'Name_of_Master' => $Name_of_Master,'radio_call_sign' => $radio_call_sign
			,'loa_cm' => $loa_cm,'gross_registered_ton' => $gross_registered_ton,'net_registered_ton' => $net_registered_ton,'localagent' => $localagent
			,'flag' => $flag,'beam_cm' => $beam_cm,'last_port' => $last_port,'next_port' => $next_port);

		}
		
		
		if(!$status){
			$response["success"] = "0";
			$response["message"]="Data Not Found";
			mysqli_close($conn);
			mysqli_close($conn21);
			echo json_encode($response);
			
		}else{
			$response["success"]="1";
			$response["message"]="Data Found";
		  	$response['data']=$dataList;
			mysqli_close($conn);
			mysqli_close($conn21);
			echo json_encode($response);
			
		}
	
	}else{
		$response["success"] = "0";
		$response["message"]="Please Try Again";
		echo json_encode($response);
	}
	

?>