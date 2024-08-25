<?php
	$request_for=$_POST['request_for'];	
	
	if($request_for !=""){
		require_once 'dbconfig.php';

		$qtable = "";
	
		if($request_for=="incoming"){
			$sql="SELECT sparcsn4.vsl_vessel_visit_details.vvd_gkey,sparcsn4.vsl_vessels.name,sparcsn4.vsl_vessel_visit_details.ib_vyg,sparcsn4.vsl_vessel_visit_details.ob_vyg,
			LEFT(sparcsn4.argo_carrier_visit.phase,2) AS phase_num,SUBSTR(sparcsn4.argo_carrier_visit.phase,3) AS phase_str,
			sparcsn4.ref_bizunit_scoped.id AS agent,DATE(sparcsn4.vsl_vessel_visit_details.flex_date07) AS close_date
			FROM sparcsn4.argo_carrier_visit
			INNER JOIN sparcsn4.argo_visit_details ON sparcsn4.argo_visit_details.gkey=sparcsn4.argo_carrier_visit.cvcvd_gkey
			INNER JOIN sparcsn4.vsl_vessel_visit_details ON sparcsn4.vsl_vessel_visit_details.vvd_gkey=sparcsn4.argo_visit_details.gkey
			INNER JOIN sparcsn4.vsl_vessels ON sparcsn4.vsl_vessels.gkey=sparcsn4.vsl_vessel_visit_details.vessel_gkey
			INNER JOIN sparcsn4.ref_bizunit_scoped ON sparcsn4.ref_bizunit_scoped.gkey=sparcsn4.vsl_vessel_visit_details.bizu_gkey
			WHERE sparcsn4.argo_carrier_visit.phase IN ('20INBOUND','30ARRIVED','40WORKING','50COMPLETE','70CLOSED','60DEPARTED') AND sparcsn4.vsl_vessel_visit_details.vvd_gkey> '19904722' AND  (DATE(sparcsn4.argo_carrier_visit.ata)>DATE_SUB(DATE(NOW()), INTERVAL 19 DAY) OR sparcsn4.argo_carrier_visit.ata IS NULL)
			ORDER BY sparcsn4.argo_carrier_visit.phase";
			$qtable="doc_vsl_arrival";	
		}else if($request_for=="shifting"){
			$sql="SELECT sparcsn4.vsl_vessel_visit_details.vvd_gkey,sparcsn4.vsl_vessels.name,sparcsn4.vsl_vessel_visit_details.ib_vyg,sparcsn4.vsl_vessel_visit_details.ob_vyg,
					LEFT(sparcsn4.argo_carrier_visit.phase,2) AS phase_num,SUBSTR(sparcsn4.argo_carrier_visit.phase,3) AS phase_str,
					sparcsn4.ref_bizunit_scoped.id AS agent,DATE(sparcsn4.vsl_vessel_visit_details.flex_date07) as close_date
					FROM sparcsn4.argo_carrier_visit
					INNER JOIN sparcsn4.argo_visit_details ON sparcsn4.argo_visit_details.gkey=sparcsn4.argo_carrier_visit.cvcvd_gkey
					INNER JOIN sparcsn4.vsl_vessel_visit_details ON sparcsn4.vsl_vessel_visit_details.vvd_gkey=sparcsn4.argo_visit_details.gkey
					INNER JOIN sparcsn4.vsl_vessels ON sparcsn4.vsl_vessels.gkey=sparcsn4.vsl_vessel_visit_details.vessel_gkey
					INNER JOIN sparcsn4.vsl_vessel_berthings ON sparcsn4.vsl_vessel_berthings.vvd_gkey=sparcsn4.vsl_vessel_visit_details.vvd_gkey
					INNER JOIN sparcsn4.argo_quay ON sparcsn4.argo_quay.gkey=sparcsn4.vsl_vessel_berthings.quay
					INNER JOIN sparcsn4.ref_bizunit_scoped ON sparcsn4.ref_bizunit_scoped.gkey=sparcsn4.vsl_vessel_visit_details.bizu_gkey
					WHERE sparcsn4.argo_carrier_visit.phase IN ('20INBOUND','30ARRIVED','40WORKING','50COMPLETE','70CLOSED','60DEPARTED') AND sparcsn4.vsl_vessel_visit_details.vvd_gkey> '19904722' AND  (DATE(sparcsn4.argo_carrier_visit.ata)>DATE_SUB(DATE(NOW()), INTERVAL 19 DAY) OR sparcsn4.argo_carrier_visit.ata IS NULL)
					ORDER BY sparcsn4.argo_carrier_visit.phase";
			$qtable="doc_vsl_shift";			
		}else if($request_for=="outgoing"){
			$sql="SELECT sparcsn4.vsl_vessel_visit_details.vvd_gkey,sparcsn4.vsl_vessels.name,sparcsn4.vsl_vessel_visit_details.ib_vyg,sparcsn4.vsl_vessel_visit_details.ob_vyg,
					LEFT(sparcsn4.argo_carrier_visit.phase,2) AS phase_num,SUBSTR(sparcsn4.argo_carrier_visit.phase,3) AS phase_str,
					sparcsn4.ref_bizunit_scoped.id AS agent,DATE(sparcsn4.vsl_vessel_visit_details.flex_date07) as close_date
					FROM sparcsn4.argo_carrier_visit
					INNER JOIN sparcsn4.argo_visit_details ON sparcsn4.argo_visit_details.gkey=sparcsn4.argo_carrier_visit.cvcvd_gkey
					INNER JOIN sparcsn4.vsl_vessel_visit_details ON sparcsn4.vsl_vessel_visit_details.vvd_gkey=sparcsn4.argo_visit_details.gkey
					INNER JOIN sparcsn4.vsl_vessels ON sparcsn4.vsl_vessels.gkey=sparcsn4.vsl_vessel_visit_details.vessel_gkey
					INNER JOIN sparcsn4.vsl_vessel_berthings ON sparcsn4.vsl_vessel_berthings.vvd_gkey=sparcsn4.vsl_vessel_visit_details.vvd_gkey
					INNER JOIN sparcsn4.argo_quay ON sparcsn4.argo_quay.gkey=sparcsn4.vsl_vessel_berthings.quay
					INNER JOIN sparcsn4.ref_bizunit_scoped ON sparcsn4.ref_bizunit_scoped.gkey=sparcsn4.vsl_vessel_visit_details.bizu_gkey
					WHERE sparcsn4.argo_carrier_visit.phase IN ('20INBOUND','30ARRIVED','40WORKING','50COMPLETE','70CLOSED','60DEPARTED') AND sparcsn4.vsl_vessel_visit_details.vvd_gkey> '19904722' AND  (DATE(sparcsn4.argo_carrier_visit.ata)>DATE_SUB(DATE(NOW()), INTERVAL 19 DAY) OR sparcsn4.argo_carrier_visit.ata IS NULL)
					ORDER BY sparcsn4.argo_carrier_visit.phase";
			$qtable="doc_vsl_depart";			
		}else if($request_for=="cancel"){
			$sql="SELECT sparcsn4.vsl_vessel_visit_details.vvd_gkey,sparcsn4.vsl_vessels.name,sparcsn4.vsl_vessel_visit_details.ib_vyg,sparcsn4.vsl_vessel_visit_details.ob_vyg,
					LEFT(sparcsn4.argo_carrier_visit.phase,2) AS phase_num,SUBSTR(sparcsn4.argo_carrier_visit.phase,3) AS phase_str,
					sparcsn4.ref_bizunit_scoped.id AS agent,DATE(sparcsn4.vsl_vessel_visit_details.flex_date07) as close_date
					FROM sparcsn4.argo_carrier_visit
					INNER JOIN sparcsn4.argo_visit_details ON sparcsn4.argo_visit_details.gkey=sparcsn4.argo_carrier_visit.cvcvd_gkey
					INNER JOIN sparcsn4.vsl_vessel_visit_details ON sparcsn4.vsl_vessel_visit_details.vvd_gkey=sparcsn4.argo_visit_details.gkey
					INNER JOIN sparcsn4.vsl_vessels ON sparcsn4.vsl_vessels.gkey=sparcsn4.vsl_vessel_visit_details.vessel_gkey
					INNER JOIN sparcsn4.vsl_vessel_berthings ON sparcsn4.vsl_vessel_berthings.vvd_gkey=sparcsn4.vsl_vessel_visit_details.vvd_gkey
					INNER JOIN sparcsn4.argo_quay ON sparcsn4.argo_quay.gkey=sparcsn4.vsl_vessel_berthings.quay
					INNER JOIN sparcsn4.ref_bizunit_scoped ON sparcsn4.ref_bizunit_scoped.gkey=sparcsn4.vsl_vessel_visit_details.bizu_gkey
					WHERE sparcsn4.argo_carrier_visit.phase IN ('20INBOUND','30ARRIVED','40WORKING','50COMPLETE','70CLOSED','60DEPARTED') AND sparcsn4.vsl_vessel_visit_details.vvd_gkey> '19904722' AND  (DATE(sparcsn4.argo_carrier_visit.ata)>DATE_SUB(DATE(NOW()), INTERVAL 19 DAY) OR sparcsn4.argo_carrier_visit.ata IS NULL)
					ORDER BY sparcsn4.argo_carrier_visit.phase";
			$qtable="doc_vsl_cancel";			
		}

		$result=mysqli_query($conn21,$sql);
		$dataList=array();
		if(mysqli_num_rows($result)>0){
			while($row=mysqli_fetch_array($result)){
				$vvd_gkey=$row['vvd_gkey'];
				$name=$row['name'];
				$ib_vyg=$row['ib_vyg'];
				$ob_vyg=$row['ob_vyg'];
				$phase_num="phase_num";
				$phase_str=$row['phase_str'];
				$agent=$row['agent'];
				$close_day=$row['close_date'];
				$noOfDays = "";
				if($close_day !=NULL){
					 
					$now = time(); // or your date as well
					$your_date = strtotime($close_day);
					$datediff = $now - $your_date;
					$noOfDays= round($datediff / (60 * 60 * 24));
					
					
				}else{
					$noOfDays = "-1";
				}
				
				$CheckVesselInputDoneQuery="SELECT id FROM ".$qtable." WHERE vvd_gkey='$vvd_gkey'";
				$result2=mysqli_query($conn,$CheckVesselInputDoneQuery);
				if(mysqli_num_rows($result2)<=0){
					
					if($request_for=="shifting" or $request_for=="outgoing"){
						$CheckVesselIncomingDoneQuery="SELECT id FROM doc_vsl_arrival WHERE vvd_gkey='$vvd_gkey'";
						$result3=mysqli_query($conn,$CheckVesselIncomingDoneQuery);
						if(mysqli_num_rows($result3)>=0){
						
							$dataList[] = array('vvd_gkey' => $vvd_gkey,'name' => $name,'ib_vyg' => $ib_vyg,'ob_vyg' => $ob_vyg,'phase_num' => $phase_num,'phase_str' => $phase_str,'agent' => $agent,'noOfDays' => $noOfDays);
						}
					}else{
						$dataList[] = array('vvd_gkey' => $vvd_gkey,'name' => $name,'ib_vyg' => $ib_vyg,'ob_vyg' => $ob_vyg,'phase_num' => $phase_num,'phase_str' => $phase_str,'agent' => $agent,'noOfDays' => $noOfDays);
					}
				}
				//echo $CheckVesselInputDoneQuery;
				
			}
		}
		
		if(mysqli_num_rows($result)==0){
			$response["success"] = "0";
			$response["message"]="Data Not Found";
			echo json_encode($response);
			mysqli_close($conn21);
		}else{
			$response["success"]="1";
			$response["message"]="Data Found";
		    	$response['data']=$dataList;
			echo json_encode($response);
			mysqli_close($conn21);
		}
	
	}else{
		$response["success"] = "0";
		$response["message"]="Please Try Again";
		echo json_encode($response);
	}	

?>