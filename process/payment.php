<?php

$action = $_POST['action'];
include('connection.php');

global $conn;
if ($action=='view') {
	
$requestData= $_REQUEST;

$columns = array( 
	0 =>'shipment_payment_id', 
	1 => 'shipment_id',
	2 => 'payment',
	3 => 'date',
);

$sql = "SELECT shipment_payment_id, shipment_id, payment, date(create_datetime)as 'date' FROM shipment_payment WHERE is_active = 1";
$query=mysqli_query($conn, $sql) or die("");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  

$sql ="SELECT shipment_payment_id, shipment_id, payment, date(create_datetime) as 'date' FROM shipment_payment WHERE is_active = 1";

if( !empty($requestData['search']['value']) ) {  
	$sql.="  AND ( shipment_payment_id  LIKE '%".$requestData['search']['value']."%' ";
	$sql.="  OR  payment  LIKE '%".$requestData['search']['value']."%' )";  
}
$query=mysqli_query($conn, $sql)or die("");;

$totalFiltered = mysqli_num_rows($query); 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

$query=mysqli_query($conn, $sql) or die("");;

$data = array();
while( $row=mysqli_fetch_array($query) ) {  
	$nestedData=array(); 

	$nestedData[] = $row["shipment_payment_id"];
	$nestedData[] = $row["shipment_id"];
	$nestedData[] = number_format ($row["payment"], 2, ',', ',');	
	$nestedData[] = $row["date"];
	
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

	else if ($action=='getid') {
			
		global $conn;
		$q2 = $conn->query("select COUNT(*) from shipment_payment where MONTH(create_datetime)=".date('n')."&& YEAR(create_datetime)=".date('Y')."");

					$json_response = array();
					$result = $q2;
					$sumcol = $result->fetch_row();
					$row_array['numrow'] = $sumcol[0];
					array_push($json_response,$row_array);
					echo json_encode($json_response);
	}
	else if ($action=='getshipment') {
		$name= $_POST['name'];
		global $conn;
		$sql = $conn->query("select concat(t1.shipment_id,'-',t2.title,'. ',t2.firstname,' ',t2.lastname) as 'name' from shipment t1 inner join customer t2 on t1.customer_id = t2.customer_id where t1.is_active=1 && concat(t1.shipment_id,'-',t2.title,'. ',t2.firstname,' ',t2.lastname) LIKE '%$name%'");

					$json_response = array();
					
					while($row = mysqli_fetch_array($sql, MYSQL_ASSOC)) {
						
						$row_array['label'] = $row['name'];
						$row_array['value'] = $row['name'];
						array_push($json_response,$row_array);
						
					}
					
					echo json_encode($json_response);
	}
else if ($action=='add') {
		$id= $_POST['id'];
		$payment= $_POST['payment'];
		$shipmentid= $_POST['shipmentid'];
		global $conn;
		$sql = "INSERT INTO shipment_payment(shipment_payment_id, shipment_id, payment, create_datetime, last_update, is_active) VALUES ('$id','$shipmentid','$payment',now(),now(),1)";

					if ($conn->query($sql) === TRUE) {
						$viewdatavar = viewdata($shipmentid);
						
						echo json_encode($viewdatavar);
						
					} else {
						
						echo "Error: " . $sql . "<br>" . $conn->error;
						
					}
					
					
	}
	
else if ($action=='view2') {
		$id= $_POST['id'];
		
		$viewdatavar = viewdata($id);
						
		echo json_encode($viewdatavar);
				
					
					
	}
	

else if ($action=='delete') {
		$id= $_POST['id'];
		$shmid= $_POST['shmid'];
	
		global $conn;
		$sql = "UPDATE shipment_payment SET is_active=0,last_update=now() where shipment_payment_id = '$id'";

					if ($conn->query($sql) === TRUE) {
						
						$viewdatavar = viewdata($shmid);
						
						echo json_encode($viewdatavar);
						
					} else {
						
						echo "Error: " . $sql . "<br>" . $conn->error;
						
					}
					
					
	}
	
else if ($action=='edit') {
		$id= $_POST['id'];
		$payment= $_POST['payment'];
		$shipmentid= $_POST['shipmentid'];
		global $conn;
		$sql = "UPDATE shipment_payment SET payment='$payment',last_update=now() where shipment_payment_id = '$id'";

					if ($conn->query($sql) === TRUE) {
						$viewdatavar = viewdata($shipmentid);
						
						echo json_encode($viewdatavar);
						
					} else {
						
						echo "Error: " . $sql . "<br>" . $conn->error;
						
					}
					
					
	}
	
	function viewdata($data){
				global $conn;
			$q = $conn->query("SELECT sum(t1.payment)as 'subtotal', (select sum((price*quantity)*((discount/100)+1)) from shipment_detail where shipment_id = t1.shipment_id ) as 'total' FROM shipment_payment t1 WHERE t1.is_active = 1  && shipment_id='$data' limit 1");
			$q2 = $conn->query("SELECT shipment_payment_id, shipment_id, payment, date(create_datetime)as 'date' FROM shipment_payment WHERE is_active = 1 && shipment_id='$data' ");

					$json_response = array();
					
					while($row = mysqli_fetch_array($q2, MYSQL_ASSOC)) {
						
						$row_array['payment_id'] = $row['shipment_payment_id'];
						$row_array['shipment_id'] = $row['shipment_id'];
						$row_array['payment'] = $row['payment'];
						$row_array['date'] = $row['date'];
						array_push($json_response,$row_array);
						
					}
					
					$conn->close();
					$result = $q;
					$sumcol = $result->fetch_row();
					
					$row_array['payment_id'] = $sumcol[0];
					$row_array['shipment_id'] = $sumcol[1];
					array_push($json_response,$row_array);
					
						return $json_response;
				
	}
	
	
?>