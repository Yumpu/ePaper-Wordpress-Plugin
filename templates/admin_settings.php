<div class="wrap">
	<h2>Yumpu.com Settings</h2>
	
	<div id="msgBox_reg"></div>
	<div id="iconNewUser" class=""><br></div>
    <h3 style="margin: 0;line-height: 35px;"><a style="text-decoration: none;" id="createNewUser" href="#"><span>+</span> Create Free Account</a></h3>
	<form style="margin-left: 30px; display:none" id="create_account_form" style="">
        <table class="form-table">
            <tbody>
                <tr>
                    <th>E-Mail:</th>
                    <td>
                        <input type="text" class="regular-text" name="create_account_email" id="create_account_email" value=""/>
                        <small>valid email address</small>
                    </td>
                </tr>
                
                <tr>
                    <th>Username:</th>
                    <td>
                        <input type="text" class="regular-text" name="create_account_username" id="create_account_username" value=""/>
                        <small>Allowed characters a-z, A-Z, 0-9 and a dot, min. length 5 characters, max. length 30 characters</small>                        
                    </td>
                </tr>
                
                <tr>
                    <th>Password:</th>
                    <td>
                        <input type="password" class="regular-text" name="create_account_password" id="create_account_password" value=""/>
                        <small>min. length 6 characters</small>                 
                    </td>
                </tr>
                
                <tr>
                    <th>Firstname:</th>
                    <td>
                        <input type="text" class="regular-text" name="create_account_firstname" id="create_account_firstname" value=""/>
                        <small>min. length 2 characters, max. length 100 characters</small>                
                    </td>
                </tr>
                
                <tr>
                    <th>Lastname:</th>
                    <td>
                        <input type="text" class="regular-text" name="create_account_lastname" id="create_account_lastname" value=""/>
                        <small>min. length 2 characters, max. length 100 characters</small>                        
                    </td>
                </tr>
                
                <tr>
                    <th>Gender:</th>
                    <td>
                        <select class="regular-text" name="create_account_gender" id="create_account_gender">
                        	<option value="male">Male</option>
                        	<option value="female">Female</option>
                        </select>
                        <span class="ajaxLoader"></span>                        
                    </td>
                </tr>
                
                <tr>
                    <th colspan="2">
                        <?php submit_button('Create account', 'primary', 'submit', false, array('id' => "api_register_account"));?>
                    </th>
                </tr>
            </tbody>
        </table>                 
    </form>
	
	
	<div id="msgBox"></div>
	<form style="margin-left: 30px;" id="api_token_form">
        <table class="form-table">
            <tbody>
                <tr>
                    <th>API Token:</th>
                    <td>
                        <input type="text" class="regular-text" name="API_TOKEN" id="API_TOKEN" value="<?php echo $this->API_TOKEN; ?>"/>
                        <?php submit_button('Check','primary', 'submit',false, array('id'=>"api_check_button") );?>
                        <span class="ajaxLoader"></span>                        
                    </td>
                </tr>
                <tr>
                    <th colspan="2">
                        <span id="APImsg"></span>
                    </th>
                </tr>
            </tbody>
        </table>                 
    </form>
</div>

<style>
	span.ajaxLoader {
		background-image: url(./images/wpspin_light.gif);
        width: 16px;
        height: 16px;
        display: inline-block;
        background-repeat: no-repeat;
        margin-left: 5px;
        display: none;
	}
        
	#iconNewUser {
		float: left;
		width: 28px;
		height: 28px;
		background-position: -300px -29px;
		background-image: url(./images/menu.png?ver=20121105);
	}
        
	div.iconon {
		background-position: -300px 3px !important;
	}
        
	input.api-ok {
		background: url(./images/yes.png) top right no-repeat;
	}
	
	input.api-ic {
		background: url(./images/no.png) top right no-repeat;   
	}
	
	input.required {
		border-color: red;
	}
</style>


<script>
jQuery(document).ready(function($) {

	function check_token_key() {
		var API_TOKEN = $('#API_TOKEN').val();

		var element = $('#api_check_button');
		$(element).next('.ajaxLoader').css('display','inline-block');
		
		$.post(ajaxurl, 
				{
					'action': 'wp_yumpu',
					'run' : 'checkAPI_Token',
			        'API_TOKEN': API_TOKEN
			    }, 
			    function(response){
			    	$(element).next('.ajaxLoader').css('display','none');
				    
				    if(response.result) {
					    $('#API_TOKEN').removeClass('api-ic').addClass('api-ok');
					    $('#msgBox').html('<div id="message" class="updated"><p>Access Token updated successfully. </p></div>').hide().slideToggle();
					} else {
						$('#API_TOKEN').removeClass('api-ok').addClass('api-ic');
						$('#msgBox').html('<div id="message" class="error"><p>'+response.error.join('<br>')+' </p></div>').hide().slideToggle();
					}
			    },'json'
		);
	}

	
	function validateEmail(email) { 
	    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	    return re.test(email);
	}

	function register_account() {
		var acc_email = $('#create_account_email').val();
		var acc_username = $('#create_account_username').val();
		var acc_password = $('#create_account_password').val();
		var acc_firstname = $('#create_account_firstname').val();
		var acc_lastname = $('#create_account_lastname').val();
		var acc_gender = $('#create_account_gender').val();

		var errors = new Array();
		if(!validateEmail(acc_email)) {
			errors.push('create_account_email');
		}

		if(acc_username.length < 5 || acc_username.length > 30) {
			errors.push('create_account_username');
		}

		if(acc_password.length < 6) {
			errors.push('create_account_password');
		}

		if(acc_firstname.length < 2 || acc_firstname.length > 100) {
			errors.push('create_account_firstname');
		}

		if(acc_lastname.length < 2 || acc_lastname.length > 100) {
			errors.push('create_account_lastname');
		}
		


		$('#create_account_form').find('input').removeClass('required');

		if(errors.length > 0) {
			for(var i = 0; i < errors.length; i++) {
				$('#'+errors[i]).addClass('required');
			}
		} else {
			var postdata = "action=wp_yumpu";
			
			postdata += "&run=createAccount";
			postdata += "&API_TOKEN=";
			postdata += "&email="+encodeURIComponent(acc_email);
			postdata += "&username="+encodeURIComponent(acc_username);
			postdata += "&password="+encodeURIComponent(acc_password);
			postdata += "&firstname="+encodeURIComponent(acc_firstname);
			postdata += "&lastname="+encodeURIComponent(acc_lastname);
			postdata += "&gender="+encodeURIComponent(acc_gender);

			$.post(ajaxurl, postdata, function(response){
				if(response.result) {
					api_token = response.API_TOKEN;
					$('#API_TOKEN').val(api_token);
					$('#msgBox_reg').html('<div id="message" class="updated"><p>Erfolgreich --- TOKEN: ' + response.API_TOKEN + ' </p></div>').hide().slideToggle();
				} else {
					$('#msgBox_reg').html('<div id="message" class="error"><p>'+response.error.join('<br>')+' </p></div>').hide().slideToggle();
				}
			},'json');
		}
	}

	$('#api_check_button').click(function(e){
		check_token_key();
        e.preventDefault();
    });


	$('#api_register_account').click(function(e){
		register_account();
        e.preventDefault();
    });

    $('#createNewUser').click(function(e) {
    	$(this).find('span').html()=='+'?$(this).find('span').html('‒'):$(this).find('span').html('+');
    	$('#iconNewUser').toggleClass('iconon')
		$('#create_account_form').slideToggle();
    });
});

</script>