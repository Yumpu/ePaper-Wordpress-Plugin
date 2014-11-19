<div class="wrap">
	<h2>Yumpu.com Settings</h2>
	
	<div id="msgBox_reg"></div>
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

	$('#api_check_button').click(function(e){
		check_token_key();
        e.preventDefault();
    });
});

</script>