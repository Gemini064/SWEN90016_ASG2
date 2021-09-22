<?php 
	session_start();
	if (isset($_POST['SignUpBtn'])) {
		global $wpdb;
		$school_table = $wpdb->prefix . 'registered_school';
		$wpdb->insert(
	        $school_table,
	        array(
	        	'SchoolName'    => $_POST['SchoolName'],  
	            'ContactName'   => $_POST['ContactName'],     
	            'ContactNumber' => $_POST['ContactNumber'],            
	            'Email'   		=> $_POST['Email'],
	            'Password'		=> md5($_POST['Password']),
	        )
	    );
	    header('location: http://localhost:8888/login');
	}

	// if (!isset($_SESSION['decide']) || $_POST['decide'] != $_SESSION['decide']) {
	    
	// }
	// else{
		
	// }
	
	// unset($_SESSION['decide']);
?>

<!DOCTYPE html>
<html lang="en">
	<?php // get_header(); ?>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=, initial-scale=1.0">
		<title>Sign Up</title>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	</head>
	<body>		
		<div class="user">
		    <form class="form" method="post" action="./"> 
		        <div class="form__group">
		            <input type="text" placeholder="School Name" class="form__input" name="SchoolName"/>
		            <span class="warning" style="display: none;">Please enter the School Name.</span>
		        </div>
		        
		        <div class="form__group">
		            <input type="text" placeholder="Contact Name" class="form__input" name="ContactName"/>
		            <span class="warning" style="display: none;">Please enter the Contact Name.</span>
		        </div>

		        <div class="form__group">
		            <input type="text" placeholder="Contact Number" class="form__input" name="ContactNumber"/>
		            <span class="warning" style="display: none;">Please enter the Contact Number.</span>
		        </div>

		        <div class="form__group">
		            <input type="email" placeholder="Email" class="form__input" name="Email"/>
		            <span class="warning" style="display: none;">Please enter the Email.</span>
		        </div>
		        
		        <div class="form__group">
		            <input type="password" placeholder="Password" class="form__input" name="Password"/>
		            <span class="warning" style="display: none;">Please enter the Password.</span>
		        </div>
		        
		        <button class="btn" type="submit" name="SignUpBtn" style="disabled: false;">Sign Up</button>

		        <p>
		        	Already a member? <a href="http://localhost:8888/login/">Login</a>
		        </p>
		    </form>
		</div>
	</body>
	<?php // get_footer() ?>
</html>

<script type="text/javascript">
	$(function() {
		window.history.replaceState(null, null, window.location.href);
	})

	// check if the form is valid
	$('.form').submit(function(){
		var isFormValid = true;


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

		if(isFormValid === true){
			$('.btn').css('disabled', 'true');
		}

		return isFormValid;
	});

</script>
