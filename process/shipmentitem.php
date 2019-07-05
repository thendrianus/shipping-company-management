<?php

$action = $_POST['action'];
include('connection.php');

global $conn;
if ($action=='view') {
	
$requestData= $_REQUEST;

$columns = array( 
	0 =>'shipment_item_id', 
	1 => 'name',
	2 => 'UOM',
);

$sql = "SELECT t1.shipment_item_id, t1.name, concat(t1.UOM_id,'-',t2.UOM_name) as 'UOM', t1.create_datetime, t1.last_update, t1.is_active FROM shipment_item t1 inner join uom t2 on t1.UOM_id = t2.UOM_id WHERE t1.is_active = 1";
$query=mysqli_query($conn, $sql) or die("");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  

$sql ="SELECT t1.shipment_item_id as 'shipment_item_id' , t1.name as 'name', concat(t1.UOM_id,'-',t2.UOM_name) as 'UOM', t1.create_datetime, t1.last_update, t1.is_active FROM shipment_item t1 inner join uom t2 on t1.UOM_id = t2.UOM_id WHERE t1.is_active = 1";

if( !empty($requestData['search']['value']) ) {  
	$sql.="  AND ( t1.shipment_item_id  LIKE '%".$requestData['search']['value']."%' ";
	$sql.="  OR  t1.name  LIKE '%".$requestData['search']['value']."%' )";  
}
$query=mysqli_query($conn, $sql)or die("");;

$totalFiltered = mysqli_num_rows($query); 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

$query=mysqli_query($conn, $sql) or die("");;

$data = array();
while( $row=mysqli_fetch_array($query) ) {  
	$nestedData=array(); 

	$nestedData[] = $row["shipment_item_id"];
	$nestedData[] = $row["name"];
	$nestedData[] = $row["UOM"];
	$nestedData[] = "<button class='btn btn-sm btn-default' data-toggle=\"modal\" onClick='detailbutton(\"".$row["shipment_item_id"]."\")'>Edit</button> <button class='btn btn-sm btn-default' data-toggle=\"modal\" onClick='deletebutton(\"".$row["shipment_item_id"]."\")'>Delete</button> ";
	
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
		$q2 = $conn->query("select COUNT(*) from shipment_item where MONTH(create_datetime)=".date('n')."&& YEAR(create_datetime)=".date('Y')."");

					$json_response = array();
					$result = $q2;
					$sumcol = $result->fetch_row();
					$row_array['numrow'] = $sumcol[0];
					array_push($json_response,$row_array);
					echo json_encode($json_response);
	}
	else if ($action=='getuom') {
		$name= $_POST['name'];
		global $conn;
		$sql = $conn->query("select concat(UOM_id,'-',UOM_name) as 'name' from uom where is_active=1 && concat(UOM_id,'-',UOM_name) LIKE '%$name%'");

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
		$name= $_POST['name'];
		$uom= $_POST['uom'];
		global $conn;
		$sql = "INSERT INTO shipment_item(shipment_item_id, name, UOM_id, create_datetime, last_update, is_active) VALUES ('$id','$name','$uom',now(),now(),1)";

					if ($conn->query($sql) === TRUE) {
						
						echo "Data berhasil dimasukkan";
						
					} else {
						
						echo "Error: " . $sql . "<br>" . $conn->error;
						
					}
					
					
	}
	
else if ($action=='delete') {
		$id= $_POST['id'];
	
		global $conn;
		$sql = "UPDATE shipment_item SET is_active=0,last_update=now() where shipment_item_id = '$id'";

					if ($conn->query($sql) === TRUE) {
						
						echo "Data berhasil dihapus";
						
					} else {
						
						echo "Error: " . $sql . "<br>" . $conn->error;
						
					}
					
					
	}
	
else if ($action=='getedit') {
		$id = $_POST['id'];
		global $conn;
		$sql = $conn->query("SELECT t1.shipment_item_id as 'shipment_item_id' , t1.name as 'name', concat(t1.UOM_id,'-',t2.UOM_name) as 'UOM', t1.create_datetime, t1.last_update, t1.is_active FROM shipment_item t1 inner join uom t2 on t1.UOM_id = t2.UOM_id WHERE t1.is_active = 1 AND t1.shipment_item_id = '$id'");

					$json_response = array();
					while( $row=mysqli_fetch_array($sql, MYSQL_ASSOC) ) {  
						
						$row_array['shipment_item_id'] = $row['shipment_item_id'];
						$row_array['name'] = $row['name'];
						$row_array['UOM'] = $row['UOM'];
						array_push($json_response,$row_array);
					}
					echo json_encode($json_response);
	}
	
else if ($action=='getedit') {
		$id = $_POST['id'];
		global $conn;
		$sql = $conn->query("SELECT t1.shipment_item_id as 'shipment_item_id' , t1.name as 'name', concat(t1.UOM_id,'-',t2.UOM_name) as 'UOM', t1.create_datetime, t1.last_update, t1.is_active FROM shipment_item t1 inner join uom t2 on t1.UOM_id = t2.UOM_id WHERE t1.is_active = 1 AND t1.shipment_item_id = '$id'");

					$json_response = array();
					while( $row=mysqli_fetch_array($sql, MYSQL_ASSOC) ) {  
						
						$row_array['shipment_item_id'] = $row['shipment_item_id'];
						$row_array['name'] = $row['name'];
						$row_array['UOM'] = $row['UOM'];
						array_push($json_response,$row_array);
					}
					echo json_encode($json_response);
	}
	
else if ($action=='edit') {
		$id= $_POST['id'];
		$name= $_POST['name'];
		$uom= $_POST['uom'];
		global $conn;
		$sql = "UPDATE shipment_item SET name='$name',UOM_id='$uom',last_update=now() where shipment_item_id = '$id'";

					if ($conn->query($sql) === TRUE) {
						
						echo "Data berhasil dirubah";
						
					} else {
						
						echo "Error: " . $sql . "<br>" . $conn->error;
						
					}
					
					
	}
	
?>