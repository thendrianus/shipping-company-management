<?php

include('connection.php');
$action =$_POST['action'];

	if ($action=='view') {
			
		global $conn;
		$viewdatavar = viewdata('view');
		
		echo json_encode($viewdatavar);
		
	}
		else if($action == "add"){
			
			$id =$_POST['id'];
			$username =$_POST['username'];
			$password =$_POST['password'];
			$status =$_POST['status'];
			global $conn;
			$sql = "INSERT INTO system_account ( system_account_id, username, password, account_status, create_datetime, last_update, is_active)VALUES('$id','$username' ,'$password','$status', now(), now(), 1)";

			if ($conn->query($sql) === TRUE) {
				
				global $conn;
				$viewdatavar = viewdata('add');
				
				echo json_encode($viewdatavar);
				
					
				}else {
					echo "error";
				}
		}
		
		else if($action == "getpass"){
			
			$id =$_POST['id'];
			
			global $conn;
			$sql =$conn->query("select password from system_account where system_account_id='$id' limit 1");
			$result = $sql;
			$sumcol = $result->fetch_row();
			
			echo $sumcol[0];
			
		}
		
		else if($action == "edit"){
			
			$id =$_POST['id'];
			$username =$_POST['username'];
			$password =$_POST['password'];
			$status =$_POST['status'];
			global $conn;
			$sql = "UPDATE system_account SET username='$username', password='$password', account_status='$status',last_update= now() where system_account_id='$id'";

			if ($conn->query($sql) === TRUE) {
				
				global $conn;
				$viewdatavar = viewdata('edit');
				
				echo json_encode($viewdatavar);
				
					
				}else {
					echo "error";
				}
		}
		
		else if($action == "user_edit"){
			
			$id =$_POST['id'];
			$password =$_POST['password'];
			global $conn;
			$sql = "UPDATE system_account SET password='$password',last_update= now() where system_account_id='$id'";

			if ($conn->query($sql) === TRUE) {
				
				
				echo json_encode("Password telah berhasil di ganti");
				
					
				}else {
					echo "error";
			}
		}
	else if ($action == "delete"){
	  $id =$_POST['id'];
	  $sql = "UPDATE system_account SET last_update=now(),is_active=0 WHERE system_account_id='$id' ";

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
				
				$q = $conn->query("select system_account_id, username, account_status from system_account where is_active=1");
				$q2 = $conn->query("select COUNT(*) from system_account where MONTH(create_datetime)=".date('m')." && YEAR(create_datetime)=".date('Y')."");

					$json_response = array();
					
					while($row = mysqli_fetch_array($q, MYSQL_ASSOC)) {
						
						$row_array['system_account_id'] = $row['system_account_id'];
						$row_array['username'] = $row['username'];
						$row_array['account_status'] = $row['account_status'];
						array_push($json_response,$row_array);
						
					}
					$conn->close();
					$result = $q2;
					$sumcol = $result->fetch_row();
					
						$row_array['system_account_id'] = $sumcol[0];
						
						if( $data=='add')
						{
							$row_array['username'] = 'Data behasil di tambahkan ';
						}
						else if($data=='edit')
						{
							$row_array['username'] = 'Data behasil di ubah ';
						}
						else if($data=='delete')
						{
							$row_array['username'] = 'Data behasil di hapus ';
						}
						else 
						{
							$row_array['username'] = $data;
							}
						array_push($json_response,$row_array);
					
						
						return $json_response;
				
	}

?>