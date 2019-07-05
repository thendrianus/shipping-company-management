<?php

$action = $_POST['action'];
include('connection.php');

global $conn;
if ($action=='viewpersonal') {
	
$requestData= $_REQUEST;

$columns = array( 
	0 =>'customer_id', 
	1 => 'title',
	2 => 'name',
	3=> 'contact_person',
	4=> 'email_address',
	5=> 'note',
	6=> 'customer_id'
);

$sql = "SELECT customer_id,  title, concat(firstname,' ', lastname) as 'name', contact_person, email_address, note FROM customer  WHERE is_active = 1";
$query=mysqli_query($conn, $sql) or die("");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  

$sql ="SELECT customer_id, title, concat(firstname,' ', lastname) as 'name', contact_person, email_address, note FROM customer  WHERE is_active = 1";

if( !empty($requestData['search']['value']) ) {  
	$sql.="  AND ( customer_id  LIKE '%".$requestData['search']['value']."%' ";
	$sql.="  OR  firstname  LIKE '%".$requestData['search']['value']."%' ";
	$sql.="  OR  lastname  LIKE '%".$requestData['search']['value']."%' )";    
}
$query=mysqli_query($conn, $sql)or die("");;

$totalFiltered = mysqli_num_rows($query); 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

$query=mysqli_query($conn, $sql) or die("");;

$data = array();
while( $row=mysqli_fetch_array($query) ) {  
	$nestedData=array(); 

	$nestedData[] = $row["customer_id"];
	$nestedData[] = $row["title"];
	$nestedData[] = $row["name"];
	$nestedData[] = $row["contact_person"];
	$nestedData[] = $row["email_address"];
	$nestedData[] = $row["note"];
	$nestedData[] = "<button class='btn btn-sm btn-default' data-toggle=\"modal\" onClick='detailbutton(\"".$row["customer_id"]."\")'>Edit</button><button class='btn btn-sm btn-default' data-toggle=\"modal\" onClick='deletebutton(\"".$row["customer_id"]."\")'>Delete</button> ";
	
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

else if ($action=='show_data_cus') {
	$id = $_POST['id'];
	global $conn;
	$q = $conn->query("SELECT t1.customer_id, t1.title, t1.firstname, t1.lastname, t1.id_card_number, t1.address, t1.contact_person, t1.email_address, t1.country, concat(t1.city_id,'-',t2.name) as 'city_id', t1.note FROM customer t1 inner join city t2 on t1.city_id = t2.city_id where t1.customer_id ='$id'");
	
$json_response = array();
					
					while($row = mysqli_fetch_array($q, MYSQL_ASSOC)) {
						
						$row_array['customer_id'] = $row['customer_id'];
						$row_array['title'] = $row['title'];
						$row_array['firstname'] = $row['firstname'];
						$row_array['lastname'] = $row['lastname'];
						$row_array['id_card_number'] = $row['id_card_number'];
						$row_array['address'] = $row['address'];
						$row_array['contact_person'] = $row['contact_person'];
						$row_array['email_address'] = $row['email_address'];
						$row_array['country'] = $row['country'];
						$row_array['city_id'] = $row['city_id'];
						$row_array['note'] = $row['note'];
						array_push($json_response,$row_array);
						
					}
					echo json_encode($json_response);
					
}


	else if ($action == "delete_data_cus"){
		$id = $_POST['id'];
	  $sql = "UPDATE customer SET last_update=now(),is_active=0 WHERE customer_id='$id'";

			if ($conn->query($sql) === TRUE) {
				
				
				echo "Data berhasil di hapus";
				
					
				}else {
					echo "error";
				}
	}
	else if ($action == "edit_cus"){
		$id= $_POST['id'];
		$title= $_POST['title'];
		$firstname= $_POST['firstname'];
		$lastname= $_POST['lastname'];
		$cardid= $_POST['cardid'];
		$address= $_POST['address'];
		$contact= $_POST['contact'];
		$email= $_POST['email'];
		$country= $_POST['country'];
		$city= $_POST['city'];
		$note= $_POST['note'];
		$sql = "UPDATE customer SET title='$title', firstname='$firstname', lastname='$lastname', id_card_number='$cardid', address='$address', contact_person='$contact', email_address='$email', country='$country', city_id='$city', note='$note', last_update= now() WHERE customer_id='$id'";

			if ($conn->query($sql) === TRUE) {
				
				
				echo "1";
				
					
				}else {
					echo "2";
				}
	}
	

?>