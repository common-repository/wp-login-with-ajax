<?php
	// JS INCLUDE
	wp_enqueue_script( 'wlwa-ALogin', plugins_url( 'js/ajaxLogin.js',  __FILE__ ) , array( 'jquery' ) );	 
	wp_localize_script( 'wlwa-ALogin', 'wlwa_ajax_login', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	add_action( 'wp_ajax_nopriv_wlwa-ALogin', 'wlwa_ajaxLogin' );
	add_action( 'wp_ajax_wlwa-ALogin', 'wlwa_ajaxLogin' );	
	function wlwa_ajaxLogin()
	{		
		$creds = array();
		
		if(!empty($_POST['redUrl']))
		{
			$RedirectUrl = esc_url($_POST['redUrl']);
		}
		else
		{
			$RedirectUrl = esc_url(site_url());
		}
		
		if(!empty($_POST['Cusername']))
		{
			// First check the nonce, if it fails the function will break
			check_ajax_referer( 'wlwa-ALogin-nonce', 'security' );
			
			$userName 	= sanitize_user($_POST['Cusername'],true);
			$Userchk  	= username_exists($userName);
			if($Userchk)
			{
				$creds['user_login'] 	= $userName;
				$creds['user_password'] = $_POST['Cpassword'];
				$creds['remember'] 		= (isset( $_POST['CRemember'] ) && true == $_POST['CRemember'] ? true : false);
				
				$user_signon = wp_signon( $creds, false );
			
				if ( is_wp_error($user_signon) ){
					echo json_encode(array('loggedin'=>false, 'message'=>__('Wrong username or password.'),'error'=>__('error_w')));
				} else {
					echo json_encode(array('loggedin'=>true, 'message'=>__('Login successful, redirecting...'),'error'=>__('valid'),'redirecturl'=>__($RedirectUrl)));
				}
			}
			else
			{
				echo json_encode(array('loggedin'=>false, 'message'=>__('Invalid Username.'),'error'=>__('invalid')));
			}
		}		
		die();		
	}
?>