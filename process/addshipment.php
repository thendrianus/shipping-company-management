<?php

include('connection.php');


$action= $_POST['action'];

	if ($action=='getid') {
			
		global $conn;
		$q2 = $conn->query("select COUNT(*) from shipment where MONTH(create_datetime)=".date('n')."&& YEAR(create_datetime)=".date('Y')."");

					$json_response = array();
					$result = $q2;
					$sumcol = $result->fetch_row();
					$row_array['numrow'] = $sumcol[0];
					array_push($json_response,$row_array);
					echo json_encode($json_response);
	}
		else if ($action == "tripdata"){
		$tripid = $_POST['tripid'];
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
	
	else if ($action == "tripdetaildata"){
		$tripid = explode("-",$_POST['tripid']);
		$tripdetailid = $_POST['tripdetailid'];
	 	global $conn;
	  	$sql = $conn->query("select concat(t1.ship_trip_detail_id,'-',t2.ship_trip_name) as 'trip_id' from ship_trip_detail t1 inner join ship_trip t2 on t1.ship_trip_id = t2.ship_trip_id where t1.is_active=1 && t1.status =0 && t1.ship_trip_id = '$tripid[0]' &&  concat(t1.ship_trip_detail_id,'-',t2.ship_trip_name) LIKE '%$tripdetailid%'");
			
				$json_response = array();
					
					while($row = mysqli_fetch_array($sql, MYSQL_ASSOC)) {
						
						$row_array['label'] = $row['trip_id'];
						$row_array['value'] = $row['trip_id'];
						array_push($json_response,$row_array);
						
					}
				
				echo json_encode($json_response);
				
				
	}
	
	else if ($action=='get_cus') {
		$name= $_POST['name'];
		global $conn;
		$sql = $conn->query("select concat(customer_id,'-',firstname,' ',lastname) as 'name' from customer where is_active=1 && concat(customer_id,'-',firstname,' ',lastname) LIKE '%$name%'");

					$json_response = array();
					
					while($row = mysqli_fetch_array($sql, MYSQL_ASSOC)) {
						
						$row_array['label'] = $row['name'];
						$row_array['value'] = $row['name'];
						array_push($json_response,$row_array);
						
					}
					
					echo json_encode($json_response);
	}
	
	else if ($action=='get_item') {
		$name= $_POST['name'];
		$trip = explode("-",$_POST['trip']);
		
		global $conn;
		$sql = $conn->query("select concat(t1.shipment_item_detail_id,'-',t2.name) as 'name', t1.price from shipment_item_detail t1 inner join shipment_item t2 on t1.shipment_item_id = t2.shipment_item_id where t1.is_active=1 && t1.ship_trip_id = '$trip[0]' && concat(t1.shipment_item_detail_id,'-',t2.name) LIKE '%$name%'");

					$json_response = array();
					
					while($row = mysqli_fetch_array($sql, MYSQL_ASSOC)) {
						
						$row_array['label'] = $row['name'];
						$row_array['value'] = $row['name'];
						$row_array['price'] = $row['price'];
						array_push($json_response,$row_array);
						
					}
					
					echo json_encode($json_response);
	}
	
	else if ($action=='add') {
		$id=$_POST['id'];
		$cusid=$_POST['cusid'];
		$payment=$_POST['payment'];
		$tripid=$_POST['tripid'];
		$receiver=$_POST['receiver'];
		$rcontact=$_POST['rcontact'];
		$date=$_POST['date'];
		$note=$_POST['note'];
		$extra=$_POST['extra'];
		$discount=$_POST['discount'];
		$itemdata=$_POST['itemdata'];
		$pay=$_POST['pay'];
		if($payment == 'Cash On Delivery')
		{
			$status_id ='PYS15061';
		}else if($payment == 'Cash'){
			$status_id ='PYS15062';
		}
		else{$status_id ='PYS15063';}
		global $conn;
		$sql = "INSERT INTO shipment(shipment_id, customer_id, payment, ship_trip_id, receiver, receiver_contact, shipted_date, payment_status_id, note, shipment_status_id, extra_charge, discount, create_datetime, last_update, is_active) VALUES ('$id', '$cusid', '$payment', '$tripid', '$receiver', '$rcontact', '$date', '$status_id', '$note', 'SPS15061', '$extra', '$discount', now(), now(), 1)";
		
					if ($conn->query($sql) === TRUE) {
						$json2 = json_decode($itemdata, true);
						$q2 = $conn->query("select COUNT(*) from shipment_detail where MONTH(create_datetime)=".date('n')."&& YEAR(create_datetime)=".date('Y')."");

							$result = $q2;
							$sumcol = $result->fetch_row();
							$numrow = $sumcol[0]+1;
							
						
						foreach($json2 as $i){
							$detail_id = 'SPD'.date('ym').$numrow;
							$itemid =split('-',$i['itemid']);
							$sql2 = "INSERT INTO shipment_detail(shipment_detail_id, shipment_item_detail_id, shipment_id, price, quantity, discount, note, create_datetime, last_update, is_active) VALUES ('".$detail_id."','".$itemid[0]."','".$id."','".$i['price']."','".$i['quantity']."','".$i['discount']."','".$i['note']."',now(),now(),1)";
							if ($conn->query($sql2) === TRUE) 
							{
								$numrow += 1;
							} else
							{
								echo "Error: " . $sql2 . "<br>" . $conn->error;
								}
						}
							$q3 = $conn->query("select COUNT(*) from shipment_payment where MONTH(create_datetime)=".date('n')."&& YEAR(create_datetime)=".date('Y')."");

								$result2 = $q3;
								$sumcol2 = $result2->fetch_row();
								$numrow2 = $sumcol2[0]+1;
								$payment_id = 'SPP'.date('ym').$numrow;																		
								$sql3 = "INSERT INTO shipment_payment(shipment_payment_id, shipment_id, payment, note, create_datetime, last_update, is_active) VALUES ('$payment_id','$id','$pay','$payment',now(),now(),1)";
								if ($conn->query($sql3) === TRUE) {
										echo"insert berhasil";
								}
								else{
									
									echo "Error: " . $sql3 . "<br>" . $conn->error;
								}
						
						
					} else {
						
						echo "Error: " . $sql . "<br>" . $conn->error;
						
					}
					
					
	}

?>