	jQuery(document).ready(function(){

		jQuery('#redirecturllogin').on('change',function(){

			let val = jQuery(this).val();
			if(val == 'custom-page'){
				jQuery('#custom_redirecturllogin').show();
			} else {
				jQuery('#custom_redirecturllogin').hide();
			}
		});
		jQuery('#redirecturllogout').on('change',function(){

			let val = jQuery(this).val();
			if(val == 'custom-page'){
				jQuery('#custom_redirecturllogout').show();
			} else {
				jQuery('#custom_redirecturllogout').hide();
			}
		});


		jQuery('#Clogin').submit(function(e){
			jQuery('#subimg').hide();
			jQuery('#loader').addClass('loader');
			jQuery('#CLMsg').html('');
			jQuery('#CLMsg').removeClass();
			jQuery('#CLMsg').hide();
			var redUrl	= jQuery('#CRediUrl').val();
			var user	= jQuery('#Cusername').val();
			var pass	= jQuery('#Cpassword').val();
			var security	= jQuery('#security').val();
			var rem		= jQuery('#CRemember').is(":checked");
			var err		= false;
			if(user == ''){
				err = true;
				jQuery('#Cusername').addClass('errorDisplay');
			}
				
			if(pass == ''){
				err = true;
				jQuery('#Cpassword').addClass('errorDisplay');
			}
				
			if(err)
			{
				jQuery('#loader').removeClass('loader');
				jQuery('#subimg').show();
							
				jQuery('#CLMsg').attr('class','CError');
				jQuery('#CLMsg').html("Invaild Username or Password");
				jQuery('#CLMsg').css('display','block');
				
				
			}
			else
			{
				jQuery.ajax({
					url: wlwa_ajax_login.ajaxurl,
					type : "POST",
					dataType: 'json',
					async: true, // make it true for chrome if you want to load image while ajax running
					data:{action : 'wlwa-ALogin',Cusername:user,Cpassword:pass,CRemember:rem,redUrl:redUrl,security:security},
					success: function(res)
					{   
						jQuery('#loader').removeClass('loader');
						jQuery('#subimg').show();	
						if(res.error == 'invalid')
						{
							jQuery('#Cusername').addClass('errorDisplay');	
							jQuery('#Cpassword').addClass('errorDisplay');
							jQuery('#CLMsg').attr('class','CError');
							jQuery('#CLMsg').html(res.message);	
							jQuery('#CLMsg').css('display','block');
						}
						else if(res.error == 'error_w')
						{
							jQuery('#CLMsg').attr('class','CError');
							jQuery('#CLMsg').html(res.message);
							jQuery('#CLMsg').css('display','block');
							
						}
						else if(res.error == 'valid')
						{
							jQuery('#Cusername').removeClass('errorDisplay');
							jQuery('#Cpassword').removeClass('errorDisplay');
							jQuery('#CLMsg').attr('class','CSuccess');
							jQuery('#CLMsg').css('display','block');
							jQuery('#CLMsgq').hide();
							jQuery('#CLMsg').html(res.message);
							document.location.href = res.redirecturl; 
						}
					}
				});
			}
			 e.preventDefault();
		});	
	});

