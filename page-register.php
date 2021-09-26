<?php 
	session_start();
	global $custom_url;

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
	    header("Location: " . $custom_url . "/login");
	}
?>

<!DOCTYPE html>
<html lang="en">
	<?php // get_header(); ?>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=, initial-scale=1.0">
		<title>Sign Up</title>
		<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
	</head>
	<body>		
		<div class="user">
		    <form class="form" method="post" action="./"> 
		        <div class="form__group">
		            <input type="text" placeholder="&#xF1ad;  School Name" class="form__input" name="SchoolName" style="font-family:Arial, FontAwesome"/>
		            <span class="warning" style="display: none;">Please enter the School Name.</span>
		        </div>
		        
		        <div class="form__group">
		            <input type="text" placeholder="&#xF007;  Contact Name" class="form__input" name="ContactName" style="font-family:Arial, FontAwesome"/>
		            <span class="warning" style="display: none;">Please enter the Contact Name.</span>
		        </div>

		        <div class="form__group">
		            <input type="text" placeholder="&#xF095;  Contact Number" class="form__input" name="ContactNumber" style="font-family:Arial, FontAwesome"/>
		            <span class="warning" style="display: none;">Please enter the Contact Number.</span>
		        </div>

		        <div class="form__group">
		            <input type="email" placeholder="&#xF0e0;  Email" class="form__input" name="Email" style="font-family:Arial, FontAwesome"/>
		            <span class="warning" style="display: none;">Please enter your email.</span>
		        </div>
		        
		        <div class="form__group">
		            <input type="password" placeholder="&#xF023;  Password" class="form__input" name="Password" style="font-family:Arial, FontAwesome"/>
		            <span class="warning" style="display: none;">Please enter your password.</span>
		        </div>
		        
		        <button class="btn" type="submit" name="SignUpBtn" style="disabled: false;">Sign Up</button>

		        <p>
		        	Already a member? <a href="<?php echo $custom_url; ?>/login/">Login</a>
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
