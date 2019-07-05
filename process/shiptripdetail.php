<?php

include('connection.php');


$action =$_POST['action'];
$id =$_POST['id'];
$tripid = $_POST['tripid'];
$departure = $_POST['departure'];
$arrival = $_POST['arrival'];
$note = $_POST['note'];

	if ($action=='view') {
			
		global $conn;
		$viewdatavar = viewdata('view');
		
		echo json_encode($viewdatavar);
		
	}
	else if ($action == "tripdata"){
	 	global $conn;
	  	$sql = $conn->query("select concat(ship_trip_id,'-',ship_trip_name) as 'trip_id' from ship_trip where is_active=1 && concat(ship_trip_id,'-',ship_trip_name) LIKE '%$tripid%'");
			
				$json_response = array();
					
					while($row = mysqli_fetch_array($sql, MYSQL_ASSOC)) {
						
						$row_array['label'] = $row['trip_id'];
						$row_array['value'] = $row['trip_id'];
						array_push($json_response,$row_array);
						
					}
				
				echo json_encode($json_response);
				
				
	}
	else if($action == "add"){
	
			$sql = "INSERT INTO ship_trip_detail ( ship_trip_detail_id, ship_trip_id, departure_date, arrival_date, note, create_datetime, last_update, is_active)
			VALUES('$id', '$tripid', '$departure','$arrival','$note', now(), now(), 1)";

			if ($conn->query($sql) === TRUE) {
				global $conn;
				
				global $conn;
				$viewdatavar = viewdata('add');
				
				echo json_encode($viewdatavar);
				
					
				}else { 	
					echo "error";
				}
		}
	else if ($action == "edit") {
	  $sql = "UPDATE ship_trip_detail SET ship_trip_id='$tripid', departure_date='$departure', arrival_date = '$arrival', note='$note',  last_update=now() WHERE ship_trip_detail_id='$id'";

			if ($conn->query($sql) === TRUE) {
				global $conn;
				
				global $conn;
				$viewdatavar = viewdata('edit');
				
				echo json_encode($viewdatavar);
				
					
				}else {
					echo $date;
				}
	}
	else if ($action == "delete"){
	  $sql = "UPDATE ship_trip_detail SET last_update=now(),is_active=0 WHERE ship_trip_detail_id='$id'";

			if ($conn->query($sql) === TRUE) {
				global $conn;
				
				$viewdatavar = viewdata('delete');
				
				echo json_encode($viewdatavar);
				
					
				}else {
					echo "error";
				}
	}
	
function viewdata($data){
				global $conn;
				
				$q = $conn->query("select t1.ship_trip_detail_id as 'ship_trip_detail_id',concat(t1.ship_trip_id, '-',t2.ship_trip_name)as 'ship_trip_id',t1.departure_date, t1.arrival_date, t1.note as 'note' from ship_trip_detail t1 inner join ship_trip t2 on t1.ship_trip_id = t2.ship_trip_id where t1.is_active=1");
				$q2 = $conn->query("select COUNT(*) from ship_trip_detail where MONTH(create_datetime)=".date('n')."&& YEAR(create_datetime)=".date('Y')."");

					$json_response = array();
					
					while($row = mysqli_fetch_array($q, MYSQL_ASSOC)) {
						
						$row_array['ship_trip_detail_id'] = $row['ship_trip_detail_id'];
						$row_array['ship_trip_id'] = $row['ship_trip_id'];
						$row_array['departure_date'] = $row['departure_date'];
						$row_array['arrival_date'] = $row['arrival_date'];
						$row_array['note'] = $row['note'];
						array_push($json_response,$row_array);
						
					}
					$conn->close();
					$result = $q2;
					$sumcol = $result->fetch_row();
					
						$row_array['ship_trip_detail_id'] = $sumcol[0];
						
						if( $data=='add')
						{
							$row_array['ship_trip_id'] = 'Data behasil di tambahkan ';
						}
						else if($data=='edit')
						{
							$row_array['ship_trip_id'] = 'Data behasil di ubah ';
						}
						else if($data=='delete')
						{
							$row_array['ship_trip_id'] = 'Data behasil di hapus ';
						}
						else 
						{
							$row_array['ship_trip_id'] = $data;
							}
						
						$row_array['departure_date'] = '';
						$row_array['arrival_date'] = '';
						$row_array['note'] = '';
						array_push($json_response,$row_array);
						
						
						return $json_response;
				
	}

?>