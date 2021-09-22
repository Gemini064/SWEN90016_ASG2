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

	/*
		Status  |  Description
	    -----------------------------------------------------------------------------
		   1    |   not yet registered eoi
		-----------------------------------------------------------------------------
		   2    |   registered eoi, not yet scheduled 
		-----------------------------------------------------------------------------
		   3    |   registered eoi, has scheduled, not yet confirmed
		-----------------------------------------------------------------------------
		
		p.s. If there's any eoi that has been registered, scheduled and confirmed, shown
		on the interface for the school representative to cancel
	*/

	$status = 1;
	$result = array();
	global $wpdb;
	$eoi_table = $wpdb->prefix . 'eoi';
	$school_table = $wpdb->prefix . 'registered_school';
	$email = $_SESSION['account'];
	$sql = "SELECT * FROM $school_table WHERE Email = '$email'";
	$rows = $wpdb->get_results($sql);
	$sid;
	foreach($rows as $row) {
		$sid = $row->ID;
	}
	if (isset($_SESSION['status'])) {
		$status = $_SESSION['status'];
		if ($_SESSION['status'] == 2){
			$status = 2;
			$sql = "SELECT * FROM $eoi_table WHERE School_ID = $sid AND Active = 1;";
			$rows = $wpdb->get_results($sql);
			foreach ($rows as $row) {
				array_push($result, "School Name", $row->SchoolName);
				array_push($result, "Address", $row->Address);
				if ($row->SchoolType == 'H'){
					array_push($result, "School Type", "Hosting");
					if ($row->SecureParking == 1) {
						array_push($result, "Is Secure Parking Present", "Yes");
					} else {
						array_push($result, "Is Secure Parking Present", "No");
					}
					array_push($result, "Total Car Parking Spaces", $row->ParkingSpaces);
					array_push($result, "Total Open Areas", $row->OpenAreas);
				} else {
					array_push($result, "School Type", "Visiting");
					array_push($result, "Visiting School Name", $row->vSchoolName);
					array_push($result, "Nearest Host School Name", $row->hSchoolName);
					array_push($result, "Distance from Nearest Host School", $row->Distance);
				}
				array_push($result, "City", $row->City);
				array_push($result, "State", $row->State);
				array_push($result, "Postal Code", $row->PostalCode);
				array_push($result, "Message", $row->Message);
			}
		} else if ($status == 3) {
			// Active == 0: has been scheduled by Admin User
			// Confirmation == 0: hasn't been confirmed by the school representative
			$sql = "SELECT * FROM $eoi_table WHERE School_ID = $sid AND Active = 0 AND Confirmation = 0;";
			$rows = $wpdb->get_results($sql);
			$eid; $school_name; $school_type; $start_date; $end_date;
			foreach($rows as $row) {
				$eid = $row->ID;
				$school_name = $row->SchoolName;
				if ($row->SchoolType == 'H') {
					$school_type = "Hosting";
				} else {
					$school_type = "Visiting";
				}
			}

			$sql = "SELECT * FROM wp_schedule WHERE EID = $eid ORDER BY ID LIMIT 1;";
			$rows = $wpdb->get_results($sql);
			foreach($rows as $row) {
				$start_date = date("m/d/Y", strtotime($row->StartDate));
				$end_date = date("m/d/Y", strtotime($row->EndDate));
			}


			// Create seeded random numbers
			srand($eid);
			$randomID = rand(100, 999);

			if (isset($_POST['SubmitBtn'])) {
				$success = $wpdb->insert(
		        	"wp_confirmation",
			        array(
			        	'EID'			=> $eid,  
			            'StartDate'		=> date("Y-m-d", strtotime($_POST["StartDate"])),
			            'EndDate'		=> date("Y-m-d", strtotime($_POST["EndDate"])),
			            'TotalCost'		=> $_POST["TotalCost"],
			            'TotalStudent'	=> $_POST["TotalStudent"],
			        )
			    );

				if ($success) {
					$wpdb->update(
					    $eoi_table,
					    array('Confirmation' => 1),
					    array('ID' => $eid)
					);
					header('location: http://localhost:8888/expressionofinterests');
				} else {
					echo "Oops! Something went wrong, please try again!!!";
				}
			}
		}
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Confirmation</title>
	<link rel="stylesheet" href="//apps.bdimg.com/libs/jqueryui/1.10.4/css/jquery-ui.min.css">
	<script src="//apps.bdimg.com/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="//apps.bdimg.com/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
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
		<button><a href="http://localhost:8888/expressionofinterests/">Back</a></button>
		<?php if ($status == 3) { ?>
		<form class="form" method="post" action="./">
			<table>
				<thead>
					<tr>
			            <th colspan="2">Confirmation</th>
			        </tr>
				</thead>
				<tbody>
					<tr>
			            <td>Expression of Interest Acceptance ID</td>
			            <td><?php echo $randomID; ?></td>
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
			        	<td><input type="text" id="from" readonly="readonly" name="StartDate" value="<?php echo $start_date; ?>"/></td>
			        </tr>
			        <tr>
			        	<td>End Date</td>
			        	<td><input type="text" id="to" readonly="readonly" name="EndDate" value="<?php echo $end_date; ?>"/></td>
			        </tr>
			        <tr>
			        	<td>Participate in Specialized Activities?</td>
			        	<td>
			        		<input type="radio" id="yes" name="participate" value="Yes" checked onclick="Undisabled()">
							<label for="yes">Yes</label><br>
							<input type="radio" id="no" name="participate" value="No" onclick="Disabled()">
							<label for="no">No</label><br>
			        	</td>
			        </tr>
			        <tr>
			        	<td>Total Students Participating</td>
			        	<td><input id="totalStudent" type="number" name="TotalStudent" min="0" value="0" onchange="Recalculation()"/></td>
			        </tr>
			        <tr>
			        	<td>Cost Per Student</td>
			        	<td>$ 30</td>
			        </tr>
			        <tr>
			        	<td>Total Cost</td>
			        	<td>$<input id="totalCost" type="text" name="TotalCost" readonly="readonly" value="0"></td>
			        </tr>
				</tbody>
			</table>
			<div class="warning">
				<p id="tooShort" style="display: none;">Should be at least 1 week!</p>
				<p id="tooLong" style="display: none;">Should be no more than 3 weeks!</p>
			</div>
			<button class="btn" type="submit" name="SubmitBtn" style="disabled: false;">Confirm</button>
		</form>
		<?php } else if ($status == 2) { ?>
			<div>
				<p>Registered EOI will be scheduled soon~ The following is the information of your Expression of Interest!</p>
				<?php 
					$size = count($result);
					for ($i = 0; $i < $size; $i+=2){
				?>
					<p><?php echo $result[$i] . ": " . $result[$i+1]; ?></p>
				<?php } ?>
			</div>
		<?php } ?>
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

	function Disabled() {
		$('#totalStudent').val("");
		$('#totalCost').html("$ 0");
		$('#totalStudent').prop("disabled", true);
	}

	function Undisabled() {
		$('#totalStudent').prop("disabled", false);
	}

	function Recalculation() {
		var sum = $("#totalStudent").val() * 30;
		$('#totalCost').val(sum);
	}

	$(function() {
		$("#from").datepicker({
			changeMonth: true,
			minDate: '<?php echo $start_date; ?>',
			maxDate: '<?php echo $end_date; ?>',
			onClose: function(selectedDate) {
				$("#to").datepicker("option", "minDate", selectedDate);
			}
	    });
	    $("#to").datepicker({
	    	changeMonth: true,
	    	minDate: '<?php echo $start_date; ?>',
			maxDate: '<?php echo $end_date; ?>',
	    	onClose: function(selectedDate) {
	        	$("#from").datepicker("option", "maxDate", selectedDate);
	    	}
	    });
	});

	// Check if the form is valid
	$('.form').submit(function(){
		var isFormValid = true;
		var start = new Date($("#from").val());
		var end = new Date($("#to").val());
		var diff = end.getTime() - start.getTime(); // in milliseconds
		diff = diff/(1000*3600*24); // milliseconds to days

		if (diff < 7) {
			isFormValid = false;
			$("#tooShort").css("display", "block");
			$("#tooLong").css("display", "none");

		} else if (diff > 21) {
			isFormValid = false;
			$("#tooLong").css("display", "block");
			$("#tooShort").css("display", "none");
		}

		if (isFormValid) {
			$("#tooShort").css("display", "none");
			$("#tooLong").css("display", "none");
		}

		return isFormValid;
	});
</script>