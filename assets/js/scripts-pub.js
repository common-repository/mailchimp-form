
//PLUGIN $ ENVIROMENT ($)
var cUsMC_myjq = jQuery.noConflict();

cUsMC_myjq(window).error(function(e){
    e.preventDefault();
});

//ON READY DOM LOADED
cUsMC_myjq(document).ready(function($) {
    
    try{
        
        //LOADING UI BOX
        $( ".cUsMC_preloadbox" ).delay(1000).fadeOut();

        $('.btn').tooltip();

        //UI TABS
        //$( "#cUsMC_tabs" ).tabs({active: false});
        
        //UNBIND UI TABS LINK ON CLICK
        $("li.gotohelp a").unbind('click');

        //colorbox window
        $(".tooltip_formsett").colorbox({iframe:true, innerWidth:'75%', innerHeight:'80%'});

        cUsMC_myjq('.sign-in').click(function() {
            cUsMC_myjq('.signup-form').slideToggle('slow');
            cUsMC_myjq('.login-form').slideToggle('slow');
        });

       
    }catch(err){
        $('.advice_notice').html('Error - please update your WordPress  to the latest version. If the problem continues, contact us at support@contactus.com.: ' + err ).slideToggle().delay(2000).fadeOut(2000);
    }
    
    //TOOLTIPS
    try{
        //JQ UI TOOLTIPS
        $(".setLabels").tooltip();
    }catch(err){
        $('.advice_notice').html('Error - please update your WordPress version to the latest version. If the problem continues, contact us at support@contactus.com. ' + err ).slideToggle().delay(2000).fadeOut(2000);
    }

    try{
        //LOGIN
        // validate signup form on keyup and submit
        var oLoginForm = $("#login-form");
        var loadingSta = $(".loading");
        var validateLogin = oLoginForm.validate({
            rules: {
                username: { required: true },
                password: { required: true }
            },
            messages: {
                username: { required: "User Email is a required and valid field"},
                password: { required: "User password is a required field" }
            },
            submitHandler: function(e) {

                //console.log(e);
                $('.cUsMC_LoginUser').html('Sign In...').attr('disabled', true);
                var oData = oLoginForm.serialize();
                loadingSta.show();

                //return;

                //AJAX POST CALL
                $.ajax({ type: "POST", dataType:'json', url: ajax_object.ajax_url, data: oData,
                    success: function(data) {

                        switch(data.status){

                            //USER CRATED SUCCESS
                            case 1:

                                $('.cUsMC_LoginUser').html('Success . . .');

                                message = '<p>Welcome back to ContactUs.com</p>';

                                setTimeout(function(){
                                    //oLoginForm.slideUp().fadeOut();
                                    location.reload();
                                },2500);

                                $('.notice_success').html(message).show().delay(3000).fadeOut();
                                $('.cUsMC_LoginUser').html('Login').attr('disabled', false);

                                break;

                            //CURRENT USER DON'T HAVE DEFAULT NEWSLETTER FORM
                            case 2:

                                $('.cUsMC_LoginUser').html('Error . . .');

                                message = '<p>To continue, you will need to create a default Newsletter Form.</p>';
                                message += '<p> This takes just a few minutes by logging in to your ContactUs.com admin panel with the credentials you used to setup the plugin. ';
                                message += '<a class="btnpb-green btnpb" href="https://admin.contactus.com/partners/index.php?loginName='+data.cUs_API_Account;
                                message += '&userPsswd='+data.cUs_API_Key+'&confirmed=1&redir_url='+data.deep_link_view+'?';
                                message += encodeURIComponent('pageID=81&id=0&do=addnew&formType=newsletter');
                                message += ' " target="_blank">Click Here to Create Missing Form</a></p>';
                                message += '<p>You will be redirected to our form settings page. When in the form settings page <strong>hit the orange “SAVE and PUBLISH” button on the left sidebar. </strong> You will be prompted for your password as well.</p><p>When you are done, come back and try login in again.</p>';

                                //$.messageDialogLogin('Default Newsletter Form Required');
                                bootbox.alert(message);

                                $('.cUsMC_LoginUser').html('Login').attr('disabled', false);

                                //$('#dialog-message').html(message);

                                break;

                            //API ERROR OR CONECTION ISSUES
                            case 3:
                                $('.cUsMC_LoginUser').html('Login').attr('disabled', false);
                                message = '<p>Unfortunately, we weren’t able to log you into your ContactUs.com account.</p>';
                                message += '<p>Please try again with the email address and password used when you created a ContactUs.com account.</p>';
                                message += '<p>Forgot your password? We will send you a new one right away.  <br/><br/> <a href="http://www.contactus.com/login/#forgottenbox" class="btn btn-danger" target="_blank">I forgot my password</a></p>';
                                message += '<hr/><p> If you still aren’t able to log in, please submit a ticket to our support team at <a href="http://help.contactus.com" target="_blank">http://help.contactus.com.</a></p>';
                                message += '<p>Error:  <b>' + data.message + '</b></p>';
                                //$('.advice_notice').html(message).show();
                                bootbox.alert(message);
                                break;

                            //API ERROR OR CONECTION ISSUES
                            case '':
                            default:
                                $('.cUsMC_LoginUser').html('Login').attr('disabled', false);
                                message = '<p>Unfortunately, we weren’t able to log you into your ContactUs.com account.</p>';
                                message += '<p>Please try again with the email address and password used when you created a ContactUs.com account. If you still aren’t able to log in, please submit a ticket to our support team at <a href="http://help.contactus.com" target="_blank">http://help.contactus.com.</a></p>';
                                message += '<p>Error:  <b>' + data.message + '</b></p>';
                                //$('.advice_notice').html(message).show();
                                bootbox.alert(message);
                                break;
                        }

                        $('.loading').fadeOut();


                    },
                    fail: function(){ //AJAX FAIL
                        message = '<p>Unfortunately there has being an error during the application. If the problem continues, contact us at support@contactus.com.</p>';
                        //$('.advice_notice').html(message).show();
                        bootbox.alert(message);
                        $('.cUsMC_LoginUser').html('Login').attr('disabled', false);
                    },
                    async: false
                });


            }
        });


    }catch(err){
        console.log(err);
    }

    try{

        var oSignUpData;
        var oSignupForm = $("#signUp-form");
        var validateSignUp = oSignupForm.validate({

            rules: {
                first_name: { required: true },
                last_name: { required: true },
                email: {
                    required: true,
                    email: true,
                    remote: {
                        url: ajax_object.ajax_url, dataType: "json", type: "post",
                        data: {
                             action: 'cUsMC_verifyCustomerEmail',
                             email: function() {
                                return jQuery("#email_s").val();
                             }
                        }
                    }
                },
                password: {
                    required: true, minlength: 8
                },
                password_match: {
                    required: true, equalTo: "#password1", minlength: 8
                },
                website: {
                    required: true,
                    webpg: true
                }
            },
            messages: {
                first_name: {
                    required: "Your First Name is a required field"
                },
                last_name: {
                    required: "Your Last Name is a required field"
                },
                email: {
                    required: "Please enter a valid Email Address",
                    email: "Please enter a valid Email Address",
                    remote: "The email is already in use. Please use login form to access your forms."
                },
                password: {
                    required: "You need to enter a Password",
                    minlength: $.format("Enter at least {0} characters")
                },
                password_match: {
                    required: "You need to enter a Password!",
                    minlength: $.format("Enter at least {0} characters"),
                    equalTo: "Please enter the same Password"
                },
                website: {
                    required: "Please enter a valid Website URL",
                    webpg: "Please enter a valid Website URL"
                }

            },
            submitHandler: function() {

                oSignUpData = oSignupForm.serialize();
                //console.log(oSignUpData);
                $.colorbox({inline:true,href:"#cats_selection",escKey:false,overlayClose:false,closeButton:false, maxWidth: '100%', minHeight: '430px', scrolling: false});
                //loadingSta.show();
            }
        });

        $.validator.addMethod("webpg", function(value, element) {
            return this.optional(element) || value.length > 5 && (/^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i).test(value);
        }, "Please enter a valid Website URL");


    }catch(err){
        $('.advice_notice').html('Unfortunately there has being an error during the application. ' + err).slideToggle().delay(9000).fadeOut(2000);
        console.log(err);
    }

    //SIGNUP
    try{

        cUsMC_myjq('.btn-skip').click(function(e) {

            e.preventDefault();
            var oThis = cUsMC_myjq(this);
            oThis.hide();
            cUsMC_myjq('#open-intestes').hide();

            var CU_category 	= cUsMC_myjq('#CU_category').val();
            var CU_subcategory 	= cUsMC_myjq('#CU_subcategory').val();

            var new_goals = '';
            var CU_goals = cUsMC_myjq('input[name="the_goals[]"]').each(function(){
                new_goals += cUsMC_myjq(this).val()+',';
            });

            if( cUsMC_myjq('#other_goal').val() )
                new_goals += cUsMC_myjq('#other_goal').val()+',';

            //cUsMC_myjq(".img_loader").show();
            cUsMC_myjq('.loading').show();

            //oSignUpDataPost = eval(oSignUpData);

            //console.log(oSignUpDataPost);
            oSignUpData += '&CU_category=' + CU_category;
            oSignUpData += '&CU_subcategory=' + CU_subcategory;
            oSignUpData += '&CU_goals=' + new_goals;
            oSignUpData += '&action=cUsMC_createCustomer';
            //console.log(oSignUpData);

            //AJAX POST CALL cUsMC_createCustomer
            cUsMC_myjq.ajax({ type: "POST", dataType:'json', url: ajax_object.ajax_url, data: oSignUpData,
                success: function(data) {

                    switch(data){

                        //USER CREATED
                        case 1:
                            message = '<p>Account created successfully . . . .</p>';
                            message += '<p>Welcome to ContactUs.com, and thank you for your registration.</p>';
                            cUsMC_myjq('.notice_success').html(message).show().delay(4900).fadeOut(800);
                            //cUsMC_myjq("#cUsFC_SendTemplates").colorbox.close();
                            setTimeout(function(){
                                cUsMC_myjq('.step3').slideUp().fadeOut();
                                cUsMC_myjq('.step4').slideDown().delay(800);
                                cUsMC_myjq('#cUsMC_SendTemplates').val('Create my account').attr('disabled', false);
                                location.reload();
                            },2000);
                            break;
                        //OLD USER - LOGING
                        case 2:
                            message = 'Seems like you already have one Contactus.com Account, Please Login below';
                            cUsMC_myjq('.advice_notice').html(message).show();
                            cUsMC_myjq('#cUsMC_SendTemplates').val('Create my account').attr('disabled', false);
                            cUsMC_myjq("#cUsMC_SendTemplates").colorbox.close();
                            cUsMC_myjq(".notice_error").hide();
                            setTimeout(function(){
                                cUsMC_myjq('#login_email').val(cUsMC_email).focus();
                                cUsMC_myjq('#cUsMC_userdata').fadeOut();
                                cUsMC_myjq('#cUsMC_settings').slideDown('slow');
                                cUsMC_myjq('#cUsMC_loginform').delay(1000).fadeIn();
                            },2000);
                            break;
                        //API OR CONNECTION ISSUES
                        case '':
                        default:
                            message = '<p>Unfortunately there has being an error during the application. If the problem continues, contact us at support@contactus.com. <br/>Error: <b>' + data + '</b>.</a></p>';
                            cUsMC_myjq('.notice_error').html(message).show();
                            cUsMC_myjq('#cUsMC_SendTemplates').val('Create my account').attr('disabled', false);
                            cUsMC_myjq("#cUsMC_SendTemplates").colorbox.close();
                            break;
                    }

                    cUsMC_myjq('.loadingMessage').fadeOut();

                },
                async: false
            });


        });

    }catch(err){
        cUsMC_myjq('.notice_error').html('Unfortunately there has being an error during the application. ' + err).slideToggle().delay(9000).fadeOut(2000);
        cUsMC_myjq('#cUsMC_SendTemplates').val('Create my account').attr('disabled', false);
    }



    
});//ON LOAD