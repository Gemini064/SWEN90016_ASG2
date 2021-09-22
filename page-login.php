<?php
	session_start();

	// if login and it's school
	if (isset($_SESSION['success']) && $_SESSION['success'] == "school") {
		header("Location: http://localhost:8888/expressionofinterests");
	}

	// if login and it's admin user
	if (isset($_SESSION['success']) && $_SESSION['success'] == "admin user") {
		header("Location: http://localhost:8888/listofeoi");
	}
	//

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
			header("Location: http://localhost:8888/listofeoi");
			// echo "admin user";
		}else if ($count >= 1){
			$_SESSION['account'] = $email;
			$_SESSION['success'] = "school";
			// redirect to the school expression of interest page
			header("Location: http://localhost:8888/expressionofinterests");
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
		    background-color: #2a9d8f;
		    display: flex;
		    justify-content: center;
		    align-items: center;
		    min-height: 100vh;
		    margin: 0;
		    padding: 0;
		}

		span, p, a {
		    color: white;
		    font-size: 24px;
		    margin: 15px 0;
		    padding: 12px 30px;
		    line-height: 25px;
		    
		}


		button {
		    border: 0px;
		    border-radius: 10px;
		    color: #005f73;
		    display: inline-block;
		    padding: 20px 100px;
		    font-size: 24px;
		    text-decoration: none;
		    margin: 15px 0;
		    margin-top: 20px;
		    transition: background-color 200ms ease-in-out;
		}

		img {
		    width: 200px;
		    height: 200px;
		}

		input{
		    outline-style: none;
		    border: 1px solid #ccc; 
		    border-radius: 3px;
		    padding: 14px 14px;
		    width: 520px;
		    font-size: 24px;
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
		            <input type="email" placeholder="Email" class="form__input" name="Email"/>
		            <span class="warning" style="display: none;">Please enter the Email.</span>
		        </div>
		        
		        <div class="form__group">
		            <input type="password" placeholder="Password" class="form__input" name="Password"/>
		            <span class="warning" style="display: none;">Please enter the Password.</span>
		        </div>
		        
		        <?php if($error_flag == 0) {?>
		        	<span>The email or password you entered is incorrect. <br> Please try again.</span><br>
		        <?php } ?>
		        <button class="btn" type="submit" name="LoginBtn" style="disabled: false;">Login</button>

		        <p>
		        	Not yet a member? <a href="http://localhost:8888/register/">Sign up</a>
		        </p>
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
