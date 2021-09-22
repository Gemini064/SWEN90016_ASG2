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

	global $wpdb;
	$school_table = $wpdb->prefix . 'registered_school';
	$eoi_table = $wpdb->prefix . 'eoi';

	// Get ID of this school
	$school_id;
	$email = $_SESSION['account'];
	$sql = "SELECT * FROM $school_table WHERE Email = '$email';";
	$rows = $wpdb->get_results($sql);
	foreach($rows as $row) {
		$school_id = $row->ID;
	}

	// Assign Status
	$status = 1;
	$_SESSION['status'] = 1;
	$sql = "SELECT * FROM $eoi_table WHERE School_ID = $school_id AND Active = 1;";
	$wpdb->get_results($sql);
	$count = $wpdb->num_rows;
	if ($count > 0){
		$status = 2;
	} else {
		$sql = "SELECT * FROM $eoi_table WHERE School_ID = $school_id AND Active = 0 AND Confirmation = 0;";
		$wpdb->get_results($sql);
		$count = $wpdb->num_rows;
		if ($count > 0){
			$status = 3;
		}
	}

	// List the confirmation eoi that can be cancelled
	$confirm_arr = array();
	$sql = "SELECT * FROM $eoi_table WHERE School_ID = $school_id AND Active = 0 AND Confirmation = 1;";
	$rows = $wpdb->get_results($sql);
	foreach($rows as $row) {
		array_push($confirm_arr, $row->ID);
	}

	if (isset($_POST['SubmitBtn'])) {
		$school_type = 'H';
		if ($_POST['school_type'] == "Visiting School"){
			$school_type = 'V';
		}

		$insertResult = $wpdb->insert(
	        $eoi_table,
	        array(
	        	'SchoolName'    => $_POST['SchoolName'],  
	            'Address'   	=> $_POST['Address'],     
	            'City' 			=> $_POST['City'],            
	            'State'   		=> $_POST['State'],
	            'PostalCode'	=> $_POST['PostalCode'],
	            'SchoolType'	=> $school_type,
	            'SecureParking'	=> $_POST['secure_present'],
	            'ParkingSpaces'	=> $_POST['ParkingSpaces'],
	            'OpenAreas'		=> $_POST['OpenAreas'],
	            'vSchoolName'	=> $_POST['VisitingSchoolName'],
	            'hSchoolName'	=> $_POST['NearestSchoolName'],
	            'Distance'		=> $_POST['Distance'],
	            'Message'		=> $_POST['Message'],
	            'Active'		=> 1,
	            'School_ID'		=> $school_id,
	            'Confirmation'	=> 0,
	        )
	    );

		// Send email to admin users
	    if ($insertResult){
	    	$_SESSION['eoi'] = 'yes';
		    $user_table = $wpdb->prefix . 'users';
		    $sql = "SELECT * FROM $user_table WHERE user_login = 'super' AND user_pass = 'AdminUserSuperMan';";
		    $results = $wpdb->get_results($sql);
		    $user_email = '';
		    foreach($results as $result){  
		     	$user_email = $result -> user_email; 
		     	break; 
		    }
		    
		    $msg = "School Name: " . $_POST['SchoolName'] .
		    	   "\nAddress: " . $_POST['Address'] . 
		    	   "\nSchool Type: " . $_POST['school_type'] .
		    	   "\nMessage: " . $_POST['Message'];
		    
		    $headers .= 'From: <10099candy@gmail.com>';
		    wp_mail($user_email, "Expression of Interests", $msg, $headers);
		    header('location: http://localhost:8888/expressionofinterests');
	    } else {
	    	echo "Oops! Something goes wrong, please try again.";
	    }
	    
	}

	// Logout
	if (isset($_GET['logout'])) {
		session_destroy();
		unset($_SESSION['account']);
		unset($_SESSION['success']);
		header('location: http://localhost:8888/expressionofinterests');
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Expression of Interests</title>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<style type="text/css">
		.warning{
			color: red;
		}
	</style>
</head>
<body>
	<?php if ($error_flag == 0) { ?>
	<p> 
		<a href="http://localhost:8888/expressionofinterests?logout='1'">logout</a>
	</p>
		<?php if ($status == 1) { //inner for loop?>
		<div class="expression_of_interests">
		    <form class="form" method="post" action="./">
		        <div class="form__group">
		        	<p>School Name</p>
		            <input type="text" class="form__input" name="SchoolName"/>
			        <span class="warning" style="display: none;">Please enter the School Name.</span>
		        </div>

				<div class="form__group">
					<p>Address</p>
		            <input type="text" class="form__input" name="Address"/>
			        <span class="warning" style="display: none;">Please enter the Address.</span>
		        </div>

		        <div class="form__group">
		        	<p>City</p>
		            <input type="text" class="form__input" name="City"/>
			        <span class="warning" style="display: none;">Please enter the City.</span>
		        </div>

		        <div class="form__group">
		        	<p>State</p>
		            <input type="text" class="form__input" name="State"/>
			        <span class="warning" style="display: none;">Please enter the State.</span>
		        </div>

		        <div class="form__group">
		        	<p>Postal Code</p>
		            <input type="text" class="form__input" name="PostalCode" pattern="[0-9]{4}" title="Four digit zip code"/>
			        <span class="warning" style="display: none;">Please enter the Postal Code.</span>
		        </div>       

		        <div class="radio__group">
		        	<p>Choose one of the following options</p>
		        	<input type="radio" id="hosting_school" name="school_type" value="Hosting School" onclick="DisplayHostOptions()" checked>
					<label for="hosting_school">Hosting School</label><br>
					<input type="radio" id="visiting_school" name="school_type" value="Visiting School" onclick="DisplayVisitOptions()">
					<label for="visiting_school">Visiting School</label><br>
		        </div>

		        <!-- Hosting Options -->
		        <div id="hostingSection">
			        <div class="hosting__group">
			        	<p>Is Secure Parking Present</p>
			        	<input type="radio" id="secure" name="secure_present" value="1" checked>
						<label for="secure">Yes</label><br>
						<input type="radio" id="notSecure" name="secure_present" value="0">
						<label for="notSecure">No</label><br>
			        </div>

			        <div class="hosting__group">
			        	<p>Total Car Parking Spaces</p>
			        	<input type="number" class="form__input" name="ParkingSpaces" min="0"/>
				        <span class="warning" style="display: none;">Please enter total car parking spaces.</span>
			        </div>

			        <div class="hosting__group">
			        	<p>Total Open Areas</p>
			        	<input type="number" class="form__input" name="OpenAreas" min="0"/>
				        <span class="warning" style="display: none;">Please enter total open areas.</span>
			        </div>
			    </div>

		        <!-- Visiting Options -->
		        <div id="visitingSection" style="display: none;">
			        <div class="visiting__group">
			        	<p>Visiting School Name</p>
			        	<input type="text" class="form__input" name="VisitingSchoolName"/>
				        <span class="warning" style="display: none;">Please enter the visiting school name.</span>
			        </div>

			        <div class="visiting__group">
			        	<p>Nearest Host School Name</p>
			        	<input type="text" class="form__input" name="NearestSchoolName"/>
				        <span class="warning" style="display: none;">Please enter the nearest school name.</span>
			        </div>

			        <div class="visiting__group">
			        	<p>Distance from Nearest Host School</p>
			        	<input type="number" class="form__input" name="Distance" min="0"/>
				        <span class="warning" style="display: none;">Please enter distance.</span>
			        </div>
			    </div>

		        <!-- Optional -->
		        <div class="optional__group">
		        	<p>Message</p>
			        <textarea name="Message"
			        		  rows="5"
			        		  cols="30">
			        </textarea>
		        </div>

		        <br>
		        <button class="btn" type="submit" name="SubmitBtn" style="disabled: false;">Submit</button>
		    </form>
		</div>
		<?php } else if ($status == 2 || $status == 3) { 
				$_SESSION['status'] = $status;
		?>
			<div>
				<button><a href="http://localhost:8888/confirmation">Confirmation</a></button>
			</div>
		<?php } ?>
		<div class="confirm_area">
			<?php
				$size = count($confirm_arr);
				for ($i = 0; $i < $size; $i++) {
			?>
					<div class="confirm_block" style="border: solid;">
						<a href="http://localhost:8888/cancel/?eid=<?php echo $confirm_arr[$i]; ?>">Cancel EOI #<?php echo $confirm_arr[$i]; ?></a>
					</div>
			<?php
				}
			?>
		</div>
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
	
	// check if the form is valid
	$('.form').submit(function(){
		var isFormValid = true;

		console.log('phase 0: ' + isFormValid);
		// check if there's any blank text box
		$('.form__group').each(function(){
			var $input = $(this).find('input');
			if ($.trim($input.val()).length === 0){
	        	$(this).find('span').css('display', 'block');
	        	isFormValid = false;
	        }
	        else{
	        	$(this).find('span').css('display', 'none');
	        }
		});
		console.log('phase 1: ' + isFormValid);

		// if choosing hosting school, check if there's any blank text box in this section
		if($('#hosting_school').is(':checked')) { 
			$('.hosting__group').each(function(){
				var $input = $(this).find('input');
				if ($.trim($input.val()).length === 0){
		        	$(this).find('span').css('display', 'block');
		        	isFormValid = false;
		        }
		        else{
		        	$(this).find('span').css('display', 'none');
		        }
			});
		}
		console.log('phase 2: ' + isFormValid);

		// if choosing visiting school, check if there's any blank text box in this section
		if($('#visiting_school').is(':checked')) { 
			$('.visiting__group').each(function(){
				var $input = $(this).find('input');
				if ($.trim($input.val()).length === 0){
		        	$(this).find('span').css('display', 'block');
		        	isFormValid = false;
		        }
		        else{
		        	$(this).find('span').css('display', 'none');
		        }
			});
		}
		console.log('phase 3: ' + isFormValid);

		if(isFormValid === true){
			$('.btn').css('disabled', 'true');
			$('.btn').css('color', 'grey');
		}

		return isFormValid;
	});

	// if hosting school is chosen
	function DisplayHostOptions(){
		$("#hostingSection").css('display', 'block');
		$("#visitingSection").css('display', 'none');
	}

	// if visiting school is chosen
	function DisplayVisitOptions(){
		$("#hostingSection").css('display', 'none');
		$("#visitingSection").css('display', 'block');
	}
</script>