<?php

include('connection.php');


$action= $_POST['action'];

	if ($action=='getid') {
			
		global $conn;
		$q2 = $conn->query("select COUNT(*) from customer where MONTH(create_datetime)=".date('n')."&& YEAR(create_datetime)=".date('Y')."");

					$json_response = array();
					$result = $q2;
					$sumcol = $result->fetch_row();
					$row_array['numrow'] = $sumcol[0];
					array_push($json_response,$row_array);
					echo json_encode($json_response);
	}
	
	
	else if ($action=='add') {
		$id= $_POST['id'];
		$title= $_POST['title'];
		$firstname= $_POST['firstname'];
		$lastname= $_POST['lastname'];
		$cardid=$_POST['cardid'];
		$address=$_POST['address'];
		$email=$_POST['email'];
		$country=$_POST['country'];
		$city=$_POST['city'];
		$contact=$_POST['contact'];		
		$note=$_POST['note'];
		global $conn;
		
					
						$q = "INSERT INTO customer(customer_id, title, firstname, lastname, id_card_number, address, contact_person, email_address, country, city_id, note, create_datetime, last_update, is_active) VALUES ('$id', '$title', '$firstname', '$lastname', '$cardid', '$address', '$contact', '$email', '$country', '$city', '$note', now(), now(), 1)";
						if ($conn->query($q) === TRUE) {
							echo "Data berhasil dimasukkan";
						}
						else {
						
						echo "Error: " . $sql . "<br>" . $conn->error;
						
					}
						
				
					
					
	}
?>