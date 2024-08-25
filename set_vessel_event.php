<?php
	$rotation=$_POST['rotation'];
	if($rotation !=""){

		require_once 'dbconfig.php';

		$vvd_gkey=$_POST['vvd_gkey'];
		$request_type=$_POST['request_type'];
		$pilot_name=$_POST['pilot_name'];
		$str_query_for_insert = "";
		$str_query_for_update = "";
		$pob=$_POST['pob'];
		$lastLine=$_POST['lastLine'];
		$firstLine=$_POST['firstLine'];
		$dop=$_POST['dop'];
		$berth_from=$_POST['berth_from'];
		$berth=$_POST['berth'];
		$final_submit=$_POST['final_submit'];
		if($pilot_name =="p499"){
			$result["success"] = "0";
			$result["message"] = "Please Try Again";
			echo json_encode($result);
		}
		$n4User = "user:".$pilot_name;

		$pilot_user_name = "";
		$user_name_query = "SELECT u_name FROM users WHERE login_id='$pilot_name' LIMIT 1";	
		$results_user=mysqli_query($conn,$user_name_query);
		if(mysqli_num_rows($results_user)>0){
			while($row_user=mysqli_fetch_array($results_user)){
				$pilot_user_name=$row_user['u_name'];
			}
		}
		//echo 	$pilot_user_name;		
		$igm_mst_id="";
		$query="SELECT distinct id as igm_mst_id FROM igm_masters WHERE Import_Rotation_No='$rotation'";
		$qresult=mysqli_query($conn,$query);
		$igm_mst_id=0;
		while($row=mysqli_fetch_array($qresult)){
			$igm_mst_id=$row['igm_mst_id'];
		}
				
		$table="";

		//N4 LIVE DATA UPDATE START
		if($final_submit=="1"){
				if($request_type =="incoming"){
					$query_final_submit_update = "UPDATE doc_vsl_arrival set final_submit='1' where vvd_gkey='$vvd_gkey'";
					mysqli_query($conn,$query_final_submit_update);
					$result["success"] = "1";
					$result["message"] = "Successful";
					mysqli_close($conn);
					echo json_encode($result);	
	
				}else if($request_type =="shifting"){
					$query_final_submit_update = "UPDATE doc_vsl_shift set final_submit='1' where vvd_gkey='$vvd_gkey'";
					mysqli_query($conn,$query_final_submit_update);
					$result["success"] = "1";
					$result["message"] = "Successful";
					mysqli_close($conn);
					echo json_encode($result);
				}else if($request_type =="outgoing"){
					$query_final_submit_update = "UPDATE doc_vsl_depart set final_submit='1' where vvd_gkey='$vvd_gkey'";
					mysqli_query($conn,$query_final_submit_update);
					$result["success"] = "1";
					$result["message"] = "Successful";
					mysqli_close($conn);
					echo json_encode($result);
			
				}else if($request_type =="cancel"){
					
					$result["success"] = "1";
					$result["message"] = "Successful";
					mysqli_close($conn);
					echo json_encode($result);		
				}else if($request_type =="additional"){
					$result["success"] = "1";
					$result["message"] = "Successful";
					mysqli_close($conn);
					echo json_encode($result);
				
				}else{
					$result["success"] = "1";
					$result["message"] = "Successful";
					mysqli_close($conn);
					echo json_encode($result);
				}
		}else{
			if($request_type =="incoming"){
				$table="doc_vsl_arrival";
				$str_query_for_insert = "INSERT INTO doc_vsl_arrival(igm_id,vvd_gkey,pilot_name,pilot_on_board,mooring_frm_time,pilot_off_board,berth,pilot_frm,pilot_to) VALUES('$igm_mst_id','$vvd_gkey','$pilot_name','$pob','$firstLine','$dop','$berth','SEA','$berth')";
				$str_query_for_update = "UPDATE doc_vsl_arrival set igm_id='$igm_mst_id',vvd_gkey='$vvd_gkey',pilot_name='$pilot_name',pilot_on_board='$pob',mooring_frm_time='$firstLine',pilot_off_board='$dop',berth='$berth',pilot_frm='SEA',pilot_to='$berth' where vvd_gkey='$vvd_gkey'";
									
			}else if($request_type =="shifting"){
				$table="doc_vsl_shift";
				$str_query_for_insert = "INSERT INTO doc_vsl_shift(igm_id,vvd_gkey,pilot_name,pilot_on_board,mooring_frm_time,mooring_to_time,pilot_off_board,berth,shift_frm,shift_to) VALUES ('$igm_mst_id','$vvd_gkey','$pilot_name','$pob','$lastLine','$firstLine','$dop','$berth','$berth_from','$berth')";
				$str_query_for_update = "UPDATE doc_vsl_shift set igm_id='$igm_mst_id',vvd_gkey='$vvd_gkey',pilot_name='$pilot_name',pilot_on_board='$pob',mooring_frm_time='$lastLine',mooring_to_time='$firstLine',pilot_off_board='$dop',berth='$berth',shift_frm='$berth_from',shift_to='$berth' where vvd_gkey='$vvd_gkey' ORDER BY id DESC LIMIT 1";
				$str_query_for_insert_N4 ="";
				$str_query_for_update_N4 ="";
				
			}else if($request_type =="outgoing"){
				$table="doc_vsl_depart";
				//$berth_from=$_POST['berth_from'];	
				$str_query_for_insert = "INSERT INTO doc_vsl_depart(igm_id, vvd_gkey,pilot_name,pilot_on_board,mooring_to_time,pilot_off_board,pilot_frm,pilot_to) VALUES('$igm_mst_id','$vvd_gkey','$pilot_name','$pob','$lastLine','$dop','$berth_from','SEA')";
				$str_query_for_update = "UPDATE doc_vsl_depart set igm_id='$igm_mst_id', vvd_gkey='$vvd_gkey',pilot_name='$pilot_name',pilot_on_board='$pob',mooring_to_time='$lastLine',pilot_off_board='$dop',pilot_frm='$berth_from',pilot_to='SEA' where vvd_gkey='$vvd_gkey'";
				$str_query_for_insert_N4 ="";
				$str_query_for_update_N4 ="";
			
			}else if($request_type =="cancel"){
				$table="doc_vsl_cancel";
				//$berth_from=$_POST['berth_from'];	
				$cancel_at=$_POST['cancel_at'];
				$remarks=$_POST['remarks'];
				$str_query_for_insert = "INSERT INTO doc_vsl_cancel(igm_id, vvd_gkey,pilot_name,pilot_on_board,pilot_off_board,cancel_from,cancel_to,cancel_at,remarks) VALUES('$igm_mst_id','$vvd_gkey','$pilot_name','$pob','$dop','$berth_from','$berth','$cancel_at','$remarks')";
				$str_query_for_update = "UPDATE doc_vsl_cancel set igm_id='$igm_mst_id', vvd_gkey='$vvd_gkey',pilot_name='$pilot_name',pilot_on_board='$pob',pilot_off_board='$dop',cancel_from='$berth_from',cancel_to='$berth',cancel_at='$cancel_at',remarks='$remarks' where vvd_gkey='$vvd_gkey'";
				$str_query_for_insert_N4 ="";
				$str_query_for_update_N4 ="";
			
			}else if($request_type =="additional"){
				$request_for = $_POST['request_for'];
				if($request_for =="incoming"){
					$table="doc_vsl_arrival";
				}else if($request_for =="shifting"){
					$table="doc_vsl_shift";
				}else if($request_for =="outgoing"){
					$table="doc_vsl_depart";
				}else if($request_for =="cancel"){
					$table="doc_vsl_cancel";
				}else{
					
				}
				$additonalPilot=$_POST['addiPilot'];		
				$additonalTug=$_POST['addiTug'];	
				$remarks=$_POST['remarks'];				
				$draught=$_POST['draught'];	
				$tug_name=$_POST['tug_name'];	 
				$is_main_engine_ok=$_POST['is_main_engine_ok'];
				$is_acnchors_ok=$_POST['is_acnchors_ok'];
				$is_rudder_indicator_ok=$_POST['is_rudder_indicator_ok'];
				$is_rpm_indicator_ok=$_POST['is_rpm_indicator_ok'];
				$is_bow_therster_available=$_POST['is_bow_therster_available'];
				$is_complying_soal_convention=$_POST['is_complying_soal_convention'];
				$is_night=$_POST['is_night'];
				$is_holiday=$_POST['is_holiday'];
				$str_query = "UPDATE $table
				SET aditional_pilot = '$additonalPilot',aditional_tug='$additonalTug',remarks='$remarks',draught='$draught',tug_name='$tug_name',
					is_main_engine_ok = '$is_main_engine_ok',is_acnchors_ok='$is_acnchors_ok',is_rudder_indicator_ok='$is_rudder_indicator_ok',
					is_rpm_indicator_ok = '$is_rpm_indicator_ok',is_bow_therster_available='$is_bow_therster_available',is_complying_soal_convention='$is_complying_soal_convention',
					is_night = '$is_night',is_holiday='$is_holiday'
					WHERE vvd_gkey='$vvd_gkey' ORDER BY id DESC LIMIT 1";
				$str_query_for_insert=$str_query;
				$str_query_for_update=$str_query;
			}else{
				
			}
			
			$queryToCheckIsDataExist= "";
			if($request_type =="shifting"){
				$queryToCheckIsDataExist ="SELECT vvd_gkey FROM $table WHERE vvd_gkey='$vvd_gkey' and shift_to ='$berth' ";
			}else{
				$queryToCheckIsDataExist ="SELECT vvd_gkey FROM $table WHERE vvd_gkey='$vvd_gkey'";
			}
			$res_ToCheckIsDataExist=mysqli_query($conn,$queryToCheckIsDataExist);
			
			if(mysqli_num_rows($res_ToCheckIsDataExist)>0){
				
				$NightShift ="UPDATE $table SET `is_night`='1'
				WHERE `vvd_gkey` ='$vvd_gkey'  AND (TIME(`pilot_on_board`) >='18:00:00' OR TIME(`pilot_on_board`) <'06:00:00' OR 
				TIME(`pilot_off_board`) >='18:00:00' OR TIME(`pilot_off_board`) <'06:00:00' OR
				TIME(`mooring_frm_time`) >='18:00:00' OR TIME(`mooring_frm_time`) <'06:00:00' OR 
				TIME(`mooring_to_time`) >='18:00:00' OR TIME(`mooring_to_time`) <'06:00:00')";
				mysqli_query($conn,$NightShift);
				$str_query = $str_query_for_update;
				
				
				
			}else{
				$str_query = $str_query_for_insert;
			}
		
			
			if(mysqli_query($conn, $str_query)){
				if($request_type =="incoming"){
					$vesselVisitIdQuery = "SELECT ID FROM argo_carrier_visit WHERE CVCVD_GKEY='$vvd_gkey' FETCH FIRST ROW ONLY";
					$result2 = oci_parse($con_sparcsn4_oracle, $vesselVisitIdQuery);
					oci_execute($result2);
					while(($row2= oci_fetch_array($result2)) != false)
					{
						$VSL_VISIT_ID = $row2['ID'];
						
						$PHASE="ARRIVED";
						$ATA = str_replace(" ","T",$firstLine);
						$ATD = str_replace(" ","T",$lastLine);
						$ADDITIONAL_TUG ="1";
						$ADDITIONAL_PILOT ="1";
						
						$curl = curl_init();
						curl_setopt_array($curl, array(
						  CURLOPT_URL => 'http://172.16.10.103:9081/apex/services/argoservice',
						  CURLOPT_RETURNTRANSFER => true,
						  CURLOPT_ENCODING => '',
						  CURLOPT_MAXREDIRS => 10,
						  CURLOPT_TIMEOUT => 0,
						  CURLOPT_FOLLOWLOCATION => true,
						  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						  CURLOPT_CUSTOMREQUEST => 'POST',
						  CURLOPT_POSTFIELDS =>'<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:arg="http://www.navis.com/services/argoservice">
						   <soapenv:Header/>
						   <soapenv:Body>
							  <arg:genericInvoke>
								 <arg:scopeCoordinateIdsWsType>
									<operatorId>CPA</operatorId>
									<complexId>BD</complexId>
									<facilityId>CGP</facilityId>
									<yardId>CGP</yardId>
								</arg:scopeCoordinateIdsWsType>
								 <arg:xmlDoc>
								 <![CDATA[
									<custom class="CPAVVPhaseUpdateHandler" type="extension">
										<update-vv-phase>
											<vv-id>'.$VSL_VISIT_ID.'</vv-id>
											<vv-phase>'.$PHASE.'</vv-phase>
											<ata>'.$ATA.'</ata>
											<atd>'.$ATD.'</atd>
											<additional-tug>'.$ADDITIONAL_TUG.'</additional-tug>
											<additional-pilot>'.$ADDITIONAL_PILOT.'</additional-pilot>
										</update-vv-phase>
									</custom>
								]]>
								</arg:xmlDoc>
							  </arg:genericInvoke>
						   </soapenv:Body>
						</soapenv:Envelope>',
						  CURLOPT_HTTPHEADER => array(
							'SOAPAction: add',
							'Content-Type: application/xml',
							'Authorization: Basic YWRtaW46TmF2aXMhQCNEYXRhU29mdA=='
						  ),
						));
						$response = curl_exec($curl);
						curl_close($curl);
						
					}
				}
				
				
				$result["success"] = "1";
				$result["message"] = "Successful";
				mysqli_close($conn);
				echo json_encode($result);
			
			}else{
				$result["success"] = "0";
				$result["message"] = "UnSuccessfull" ;
				mysqli_close($conn);
				echo json_encode($result);
				
			} 			
			

	}}else{
		$result["success"] = "0";
		$result["message"] = "Please Try Again1";
		echo json_encode($result);
	
	}

?>
