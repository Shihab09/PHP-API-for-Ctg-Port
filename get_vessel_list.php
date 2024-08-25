<?php
	$request_for=$_POST['request_for'];	
	
	if($request_for !=""){
		
		require_once 'dbconfig.php';

		$qtable = "";
		
		if($request_for=="incoming"){
			$sql="SELECT DISTINCT vsl_vessel_visit_details.vvd_gkey,vsl_vessels.name,vsl_vessel_visit_details.ib_vyg,vsl_vessel_visit_details.ob_vyg,
			SUBSTR(argo_carrier_visit.phase,1,2) AS phase_num,SUBSTR(argo_carrier_visit.phase,3) AS phase_str,ref_bizunit_scoped.id AS agent,
			to_char(vsl_vessel_visit_details.flex_date07, 'YYYY-MM-DD') close_date,to_char(vsl_vessel_visit_details.flex_date07 , 'YYYY-MM-DD') AS close_date,
			to_char(vsl_vessel_visit_details.published_eta, 'YYYY-MM-DD') AS eta
			FROM argo_carrier_visit
			INNER JOIN vsl_vessel_visit_details ON vsl_vessel_visit_details.vvd_gkey=argo_carrier_visit.cvcvd_gkey
			INNER JOIN vsl_vessels ON vsl_vessels.gkey=vsl_vessel_visit_details.vessel_gkey
			INNER JOIN ref_bizunit_scoped ON ref_bizunit_scoped.gkey=vsl_vessel_visit_details.bizu_gkey
			WHERE argo_carrier_visit.phase IN ('20INBOUND','30ARRIVED','40WORKING','50COMPLETE','60DEPARTED','70CLOSED')  AND  ((cast(argo_carrier_visit.ata as Date)>  trunc(sysdate-20)) 
			OR argo_carrier_visit.ata IS NULL)
			ORDER BY vsl_vessel_visit_details.vvd_gkey DESC";
			$qtable="doc_vsl_arrival";	
			
		}else if($request_for=="shifting"){
			$sql="SELECT DISTINCT vsl_vessel_visit_details.vvd_gkey,vsl_vessels.name,vsl_vessel_visit_details.ib_vyg,vsl_vessel_visit_details.ob_vyg,
			SUBSTR(argo_carrier_visit.phase,1,2) AS phase_num,SUBSTR(argo_carrier_visit.phase,3) AS phase_str,
			ref_bizunit_scoped.id AS agent,to_char(vsl_vessel_visit_details.flex_date07,'YYYY-MM-DD') AS close_date,to_char(vsl_vessel_visit_details.published_eta ,'YYYY-MM-DD') AS eta			
			FROM argo_carrier_visit
			INNER JOIN vsl_vessel_visit_details ON vsl_vessel_visit_details.vvd_gkey=argo_carrier_visit.cvcvd_gkey
			INNER JOIN vsl_vessels ON vsl_vessels.gkey=vsl_vessel_visit_details.vessel_gkey
			INNER JOIN ref_bizunit_scoped ON ref_bizunit_scoped.gkey=vsl_vessel_visit_details.bizu_gkey
			WHERE argo_carrier_visit.phase IN ('20INBOUND','30ARRIVED','40WORKING','50COMPLETE','60DEPARTED') AND  (cast(argo_carrier_visit.ata as Date)>  trunc(sysdate-20)) 
			ORDER BY vsl_vessel_visit_details.vvd_gkey DESC";
			$qtable="doc_vsl_shift";			
		}else if($request_for=="outgoing"){
			$sql="SELECT DISTINCT vsl_vessel_visit_details.vvd_gkey,vsl_vessels.name,vsl_vessel_visit_details.ib_vyg,vsl_vessel_visit_details.ob_vyg,
			SUBSTR(argo_carrier_visit.phase,1,2) AS phase_num,SUBSTR(argo_carrier_visit.phase,3) AS phase_str,
			ref_bizunit_scoped.id AS agent,to_char(vsl_vessel_visit_details.flex_date07,'YYYY-MM-DD') AS close_date,
			to_char(vsl_vessel_visit_details.published_eta,'YYYY-MM-DD') AS eta			
			FROM argo_carrier_visit
			INNER JOIN vsl_vessel_visit_details ON vsl_vessel_visit_details.vvd_gkey=argo_carrier_visit.cvcvd_gkey
			INNER JOIN vsl_vessels ON vsl_vessels.gkey=vsl_vessel_visit_details.vessel_gkey
			INNER JOIN ref_bizunit_scoped ON ref_bizunit_scoped.gkey=vsl_vessel_visit_details.bizu_gkey
			WHERE argo_carrier_visit.phase IN ('20INBOUND','30ARRIVED','40WORKING','50COMPLETE','60DEPARTED','70CLOSED') 
			AND   (cast(argo_carrier_visit.ata as Date)>  trunc(sysdate-20))
			ORDER BY vsl_vessel_visit_details.vvd_gkey DESC";
			$qtable="doc_vsl_depart";			
		}else if($request_for=="cancel"){
			$sql="SELECT DISTINCT vsl_vessel_visit_details.vvd_gkey,vsl_vessels.name,vsl_vessel_visit_details.ib_vyg,vsl_vessel_visit_details.ob_vyg,
			SUBSTR(argo_carrier_visit.phase,1,2) AS phase_num,SUBSTR(argo_carrier_visit.phase,3) AS phase_str,
			ref_bizunit_scoped.id AS agent,to_char(vsl_vessel_visit_details.flex_date07, 'YYYY-MM-DD') AS close_date,to_char(vsl_vessel_visit_details.published_eta, 'YYYY-MM-DD') AS eta
			FROM argo_carrier_visit
			INNER JOIN vsl_vessel_visit_details ON vsl_vessel_visit_details.vvd_gkey=argo_carrier_visit.cvcvd_gkey
			INNER JOIN vsl_vessels ON vsl_vessels.gkey=vsl_vessel_visit_details.vessel_gkey
			INNER JOIN ref_bizunit_scoped ON ref_bizunit_scoped.gkey=vsl_vessel_visit_details.bizu_gkey
			WHERE argo_carrier_visit.phase IN ('20INBOUND','30ARRIVED','40WORKING','50COMPLETE','70CLOSED')  AND  
            ((cast(argo_carrier_visit.ata as Date)>  trunc(sysdate-20)) OR argo_carrier_visit.ata IS NULL)
			ORDER BY vsl_vessel_visit_details.vvd_gkey DESC";
			$qtable="doc_vsl_cancel";			
		}
		$gkeyList=array();
		$getLastTowmonthEntry = "SELECT vvd_gkey FROM ".$qtable." WHERE DATE(date_modified) >  DATE(DATE_SUB(NOW(), INTERVAL 1 MONTH))";
		$queryResult = mysqli_query($conn,$getLastTowmonthEntry);
		if(mysqli_num_rows($queryResult)>0){
			while($row=mysqli_fetch_array($queryResult)){
				 $gkeyList[] = $row['vvd_gkey'];
			}
		}
		
		$docVslArrivalGkeyList=array();
		if($request_for=="outgoing"){
			$getDocVslArrivalEntry = "SELECT vvd_gkey FROM doc_vsl_arrival WHERE DATE(date_modified) >  DATE(DATE_SUB(NOW(), INTERVAL 1 MONTH))";
			$queryDocVslResult = mysqli_query($conn,$getDocVslArrivalEntry);
			
			if(mysqli_num_rows($queryDocVslResult)>0){
				while($rowDocVsl=mysqli_fetch_array($queryDocVslResult)){
					 $docVslArrivalGkeyList[] = $rowDocVsl['vvd_gkey'];
				}
			}			
		}

		$result2oracle = oci_parse($con_sparcsn4_oracle, $sql);
		oci_execute($result2oracle);
		$status = false;
		$dataList=array();
		while(($row= oci_fetch_array($result2oracle)) != false){
			$status = true;
			$vvd_gkey=$row['VVD_GKEY'];
			$name=$row['NAME'];
			$ib_vyg=$row['IB_VYG'];
			$ob_vyg=$row['OB_VYG'];
			$phase_num=$row['PHASE_NUM'];
			$phase_str=$row['PHASE_STR'];
			$agent=$row['AGENT'];
			$close_day=$row['CLOSE_DATE'];
			$eta=$row['ETA'];
			$noOfDays = "";
			if($close_day !=NULL){
				$now = time(); // or your date as well
				$your_date = strtotime($close_day);
				$datediff = $now - $your_date;
				$noOfDays= round($datediff / (60 * 60 * 24));
			}else{
				$noOfDays = "-1";
			}
			if($request_for=="shifting"){
					/* $docVslArrivalfiltered = array_filter($docVslArrivalGkeyList, function($myGKey) use ($vvd_gkey) {
										return $myGKey === $vvd_gkey;
									});
									 
					if (count($docVslArrivalfiltered)){
							$dataList[] = array('vvd_gkey' => $vvd_gkey,'name' => $name,'ib_vyg' => $ib_vyg,'ob_vyg' => $ob_vyg,'phase_num' => $phase_num,'phase_str' => $phase_str,'agent' => $agent,'noOfDays' => $noOfDays,'eta' => $eta);
					}else{
						//Arrival Data Not Entry Yet	
					} */
				
				$dataList[] = array('vvd_gkey' => $vvd_gkey,'name' => $name,'ib_vyg' => $ib_vyg,'ob_vyg' => $ob_vyg,'phase_num' => $phase_num,'phase_str' => $phase_str,'agent' => $agent,'noOfDays' => $noOfDays,'eta' => $eta);
			
								
			}else{
				
				if(count($gkeyList) ==0){

					}else{
							$filtered = array_filter($gkeyList, function($element) use ($vvd_gkey) {
								return $element === $vvd_gkey;
							});
							if (count($filtered)) {
							//Already Vsl Added By Pilot on Selected Table
							} else {
								if($request_for=="outgoing"){
									$docVslArrivalfiltered = array_filter($docVslArrivalGkeyList, function($myGKey) use ($vvd_gkey) {
										return $myGKey === $vvd_gkey;
									});
									 
									if (count($docVslArrivalfiltered)){
											$dataList[] = array('vvd_gkey' => $vvd_gkey,'name' => $name,'ib_vyg' => $ib_vyg,'ob_vyg' => $ob_vyg,'phase_num' => $phase_num,'phase_str' => $phase_str,'agent' => $agent,'noOfDays' => $noOfDays,'eta' => $eta);
									}else{
										//Arrival Data Not Entry Yet	
									}
								}else{
									$dataList[] = array('vvd_gkey' => $vvd_gkey,'name' => $name,'ib_vyg' => $ib_vyg,'ob_vyg' => $ob_vyg,'phase_num' => $phase_num,'phase_str' => $phase_str,'agent' => $agent,'noOfDays' => $noOfDays,'eta' => $eta);
								}
								
							}
					}
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
			$response['data']=$dataList;
			mysqli_close($conn);
			echo json_encode($response);;
		}
	
	}else{
		$response["success"] = "0";
		$response["message"]="Please Try Again";
		echo json_encode($response);
	}	

?>