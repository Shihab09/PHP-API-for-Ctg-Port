<?php
	$request_for=$_POST['request_for'];	
	$vvd_gkey=$_POST['vvd_gkey'];	
	
	if($request_for !="" AND $vvd_gkey !=""){
		require_once 'dbconfig.php';

	
		$tableSelect ="";
		$dataList=array();
		$incomingDataList=array();
		$shiftingDataList=array();
		$outgoingDataList=array();
		$getBearthList = array();
		
		
		if($request_for=="incoming")
		{
			$tableSelect= "doc_vsl_arrival";
			$sql2="SELECT IFNULL(pilot_on_board,'') AS pilot_on_board,IFNULL(pilot_off_board,'') AS pilot_off_board, IFNULL(mooring_frm_time,'') AS mooring_frm_time,IFNULL(mooring_to_time,'') AS mooring_to_time ,
			IFNULL(aditional_pilot,'0') AS aditional_pilot,IFNULL(aditional_tug,'0') AS aditional_tug,IFNULL(remarks,'') AS remarks,IFNULL(berth,'') AS berth,IFNULL(photo_base_64,'') AS photo 
				FROM $tableSelect WHERE vvd_gkey=$vvd_gkey";
			$result2=mysqli_query($conn,$sql2);
			//echo $sql2;
			
			if(mysqli_num_rows($result2)>0){
					while($row2=mysqli_fetch_array($result2)){
						$pilot_on_board1=$row2['pilot_on_board'];
						$pilot_off_board1=$row2['pilot_off_board'];
						$mooring_frm_time1=$row2['mooring_frm_time'];
						$mooring_to_time1=$row2['mooring_to_time'];
						$aditional_pilot1=$row2['aditional_pilot'];
						$aditional_tug1=$row2['aditional_tug'];
						$remarks1=$row2['remarks'];
						$berth1=$row2['berth'];
						$photo=$row2['photo'];
						
						$incomingDataList[] = array( 'pilot_on_board' => $pilot_on_board1,
										   'pilot_off_board' => $pilot_off_board1,
										   'mooring_frm_time' => $mooring_frm_time1,
										   'mooring_to_time' => $mooring_to_time1,
										   'aditional_pilot' => $aditional_pilot1,
										   'aditional_tug' => $aditional_tug1,
										   'remarks' => $remarks1,
										   'berth' => $berth1,
											'photo' => $photo
										);
						}
				}
		}
		else if($request_for=="shifting")
		{
			$tableSelect= "doc_vsl_shift";
			$sql2="SELECT IFNULL(pilot_on_board,'') AS pilot_on_board,IFNULL(pilot_off_board,'') AS pilot_off_board, IFNULL(mooring_frm_time,'') AS mooring_frm_time,IFNULL(mooring_to_time,'') AS mooring_to_time ,
			IFNULL(aditional_pilot,'0') AS aditional_pilot,IFNULL(aditional_tug,'0') AS aditional_tug,IFNULL(remarks,'') AS remarks,IFNULL(berth,'') AS berth,IFNULL(photo_base_64,'') AS photo 
				FROM $tableSelect WHERE vvd_gkey=$vvd_gkey";
			$result2=mysqli_query($conn,$sql2);
			//echo $sql2;
			
			if(mysqli_num_rows($result2)>0){
					while($row2=mysqli_fetch_array($result2)){
						$pilot_on_board1=$row2['pilot_on_board'];
						$pilot_off_board1=$row2['pilot_off_board'];
						$mooring_frm_time1=$row2['mooring_frm_time'];
						$mooring_to_time1=$row2['mooring_to_time'];
						$aditional_pilot1=$row2['aditional_pilot'];
						$aditional_tug1=$row2['aditional_tug'];
						$remarks1=$row2['remarks'];
						$berth1=$row2['berth'];
						$photo=$row2['photo'];
						
						$shiftingDataList[] = array( 'pilot_on_board' => $pilot_on_board1,
										   'pilot_off_board' => $pilot_off_board1,
										   'mooring_frm_time' => $mooring_frm_time1,
										   'mooring_to_time' => $mooring_to_time1,
										   'aditional_pilot' => $aditional_pilot1,
										   'aditional_tug' => $aditional_tug1,
										   'remarks' => $remarks1,
										   'berth' => $berth1,
											'photo' => $photo
										);
						}
				}
			
						
		}
		else if($request_for=="outgoing") // Get Berth For Outgoing instead of Agent
		{
			$tableSelect= "doc_vsl_depart";
			$sql2="SELECT IFNULL(pilot_on_board,'') AS pilot_on_board,IFNULL(pilot_off_board,'') AS pilot_off_board, IFNULL(mooring_frm_time,'') AS mooring_frm_time,IFNULL(mooring_to_time,'') AS mooring_to_time ,
			IFNULL(aditional_pilot,'0') AS aditional_pilot,IFNULL(aditional_tug,'0') AS aditional_tug,IFNULL(remarks,'') AS remarks,IFNULL(berth,'') AS berth,IFNULL(photo_base_64,'') AS photo 
				FROM $tableSelect WHERE vvd_gkey=$vvd_gkey";
		
			$result2=mysqli_query($conn,$sql2);
			if(mysqli_num_rows($result2)>0){
				while($row2=mysqli_fetch_array($result2)){
					$pilot_on_board1=$row2['pilot_on_board'];
					$pilot_off_board1=$row2['pilot_off_board'];
					$mooring_frm_time1=$row2['mooring_frm_time'];
					$mooring_to_time1=$row2['mooring_to_time'];
					$aditional_pilot1=$row2['aditional_pilot'];
					$aditional_tug1=$row2['aditional_tug'];
					$remarks1=$row2['remarks'];
					$berth1=$row2['berth'];
					$photo=$row2['photo'];
					
					$outgoingDataList[] = array( 'pilot_on_board' => $pilot_on_board1,
									   'pilot_off_board' => $pilot_off_board1,
									   'mooring_frm_time' => $mooring_frm_time1,
									   'mooring_to_time' => $mooring_to_time1,
									   'aditional_pilot' => $aditional_pilot1,
									   'aditional_tug' => $aditional_tug1,
									   'remarks' => $remarks1,
									   'berth' => $berth1,
									    'photo' => $photo
									);
					}
			}
		
		}
		
		
		$sql="SELECT IFNULL(pilot_on_board,'') AS pilot_on_board,IFNULL(pilot_off_board,'') AS pilot_off_board, IFNULL(mooring_frm_time,'') AS mooring_frm_time,IFNULL(mooring_to_time,'') AS mooring_to_time ,
			IFNULL(aditional_pilot,'0') AS aditional_pilot,IFNULL(aditional_tug,'0') AS aditional_tug,IFNULL(remarks,'') AS remarks,IFNULL(berth,'') AS berth,draught 
				FROM ".$tableSelect." WHERE vvd_gkey=$vvd_gkey";
		$result=mysqli_query($conn,$sql);
		if(mysqli_num_rows($result)>0){
			while($row=mysqli_fetch_array($result)){
				$pilot_on_board=$row['pilot_on_board'];
				$pilot_off_board=$row['pilot_off_board'];
				$mooring_frm_time=$row['mooring_frm_time'];
				$mooring_to_time=$row['mooring_to_time'];
				$aditional_pilot=$row['aditional_pilot'];
				$aditional_tug=$row['aditional_tug'];
				$remarks=$row['remarks'];
				$berth=$row['berth'];
				$draught=$row['draught'];
				
		
				$dataList[] = array( 'pilot_on_board' => $pilot_on_board,
								   'pilot_off_board' => $pilot_off_board,
								   'mooring_frm_time' => $mooring_frm_time,
								   'mooring_to_time' => $mooring_to_time,
								   'aditional_pilot' => $aditional_pilot,
								   'aditional_tug' => $aditional_tug,
								   'remarks' => $remarks,
								   'berth' => $berth,
								   'draught' => $draught
								);
				}
		}
		
		if(mysqli_num_rows($result)==0){
			$response["success"] = "0";
			$response["message"]="Data Not Found";
			echo json_encode($response);
			mysqli_close($conn);
		}else{
			$response["success"]="1";
			$response["message"]="Data Found";
		    $response['data']=$dataList;
			$response['incoming_data']=$incomingDataList;
			$response['shifting_data']=$shiftingDataList;
			$response['outgoing_data']=$outgoingDataList;
			echo json_encode($response);
			mysqli_close($conn);
		}
	
	}else{
		$response["success"] = "0";
		$response["message"]="Please Try Again";
		echo json_encode($response);
	}	

?>