<?php

include('connection.php');


$action =$_POST['action'];
$id =$_POST['id'];
$name = $_POST['name'];


	
	if ($action=='view') {
			
		global $conn;
		$viewdatavar = viewdata('view');
		
		echo json_encode($viewdatavar);
		
	}
	else if($action == "add"){
	
			$sql = "INSERT INTO city ( city_id, name, create_datetime, last_update, is_active)VALUES('$id', '$name', now(), now(), 1)";

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
	  $sql = "UPDATE city SET name='$name', last_update=now() WHERE city_id='$id'";

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
	  $sql = "UPDATE city SET last_update=now(),is_active=0 WHERE city_id='$id'";

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
				
				$q = $conn->query("select city_id,name as 'city_name' from city where is_active=1");
				$q2 = $conn->query("select COUNT(*) from city where MONTH(create_datetime)=".date('n')." && YEAR(create_datetime)=".date('Y')."");

					$json_response = array();
					
					while($row = mysqli_fetch_array($q, MYSQL_ASSOC)) {
						
						$row_array['city_id'] = $row['city_id'];
						$row_array['city_name'] = $row['city_name'];
						array_push($json_response,$row_array);
						
					}
					$conn->close();
					$result = $q2;
					$sumcol = $result->fetch_row();
					
						$row_array['city_id'] = $sumcol[0];
						
						if( $data=='add')
						{
							$row_array['city_name'] = 'Data behasil di tambahkan ';
						}
						else if($data=='edit')
						{
							$row_array['city_name'] = 'Data behasil di ubah ';
						}
						else if($data=='delete')
						{
							$row_array['city_name'] = 'Data behasil di hapus ';
						}
						else 
						{
							$row_array['city_name'] = $data;
							}
						array_push($json_response,$row_array);
					
						
						return $json_response;
				
	}

?>