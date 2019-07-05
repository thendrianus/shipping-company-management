<?php

$action = $_POST['action'];
include('connection.php');

global $conn;
if ($action=='view') {
	
	$date1 = $_POST['date1'];
	$date2 = $_POST['date2'];
	$filter = $_POST['filter'];
	
$requestData= $_REQUEST;

$columns = array( 
	0 =>'shipment_id', 
	1 => 'customer',
	2 => 'ship_trip',
	3=> 'shipted_date',
	4=> 'total',
	5=> 'payment_status',
	6=> 'shipment_status',
	7=> 'note'
);



$sql = "SELECT t1.shipment_id, concat(t2.title,'. ', t2.firstname,' ',t2.lastname) as 'customer', concat(t1.ship_trip_id,'-',(select t4.ship_trip_name from ship_trip_detail t3 inner join ship_trip t4 on t3.ship_trip_id = t4.ship_trip_id where t3.ship_trip_detail_id = t1.ship_trip_id)) as 'ship_trip',date(t1.shipted_date) as'shipted_date' ,(SELECT sum(price * quantity) FROM shipment_detail WHERE is_active =1 && shipment_id =t1.shipment_id) as 'total',t5.payment_status as 'payment_status',t6.shipment_status as 'shipment_status', t1.note FROM shipment t1 inner join customer t2 on t1.customer_id = t2.customer_id inner join payment_status t5 on t1.payment_status_id = t5.payment_status_id inner join shipment_status t6 on t1.shipment_status_id = t6.shipment_status_id WHERE t1.is_active = 1 && t1.create_datetime between '$date1' and '$date2' ";
	if($filter == '0')
	{
		$sql .="";
	}
	else if($filter == '1')
	{
		$sql .="&& t1.shipment_status_id = 'SPS15061'";
	}
	else if($filter == '2')
	{
		$sql .="&& t1.shipment_status_id = 'SPS15062'";
	}
	else if($filter == '3')
	{
		$sql .="&& t1.shipment_status_id = 'SPS15063'";
	}
	else if($filter == '4')
	{
		$sql .="&& t1.shipment_status_id = 'SPS15101'";
	}
$query=mysqli_query($conn, $sql) or die("");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  


$sql = "SELECT t1.shipment_id, concat(t2.title,'. ', t2.firstname,' ',t2.lastname) as 'customer', concat(t1.ship_trip_id,'-',(select t4.ship_trip_name from ship_trip_detail t3 inner join ship_trip t4 on t3.ship_trip_id = t4.ship_trip_id where t3.ship_trip_detail_id = t1.ship_trip_id)) as 'ship_trip',date(t1.shipted_date) as'shipted_date' ,(SELECT sum(price * quantity) FROM shipment_detail WHERE is_active =1 && shipment_id =t1.shipment_id) as 'total',t5.payment_status as 'payment_status',t6.shipment_status as 'shipment_status', t1.note FROM shipment t1 inner join customer t2 on t1.customer_id = t2.customer_id inner join payment_status t5 on t1.payment_status_id = t5.payment_status_id inner join shipment_status t6 on t1.shipment_status_id = t6.shipment_status_id WHERE t1.is_active = 1 && t1.create_datetime between '$date1' and '$date2' ";
	if($filter == '0')
	{
		$sql .="";
	}
	else if($filter == '1')
	{
		$sql .="&& t1.shipment_status_id = 'SPS15061'";
	}
	else if($filter == '2')
	{
		$sql .="&& t1.shipment_status_id = 'SPS15062'";
	}
	else if($filter == '3')
	{
		$sql .="&& t1.shipment_status_id = 'SPS15063'";
	}
	else if($filter == '4')
	{
		$sql .="&& t1.shipment_status_id = 'SPS15101'";
	}

if( !empty($requestData['search']['value']) ) {  
	$sql.="  AND ( t1.shipment_id  LIKE '%".$requestData['search']['value']."%' ";
	$sql.="  OR  (concat(t2.title,'. ', t2.firstname,' ',t2.lastname))  LIKE '%".$requestData['search']['value']."%' ";
	$sql.="  OR  (concat(t1.ship_trip_id,'-',(select t4.ship_trip_name from ship_trip_detail t3 inner join ship_trip t4 on t3.ship_trip_id = t4.ship_trip_id where t3.ship_trip_detail_id = t1.ship_trip_id)))  LIKE '%".$requestData['search']['value']."%' )";    
}
$query=mysqli_query($conn, $sql)or die("");;

$totalFiltered = mysqli_num_rows($query); 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

$query=mysqli_query($conn, $sql) or die("");;

$data = array();
while( $row=mysqli_fetch_array($query) ) {  
	$nestedData=array(); 

	$nestedData[] = $row["shipment_id"];
	$nestedData[] = $row["customer"];
	$nestedData[] = $row["ship_trip"];
	$nestedData[] = $row["shipted_date"];
	$nestedData[] = number_format ($row["total"], 2, ',', ',');	
	$nestedData[] = $row["payment_status"];
	$nestedData[] = $row["shipment_status"];
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