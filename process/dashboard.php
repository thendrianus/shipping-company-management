<?php

include('connection.php');

$action= $_POST['action'];

	if($action == 'session')
	{
		
		 session_start();

		 if (isset($_SESSION['shippinguser'])) {
			$json_response = array();
		   $row_array['session'] = $_SESSION['shippinguser'];
		   $row_array['session2'] = $_SESSION['account_status'];
		   $row_array['session3'] = $_SESSION['shippinguserid'];
		   array_push($json_response,$row_array);
			echo json_encode($json_response);
		 } else {
			 $json_response = array();
		   $row_array['session'] = "index";
		   array_push($json_response,$row_array);
					echo json_encode($json_response);
		 }
		
		
	}
	else if($action == 'logout')
	{
		session_start();
		session_destroy();
		echo"";
	}
	
else if($action == 'setStartData')
	{
		
		 
					$q2 = $conn->query("SELECT sum(`quantity`) FROM `shipment_detail` WHERE `is_active`=1");
					$q3 = $conn->query("SELECT sum(`payment`) FROM `shipment_payment` WHERE `is_active`=1");
					$q4 = $conn->query("SELECT count(`customer_id`) FROM `customer` WHERE `is_active`=1");
					$q5 = $conn->query("SELECT count(`ship_trip_detail_id`) FROM `ship_trip_detail` WHERE `is_active`=1");
					

					$json_response = array();
					$result = $q2;
					$sumcol = $result->fetch_row();
					$revenue = $q3->fetch_row();
					$cus = $q4->fetch_row();
					$trip = $q5->fetch_row();
					$row_array['setSumShipment'] = $sumcol[0];
					$row_array['revenue'] = $revenue[0];
					$row_array['cus'] = $cus[0];
					$row_array['trip'] = $trip[0];
					array_push($json_response,$row_array);
					echo json_encode($json_response);
		   		
		
		
	}
else if ($action=="view6") {
	global $conn;
$requestData= $_REQUEST;

$columns = array( 
	0 =>'num', 
	1 => 'id'
);

$sql = "select (select if((SELECT sum(t2.price * t2.quantity) FROM shipment t1 inner join shipment_detail t2 on t1.shipment_id = t2.shipment_id  WHERE t1.is_active =1 && t1.shipment_id =t5.shipment_id) > (SELECT sum(t4.payment) FROM shipment t3 inner join shipment_payment t4 on t3.shipment_id = t4.shipment_id  WHERE t3.is_active =1 && t3.shipment_id =t5.shipment_id),(SELECT sum(t2.price * t2.quantity) FROM shipment t1 inner join shipment_detail t2 on t1.shipment_id = t2.shipment_id  WHERE t1.is_active =1 && t1.shipment_id =t5.shipment_id) - (SELECT sum(t4.payment) FROM shipment t3 inner join shipment_payment t4 on t3.shipment_id = t4.shipment_id  WHERE t3.is_active =1 && t3.shipment_id =t5.shipment_id) ,0))as 'num', concat(t5.shipment_id,'-',t6.title,' ',t6.firstname,' ',t6.lastname,'(',t6.customer_id,')') as 'id' from shipment t5 inner join customer t6 on t5.customer_id = t6.customer_id where t5.is_active =1 order by (SELECT sum(t2.price * t2.quantity)  FROM shipment t1 inner join shipment_detail t2 on t1.shipment_id = t2.shipment_id WHERE t1.is_active =1 && t1.shipment_id =t5.shipment_id) - (SELECT sum(t4.payment) FROM shipment t3 inner join shipment_payment t4 on t3.shipment_id = t4.shipment_id  WHERE t3.is_active =1 AND t3.shipment_id =t5.shipment_id)";
$query=mysqli_query($conn, $sql) or die("");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  

$sql ="select (select if((SELECT sum(t2.price * t2.quantity) FROM shipment t1 inner join shipment_detail t2 on t1.shipment_id = t2.shipment_id  WHERE t1.is_active =1 && t1.shipment_id =t5.shipment_id) > (SELECT sum(t4.payment) FROM shipment t3 inner join shipment_payment t4 on t3.shipment_id = t4.shipment_id  WHERE t3.is_active =1 && t3.shipment_id =t5.shipment_id),(SELECT sum(t2.price * t2.quantity) FROM shipment t1 inner join shipment_detail t2 on t1.shipment_id = t2.shipment_id  WHERE t1.is_active =1 && t1.shipment_id =t5.shipment_id) - (SELECT sum(t4.payment) FROM shipment t3 inner join shipment_payment t4 on t3.shipment_id = t4.shipment_id  WHERE t3.is_active =1 && t3.shipment_id =t5.shipment_id) ,0))as 'num', concat(t5.shipment_id,'-',t6.title,' ',t6.firstname,' ',t6.lastname,'(',t6.customer_id,')') as 'id' from shipment t5 inner join customer t6 on t5.customer_id = t6.customer_id where t5.is_active =1 order by (SELECT sum(t2.price * t2.quantity)  FROM shipment t1 inner join shipment_detail t2 on t1.shipment_id = t2.shipment_id WHERE t1.is_active =1 && t1.shipment_id =t5.shipment_id) - (SELECT sum(t4.payment) FROM shipment t3 inner join shipment_payment t4 on t3.shipment_id = t4.shipment_id  WHERE t3.is_active =1 AND t3.shipment_id =t5.shipment_id)";

if( !empty($requestData['search']['value']) ) {  
	$sql.=" ";    
}
$query=mysqli_query($conn, $sql)or die("");;

$totalFiltered = mysqli_num_rows($query); 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

$query=mysqli_query($conn, $sql) or die("");;

$data = array();
while( $row=mysqli_fetch_array($query) ) {  
	$nestedData=array(); 

	$nestedData[] = $row["num"];
	$nestedData[] = $row["id"];
	
	$data[] = $nestedData;
}

$json_data = array(
			"draw"            => intval( $requestData['draw'] ),
			"recordsTotal"    => intval( $totalData ),  
			"recordsFiltered" => intval( $totalFiltered ), 
			"data"            => $data   
			);

echo json_encode($json_data); 

}
	else if ($action=="view") {
	global $conn;
$requestData= $_REQUEST;

$columns = array( 
	0 =>'id', 
	1 => 'num'
);

$sql = "select (select if((SELECT sum(t2.price * t2.quantity) FROM shipment t1 inner join shipment_detail t2 on t1.shipment_id = t2.shipment_id  WHERE t1.is_active =1 && t1.shipment_id =t5.shipment_id) > (SELECT sum(t4.payment) FROM shipment t3 inner join shipment_payment t4 on t3.shipment_id = t4.shipment_id  WHERE t3.is_active =1 && t3.shipment_id =t5.shipment_id),(SELECT sum(t2.price * t2.quantity) FROM shipment t1 inner join shipment_detail t2 on t1.shipment_id = t2.shipment_id  WHERE t1.is_active =1 && t1.shipment_id =t5.shipment_id) - (SELECT sum(t4.payment) FROM shipment t3 inner join shipment_payment t4 on t3.shipment_id = t4.shipment_id  WHERE t3.is_active =1 && t3.shipment_id =t5.shipment_id) ,0))as 'num', concat(t5.shipment_id,'-',t6.title,' ',t6.firstname,' ',t6.lastname,'(',t6.customer_id,')') as 'id' from shipment t5 inner join customer t6 on t5.customer_id = t6.customer_id where t5.is_active =1 order by (SELECT sum(t2.price * t2.quantity)  FROM shipment t1 inner join shipment_detail t2 on t1.shipment_id = t2.shipment_id WHERE t1.is_active =1 && t1.shipment_id =t5.shipment_id) - (SELECT sum(t4.payment) FROM shipment t3 inner join shipment_payment t4 on t3.shipment_id = t4.shipment_id  WHERE t3.is_active =1 AND t3.shipment_id =t5.shipment_id)";
$query=mysqli_query($conn, $sql) or die("");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  

$sql ="select (select if((SELECT sum(t2.price * t2.quantity) FROM shipment t1 inner join shipment_detail t2 on t1.shipment_id = t2.shipment_id  WHERE t1.is_active =1 && t1.shipment_id =t5.shipment_id) > (SELECT sum(t4.payment) FROM shipment t3 inner join shipment_payment t4 on t3.shipment_id = t4.shipment_id  WHERE t3.is_active =1 && t3.shipment_id =t5.shipment_id),(SELECT sum(t2.price * t2.quantity) FROM shipment t1 inner join shipment_detail t2 on t1.shipment_id = t2.shipment_id  WHERE t1.is_active =1 && t1.shipment_id =t5.shipment_id) - (SELECT sum(t4.payment) FROM shipment t3 inner join shipment_payment t4 on t3.shipment_id = t4.shipment_id  WHERE t3.is_active =1 && t3.shipment_id =t5.shipment_id) ,0))as 'num', concat(t5.shipment_id,'-',t6.title,' ',t6.firstname,' ',t6.lastname,'(',t6.customer_id,')') as 'id' from shipment t5 inner join customer t6 on t5.customer_id = t6.customer_id where t5.is_active =1 order by (SELECT sum(t2.price * t2.quantity)  FROM shipment t1 inner join shipment_detail t2 on t1.shipment_id = t2.shipment_id WHERE t1.is_active =1 && t1.shipment_id =t5.shipment_id) - (SELECT sum(t4.payment) FROM shipment t3 inner join shipment_payment t4 on t3.shipment_id = t4.shipment_id  WHERE t3.is_active =1 AND t3.shipment_id =t5.shipment_id)";

if( !empty($requestData['search']['value']) ) {  
	$sql.="  ";    
}
$query=mysqli_query($conn, $sql)or die("");;

$totalFiltered = mysqli_num_rows($query); 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

$query=mysqli_query($conn, $sql) or die("");;

$data = array();
while( $row=mysqli_fetch_array($query) ) {  
	$nestedData=array(); 

	$nestedData[] = $row["id"];
	$nestedData[] = $row["num"];
	
	$data[] = $nestedData;
}

$json_data = array(
			"draw"            => intval( $requestData['draw'] ),
			"recordsTotal"    => intval( $totalData ),  
			"recordsFiltered" => intval( $totalFiltered ), 
			"data"            => $data   
			);

echo json_encode($json_data); 

}
	else if ($action == "omset_pengiriman") {
		global $conn;
	  $sql =  $conn->query("SELECT concat(ship_trip_id,'-',ship_trip_name) as 'name' FROM ship_trip WHERE is_active = 1");

			$json_response = array();
					
					while($row = mysqli_fetch_array($sql, MYSQL_ASSOC)) {
						
						$row_array['name'] = $row['name'];
						array_push($json_response,$row_array);
						
					}
			echo json_encode($json_response);
	}
	else if ($action == "chartdata") {
		$trip = $_POST['trip'];
		global $conn;
	  $sql =  $conn->query("select concat(month(t3.departure_date),'-',t3.ship_trip_detail_id) as 'trip', ifnull((SELECT sum((t2.price * t2.quantity)) FROM shipment t1 inner join shipment_detail t2 on t1.shipment_id = t2.shipment_id WHERE t1.ship_trip_id = t3.ship_trip_detail_id),0) as 'num' from ship_trip_detail t3 inner join ship_trip t4 on t3.ship_trip_id = t4.ship_trip_id where t3.is_active='1' && t4.ship_trip_id = '$trip' && year(t3.departure_date)=YEAR(CURDATE())order by t3.departure_date ASC");

			$json_response = array();
					
					while($row = mysqli_fetch_array($sql, MYSQL_ASSOC)) {
						
						$row_array['trip'] = $row['trip'];
						$row_array['num'] = $row['num'];
						array_push($json_response,$row_array);
						
					}
			echo json_encode($json_response);
	}
	
	
	
	else if ($action=="view1") {
	global $conn;
$requestData= $_REQUEST;

$columns = array( 
	0 =>'customer', 
	1 => 'ship_trip',
	2 => 'date',
	3=> 'note'
);

$sql = "SELECT concat(t1.customer_id,'-',t2.title,'. ',t2.firstname,' ',t2.lastname) as 'customer',(select concat(t1.ship_trip_id,'-',t3.ship_trip_name) from ship_trip t3 inner join ship_trip_detail t4 on t3.ship_trip_id = t4.ship_trip_id where t4.ship_trip_detail_id = t1.ship_trip_id ) as 'ship_trip',t1.shipted_date as 'date', t1.note FROM shipment t1 inner join customer t2 on t1.customer_id = t2.customer_id where month(t1.create_datetime ) = month(now()) AND t1.is_active = 1";
$query=mysqli_query($conn, $sql) or die("");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  

$sql ="SELECT concat(t1.customer_id,'-',t2.title,'. ',t2.firstname,' ',t2.lastname) as 'customer',(select concat(t1.ship_trip_id,'-',t3.ship_trip_name) from ship_trip t3 inner join ship_trip_detail t4 on t3.ship_trip_id = t4.ship_trip_id where t4.ship_trip_detail_id = t1.ship_trip_id ) as 'ship_trip',t1.shipted_date as 'date', t1.note FROM shipment t1 inner join customer t2 on t1.customer_id = t2.customer_id where month(t1.create_datetime ) = month(now()) AND t1.is_active = 1";

if( !empty($requestData['search']['value']) ) {  
	$sql.="  AND ( t1.customer_id  LIKE '%".$requestData['search']['value']."%' ";
	$sql.="  OR  (concat(t2.title,'. ', t2.firstname,' ',t2.lastname))  LIKE '%".$requestData['search']['value']."%' ";
	$sql.="  OR  (select concat(t1.ship_trip_id,'-',t3.ship_trip_name) from ship_trip t3 inner join ship_trip_detail t4 on t3.ship_trip_id = t4.ship_trip_id where t4.ship_trip_detail_id = t1.ship_trip_id)  LIKE '%".$requestData['search']['value']."%' )";    
}
$query=mysqli_query($conn, $sql)or die("");;

$totalFiltered = mysqli_num_rows($query); 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

$query=mysqli_query($conn, $sql) or die("");;

$data = array();
while( $row=mysqli_fetch_array($query) ) {  
	$nestedData=array(); 

	$nestedData[] = $row["customer"];
	$nestedData[] = $row["ship_trip"];
	$nestedData[] = $row["date"];
	$nestedData[] = $row["note"];
	
	$data[] = $nestedData;
}

$json_data = array(
			"draw"            => intval( $requestData['draw'] ),
			"recordsTotal"    => intval( $totalData ),  
			"recordsFiltered" => intval( $totalFiltered ), 
			"data"            => $data   
			);

echo json_encode($json_data); 

}

	else if ($action=="view2") {
	global $conn;
$requestData= $_REQUEST;

$columns = array( 
	0 =>'customer', 
	1 => 'ship_trip',
	2 => 'date',
	3=> 'note'
);

$sql = "SELECT concat(t1.customer_id,'-',t2.title,'. ',t2.firstname,' ',t2.lastname) as 'customer',(select concat(t1.ship_trip_id,'-',t3.ship_trip_name) from ship_trip t3 inner join ship_trip_detail t4 on t3.ship_trip_id = t4.ship_trip_id where t4.ship_trip_detail_id = t1.ship_trip_id ) as 'ship_trip',t1.shipted_date as 'date', t1.note FROM shipment t1 inner join customer t2 on t1.customer_id = t2.customer_id where t1.shipment_status_id = 'SPS15061' AND t1.is_active = 1";
$query=mysqli_query($conn, $sql) or die("");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  

$sql ="SELECT concat(t1.customer_id,'-',t2.title,'. ',t2.firstname,' ',t2.lastname) as 'customer',(select concat(t1.ship_trip_id,'-',t3.ship_trip_name) from ship_trip t3 inner join ship_trip_detail t4 on t3.ship_trip_id = t4.ship_trip_id where t4.ship_trip_detail_id = t1.ship_trip_id ) as 'ship_trip',t1.shipted_date as 'date', t1.note FROM shipment t1 inner join customer t2 on t1.customer_id = t2.customer_id where t1.shipment_status_id = 'SPS15061' AND t1.is_active = 1";

if( !empty($requestData['search']['value']) ) {  
	$sql.="  AND ( t1.customer_id  LIKE '%".$requestData['search']['value']."%' ";
	$sql.="  OR  (concat(t2.title,'. ', t2.firstname,' ',t2.lastname))  LIKE '%".$requestData['search']['value']."%' ";
	$sql.="  OR  (select concat(t1.ship_trip_id,'-',t3.ship_trip_name) from ship_trip t3 inner join ship_trip_detail t4 on t3.ship_trip_id = t4.ship_trip_id where t4.ship_trip_detail_id = t1.ship_trip_id)  LIKE '%".$requestData['search']['value']."%' )";    
}
$query=mysqli_query($conn, $sql)or die("");;

$totalFiltered = mysqli_num_rows($query); 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

$query=mysqli_query($conn, $sql) or die("");;

$data = array();
while( $row=mysqli_fetch_array($query) ) {  
	$nestedData=array(); 

	$nestedData[] = $row["customer"];
	$nestedData[] = $row["ship_trip"];
	$nestedData[] = $row["date"];
	$nestedData[] = $row["note"];
	
	$data[] = $nestedData;
}

$json_data = array(
			"draw"            => intval( $requestData['draw'] ),
			"recordsTotal"    => intval( $totalData ),  
			"recordsFiltered" => intval( $totalFiltered ), 
			"data"            => $data   
			);

echo json_encode($json_data); 

}

	else if ($action=="view3") {
	global $conn;
$requestData= $_REQUEST;

$columns = array( 
	0 =>'customer', 
	1 => 'ship_trip',
	2 => 'payment',
	3=> 'note'
);

$sql = "SELECT concat(t1.customer_id,'-',t2.title,'. ',t2.firstname,' ',t2.lastname) as 'customer',(select concat(t1.ship_trip_id,'-',t3.ship_trip_name) from ship_trip t3 inner join ship_trip_detail t4 on t3.ship_trip_id = t4.ship_trip_id where t4.ship_trip_detail_id = t1.ship_trip_id ) as 'ship_trip',t5.payment_status as'payment', t1.note FROM shipment t1 inner join customer t2 on t1.customer_id = t2.customer_id inner join  payment_status t5 on t1.payment_status_id = t5.payment_status_id where t1.shipment_status_id = 'SPS15062' AND t1.is_active = 1";
$query=mysqli_query($conn, $sql) or die("");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  

$sql ="SELECT concat(t1.customer_id,'-',t2.title,'. ',t2.firstname,' ',t2.lastname) as 'customer',(select concat(t1.ship_trip_id,'-',t3.ship_trip_name) from ship_trip t3 inner join ship_trip_detail t4 on t3.ship_trip_id = t4.ship_trip_id where t4.ship_trip_detail_id = t1.ship_trip_id ) as 'ship_trip',t5.payment_status as 'payment', t1.note FROM shipment t1 inner join customer t2 on t1.customer_id = t2.customer_id inner join  payment_status t5 on t1.payment_status_id = t5.payment_status_id where t1.shipment_status_id = 'SPS15062' AND t1.is_active = 1";

if( !empty($requestData['search']['value']) ) {  
	$sql.="  AND ( t1.customer_id  LIKE '%".$requestData['search']['value']."%' ";
	$sql.="  OR  (concat(t2.title,'. ', t2.firstname,' ',t2.lastname))  LIKE '%".$requestData['search']['value']."%' ";
	$sql.="  OR  (select concat(t1.ship_trip_id,'-',t3.ship_trip_name) from ship_trip t3 inner join ship_trip_detail t4 on t3.ship_trip_id = t4.ship_trip_id where t4.ship_trip_detail_id = t1.ship_trip_id)  LIKE '%".$requestData['search']['value']."%' )";    
}
$query=mysqli_query($conn, $sql)or die("");;

$totalFiltered = mysqli_num_rows($query); 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

$query=mysqli_query($conn, $sql) or die("");;

$data = array();
while( $row=mysqli_fetch_array($query) ) {  
	$nestedData=array(); 

	$nestedData[] = $row["customer"];
	$nestedData[] = $row["ship_trip"];
	$nestedData[] = $row["payment"];
	$nestedData[] = $row["note"];
	
	$data[] = $nestedData;
}

$json_data = array(
			"draw"            => intval( $requestData['draw'] ),
			"recordsTotal"    => intval( $totalData ),  
			"recordsFiltered" => intval( $totalFiltered ), 
			"data"            => $data   
			);

echo json_encode($json_data); 

}
	else if ($action=="view4") {
	global $conn;
$requestData= $_REQUEST;

$columns = array( 
	0 =>'customer', 
	1 => 'ship_trip',
	2 => 'date',
	3=> 'note'
);

$sql = "SELECT concat(t1.customer_id,'-',t2.title,'. ',t2.firstname,' ',t2.lastname) as 'customer',(select concat(t1.ship_trip_id,'-',t3.ship_trip_name) from ship_trip t3 inner join ship_trip_detail t4 on t3.ship_trip_id = t4.ship_trip_id where t4.ship_trip_detail_id = t1.ship_trip_id ) as 'ship_trip',t1.shipted_date as 'date', t1.note FROM shipment t1 inner join customer t2 on t1.customer_id = t2.customer_id  where t1.shipment_status_id = 'SPS15101' AND t1.is_active = 1";
$query=mysqli_query($conn, $sql) or die("");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  

$sql ="SELECT concat(t1.customer_id,'-',t2.title,'. ',t2.firstname,' ',t2.lastname) as 'customer',(select concat(t1.ship_trip_id,'-',t3.ship_trip_name) from ship_trip t3 inner join ship_trip_detail t4 on t3.ship_trip_id = t4.ship_trip_id where t4.ship_trip_detail_id = t1.ship_trip_id ) as 'ship_trip',t1.shipted_date as 'date', t1.note FROM shipment t1 inner join customer t2 on t1.customer_id = t2.customer_id  where t1.shipment_status_id = 'SPS15101' AND t1.is_active = 1";

if( !empty($requestData['search']['value']) ) {  
	$sql.="  AND ( t1.customer_id  LIKE '%".$requestData['search']['value']."%' ";
	$sql.="  OR  (concat(t2.title,'. ', t2.firstname,' ',t2.lastname))  LIKE '%".$requestData['search']['value']."%' ";
	$sql.="  OR  (select concat(t1.ship_trip_id,'-',t3.ship_trip_name) from ship_trip t3 inner join ship_trip_detail t4 on t3.ship_trip_id = t4.ship_trip_id where t4.ship_trip_detail_id = t1.ship_trip_id)  LIKE '%".$requestData['search']['value']."%' )";    
}
$query=mysqli_query($conn, $sql)or die("");;

$totalFiltered = mysqli_num_rows($query); 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

$query=mysqli_query($conn, $sql) or die("");;

$data = array();
while( $row=mysqli_fetch_array($query) ) {  
	$nestedData=array(); 

	$nestedData[] = $row["customer"];
	$nestedData[] = $row["ship_trip"];
	$nestedData[] = $row["date"];
	$nestedData[] = $row["note"];
	
	$data[] = $nestedData;
}

$json_data = array(
			"draw"            => intval( $requestData['draw'] ),
			"recordsTotal"    => intval( $totalData ),  
			"recordsFiltered" => intval( $totalFiltered ), 
			"data"            => $data   
			);

echo json_encode($json_data); 

}
?>