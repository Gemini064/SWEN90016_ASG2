<?php
	session_start();
	$error_flag = 0;

	if (!isset($_SESSION['account'])) {
		$error_flag = 1;
		$_SESSION['msg'] = "You must log in first";
  		// header('location: http://localhost:8888/login');
	}

	if (isset($_SESSION['success']) && $_SESSION['success'] != "admin user") {
		$error_flag = 2;
		$_SESSION['msg'] = "Oops! Wrong Authority!";
  		// header('location: http://localhost:8888/login');
	}

	// Get data of active schools from table "wp_eoi"
	global $wpdb;
	$eoi_table = $wpdb->prefix . 'eoi';
	$sql = "SELECT * FROM $eoi_table WHERE Active = 1;";
	$rows = $wpdb->get_results($sql);
	
	$arr_name = array();
	$arr_type = array();
	$arr_eoi_id = array();
	foreach ($rows as $row){
		array_push($arr_name, $row->SchoolName);
		array_push($arr_eoi_id, $row->ID);

		if($row->SchoolType == 'H'){
			array_push($arr_type, "Hosting");
		} else {
			array_push($arr_type, "Visiting");
		}
	}

	// Get data of not active schools from table "wp_eoi"
	$sql = "SELECT * FROM $eoi_table WHERE Active = 0;";
	$rows = $wpdb->get_results($sql);
	
	$arr_name_2 = array();
	$arr_type_2 = array();
	$arr_eoi_id_2 = array();
	foreach ($rows as $row){
		array_push($arr_name_2, $row->SchoolName);
		array_push($arr_eoi_id_2, $row->ID);
		if($row->SchoolType == 'H'){
			array_push($arr_type_2, "Hosting");
		} else {
			array_push($arr_type_2, "Visiting");
		}
	}

	// Logout
	if (isset($_GET['logout'])) {
		session_destroy();
		unset($_SESSION['account']);
		unset($_SESSION['success']);
		header('location: http://localhost:8888/listofeoi');
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>EOI Lists</title>
	<style type="text/css">
		table, td {
		    border: 1px solid #333;
		}

		thead, tfoot {
		    background-color: #333;
		    color: #fff;
		}
	</style>
</head>
<body>
	<?php if ($error_flag == 0) { ?>
		<a href="http://localhost:8888/expressionofinterests?logout='1'">logout</a>
		<table>
	    <thead>
	        <tr>
	            <th colspan="2">Expressions of Interest List</th>
	        </tr>
	    </thead>
	    <tbody>
	        <tr>
	            <td>School Name</td>
	            <td>School Type</td>
	        </tr>
	        <?php 
            	$size = count($arr_name);
            	for ($i = 0; $i < $size; $i++){
            ?>
	            <tr>
	            	<td><a href="http://localhost:8888/schedule/?eid=<?php echo $arr_eoi_id[$i]; ?>"><?php echo $arr_name[$i]; ?></a></td>
	            	<td><?php echo $arr_type[$i]; ?></td>
	            </tr>
            <?php } ?>
	    </tbody>
		</table> <br>

		<table>
	    <thead>
	        <tr>
	            <th colspan="3">Rostered EOI</th>
	        </tr>
	    </thead>
	    <tbody>
	        <tr>
	        	<td>EOI ID</td>
	            <td>School Name</td>
	            <td>School Type</td>
	        </tr>
	        <?php 
            	$size = count($arr_name_2);
            	for ($i = 0; $i < $size; $i++){
            ?>
	            <tr>
	            	<td><?php echo $arr_eoi_id_2[$i]; ?></td>
	            	<td><?php echo $arr_name_2[$i]; ?></td>
	            	<td><?php echo $arr_type_2[$i]; ?></td>
	            </tr>
            <?php } ?>
	    </tbody>
		</table>
	<?php } else if ($error_flag == 1) { ?>
	<div class="error_login">
		<p>
			You must login first. <a href="http://localhost:8888/login/">Login</a>
		</p>	
	</div>
	
	<?php } else if ($error_flag == 2) { ?>
	<div class="error_login">
		<p>
			Oops! Wrong Authority. Please try another account!
		</p>
	</div>
	<?php } ?>
</body>
</html>

<script type="text/javascript">
	
</script>