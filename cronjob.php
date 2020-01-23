<?php
	include_once "config/database_conf.php"; //to connect to our database
	$fetchSpotID = $surfPh->Query_selectStatement('*', 'surf_spot_phs');
	$dateToday = date("Y-m-d");
	foreach($fetchSpotID as $row){
		$IDsurfSpotPh = $row['IDsurf_spot_phs'];
		//save log
		
		$where ='DATE_FORMAT(dateTime, "%Y-%m-%d")="'.$dateToday.'" AND surf_spot_phs_IDsurf_spot_phs = '.$IDsurfSpotPh;
		$checkLog = $surfPh->Query_selectStatementWhere('*', 'surf_ph_log',$where);
		if(EMPTY($checkLog)){
			$log_date = date("Y-m-d H:i:s");
			$table_log = 'surf_ph_log';
			$field_log = 'surf_spot_phs_IDsurf_spot_phs,dateTime';
			$value_log =  "'".$IDsurfSpotPh."','".$log_date."'";
			$fetchSpotID = $surfPh->Query_insertStatement($table_log, $field_log, $value_log);
		}
	}




	//save information of todays data
	$where_execute = "status = '0'";
	$getLog_toExecute = $surfPh->Query_selectStatementWhere('*', 'surf_ph_log as log INNER JOIN surf_spot_phs as phs ON log.surf_spot_phs_IDsurf_spot_phs = phs.IDsurf_spot_phs ',$where_execute);
	foreach ($getLog_toExecute as $rowExecute) {
		$url="http://magicseaweed.com/api/2OD858fTGZB5UEM4K3S4th7k7H32emF4/forecast/?spot_id=".$rowExecute['Spotid']."<br>";

		$get_contents = file_get_contents($url);
	 	$decode = json_decode($get_contents,true);
		$total_json = count($decode);
		//to get each value per datetime
		for($i=0;$i<$total_json;$i++){
			$localTimestamp = $decode[$i]['localTimestamp'];
			$year 	= date("Y", $localTimestamp);
			$month 	= date("m", $localTimestamp);
			$day 	= date("d", $localTimestamp);
			$time 	= date("H:i:s", $localTimestamp);
			//swell information
			$SwellMin 		= $decode[$i]['swell']['minBreakingHeight'];
			$SwellAbsMin 	= $decode[$i]['swell']['absMinBreakingHeight'];
			$SwellMax 		= $decode[$i]['swell']['maxBreakingHeight'];
			$SwellAbsMax 	= $decode[$i]['swell']['absMaxBreakingHeight'];
			$SwellUnit	 	= $decode[$i]['swell']['unit'];
			//wind information
			$WindSpeed				= $decode[$i]['wind']['speed'];
			$WindDirection			= $decode[$i]['wind']['direction'];
			$WindCompassDirection	= $decode[$i]['wind']['compassDirection'];
			$WindChill				= $decode[$i]['wind']['chill'];
			$WindGusts				= $decode[$i]['wind']['gusts'];
			$WindUnit				= $decode[$i]['wind']['unit'];
			//condition information
			$ConditionPress 		= $decode[$i]['condition']['pressure'];
			$ConditionTemp			= $decode[$i]['condition']['temperature'];
			$ConditionWeather		= $decode[$i]['condition']['weather'];
			$ConditionUnitPress		= $decode[$i]['condition']['unitPressure'];
			$ConditionUnit			= $decode[$i]['condition']['unit'];

			$table = 'surfSpotPh_perday';
			//check if data needs to be updated or added
			$whereUpdateData = "year = '".$year."' AND Month = '".$month."' AND Day = '".$day."' AND surf_spot_phs_IDsurf_spot_phs = '".$rowExecute['surf_spot_phs_IDsurf_spot_phs']."'";
			$dataUpdateAdd = $surfPh->Query_selectStatementWhere('*', $table,$whereUpdateData);
			
			if(!EMPTY($dataUpdateAdd)){
				//delete previous forecast
				$whereDelete = "surf_spot_phs_IDsurf_spot_phs = '".$rowExecute['surf_spot_phs_IDsurf_spot_phs']."'";
				$surfPh->Query_deleteStatement($table, $whereDelete);			
			}
			//save data in database
				$fieldNames = 'surf_spot_phs_IDsurf_spot_phs, year, Month, Day,	Time, SwellMin, SwellAbsMin, SwellMax, SwellAbsMax, SwellUnit, WindSpeed, WindDirection, WindCompassDirection, WindChill, WindGusts, WindUnit, ConditionPress, ConditionTemp, ConditionWeather, ConditionUnitPress, ConditionUnit';
				$values = "'".$rowExecute['surf_spot_phs_IDsurf_spot_phs']."', '".$year."', '".$month."', '".$day."', '".$time."', '".$SwellMin."', '".$SwellAbsMin."', '".$SwellMax."', '".$SwellAbsMax."', '".$SwellUnit."', '".$WindSpeed."', '".$WindDirection."', '".$WindCompassDirection."', '".$WindChill."', '".$WindGusts."', '".$WindUnit."', '".$ConditionPress."', '".$ConditionTemp."', '".$ConditionWeather."', '".$ConditionUnitPress."', '".$ConditionUnit."'";

				$saveSurfdataPerday = $surfPh->Query_insertStatement($table, $fieldNames, $values);
		}

		//update log
		$table_update = 'surf_ph_log';
		$fieldVal = "status = '1'";
		$where_update = "IDsurf_ph_log = '".$rowExecute['IDsurf_ph_log']."'";
		$updateLog = $surfPh->Query_updateStatement($table_update, $fieldVal, $where_update);
		break;
	}
	
		
	
	
?>