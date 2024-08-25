<?php
	$request_for=$_POST['request_for'];	
	$vvd_gkey=$_POST['vvd_gkey'];	
	$rotation=$_POST['rotation'];	
	
	if($request_for !="" AND $vvd_gkey !=""){
		require_once 'dbconfig.php';

		$tableSelect ="";
		$dataList=array();
		$headingN4DataList=array();
		
		
		if($request_for=="incoming")
		{
			$tableSelect= "doc_vsl_arrival";
		}
		else if($request_for=="shifting")
		{
			$tableSelect= "doc_vsl_shift";
		}
		else if($request_for=="outgoing") // Get Berth For Outgoing instead of Agent
		{
			$tableSelect= "doc_vsl_depart";
		}
		$sql1="SELECT IFNULL(pilot_on_board,'') AS pilot_on_board,IFNULL(pilot_off_board,'') AS pilot_off_board, IFNULL(mooring_frm_time,'') AS mooring_frm_time,IFNULL(mooring_to_time,'') AS mooring_to_time ,
			IFNULL(aditional_pilot,'0') AS aditional_pilot,IFNULL(aditional_tug,'0') AS aditional_tug,IFNULL(remarks,'') AS remarks,IFNULL(tug_name,'') AS tug_name,IFNULL(berth,'') AS berth,draught 
				FROM ".$tableSelect." WHERE vvd_gkey=$vvd_gkey";
		$result=mysqli_query($conn,$sql1);
		if(mysqli_num_rows($result)>0){
			while($row=mysqli_fetch_array($result)){
				$pilot_on_board=$row['pilot_on_board'];
				$pilot_off_board=$row['pilot_off_board'];
				$mooring_frm_time=$row['mooring_frm_time'];
				$mooring_to_time=$row['mooring_to_time'];
				$aditional_pilot=$row['aditional_pilot'];
				$aditional_tug=$row['aditional_tug'];
				$tug_name=$row['tug_name'];
				$remarks=$row['remarks'];
				$berth=$row['berth'];
				$draught=$row['draught'];
				
		
				$dataList[] = array( 'pilot_on_board' => $pilot_on_board,
								   'pilot_off_board' => $pilot_off_board,
								   'mooring_frm_time' => $mooring_frm_time,
								   'mooring_to_time' => $mooring_to_time,
								   'aditional_pilot' => $aditional_pilot,
								   'aditional_tug' => $aditional_tug,
								    'tug_name' => $tug_name,
								   'remarks' => $remarks,
								   'draught' => $draught,
								   'berth' => $berth
								  
								);
				}
		}
		$sql3="SELECT vsl_vessels.name,vsl_vessel_visit_details.vvd_gkey,vsl_vessels.radio_call_sign,vsl_vessel_classes.loa_cm,vsl_vessel_classes.gross_registered_ton, 
		vsl_vessel_classes.net_registered_ton,ref_bizunit_scoped.id AS localagent,ref_country.cntry_name AS flag, vsl_vessel_classes.beam_cm 
		,NVL((SELECT NAME FROM argo_facility WHERE gkey=argo_carrier_visit.fcy_gkey) ,'') AS last_port,NVL((SELECT NAME FROM argo_facility WHERE gkey=argo_carrier_visit.next_fcy_gkey),'') AS next_port
		FROM vsl_vessels 
		INNER JOIN vsl_vessel_visit_details ON vsl_vessels.gkey=vsl_vessel_visit_details.vessel_gkey 
		INNER JOIN vsl_vessel_classes ON vsl_vessel_classes.gkey=vsl_vessels.vesclass_gkey 
		INNER JOIN ref_bizunit_scoped ON ref_bizunit_scoped.gkey=vsl_vessel_visit_details.bizu_gkey 
		INNER JOIN ref_country ON ref_country.cntry_code=vsl_vessels.country_code 
		INNER JOIN argo_carrier_visit ON argo_carrier_visit.cvcvd_gkey =vsl_vessel_visit_details.vvd_gkey
		WHERE vsl_vessel_visit_details.ib_vyg='$rotation' FETCH FIRST ROW ONLY";
		$result2 = oci_parse($con_sparcsn4_oracle, $sql3);
		oci_execute($result2);
		$status =false;
		while(($row= oci_fetch_array($result2)) != false){

			$status =true;
			$vvd_gkey=$row['VVD_GKEY'];
			$vsl_name=$row['NAME'];
			$radio_call_sign=$row['RADIO_CALL_SIGN'];
			$loa_cm=$row['LOA_CM'];
			$gross_registered_ton=$row["GROSS_REGISTERED_TON"];
			$net_registered_ton=$row['NET_REGISTERED_TON'];
			$localagent=$row['LOCALAGENT'];
			$flag=$row['FLAG'];
			$beam_cm=$row['BEAM_CM'];
			$headingN4DataList[] = array( 'vvd_gkey' => $vvd_gkey,'vsl_name'=> $vsl_name,
							   'radio_call_sign' => $radio_call_sign,
							   'loa_cm' => $loa_cm,
							   'gross_registered_ton' => $gross_registered_ton,
							   'net_registered_ton' => $net_registered_ton,
							   'localagent' => $localagent,
							   'flag' => $flag,
							   'beam_cm' => $beam_cm
							);
				
							
		} 
		
		if(mysqli_num_rows($result)==0){
			$response["success"] = "0";
			$response["message"]="Data Not Found";
			mysqli_close($conn);
			mysqli_close($conn21);
			echo json_encode($response);
		}else{
			$response["success"]="1";
			$response["message"]="Data Found";
		    $response["data"]=$dataList;
			$response["heading_n4data"]=$headingN4DataList;
			mysqli_close($conn);
			mysqli_close($conn21);
			echo json_encode($response);
			
		}
	
	}else{
		$response["success"] = "0";
		$response["message"]="Please Try Again1";
		echo json_encode($response);
	}	

?>