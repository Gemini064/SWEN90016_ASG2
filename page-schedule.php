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

	// Get data from table "wp_eoi"
	$eid = $_REQUEST['eid']; // Index of the Expression of Interest Registers
	if ($eid == null) {
		$eid = $_SESSION['EID'];
	} else {
		$_SESSION['EID'] = $eid;
	}

	global $wpdb;
	$eoi_table = $wpdb->prefix . 'eoi';
	$sql = "SELECT * FROM $eoi_table WHERE ID = $eid;";
	$rows = $wpdb->get_results($sql);
	
	foreach($rows as $row){
		$_SESSION['SchoolName'] = $row->SchoolName;
		$_SESSION['SchoolID'] = $row->School_ID;
		$_SESSION['Address'] = $row->Address;
		if($row->SchoolType == 'H'){
			$_SESSION['SchoolType'] = "Hosting";
		} else {
			$_SESSION['SchoolType'] = "Visiting";
		}
	}


	// Create seeded random numbers
	srand($eid);
	$randomID = rand(100, 999);

	// Get period that have been scheduled for other schools
	$today_date = date("Y-m-d");
	$sql = "SELECT * FROM wp_schedule WHERE EndDate >= '$today_date';";
	$rows = $wpdb->get_results($sql);
	$not_avaliable_startdate = array();
	$not_avaliable_enddate = array();
	foreach($rows as $row) {
		array_push($not_avaliable_startdate, $row->StartDate);
		array_push($not_avaliable_enddate, $row->EndDate);

	}


	// Get email the eoi school
	$school_table = $wpdb->prefix . 'registered_school';
	$school_id = $_SESSION['SchoolID'];
	$sql = "SELECT * FROM $school_table WHERE ID = $school_id;";
	$rows = $wpdb->get_results($sql);
	$email;
	foreach($rows as $row) {
		$email = $row->Email;
	}

	// Send email to the school representitive
	if (isset($_POST['SubmitBtn'])) {
		$msg = "Expression of Interest Acceptance ID: " . $randomID .
		       "\nSchool Name: " . $_SESSION['SchoolName'] .
		       "\nAddress: " . $_SESSION['Address'] .
		       "\nSchool Type: " . $_SESSION['SchoolType'] .
		       "\nStart Date: " .  $_POST["StartDate"].
		       "\nEnd Date: " . $_POST["EndDate"];
		$headers .= 'From: <10099candy@gmail.com>';
		
		if (wp_mail($email, "Schedule for Technology Bus", $msg, $headers)){
			$wpdb->update(
			    $eoi_table,
			    array('Active' => 0),
			    array('ID' => $eid)
			);

			$wpdb->insert(
	        	"wp_schedule",
		        array(
		        	'StartDate'	=> date("Y-m-d", strtotime($_POST["StartDate"])),  
		            'EndDate'	=> date("Y-m-d", strtotime($_POST["EndDate"])),
		            'EID'		=> $eid,
		        )
		    );
			unset($_SESSION['SchoolName']);
			unset($_SESSION['SchoolType']);
			unset($_SESSION['SchoolID']);
			unset($_SESSION['Address']);
			unset($_SESSION['EID']);
			header('location: http://localhost:8888/listofeoi');
		} else {
			echo "Fail to send email, check if the email provided exists.";
		}

	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Schedule</title>
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
	<button><a href="http://localhost:8888/listofeoi/">Back</a></button>
	<table>
	    <thead>
	        <tr>
	            <th colspan="2">Expressions of Interest List</th>
	        </tr>
	    </thead>
	    <tbody>
	        <tr>
	            <td>Expression of Interest Acceptance ID</td>
	        	<td><?php echo $randomID; ?></td>
	        </tr>
	        <tr>
	            <td>School Name</td>
	        	<td><?php echo $_SESSION['SchoolName']; ?></td>
	        </tr>
	        <tr>
	            <td>School Type</td>
	        	<td><?php echo $_SESSION['SchoolType']; ?></td>
	        </tr>
	    </tbody>
		</table>

		<form class="form" method="post" action="./">
			<div class="datePicker">
				<div class="form__group">
					<span>Start Date</span>
					<input type="text" id="from" readonly="readonly" name="StartDate">
					<span class="warning" style="display: none;">Please select start date.</span>
				</div>

				<div class="form__group">
					<span>End Date</span>
					<input type="text" id="to" readonly="readonly" name="EndDate"></p>
					<span class="warning" style="display: none;">Please select end date.</span>
				</div>
			</div>
			<div class="warning">
				<p id="tooShort" style="display: none;">Should be at least 1 week!</p>
			</div>
			<button class="btn" type="submit" name="SubmitBtn" style="disabled: false;">Confirm</button>
		</form>
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


	// Disable Dates that have been allocated to other schools
	var startDate = [], endDate = [];
	<?php 
		$size = count($not_avaliable_startdate);
		for ($i = 0; $i < $size; $i++) {
	?>
			startDate.push('<?php echo $not_avaliable_startdate[$i]; ?>');
			endDate.push('<?php echo $not_avaliable_enddate[$i]; ?>');
	<?php } ?>

	
	var dateRange = [];
	for(var i = 0; i < <?php echo $size; ?>; i++) {
		for(var d = new Date(startDate[i]); d <= new Date(endDate[i]); d.setDate(d.getDate() + 1)) {
		    dateRange.push($.datepicker.formatDate('yy-mm-dd', d));
		}
	}
	function DisableDates(date) {
	    var dateString = jQuery.datepicker.formatDate('yy-mm-dd', date);
	    return [dateRange.indexOf(dateString) == -1];
	}


	$(function() {
		$( "#from" ).datepicker({
			changeMonth: true,
			minDate: 0,
			beforeShowDay: DisableDates, 
			onClose: function(selectedDate) {
				$("#to").datepicker("option", "minDate", selectedDate);
				var date1 = $('#from').datepicker('getDate'); 
				if (date1 == null){
					$('#from').datepicker('setDate', new Date());
					date1 = $('#from').datepicker('getDate'); 
				}
				var date2 = new Date(date1.setMonth(date1.getMonth()+2)); 
				$('#to').datepicker("option", "maxDate", date2);
			}
	    });
	    $( "#to" ).datepicker({
	      changeMonth: true,
	      beforeShowDay: DisableDates,
	      onClose: function(selectedDate) {
	        $("#from").datepicker("option", "maxDate", selectedDate);
	        var date1 = $('#to').datepicker('getDate'); 
			if (date1 == null){
				$('#to').datepicker('setDate', new Date());
			}
	      }
	    });
	});

	// Check if the form is valid
	$('.form').submit(function(){
		var isFormValid = true;

		$('.form__group').each(function(){
			var $input = $(this).find('input');
			if ($.trim($input.val()).length === 0){
	        	$(this).find('.warning').css('display', 'block');
	        	isFormValid = false;
	        }
	        else{
	        	$(this).find('.warning').css('display', 'none');
	        }
		});

		var start = new Date($("#from").val());
		var end = new Date($("#to").val());
		var diff = end.getTime() - start.getTime(); // in milliseconds
		diff = diff/(1000*3600*24); // milliseconds to days

		if (diff < 7) {
			isFormValid = false;
			$("#tooShort").css("display", "block");
		}

		if(isFormValid === true){
			$("#tooShort").css("display", "none");
			$('.btn').css('disabled', 'true');
		}

		return isFormValid;
	});
</script>