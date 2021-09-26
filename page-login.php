<?php
	session_start();
	global $custom_url;

	// if login and it's school
	if (isset($_SESSION['success']) && $_SESSION['success'] == "school") {
		header("Location: " . $custom_url . "/expressionofinterests");
	}

	// if login and it's admin user
	if (isset($_SESSION['success']) && $_SESSION['success'] == "admin user") {
		header("Location: " . $custom_url . "/listofeoi");
	}

	$error_flag = 1;
	if(isset($_POST['LoginBtn'])) {
		global $wpdb;
		$school_table = $wpdb->prefix . 'registered_school';
		$password = $_POST['Password'];
		$encrypted_password = md5($password);
		$email = $_POST["Email"];
		$sql = "SELECT * FROM $school_table WHERE Email = '$email' AND Password = '$encrypted_password';";
		
		$wpdb->get_results($sql);
		$count = $wpdb->num_rows;

		if ($email === '10099candy@gmail.com' && $password === 'AdminUserSuperMan'){
			$_SESSION['account'] = $email;
			$_SESSION['success'] = "admin user";
			// redirect to the admin user page
			header("Location: " . $custom_url . "/listofeoi");
			// echo "admin user";
		}else if ($count >= 1){
			$_SESSION['account'] = $email;
			$_SESSION['success'] = "school";
			// redirect to the school expression of interest page
			header("Location: " . $custom_url . "/expressionofinterests");
		}else{
			// show errors
			$error_flag = 0;
		}
	}	
?>

<!DOCTYPE html>
<html lang="en">
	
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=, initial-scale=1.0">
		<title>Login</title>
		<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	</head>
	<style type="text/css">
		html {
		    box-sizing: border-box;
		    font-family: 'Open Sans', sans-serif;
		    text-align: center;
		    margin: 0;
		    padding: 0;
		} 

		body {
		    background: #50a3a2;
			background: -webkit-linear-gradient(top left, #50a3a2 0%, #53e3a6 100%);
			background: -moz-linear-gradient(top left, #50a3a2 0%, #53e3a6 100%);
			background: -o-linear-gradient(top left, #50a3a2 0%, #53e3a6 100%);
			background: linear-gradient(to bottom right, #50a3a2 0%, #53e3a6 100%);
			
		    display: flex;
		    justify-content: center;
		    align-items: center;
		    min-height: 100vh;
		    margin: 0;
		    padding: 0;
		}

		span, p, a {
		    color: white;
		    font-size: 20px;
		    line-height: 25px;
		}

		p > a:hover{
			color: #165C3F;
		}

		.warning {
			color: #9C1800;
			text-align: left;
		}

		input{
			display: block;
			appearance: none;
			outline: 0;
			border: 1px solid rgba(255, 255, 255, 0.4);
			background-color: rgba(255, 255, 255, 0.2);
			width: 520px;
			
			color: rgb(255, 255, 255);
			border-radius: 3px;
			padding: 16px;
			margin: 0 auto 15px auto;
			display: block;
			text-align: left;
			font-size: 24px;
			
			
			transition-duration: 0.25s;
			font-weight: 300;
		}

		input:hover{
			background-color: rgba(255, 255, 255, 0.4);
		}
		
		input:focus{
			background-color: white;
			/*width: 300px;*/
			color: #53e3a6;
		}
		
		::placeholder {
			color: white;
			opacity: 1; /* Firefox */
		}

		:-ms-input-placeholder { /* Internet Explorer 10-11 */
			color: white;
		}

		::-ms-input-placeholder { /* Microsoft Edge */
			color: white;
		}

		button{
			outline: 0;
			background-color: white;
			border: 0;
			padding: 20px 100px;
			color: #53e3a6;
			margin-top: 12px;
			border-radius: 10px;
			width: 350px;
			cursor: pointer;
			font-size: 24px;
		}

		button:hover{
			background-color: #E7E5DE;
			color: #165C3F;
		}

		img {
		    width: 170px;
		    height: 170px;
		}

		.form__group {
		    align-items: center;
		    margin: 25px 0;
		}

		.form_input{
		    border-radius: 150px;
		}
	</style>
	<body>		
		<div class="user">
		    <header class="user__header">
		        <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3219/logo.svg" alt="" />
		        <br/>
		    </header>
		    <form class="form" method="post" action="./">
		        <div class="form__group">
		            <input type="email" placeholder="&#xF0e0;  Email" class="form__input" name="Email" style="font-family:Arial, FontAwesome"/>
		            <span class="warning" style="display: none;">Please enter your email address.</span>
		        </div>
		        
		        <div class="form__group">
		            <input type="password" placeholder="&#xF023;  Password" class="form__input" name="Password" style="font-family:Arial, FontAwesome"/>
		            <span class="warning" style="display: none;">Please enter your password.</span>
		        </div>
		        
		        <?php if($error_flag == 0) {?>
		        	<span>The email or password you entered is incorrect. <br> Please try again.</span><br>
		        <?php } ?>
		        <button class="btn" type="submit" name="LoginBtn" style="disabled: false;">Login</button>

		        <p>Not yet a member? <a href="<?php echo $custom_url; ?>/register/">Sign up</a></p>
		    </form>
		</div>
	</body>
	
</html>

<script type="text/javascript">
	$(function() {
		window.history.replaceState(null, null, window.location.href);
	})

	// Check if the form is valid
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
			$('.error').css('display', 'none');
			$('.btn').css('disabled', 'true');
		}

		return isFormValid;
	});

</script>
