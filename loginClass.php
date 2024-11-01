<?php
class wlwa_login_with_ajax
{
	public function __construct()
	{
		$this->wlwa_init();		
	}
	public function wlwa_init()
	{
		// HOOK CALL FOR MANAGE SECTION IN SETTING
		add_action( 'admin_menu', array($this, 'wlwa_admin_menu'));		
			
		// SHORTCODE CREATE FOR FORM
		add_shortcode('WP_AJAX_LOGIN', array($this, 'wlwa_Form'));
		
		// FRONTEND CSS
		add_action('wp_enqueue_scripts', array($this, 'wlwa_callback_setting_upfiles'));

		add_filter( 'plugin_action_links_'.plugin_basename( plugin_dir_path( __FILE__ ) . 'wp-login-with-ajax.php'), array( 'wlwa_login_with_ajax', 'nk_admin_plugin_settings_link' ) );
	}
	
	public static function nk_admin_plugin_settings_link( $links ) { 
		$settings_link = '<a href="'.admin_url('/options-general.php?page=wpajaxwithlogin_manage').'">'.__('Settings').'</a>';
		array_unshift( $links, $settings_link ); 
		return $links; 
  }

	public function wlwa_admin_menu() 
	{ 
		$page = add_options_page('Wp Ajax Login Manage','Wp Ajax Login Manage','manage_options','wpajaxwithlogin_manage',
		array($this,'wlwa_manage_settings'));
		
		add_action( 'load-' . $page,array($this,'wlwa_load_jsCssfiles' ));		
		
	}
	
	public function wlwa_load_jsCssfiles()
	{
		add_action( 'admin_enqueue_scripts', array($this,'wlwa_wenqueue_admin_jsCssjs' ));
	}

	
	public function wlwa_wenqueue_admin_jsCssjs()
	{
		wp_register_script('wlwa_bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js');
		wp_enqueue_script('wlwa_bootstrap');
		
		wp_register_style('wlwa_bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css');
		wp_enqueue_style('wlwa_bootstrap');
		
		wp_register_style( 'wlwa_AjaxCss', plugins_url( '/css/custom.css' , __FILE__ ));
		wp_enqueue_style( 'wlwa_AjaxCss' );	
	}
	
	
	function wlwa_callback_setting_upfiles() {
		wp_register_style( 'wlwa_AjaxCss', plugins_url( '/css/custom.css' , __FILE__ ));
		wp_enqueue_style( 'wlwa_AjaxCss' );		
	}

	public function nkGetAllPage(){
		global $wpdb;
		$arg = ['post_status'=>'publish'];
		return get_pages($arg);
	}
	
	// ADMIN MANAGE FUNCTION  
	public function wlwa_manage_settings()
	{
		// SAVE IN DATA AFTER 	
		if(isset($_POST['save_vl']))
		{		
			if(!empty($_POST['custom_redirecturllogin']) && $_POST['redirecturllogin'] == 'custom-page'){
				update_option("wlwa_Custom_Redirecturllogin",$_POST['custom_redirecturllogin']);
				update_option("wlwa_Redirecturllogin","");
			} else {
				update_option("wlwa_Redirecturllogin",$_POST['redirecturllogin']);
				update_option("wlwa_Custom_Redirecturllogin","");
			}

			if(!empty($_POST['custom_redirecturllogout']) && $_POST['redirecturllogout'] == 'custom-page'){
				update_option("wlwa_Custom_Redirecturllogout",$_POST['custom_redirecturllogout']);
				update_option("wlwa_Redirecturllogout","");
			} else {
				update_option("wlwa_Redirecturllogout",$_POST['redirecturllogout']);
				update_option("wlwa_Custom_Redirecturllogout","");
			}
			
			
		}
		if(isset($_POST['save_form_label']))
		{
			update_option("wlwaUsername",$_POST['usernameInput']);
			update_option("wlwaPassword",$_POST['passwordInput']);
		}

		$allPages = $this->nkGetAllPage();
		$wlwa_Redirecturllogin = get_option('wlwa_Redirecturllogin');
		$wlwa_Custom_Redirecturllogin = get_option('wlwa_Custom_Redirecturllogin');

		$wlwa_Redirecturllogout = get_option('wlwa_Redirecturllogout');
		$wlwa_Custom_Redirecturllogout = get_option('wlwa_Custom_Redirecturllogout');
		?>
			
			<div class="container formAdmin">
				<div class="row">
					<div class="col-sm-8 p-2 border">
						<h3> Manage Redirection</h3>
					</div>
					<div class="col-sm-8 p-2 border">
					<form name="webtechmanagesetting" method="post" action="">
					  <div class="form-group">
					
						<label for="after_login">Redirect url After Login</label> 
						<select name="redirecturllogin" id="redirecturllogin">
							
								<option value="">Select Page</option>
								<?php if(!empty($wlwa_Custom_Redirecturllogin)){
										$selectCustom = 'selected="selected"';
								} else {
									$selectCustom = "";
								}
								?>
								<option <?php echo $selectCustom ?> value="custom-page">Custom Link</option>
								<?php if(!empty($allPages)) { foreach($allPages as $page) {
									
								if($wlwa_Redirecturllogin == $page->post_name){
									$selected = 'selected="selected"';
								} else {
									$selected = "";
								}
								?>
								<option <?php echo $selected ?> value="<?php echo $page->post_name; ?>"><?php echo $page->post_title; ?></option>
							<?php
							}
							
							} ?>
							
						</select>

						<input  type="text" <?php if(!empty($wlwa_Custom_Redirecturllogin)) { echo  'style="display:block;"'; } else { echo 'style="display:none;"'; } ?> placeholder="/sample-page" id="custom_redirecturllogin" name="custom_redirecturllogin" value="<?php echo get_option('wlwa_Custom_Redirecturllogin'); ?>">
						
						<small id="afterHelp" class="form-text text-muted">[Example:http://url.com/login]</small>
					  </div>
					

					   
					  <div class="form-group">
						<label for="after_logout">Redirect url After Logout</label>

						<select name="redirecturllogout" id="redirecturllogout">
							
								<option value="">Select Page</option>
								<?php if(!empty($wlwa_Custom_Redirecturllogout)){
										$selectCustom = 'selected="selected"';
								} else {
									$selectCustom = "";
								}
								?>
								<option <?php echo $selectCustom ?> value="custom-page">Custom Link</option>
								<?php if(!empty($allPages)) { foreach($allPages as $page) {
									
								if($wlwa_Redirecturllogout == $page->post_name){
									$selected = 'selected="selected"';
								} else {
									$selected = "";
								}
								?>
								<option <?php echo $selected ?> value="<?php echo $page->post_name; ?>"><?php echo $page->post_title; ?></option>
							<?php
							}
							
							} ?>
							
						</select>

						<input  type="text" <?php if(!empty($wlwa_Custom_Redirecturllogout)) { echo  'style="display:block;"'; } else { echo 'style="display:none;"'; } ?> placeholder="/sample-page" id="custom_redirecturllogout" name="custom_redirecturllogout" value="<?php echo $wlwa_Custom_Redirecturllogout; ?>">

						
						<small id="afterHelp" class="form-text text-muted">[Example:http://url.com/logout]</small>
					  </div>
				
					  <button type="submit" name="save_vl" class="btn btn-primary">Save</button>
					
					</form>
					</div>
				</div>
			</div>
			<div class="container formAdmin">
				<div class="row">
					<div class="col-sm-8 p-2 border">
						<h3> Manage Form label</h3>
					</div>
					<div class="col-sm-8 p-2 border">
					<form name="formLabel" method="post" action="">
					  <div class="form-group">
						<label for="after_login">User Input</label>
						<input  type="text"  name="usernameInput" placeholder="Username" value="<?php echo get_option('wlwaUsername'); ?>">
						
					  </div>
					  <div class="form-group">
						<label for="after_logout">Password Input</label>
						<input type="text"  name="passwordInput" placeholder="Password" value="<?php echo get_option('wlwaPassword'); ?>">
					
					  </div>
				
					  <button type="submit" name="save_form_label" class="btn btn-primary">Save</button>
					
					</form>
					</div>
				</div>
			</div>
			<div class="container">
				<div class="row">
								
					<div class="card border-secondary mb-3" style="max-width: 200rem;">
					  <div class="card-header">Following are the methods to use this:</div>
					  <div class="card-body text-secondary">
						<h5 class="card-title">Method 1</h5>
						<p class="card-text">Use short code [WP_AJAX_LOGIN] to your Page/Post/Widget.</p>
					  </div>
					  <div class="card-body text-secondary">
						<h5 class="card-title">Method 2</h5>
						<p class="card-text">For Theme: Use &lt;?php echo do_shortcode('[WP_AJAX_LOGIN]'); ?&gt; in your template.</p>
					  </div>
					</div>
					
				</div>
			</div>
		
		<?php
	}
	
	
	
	public function wlwa_Form()
	{
		// GET URL IF EIXSTS IN DATABASE
		
		// AFTER LOGIN REDIRECT
		$wlwa_Custom_Redirecturllogin = get_option('wlwa_Custom_Redirecturllogin');
		if(!empty($wlwa_Custom_Redirecturllogin)){
			$CRediUrl = $wlwa_Custom_Redirecturllogin;
		} else {
			$CRediUrl = get_option('wlwa_Redirecturllogin');
		}
		
		
		// AFTER LOGOUT REDIRECT		
		$wlwa_Custom_Redirecturllogout = get_option('wlwa_Custom_Redirecturllogout');
		if(!empty($wlwa_Custom_Redirecturllogout)){

			$CRedilogoutUrl	= $wlwa_Custom_Redirecturllogout;
		} else {
			$CRedilogoutUrl	= get_option('wlwa_Redirecturllogout');	
		}


		$wlwaPassword	= get_option('wlwaPassword');	
		$wlwaUsername	= get_option('wlwaUsername');	
		$content	= '<div class="container">
		<div class="row">';
		// CHECK USER LOGIN OR NOT 	
		if(is_user_logged_in())
		{ 
			if(!empty($CRedilogoutUrl)){
				$logoutUrl = site_url($CRedilogoutUrl);
			} else {
				$logoutUrl = site_url(get_permalink());
			}
			$current_user = wp_get_current_user();
					
			$content .= 'Hello, '. $current_user->user_login. '<a href="'.wp_logout_url( $logoutUrl ).'"><br />Logout</a>';
		
		}
		else
		{
			
			$content .= '<div id="CLMsg"></div>
				<div class="form_login">
					<form id="Clogin" method="post">
						<input type="hidden" value="'.site_url($CRediUrl).'" id="CRediUrl" >			
						<p><label for="Cusername">
								'.(($wlwaUsername) ? $wlwaUsername : 'Username').'<br>
								<input type="text" size="20" name="Cusername" id="Cusername">
							</label>
						</p>
						<p>
							<label for="Cpassword">
							'.(($wlwaPassword) ? $wlwaPassword : 'Password').'<br>
								<input type="password" size="20" name="Cpassword" id="Cpassword" ><br>						
							</label>						
						</p>
						<label for="CRemember" >
							<input type="checkbox" name="CRemember" value="true" id="CRemember">
							Remember
						</label><br /><br />
						<input type="submit" class="button" value="Login" id="subimg" name="Clogin">
						<span id="loader"></span>
						'.wp_nonce_field( 'wlwa-ALogin-nonce', 'security' ).'
					</form>
				</div>';
			
		}
		$content .= '</div></div>';
		return $content;
	}
}
$AjaxLoginObj 	= new wlwa_login_with_ajax;
require("ajaxCall.php");
