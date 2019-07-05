<?php

include('connection.php');


$action =$_POST['action'];
$id =$_POST['id'];
$name = $_POST['name'];
$from = $_POST['from'];
$destination = $_POST['destination'];
$note = $_POST['note'];

	if ($action=='view') {
			
		global $conn;
		$viewdatavar = viewdata('view');
		
		echo json_encode($viewdatavar);
		
	}
	else if ($action == "citydata"){
	 	global $conn;
	  	$sql = $conn->query("select concat(city_id,'-',name) as 'city' from city where is_active=1 && concat(city_id,'-',name) LIKE '%$name%'");
			//&& name LIKE '%tam%'
				$json_response = array();
					
					while($row = mysqli_fetch_array($sql, MYSQL_ASSOC)) {
						
						$row_array['label'] = $row['city'];
						$row_array['value'] = $row['city'];
						array_push($json_response,$row_array);
						
					}
				
				echo json_encode($json_response);
				
				
	}
	else if($action == "add"){
	
			$sql = "INSERT INTO ship_trip ( ship_trip_id, ship_trip_name, ship_from, destination_city, note, create_datetime, last_update, is_active)
			VALUES('$id', '$name', '$from','$destination','$note', now(), now(), 1)";

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
	  $sql = "UPDATE ship_trip SET ship_trip_name='$name', ship_from = '$from',destination_city='$destination', note='$note',  last_update=now() WHERE ship_trip_id='$id'";

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
	  $sql = "UPDATE ship_trip SET last_update=now(),is_active=0 WHERE ship_trip_id='$id'";

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
				
				$q = $conn->query("select t1.ship_trip_id as 'ship_trip_id', t1.ship_trip_name as 'ship_trip_name',concat(t1.ship_from, '-',t2.name)as 'ship_from',concat(t1.destination_city, '-',t3.name)as 'destination_city', t1.note as 'note' from ship_trip t1 inner join city t2 on t1.ship_from = t2.city_id inner join city t3 on t1.destination_city = t3.city_id  where t1.is_active=1");
				$q2 = $conn->query("select COUNT(*) from ship_trip where MONTH(create_datetime)=".date('n')."&& YEAR(create_datetime)=".date('Y')."");

					$json_response = array();
					
					while($row = mysqli_fetch_array($q, MYSQL_ASSOC)) {
						
						$row_array['ship_trip_id'] = $row['ship_trip_id'];
						$row_array['ship_trip_name'] = $row['ship_trip_name'];
						$row_array['ship_from'] = $row['ship_from'];
						$row_array['destination_city'] = $row['destination_city'];
						$row_array['note'] = $row['note'];
						array_push($json_response,$row_array);
						
					}
					$conn->close();
					$result = $q2;
					$sumcol = $result->fetch_row();
					
						$row_array['ship_trip_id'] = $sumcol[0];
						
						if( $data=='add')
						{
							$row_array['ship_trip_name'] = 'Data behasil di tambahkan ';
						}
						else if($data=='edit')
						{
							$row_array['ship_trip_name'] = 'Data behasil di ubah ';
						}
						else if($data=='delete')
						{
							$row_array['ship_trip_name'] = 'Data behasil di hapus ';
						}
						else 
						{
							$row_array['ship_trip_name'] = $data;
							}
						
						$row_array['ship_from'] = '';
						$row_array['destination_city'] = '';
						$row_array['note'] = '';
						array_push($json_response,$row_array);
						
						
						return $json_response;
				
	}

?>