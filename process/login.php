<?php
session_start(); 
include('connection.php');
$action =$_POST['action'];

	if ($action=='login') {
		
		$username =$_POST['username'];
		$password =$_POST['password'];
		global $conn;
		
		$sql=  "select * from system_account where username='$username' AND password='$password'";
		
		$result =  mysqli_query($conn, $sql);
		$check_result = mysqli_num_rows($result);
		$fetchrow = $result->fetch_row();
		if($check_result>0){

			echo "Login success";
			$_SESSION['shippinguser'] = $fetchrow[1];
			$_SESSION['shippinguserid'] = $fetchrow[0];
			$_SESSION['account_status'] = $fetchrow[3];
			}

			else {

			echo "Login failed.. Please check again..";

		}
			
	}


?>