<?php

$action = $_POST['action'];
include('connection.php');

global $conn;
if ($action=='view') {
	
$requestData= $_REQUEST;

$columns = array( 
	0 =>'shipment_id', 
	1 => 'customer',
	2 => 'ship_trip',
	3=> 'shipment_status',
	4=> 'payment_status',
	5=> 'shipment_id'
);

$sql = "SELECT t1.shipment_id, concat(t2.title,'. ', t2.firstname,' ',t2.lastname) as 'customer', concat(t1.ship_trip_id,'-',(select t4.ship_trip_name from ship_trip_detail t3 inner join ship_trip t4 on t3.ship_trip_id = t4.ship_trip_id where t3.ship_trip_detail_id = t1.ship_trip_id)) as 'ship_trip',t6.shipment_status,concat(t5.payment_status) as 'payment_status', t1.note FROM shipment t1 inner join customer t2 on t1.customer_id = t2.customer_id inner join shipment_status t6 on t6.shipment_status_id = t1.shipment_status_id inner join payment_status t5 on t1.payment_status_id = t5.payment_status_id WHERE t1.is_active = 1";
$query=mysqli_query($conn, $sql) or die("");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  

$sql ="SELECT t1.shipment_id, concat(t2.title,'. ', t2.firstname,' ',t2.lastname) as 'customer', concat(t1.ship_trip_id,'-',(select t4.ship_trip_name from ship_trip_detail t3 inner join ship_trip t4 on t3.ship_trip_id = t4.ship_trip_id where t3.ship_trip_detail_id = t1.ship_trip_id)) as 'ship_trip',t6.shipment_status,concat(t5.payment_status) as 'payment_status', t1.note FROM shipment t1 inner join customer t2 on t1.customer_id = t2.customer_id inner join shipment_status t6 on t6.shipment_status_id = t1.shipment_status_id inner join payment_status t5 on t1.payment_status_id = t5.payment_status_id WHERE t1.is_active = 1";

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
	$nestedData[] = $row["shipment_status"];
	$nestedData[] = $row["payment_status"];
	$nestedData[] = " <button class='btn btn-sm btn-default' data-toggle=\"modal\" onClick='detailbutton(\"".$row["shipment_id"]."\")'>Detail</button><button class='btn btn-sm btn-default' data-toggle=\"modal\" onClick='deletebutton(\"".$row["shipment_id"]."\")'>Delete</button><select onchange='changestatus1(this.value);' style=\"width:100px\" class=\"col-sm-5 form-control m-b \"  ><option value=\"".$row["shipment_id"]."-0\">Status</option><option value=\"".$row["shipment_id"]."-SPS15061\">Di Proses</option><option value=\"".$row["shipment_id"]."-SPS15062\">Terkirim</option> <option value=\"".$row["shipment_id"]."-SPS15063\">Bermasalah</option><option value=\"".$row["shipment_id"]."-SPS15101\">Cancel</option> </select> ";
	
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

else if ($action=='show_data_detail') {
	$id = $_POST['id'];
	global $conn;
	$q = $conn->query("SELECT t1.shipment_id, concat(t2.customer_id,'-',t2.firstname,' ',t2.lastname) as 'customer', (select concat( t5.ship_trip_id,'-',t5.ship_trip_name)from ship_trip t5 inner join ship_trip_detail t6 on t5.ship_trip_id = t6.ship_trip_id where t6.ship_trip_detail_id = t1.ship_trip_id) as 'ship_trip',concat(t1.ship_trip_id,'-',(select t4.ship_trip_name from ship_trip_detail t3 inner join ship_trip t4 on t3.ship_trip_id = t4.ship_trip_id where t3.ship_trip_detail_id = t1.ship_trip_id)) as 'ship_trip_detail',t1.receiver ,t1.receiver_contact,DATE_FORMAT(t1.shipted_date, '%Y-%m-%d') DATEONLY, 
       DATE_FORMAT(t1.shipted_date,'%H:%i') TIMEONLY,t1.payment,concat(t1.shipment_status_id,'-',(select shipment_status from shipment_status where shipment_status_id = t1.shipment_status_id)) as 'shipment_status',t1.note FROM shipment t1 inner join customer t2 on t1.customer_id = t2.customer_id inner join payment_status t5 on t1.payment_status_id = t5.payment_status_id WHERE t1.is_active = 1 && t1.shipment_id ='$id'");
	
$json_response = array();
					
					while($row = mysqli_fetch_array($q, MYSQL_ASSOC)) {
						
						$row_array['shipment_id'] = $row['shipment_id'];
						$row_array['customer'] = $row['customer'];
						$row_array['ship_trip'] = $row['ship_trip'];
						$row_array['ship_trip_detail'] = $row['ship_trip_detail'];
						$row_array['receiver'] = $row['receiver'];
						$row_array['receiver_contact'] = $row['receiver_contact'];
						$row_array['DATEONLY'] = $row['DATEONLY'];
						$row_array['TIMEONLY'] = $row['TIMEONLY'];
						$row_array['payment'] = $row['payment'];
						$row_array['shipment_status'] = $row['shipment_status'];
						$row_array['note'] = $row['note'];
						
						array_push($json_response,$row_array);
						
					}
					echo json_encode($json_response);
					
}

else if ($action=='show_data_edit') {
	$id = $_POST['id'];
	global $conn;
	$q = $conn->query(" select * from shipment_detail where shipment_detail_id ='$id'");
	
$json_response = array();
					
					while($row = mysqli_fetch_array($q, MYSQL_ASSOC)) {
						
						$row_array['shipment_item_detail_id'] = $row['shipment_item_detail_id'];
						$row_array['price'] = $row['price'];
						$row_array['quantity'] = $row['quantity'];
						$row_array['discount'] = $row['discount'];
						$row_array['note'] = $row['note'];
						
						array_push($json_response,$row_array);
						
					}
					echo json_encode($json_response);
					
}


else if ($action=='show_data_detailitem') {
	$id = $_POST['id'];
	global $conn;
	$q = $conn->query("SELECT t1.shipment_detail_id as 'id2' ,concat(t1.shipment_item_detail_id,'-',(select t2.name from shipment_item t2 inner join shipment_item_detail t3 on t2.shipment_item_id = t3.shipment_item_id where t3.shipment_item_detail_id = t1.shipment_item_detail_id)) as 'id', t1.price, t1.quantity,(t1.price*t1.quantity)-((t1.price*t1.quantity)*(t1.discount/100))as 'total', t1.discount, t1.note FROM shipment_detail t1  WHERE t1.is_active = 1 && t1.shipment_id ='$id'");
	
$json_response = array();
					
					while($row = mysqli_fetch_array($q, MYSQL_ASSOC)) {
						
						$row_array['id2'] = $row['id2'];
						$row_array['id'] = $row['id'];
						$row_array['price'] = $row['price'];
						$row_array['quantity'] = $row['quantity'];
						$row_array['discount'] = $row['discount'];
						$row_array['total'] = $row['total'];
						$row_array['note'] = $row['note'];
						
						array_push($json_response,$row_array);
						
					}
					echo json_encode($json_response);
					
}

else if ($action=='view2') {
	$id = $_POST['id'];
	global $conn;
	$q = $conn->query("select shipment_payment_id, payment, create_datetime from shipment_payment where is_active =1 AND shipment_id='$id'");
	
$json_response = array();
					
					while($row = mysqli_fetch_array($q, MYSQL_ASSOC)) {
						
						$row_array['payment_id'] = $row['shipment_payment_id'];  
						$row_array['payment'] = $row['payment'];
						$row_array['datetime'] = $row['create_datetime'];
						
						array_push($json_response,$row_array);
						
					}
					echo json_encode($json_response);
					
}


	else if ($action == "delete_data_ship"){
		$id = $_POST['id'];
	  $sql = "UPDATE shipment SET last_update=now(),is_active=0 WHERE shipment_id='$id'";

			if ($conn->query($sql) === TRUE) {
				
				
				echo "Data berhasil di hapus";
				
					
				}else {
					echo $date;
				}
	}
	
	else if ($action == "delete_data_item"){
		$id = $_POST['id'];
	  $sql = "UPDATE shipment_detail SET last_update=now(),is_active=0 WHERE shipment_detail_id='$id'";

			if ($conn->query($sql) === TRUE) {
				
				
				echo "Data berhasil di hapus";
				
					
				}else {
					echo "error";
				}
	}
	
	else if ($action == "status_data_ship"){
		$id = $_POST['id'];
		$no = $_POST['no'];
	  $sql = "UPDATE shipment SET shipment_status_id='$no' WHERE shipment_id='$id'";

			if ($conn->query($sql) === TRUE) {
				
				echo "Shipment status telah di update";
				
					
				}else {
					echo $date;
				}
	}
	else if ($action=='edit') {
		$id=$_POST['id'];
		$cusid=$_POST['cusid'];
		$tripid=$_POST['tripid'];
		$receiver=$_POST['receiver'];
		$rcontact=$_POST['rcontact'];
		$date=$_POST['date'];
		$note=$_POST['note'];
		$extra=$_POST['extra'];
		$discount=$_POST['discount'];

		global $conn;
		$sql = "UPDATE shipment SET customer_id='$cusid', ship_trip_id ='$tripid', receiver = '$receiver', receiver_contact='$rcontact', shipted_date = '$date', note='$note', extra_charge='$extra', discount='$discount',  last_update=now() where shipment_id='$id'";
		
					if ($conn->query($sql) === TRUE) {
					
						echo "Data Updated";
						
					} else {
						
						echo "Error: " . $sql . "<br>" . $conn->error;
						
					}
					
	}

		else if ($action=='edit_item_detail') {	
								$id=$_POST['id'];
								$item_id=$_POST['item_id'];
								$shipment_id=$_POST['shipment_id'];
								$price=$_POST['price'];
								$quantity=$_POST['quantity'];
								$discount=$_POST['discount'];
								$note=$_POST['note'];
						$itemid =split('-',$item_id);
						$sql = "UPDATE shipment_detail SET shipment_item_detail_id='$itemid[0]', shipment_id='$shipment_id', price='$price', quantity='$quantity', discount='$discount', note='$note', last_update=now() where shipment_detail_id='$id'";
		
							if ($conn->query($sql) === TRUE) 
							{
								echo"yes";
								
							} else
							{
								echo "Error: " . $sql2 . "<br>" . $conn->error;
								}
		}

	else if ($action=='add_item_detail') {	
								$item_id=$_POST['item_id'];
								$shipment_id=$_POST['shipment_id'];
								$price=$_POST['price'];
								$quantity=$_POST['quantity'];
								$discount=$_POST['discount'];
								$note=$_POST['note'];
						
						$q2 = $conn->query("select COUNT(*) from shipment_detail where MONTH(create_datetime)=".date('n')."&& YEAR(create_datetime)=".date('Y')."");

							$result = $q2;
							$sumcol = $result->fetch_row();
							$numrow2 = $sumcol[0]+1;
							$detail_id = 'SPD'.date('ym').$numrow2;
							$itemid =split('-',$item_id);
							$sql2 = "INSERT INTO shipment_detail(shipment_detail_id, shipment_item_detail_id, shipment_id, price, quantity, discount, note, create_datetime, last_update, is_active) VALUES ('".$detail_id."','".$itemid[0]."','".$shipment_id."','".$price."','".$quantity."','".$discount."','".$note."',now(),now(),1)";
							if ($conn->query($sql2) === TRUE) 
							{
								echo"yes-".$detail_id."";
								
							} else
							{
								echo "Error: " . $sql2 . "<br>" . $conn->error;
								}
		}
?>