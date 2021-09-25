<?php
	session_start();
	$error_flag = 0;

	if (!isset($_SESSION['account'])) {
		$error_flag = 1;
		$_SESSION['msg'] = "You must log in first";
  		// header('location: http://localhost:8888/login');
	}

	if (isset($_SESSION['success']) && $_SESSION['success'] != "school") {
		$error_flag = 2;
		$_SESSION['msg'] = "Oops! Wrong Authority!";
  		// header('location: http://localhost:8888/login');
	}

	$eid = $_REQUEST['eid']; // Index of the Expression of Interest Registers
	if ($eid == null) {
		$eid = $_SESSION['EID'];
	} else {
		$_SESSION['EID'] = $eid;
	}

	global $wpdb;
	$eoi_table = $wpdb->prefix . 'eoi';
	srand($eid);
	$acceptID = rand(100, 999);
	
	$school_name; $school_type; $start_date; $end_date; $total_cost; $total_student; $participation;
	$sql = "SELECT * FROM $eoi_table WHERE ID = $eid;";
	$rows = $wpdb->get_results($sql);
	foreach($rows as $row) {
		$school_name = $row->SchoolName;
		if ($row->SchoolType == 'H') {
			$school_type = "Hosting";
		} else {
			$school_type = "Visiting";
		}
	}

	$confirm_table = $wpdb->prefix . 'confirmation';
	$sql = "SELECT * FROM $confirm_table WHERE EID = $eid;";
	$rows = $wpdb->get_results($sql);
	foreach($rows as $row) {
		$start_date = date("m/d/Y", strtotime($row->StartDate));
		$end_date = date("m/d/Y", strtotime($row->EndDate));
		$total_cost = $row->TotalCost;
		$total_student = $row->TotalStudent;
		if ($row->Participation == 1) {
			$participation = "Yes";
		} else {
			$participation = "No";
		}
	}

	if (isset($_POST["SubmitBtn"])) {
		$user_table = $wpdb->prefix . 'users';
	    $sql = "SELECT * FROM $user_table WHERE user_login = 'super' AND user_pass = 'AdminUserSuperMan';";
	    $results = $wpdb->get_results($sql);
	    $user_email = '';
	    foreach($results as $result){  
	     	$user_email = $result -> user_email; 
	     	break; 
	    }
		$msg = "Expression of Interest Acceptance ID: " . $acceptID .
	    	   "\nSchool Name: " . $school_name . 
	    	   "\nSchool Type: " . $school_type .
	    	   "\nStart Date: " . $start_date .
	    	   "\nEnd Date: " . $end_date .
	    	   "\nParticipate in Specialized Activities?: " . $participation .
	    	   "\nTotal Students Participating: " . $total_student .
	    	   "\nCost Per Student: $ 30" .
	    	   "\nTotal Cost: $ " . $total_cost .
	    	   "\nReason for Cancellation: " . $_POST["Message"];

	    // Delete relevant eoi data from table wp_eoi
	    $wpdb->delete(
		    $eoi_table,
		    array('ID' => $eid)
		);

	    // Delete relevant data from table wp_schedule
	    $wpdb->delete(
		    "wp_schedule",
		    array('EID' => $eid)
		);

		// Delete relevant data from table wp_confirmation
	    $wpdb->delete(
		    $confirm_table,
		    array('EID' => $eid)
		);


	    $headers .= 'From: <10099candy@gmail.com>';
	    wp_mail($user_email, "Cancellation for the Technology Bus", $msg, $headers);
	    header('location: http://localhost:8888/expressionofinterests');
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Cancelling the scheduled visit</title>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<style type="text/css">
		@import url(https://fonts.googleapis.com/css?family=Roboto:400,500,300,700);
		body{
			background: #50a3a2;
		  	font-family: 'Roboto', sans-serif;
		}
		section{
			margin: 20px 80px;
		}
		table{
			width: 100%;
			table-layout: fixed;
		}
		.tbl-header{
			background-color: rgba(255, 255, 255, 0.3);
		}
		.tbl-content{
			/*height:300px;*/
			overflow-x:auto;
			margin-top: 0px;
			border: 1.5px solid rgba(255,255,255,0.2);
		}
		th{
			padding: 20px 15px;
			text-algin: left;
			font-weight: 500;
			font-size: 20px;
			color: #fff;
		}
		td{
			padding: 15px;
			text-align: left;
			vertical-align:middle;
			font-weight: 500;
			font-size: 18px;
			color: #fff;
			text-shadow: -1px -1px 0 rgba(0, 0, 0, 0.1);
			border-bottom: solid 1.5px rgba(255,255,255,0.2);
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
			background: rgba(255,255,255,0.3);
		}
		button:hover{
			background: rgba(34,87,86, 0.3);
			border: 1px solid rgba(0, 0, 0, 0.05);
			box-shadow: 1px 1px 2px rgba(255, 255, 255, 0.2);
			text-shadow: -1px -1px 0 rgba(0, 0, 0, 0.5);
			color: rgba(0, 0, 0, 0.05);
		}
		button > a{
			text-decoration: none;
			color: #fff;
			transition: all 200ms linear;
		}
		button:hover > a{
			color: #BFBFBF;
		}
		form{
			text-align: center;
		}
		textarea{
			border: 1px solid rgba(255, 255, 255, 0.4);
			background-color: rgba(255, 255, 255, 0.2);
			border-radius: 3px;
			font-size: 20px;
			width: 100%;
			height: 100px;
			margin: auto;
		}
	</style>
</head>
<body>
	<?php if ($error_flag == 0) { ?>
	<button><a href="http://localhost:8888/expressionofinterests/">BACK</a></button>
	<section>
	<form class="form" method="post" action="./">
		<div class="tbl-header">
			<table>
				<thead>
			        <tr>
			            <th colspan="2">CANCELLING A SCHEDULED VISIT FOR THE TECHNOLOGY BUS</th>
			        </tr>
			    </thead>
			</table>
		</div>

		<div class="tbl-content">
			<table>
			    <tbody>
			    	<tr>
			    		<td>Expression of Interest Acceptance ID</td>
			    		<td><?php echo $acceptID; ?></td>
			    	</tr>
			    	<tr>
			    		<td>School Name</td>
			    		<td><?php echo $school_name; ?></td>
			    	</tr>
			    	<tr>
			    		<td>School Type</td>
			    		<td><?php echo $school_type; ?></td>
			    	</tr>
			    	<tr>
			    		<td>Start Date</td>
			    		<td><?php echo $start_date ?></td>
			    	</tr>
			    	<tr>
			    		<td>End Date</td>
			    		<td><?php echo $end_date ?></td>
			    	</tr>
			    	<tr>
			    		<td>Participate in Specialized Activities?</td>
			    		<td><?php echo $participation; ?></td>
			    	</tr>
			    	<tr>
			    		<td>Total Students Participating</td>
			    		<td><?php echo $total_student; ?></td>
			    	</tr>
			    	<tr>
			    		<td>Cost Per Student</td>
			    		<td>$ 30</td>
			    	</tr>
			    	<tr>
			    		<td>Total Cost</td>
			    		<td>$ <?php echo $total_cost; ?></td>
			    	</tr>
			    	<tr>
			    		<td>Reason for Cancellation</td>
			    		<td>
			    			<textarea name="Message"></textarea>
				        </div>
			    		</td>
			    	</tr>
			    </tbody>
			</table>
		</div>

		<button class="btn" type="submit" name="SubmitBtn" style="disabled: false;">CONFIRM</button>
	</form>
	</section>
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
	$(function() {
		window.history.replaceState(null, null, window.location.href);
	})
</script>