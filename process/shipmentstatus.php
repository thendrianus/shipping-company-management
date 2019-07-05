<?php

include('connection.php');


$action =$_POST['action'];
$id =$_POST['id'];
$status = $_POST['status'];


	
	if ($action=='view') {
			
		global $conn;
		$viewdatavar = viewdata('view');
		
		echo json_encode($viewdatavar);
		
	}
	else if($action == "add"){
	
			$sql = "INSERT INTO shipment_status ( shipment_status_id, shipment_status, create_datetime, last_update, is_active)VALUES('$id', '$status', now(), now(), 1)";

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
	  $sql = "UPDATE shipment_status SET shipment_status='$status', last_update=now() WHERE shipment_status_id='$id' ";

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
	  $sql = "UPDATE shipment_status SET last_update=now(),is_active=0 WHERE shipment_status_id='$id'";

			if ($conn->query($sql) === TRUE) {
				global $conn;
				
				global $conn;
				$viewdatavar = viewdata('delete');
				
				echo json_encode($viewdatavar);
				
					
				}else {
					echo $date;
				}
	}
	
function viewdata($data){
				global $conn;
				
				$q = $conn->query("select shipment_status_id,shipment_status from shipment_status where is_active=1");
				$q2 = $conn->query("select COUNT(*) from shipment_status where MONTH(create_datetime)=".date('n')."&& YEAR(create_datetime)=".date('Y')."");

					$json_response = array();
					
					while($row = mysqli_fetch_array($q, MYSQL_ASSOC)) {
						
						$row_array['shipment_status_id'] = $row['shipment_status_id'];
						$row_array['shipment_status'] = $row['shipment_status'];
						array_push($json_response,$row_array);
						
					}
					$conn->close();
					$result = $q2;
					$sumcol = $result->fetch_row();
					
						$row_array['shipment_status_id'] = $sumcol[0];
						
						if( $data=='add')
						{
							$row_array['shipment_status'] = 'Data behasil di tambahkan ';
						}
						else if($data=='edit')
						{
							$row_array['shipment_status'] = 'Data behasil di ubah ';
						}
						else if($data=='delete')
						{
							$row_array['shipment_status'] = 'Data behasil di hapus ';
						}
						else 
						{
							$row_array['shipment_status'] = $data;
							}
						array_push($json_response,$row_array);
					
						
						return $json_response;
				
	}

?>