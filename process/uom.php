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
	
			$sql = "INSERT INTO UOM ( UOM_id, UOM_name, create_datetime, last_update, is_active)VALUES('$id', '$status', now(), now(), 1)";

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
	 
	  $sql = "UPDATE UOM SET UOM_name='$status', last_update=now() WHERE UOM_id='$id' ";

			if ($conn->query($sql) === TRUE) {
				global $conn;
				$viewdatavar = viewdata('edit');
				
				echo json_encode($viewdatavar);
				
					
				}else {
					echo $date;
				}
	}
	else if ($action == "delete"){
	  
	  $sql = "UPDATE UOM SET last_update=now(),is_active=0 WHERE UOM_id='$id' ";

			if ($conn->query($sql) === TRUE) {
				global $conn;
				
				$viewdatavar = viewdata('delete');
				
				echo json_encode($viewdatavar);
				
					
				}else {
					echo $date;
				}
	}
	
function viewdata($data){
				global $conn;
				
				$q = $conn->query("select UOM_id, UOM_name from UOM where is_active=1");
				$q2 = $conn->query("select COUNT(*) from UOM where MONTH(create_datetime)=".date('m')." && YEAR(create_datetime)=".date('Y')."");

					$json_response = array();
					
					while($row = mysqli_fetch_array($q, MYSQL_ASSOC)) {
						
						$row_array['UOM_id'] = $row['UOM_id'];
						$row_array['UOM_name'] = $row['UOM_name'];
						array_push($json_response,$row_array);
						
					}
					$conn->close();
					$result = $q2;
					$sumcol = $result->fetch_row();
					
						$row_array['UOM_id'] = $sumcol[0];
						
						if( $data=='add')
						{
							$row_array['UOM_name'] = 'Data behasil di tambahkan ';
						}
						else if($data=='edit')
						{
							$row_array['UOM_name'] = 'Data behasil di ubah ';
						}
						else if($data=='delete')
						{
							$row_array['UOM_name'] = 'Data behasil di hapus ';
						}
						else 
						{
							$row_array['UOM_name'] = $data;
							}
						array_push($json_response,$row_array);
					
						
						return $json_response;
				
	}

?>