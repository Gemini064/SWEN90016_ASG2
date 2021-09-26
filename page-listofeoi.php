<?php
	session_start();
	$error_flag = 0;

	global $custom_url;

	if (!isset($_SESSION['account'])) {
		$error_flag = 1;
		$_SESSION['msg'] = "You must log in first";
	}

	if (isset($_SESSION['success']) && $_SESSION['success'] != "admin user") {
		$error_flag = 2;
		$_SESSION['msg'] = "Oops! Wrong Authority!";
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
		header("Location: " . $custom_url . "/listofeoi");
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>EOI Lists</title>
	<style type="text/css">
		body{
			background: #9CC5C6;
		  	font-family: 'Roboto', sans-serif;
		}
		section{
			margin: 50px 80px;
		}
		table{
			width: 100%;
			table-layout: fixed;
			background-color: #2C3845;
		}
		thead{
			background-color: #242e39;
		}
		.tbl-content{
			max-height:300px;
			overflow-x:auto;
			margin-bottom: 35px;
		}
		th{
			padding: 20px 15px;
			text-algin: left;
			font-weight: 500;
			font-size: 24px;
			color: #fff;
		}
		tbody>tr:nth-child(odd){
			background-color: #2C3845;
		}
		tbody>tr:nth-child(even){
			background-color: #242e39;
		}
		td{
			padding: 15px;
			text-align: left;
			vertical-align:middle;
			font-weight: 500;
			font-size: 20px;
			color: #fff;
			text-shadow: -1px -1px 0 rgba(0, 0, 0, 0.1);
			border-right: solid 2px #2C3845;
		}
		td>a{
			line-height: 1;
		    display: inline-block;
		    font-size: 1.2rem;
		    text-decoration: none;
		    border-radius: 5px;
		    color: #fff;
		    padding: 8px;
		    background-color: #4b908f;
		}
		td>a:hover {
			background-color: #9CC5C6;
			color: #2E3A3B;
		}
		button{
			color: #fff;
			cursor: pointer;
			font-size: 16px;
			font-weight: 600;
			margin: 12px;
			padding: 12px 20px;
			transition: all 200ms linear;
			border: 1px solid rgba(34, 87, 86, 0.1);
			box-shadow: 0px 2px 0 rgba(34,87,86, 0.1), 2px 4px 6px rgba(34,87,86, 0.1);
			background: #2C3845;
			opacity: 0.9;
		}
		button:hover{
			background: #242e39;
			opacity: 1;
			border: 1px solid rgba(0, 0, 0, 0.05);
			box-shadow: 1px 1px 2px rgba(255, 255, 255, 0.2);
			text-shadow: -1px -1px 0 rgba(0, 0, 0, 0.5);
		}
		button > a{
			text-decoration: none;
			color: #fff;
			transition: all 200ms linear;
		}
		button:hover > a{
			color: #BFBFBF;
		}
		h1{
			padding-bottom: 9px;
			margin: 0px;
		    color: #2C3845;
		    font-size: 20px;
		}
		::-webkit-scrollbar {
		    width: 6px;
		} 
		::-webkit-scrollbar-track {
		    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.5); 
		} 
		::-webkit-scrollbar-thumb {
		    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.5); 
		}
	</style>
</head>
<body>
	<?php if ($error_flag == 0) { ?>
		<button><a href="<?php echo $custom_url; ?>/expressionofinterests?logout='1'">LOGOUT</a></button>
		<section>
			<h1>EXPRESSION OF INTEREST LIST</h1>
			<div class="tbl-header">
				<table>
		    		<thead>
		        		<tr>
		            		<td>School Name</td>
				            <td>School Type</td>
		        		</tr>
		    		</thead>
				</table>
		    </div>

		    <div class="tbl-content">
		    	<table>
				    <tbody>
				        <?php 
			            	$size = count($arr_name);
			            	for ($i = 0; $i < $size; $i++){
			            ?>
				            <tr>
				            	<td><a href="<?php echo $custom_url; ?>/schedule/?eid=<?php echo $arr_eoi_id[$i]; ?>"><?php echo $arr_name[$i]; ?></a></td>
				            	<td><?php echo $arr_type[$i]; ?></td>
				            </tr>
			            <?php } ?>
				    </tbody>
				</table>
			</div>

			<h1>ROSTERED EOI</h1>
			<div class="tbl-header">
				<table>
				    <thead>
				        <tr>
				        	<td>EOI ID</td>
				            <td>School Name</td>
				            <td>School Type</td>
				        </tr>
				    </thead>
				</table>
			</div>
			<div class="tbl-content">
				<table>
				    <tbody>
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
			</div>
		</section>
	<?php } else if ($error_flag == 1) { ?>
	<div class="error_login">
		<p>
			You must login first. <a href="<?php echo $custom_url; ?>/login/">Login</a>
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