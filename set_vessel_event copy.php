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

				
				$str_query_for_update_N4_vsl_vessel_visit_details = "UPDATE sparcsn4.vsl_vessel_visit_details SET flex_string05 = '$pilot_user_name',flex_date01='$pob',
				flex_date02='$dop' WHERE sparcsn4.vsl_vessel_visit_details.vvd_gkey='$vvd_gkey'";
				$results_N4_vsl_vessel_visit_details=mysqli_query($conn21,$str_query_for_update_N4_vsl_vessel_visit_details);
				//echo $str_query_for_update_N4_vsl_vessel_visit_details;
				$berth_gkey="";
				$sql = "SELECT gkey FROM sparcsn4.argo_quay WHERE id ='$berth' LIMIT 1";	
				$results_argo_quay=mysqli_query($conn21,$sql);
				if(mysqli_num_rows($results_argo_quay)>0){
					while($row=mysqli_fetch_array($results_argo_quay)){
						$berth_gkey=$row['gkey'];
					}
				}
				$str_query_for_insert_N4_vsl_vessel_berthings ="INSERT INTO sparcsn4.vsl_vessel_berthings(seq,vvd_gkey,quay,ship_side_to,created,creator,ata)
																VALUES('1','$vvd_gkey','$berth_gkey','STARBOARD',NOW(),'$pilot_name','$firstLine')";
				$str_query_for_update_N4_vsl_vessel_berthings ="UPDATE sparcsn4.vsl_vessel_berthings SET changed=NOW(),changer='$pilot_name',ata='$firstLine' 
																WHERE vvd_gkey='$vvd_gkey' AND quay='$berth_gkey'";

				$final_vsl_vessel_berthings_query ="";
				$sql = "SELECT gkey FROM sparcsn4.vsl_vessel_berthings WHERE vvd_gkey='$vvd_gkey' AND quay='$berth_gkey' LIMIT 1";	
				$results_vsl_vessel_berthings=mysqli_query($conn21,$sql);
				if(mysqli_num_rows($results_vsl_vessel_berthings)>0){
					while($row=mysqli_fetch_array($results_vsl_vessel_berthings)){
						$final_vsl_vessel_berthings_query=$str_query_for_update_N4_vsl_vessel_berthings;
					}
				}else{
					$final_vsl_vessel_berthings_query = $str_query_for_insert_N4_vsl_vessel_berthings;
				}
				//echo $final_vsl_vessel_berthings_query;
				$results=mysqli_query($conn21,$final_vsl_vessel_berthings_query);
				
				
				$str_query_for_insert_N4_argo_carrier_visit = "UPDATE sparcsn4.argo_carrier_visit
							SET sparcsn4.argo_carrier_visit.ata='$firstLine',sparcsn4.argo_carrier_visit.phase='30ARRIVED'
							WHERE sparcsn4.argo_carrier_visit.cvcvd_gkey='$vvd_gkey'";
							
							
				//echo $str_query_for_insert_N4_argo_carrier_visit;			
				$results_argo_carrier_visit = mysqli_query($conn21,$str_query_for_insert_N4_argo_carrier_visit);
				
				$applied_to_natural_key="";
				$applied_to_natural_key_sql = "SELECT id FROM argo_carrier_visit WHERE cvcvd_gkey ='$vvd_gkey' LIMIT 1";	
				$applied_to_natural_key_results=mysqli_query($conn21,$applied_to_natural_key_sql);
				if(mysqli_num_rows($applied_to_natural_key_results)>0){
					while($applied_to_natural_key_row=mysqli_fetch_array($applied_to_natural_key_results)){
						$applied_to_natural_key=$applied_to_natural_key_row['id'];
					}
				}
				$str_query_for_insert_N4_srv_event = "INSERT INTO srv_event(operator_gkey,complex_gkey,facility_gkey,yard_gkey,placed_by,placed_time,event_type_gkey,applied_to_class,applied_to_gkey,applied_to_natural_key,created,creator)
				  VALUES('1','1','1','1','$n4User',NOW(),'90','VV','$vvd_gkey','$applied_to_natural_key',NOW(),'$pilot_name'),
						('1','1','1','1','$n4User',NOW(),'209','VV','$vvd_gkey','$applied_to_natural_key',NOW(),'$pilot_name'),
						('1','1','1','1','$n4User',NOW(),'189','VV','$vvd_gkey','$applied_to_natural_key',NOW(),'$pilot_name'),
						('1','1','1','1','$n4User',NOW(),'185','VV','$vvd_gkey','$applied_to_natural_key',NOW(),'$pilot_name'),
						('1','1','1','1','$n4User',NOW(),'162','VV','$vvd_gkey','$applied_to_natural_key',NOW(),'$pilot_name'),
						('1','1','1','1','$n4User',NOW(),'91','VV','$vvd_gkey','$applied_to_natural_key',NOW(),'$pilot_name')";
				$results_srv_event = mysqli_query($conn21,$str_query_for_insert_N4_srv_event);
				//echo $str_query_for_insert_N4_srv_event;
				
			}else if($request_type =="shifting"){
				$query_final_submit_update = "UPDATE doc_vsl_shift set final_submit='1' where vvd_gkey='$vvd_gkey'";
				mysqli_query($conn,$query_final_submit_update);
				$result["success"] = "1";
				$result["message"] = "Successful";
				mysqli_close($conn);
				echo json_encode($result);

				/*$berth_gkey="";
				$sql = "SELECT gkey FROM sparcsn4.argo_quay WHERE id ='$berth' LIMIT 1";	
				$results_argo_quay=mysqli_query($conn21,$sql);
				if(mysqli_num_rows($results_argo_quay)>0){
					while($row=mysqli_fetch_array($results_argo_quay)){
						$berth_gkey=$row['gkey'];
					}
				}
				$str_query_for_insert_N4_vsl_vessel_berthings ="INSERT INTO sparcsn4.vsl_vessel_berthings(seq,vvd_gkey,quay,ship_side_to,created,creator,ata)
																VALUES('1','$vvd_gkey','$berth_gkey','STARBOARD',NOW(),'$pilot_name','$firstLine')";
				$str_query_for_update_N4_vsl_vessel_berthings ="UPDATE sparcsn4.vsl_vessel_berthings SET changed=NOW(),changer='$pilot_name',ata='$firstLine' 
																WHERE vvd_gkey='$vvd_gkey' AND quay='$berth_gkey'";

				$final_vsl_vessel_berthings_query ="";
				$sql = "SELECT gkey FROM sparcsn4.vsl_vessel_berthings WHERE vvd_gkey='$vvd_gkey' AND quay='$berth_gkey' LIMIT 1";	
				$results_vsl_vessel_berthings=mysqli_query($conn21,$sql);
				if(mysqli_num_rows($results_vsl_vessel_berthings)>0){
					while($row=mysqli_fetch_array($results_vsl_vessel_berthings)){
						$final_vsl_vessel_berthings_query=$str_query_for_update_N4_vsl_vessel_berthings;
					}
				}else{
					$final_vsl_vessel_berthings_query = $str_query_for_insert_N4_vsl_vessel_berthings;
				}
				
				$results=mysqli_query($conn21,$final_vsl_vessel_berthings_query);
				
				*/
			}else if($request_type =="outgoing"){
				$query_final_submit_update = "UPDATE doc_vsl_depart set final_submit='1' where vvd_gkey='$vvd_gkey'";
				mysqli_query($conn,$query_final_submit_update);
				$result["success"] = "1";
				$result["message"] = "Successful";
				mysqli_close($conn);
				echo json_encode($result);
			
			/*
				$str_query_for_update_N4_vsl_vessel_visit_details = "UPDATE sparcsn4.vsl_vessel_visit_details SET flex_string08 = '$pilot_user_name',flex_date03='$pob',
				flex_date04='$dop' WHERE sparcsn4.vsl_vessel_visit_details.vvd_gkey='$vvd_gkey'";
				$results_N4_vsl_vessel_visit_details=mysqli_query($conn21,$str_query_for_update_N4_vsl_vessel_visit_details);
				
				$str_query_for_insert_N4_argo_carrier_visit = "UPDATE sparcsn4.argo_carrier_visit
							SET sparcsn4.argo_carrier_visit.ata='$firstLine',sparcsn4.argo_carrier_visit.phase='60DEPARTED'
							WHERE sparcsn4.argo_carrier_visit.cvcvd_gkey='$vvd_gkey'";
				$results_argo_carrier_visit = mysqli_query($conn21,$str_query_for_insert_N4_argo_carrier_visit);
				
				
				
				
				$applied_to_natural_key="";
				$applied_to_natural_key_sql = "SELECT id FROM `argo_carrier_visit` WHERE `cvcvd_gkey` ='$vvd_gkey' LIMIT 1";	
				$applied_to_natural_key_results=mysqli_query($conn21,$applied_to_natural_key_sql);
				if(mysqli_num_rows($applied_to_natural_key_results)>0){
					while($applied_to_natural_key_row=mysqli_fetch_array($applied_to_natural_key_results)){
						$applied_to_natural_key=$applied_to_natural_key_row['id'];
					}
				}
				$str_query_for_insert_N4_srv_event = "INSERT INTO srv_event(operator_gkey,complex_gkey,facility_gkey,yard_gkey,placed_by,placed_time,event_type_gkey,applied_to_class,applied_to_gkey,applied_to_natural_key,created,creator)
				VALUES(('1','1','1','1','',NOW(),'90','VV','$vvd_gkey','$applied_to_natural_key',NOW(),'$pilot_name'),
						('1','1','1','1','',NOW(),'209','VV','$vvd_gkey','$applied_to_natural_key',NOW(),'$pilot_name'),
						('1','1','1','1','',NOW(),'189','VV','$vvd_gkey','$applied_to_natural_key',NOW(),'$pilot_name'),
						('1','1','1','1','',NOW(),'185','VV','$vvd_gkey','$applied_to_natural_key',NOW(),'$pilot_name'),
						('1','1','1','1','',NOW(),'162','VV','$vvd_gkey','$applied_to_natural_key',NOW(),'$pilot_name'),
						('1','1','1','1','',NOW(),'91','VV','$vvd_gkey','$applied_to_natural_key',NOW(),'$pilot_name'))";
				$results_srv_event = mysqli_query($conn21,$str_query_for_insert_N4_srv_event);
				
				
				$result["success"] = "1";
				$result["message"] = "Successful";
				mysqli_close($conn);
				echo json_encode($result);*/
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
				/*
				$request_for = $_POST['request_for'];
				$additonalPilot=$_POST['addiPilot'];		
				$additonalTug=$_POST['addiTug'];
				
				if(($request_for =="incoming" || $request_for =="shifting" || $request_for =="outgoing") && $additonalTug>=1){
					$event_gkey="";
					if($request_for =="incoming")
						$event_gkey="187";
					else if($request_for =="shifting")
						$event_gkey="195";
					else
						$event_gkey="407";
					
					$str_query_for_insert_N4_srv_event = "INSERT INTO srv_event(operator_gkey,complex_gkey,facility_gkey,yard_gkey,placed_by,placed_time,event_type_gkey,applied_to_class,applied_to_gkey,applied_to_natural_key,created,creator)
						VALUES('1','1','1','1','',NOW(),'$event_gkey','VV','$vvd_gkey','$applied_to_natural_key',NOW(),'$pilot_name')";
					$results_srv_event = mysqli_query($conn21,$str_query_for_insert_N4_srv_event);
				}else{
					
				}
				*/
			}else{
				$result["success"] = "1";
				$result["message"] = "Successful";
				mysqli_close($conn);
				echo json_encode($result);
			}
		}else{	
		
		//N4 LIVE DATA UPDATE END
		
		
		/*
		
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
			$is_main_engine_ok=$_POST['is_main_engine_ok'];
			$is_acnchors_ok=$_POST['is_acnchors_ok'];
			$is_rudder_indicator_ok=$_POST['is_rudder_indicator_ok'];
			$is_rpm_indicator_ok=$_POST['is_rpm_indicator_ok'];
			$is_bow_therster_available=$_POST['is_bow_therster_available'];
			$is_complying_soal_convention=$_POST['is_complying_soal_convention'];
			$is_night=$_POST['is_night'];
			$is_holiday=$_POST['is_holiday'];
			$str_query = "UPDATE $table
			SET aditional_pilot = '$additonalPilot',aditional_tug='$additonalTug',remarks='$remarks',draught='$draught',
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
			$str_query = $str_query_for_update;
		}else{
			$str_query = $str_query_for_insert;
		}
	
		
		if(mysqli_query($conn, $str_query)){
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
	*/
	
	}}else{
		$result["success"] = "0";
		$result["message"] = "Please Try Again1";
		echo json_encode($result);
	
	}

?>
