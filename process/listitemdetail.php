<?php

$action = $_POST['action'];
include('connection.php');

global $conn;
	if ($action=='getid') {
			
		global $conn;
		$q2 = $conn->query("select COUNT(*) from shipment_item_detail where MONTH(create_datetime)=".date('n')."&& YEAR(create_datetime)=".date('Y')."");

					$json_response = array();
					$result = $q2;
					$sumcol = $result->fetch_row();
					$row_array['numrow'] = $sumcol[0];
					array_push($json_response,$row_array);
					echo json_encode($json_response);
	}
	
	else if ($action=='item_id') {
		$name= $_POST['name'];
		global $conn;
		$sql = $conn->query("select concat(shipment_item_id,'-',name) as 'name' from shipment_item where is_active=1 && concat(shipment_item_id,'-',name) LIKE '%$name%'");

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
		$trip= $_POST['trip'];
		$itemid= $_POST['itemid'];
		$price= $_POST['price'];
		$note= $_POST['note'];
		global $conn;
		
		$sql = "INSERT INTO shipment_item_detail(shipment_item_detail_id, shipment_item_id, ship_trip_id, price, note, create_datetime, last_update, is_active) VALUES ('$id', '$itemid','$trip','$price','$note',now(),now(),1)";

					if ($conn->query($sql) === TRUE) {
						
						echo "Data berhasil dimasukkan";
						
					} else {
						
						echo "Error: " . $sql . "<br>" . $conn->error;
						
					}
					
					
	}
if ($action=='view') {
	
$requestData= $_REQUEST;

$columns = array( 
	0 =>'shipment_item_detail_id', 
	1 => 'shipment_item_id',
	2 => 'ship_trip_id',
	3 => 'price',
	4 => 'note',
);

$sql = "SELECT t1.shipment_item_detail_id, concat(t1.shipment_item_id,'-',t2.name) as 'shipment_item_id', concat(t1.ship_trip_id,'-',t3.ship_trip_name) as 'ship_trip_id', t1.price, t1.note FROM shipment_item_detail t1 inner join shipment_item t2 on t1.shipment_item_id = t2.shipment_item_id inner join ship_trip t3 on t1.ship_trip_id =t3.ship_trip_id WHERE t1.is_active = 1";
$query=mysqli_query($conn, $sql) or die("");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  

$sql ="SELECT t1.shipment_item_detail_id, concat(t1.shipment_item_id,'-',t2.name) as 'shipment_item_id', concat(t1.ship_trip_id,'-',t3.ship_trip_name) as 'ship_trip_id', t1.price, t1.note FROM shipment_item_detail t1 inner join shipment_item t2 on t1.shipment_item_id = t2.shipment_item_id inner join ship_trip t3 on t1.ship_trip_id =t3.ship_trip_id WHERE t1.is_active = 1";

if( !empty($requestData['search']['value']) ) {  
	$sql.="  AND ( t1.shipment_item_detail_id  LIKE '%".$requestData['search']['value']."%' ";
	$sql.="  OR  concat(t1.shipment_item_id,'-',t2.name)  LIKE '%".$requestData['search']['value']."%' )";  
}
$query=mysqli_query($conn, $sql)or die("");;

$totalFiltered = mysqli_num_rows($query); 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

$query=mysqli_query($conn, $sql) or die("");;

$data = array();
while( $row=mysqli_fetch_array($query) ) {  
	$nestedData=array(); 

	$nestedData[] = $row["shipment_item_detail_id"];
	$nestedData[] = $row["shipment_item_id"];
	$nestedData[] = $row["ship_trip_id"];
	$nestedData[] = number_format ($row["price"], 2, ',', ',');
	$nestedData[] = $row["note"];
	$nestedData[] = "<button class='btn btn-sm btn-default' data-toggle=\"modal\" onClick='detailbutton(\"".$row["shipment_item_detail_id"]."\")'>Edit</button> <button class='btn btn-sm btn-default' data-toggle=\"modal\" onClick='deletebutton(\"".$row["shipment_item_detail_id"]."\")'>Delete</button> ";
	
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
else if ($action=='getedit') {
		$id = $_POST['id'];
		global $conn;
		$sql = $conn->query("SELECT t1.shipment_item_detail_id, concat(t1.shipment_item_id,'-',t2.name) as 'shipment_item_id', concat(t1.ship_trip_id,'-',t3.ship_trip_name) as 'ship_trip_id', t1.price, t1.note FROM shipment_item_detail t1 inner join shipment_item t2 on t1.shipment_item_id = t2.shipment_item_id inner join ship_trip t3 on t1.ship_trip_id =t3.ship_trip_id WHERE t1.shipment_item_detail_id = '$id'");

					$json_response = array();
					while( $row=mysqli_fetch_array($sql, MYSQL_ASSOC) ) {  
						
						$row_array['shipment_item_detail_id'] = $row['shipment_item_detail_id'];
						$row_array['shipment_item_id'] = $row['shipment_item_id'];
						$row_array['ship_trip_id'] = $row['ship_trip_id'];
						$row_array['price'] = $row['price'];
						$row_array['note'] = $row['note'];
						array_push($json_response,$row_array);
					}
					echo json_encode($json_response);
	}
	
	else if ($action=='edit') {
		$id= $_POST['id'];
		$trip= $_POST['trip'];
		$itemid= $_POST['itemid'];
		$price= $_POST['price'];
		$note= $_POST['note'];
		global $conn;
		
		$sql = "UPDATE shipment_item_detail SET shipment_item_id='$itemid', ship_trip_id='$trip', price='$price', note='$note', last_update=now() where shipment_item_detail_id ='$id'";

					if ($conn->query($sql) === TRUE) {
						
						echo "Data berhasil di rubah";
						
					} else {
						
						echo "Error: " . $sql . "<br>" . $conn->error;
						
					}
					
					
	}
else if ($action=='delete') {
		$id= $_POST['id'];
	
		global $conn;
		$sql = "UPDATE shipment_item_detail SET is_active = 0 where shipment_item_detail_id ='$id'";

					if ($conn->query($sql) === TRUE) {
						
						echo "Data berhasil dihapus";
						
					} else {
						
						echo "Error: " . $sql . "<br>" . $conn->error;
						
					}
					
					
	}

?>