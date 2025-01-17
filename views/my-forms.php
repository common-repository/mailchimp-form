<?php
/**
 *
 * MAILCHIMP FORM BY CONTACTUS.COM
 *
 * Initialization Forms List View
 * @since 1.0 First time this was introduced into plugin.
 * @author ContactUs.com <support@contactus.com>
 * @copyright 2014 ContactUs.com Inc.
 * Company      : contactus.com
 * Updated  	: 20140127
 * */
?>

<?php

/*
 * DEEP LINKS
 */

$default_deep_link = $cUsMC_api->parse_deeplink ( $default_deep_link );

$createform = $default_deep_link.'?pageID=81&id=0&do=addnew&formType=';

?>

<div class="row">
    <div class="col-md-8"><h2>My Forms</h2></div>
    <div class="col-md-4">
        <div class="btn-group pull-right">
            <button id="cu_nav_create-form" type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                + Create a New Form <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li><a href="<?php echo cUsMC_PARTNER_URL; ?>/index.php?loginName=<?php echo $cUs_API_Account; ?>&userPsswd=<?php echo urlencode($cUs_API_Key); ?>&confirmed=1&redir_url=<?php echo urlencode(trim($createform)); ?>newsletter" target="_blank">Add a Newsletter Form</a></li>

            </ul>
        </div>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-md-12"><p>This section allows you to create and customize your form(s). By default, your form is setup as an in-browser popup, that is called on when users click on a callout tab that the plugin places onto your website. You can choose to place callout tabs across your entire site (recommended).</p></div>
</div>


<?php

    /*
     * Method in charge to render form types
     * @param string $form_type Form type to validate
     * @since 1.0
     * @return string Return Html into wp admin header
     */
        
        if ($cUsMC_API_getFormKeys) {

            $cUs_json = json_decode($cUsMC_API_getFormKeys);
            switch ($cUs_json->status) {
                case 'success':
                    $formOptions        = get_option('cUsMC_FORM_settings');//GET THE NEW FORM OPTIONS
                    $cus_version        = $formOptions['cus_version'];

                    ?>
                    <div class="user_form_templates">
                        <?php
                        $nCF = 1;
                        foreach ($cUs_json->data as $oForms => $oForm) {

                            if ( cUsMC_allowedFormType($oForm->form_type) ) {
                                $formID = $oForms;
                                $cUsMC_API_getDeliveryOptions = $cUsMC_api->getDeliveryOptions($cUs_API_Account, $cUs_API_Key, $oForm->form_key); //api hook;

                                
                                $default_form_key = get_option('cUsMC_settings_form_key');
                                $formSettings =  get_option('cUsMC_settings_form_'.$oForm->form_id);
                                
                                if( !empty($default_form_key) && !isset($formSettings['updated']) && $cus_version === 'tab' ){

                                    $formSettings['form_status'] =  ($default_form_key == $oForm->form_key) ? 1 : 0;
                                    $formSettings['form_key'] = $oForm->form_key;
                                    $formSettings['form_id'] = $oForm->form_id;
                                    $formSettings['form_type'] = $oForm->form_type;
                                    $formSettings['updated'] = 1;
                                    update_option('cUsMC_settings_form_'.$oForm->form_id, $formSettings);
                                    $formSettings =  get_option('cUsMC_settings_form_'.$oForm->form_id);

                                    update_option('cUsMC_settings_form_key_def', $default_form_key);

                                }

                                ?>

                                <!-- FORM ITEM -->
                                <form name="cus_form_<?php echo $oForm->form_id; ?>" id="cus_form_<?php echo $oForm->form_id; ?>">

                                    <div class="cu_form-item <?php echo( $formSettings['form_status']  ) ? 'active': ''; ?>">

                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="white-menu">
                                                    <?php
                                                    $default_deep_link = $oForm->deep_link_view;
                                                    $ablink = $cUsMC_api->parse_deeplink($default_deep_link);
                                                    $ablink = $ablink . '?pageID=90&do=view&formID=' . $oForm->form_id;
                                                    ?>
                                                    <ul class="nav nav-pills">
                                                        <li class="active"><a href="<?php echo cUsMC_CRED_URL; ?>&confirmed=1&redir_url=<?php echo urlencode(trim(cUsMC_PARTNER_URL . '/'.$partnerID . '/en/forms/gt/'.$oForm->form_id.'/formSettings/generalSettings')); ?>" target="_blank" title="Opens ContactUs admin panel in new window." class="tooltips">General Settings</a></li>
                                                        <li class="dropdown">
                                                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">Design <span class="caret"></span></a>
                                                            <ul class="dropdown-menu" role="menu" title="Opens ContactUs admin panel in new window." class="tooltips">
                                                                <li><a href="<?php echo cUsMC_CRED_URL; ?>&confirmed=1&redir_url=<?php echo urlencode(trim($default_deep_link)); ?>%26expand=4" target="_blank" title="Opens ContactUs admin panel in new window" class="tooltips" data-placement="right">Edit Form Design</a></li>
                                                                <li class="divider"></li>
                                                                <li><a href="<?php echo cUsMC_CRED_URL; ?>&confirmed=1&redir_url=<?php echo urlencode(cUsMC_PARTNER_URL . '/'.$partnerID . '/en/forms/gt/'.$oForm->form_id.'/formTriggers/selectFromLibrary/'); ?>" target="_blank" title="Opens ContactUs admin panel in new window" class="tooltips" data-placement="right">Edit Tab Design</a></li>
                                                            </ul>
                                                        </li>
                                                        <li class="active"><a href="<?php echo cUsMC_CRED_URL; ?>&confirmed=1&redir_url=<?php echo urlencode(cUsMC_PARTNER_URL . '/'.$partnerID . '/en/forms/gt/'.$oForm->form_id.'/formTriggers/'); ?>" target="_blank" title="Opens ContactUs admin panel in new window." class="tooltips">Form Triggers</a></li>
                                                        <li class="active"><a href="<?php echo cUsMC_CRED_URL; ?>&confirmed=1&redir_url=<?php echo urlencode(cUsMC_PARTNER_URL . '/'.$partnerID . '/en/forms/gt/'.$oForm->form_id.'/formSubmissionSettings/formNotifications/'); ?>" target="_blank" title="Opens ContactUs admin panel in new window." class="tooltips">Notifications</a></li>
                                                        <li class="active mc_navigate_sync"><a href="<?php echo cUsMC_CRED_URL; ?>&confirmed=1&redir_url=<?php echo urlencode(cUsMC_PARTNER_URL . '/'.$partnerID . '/en/forms/gt/'.$oForm->form_id.'/formSubmissionSettings/softwareIntegrations?d=mailchimp'); ?>" target="_blank" title="Sync your form submissions with yourß MailChimp Account" class="tooltips"> <i class="icon-export"></i> MailChimp Sync</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-3">
                                                <img class="img-responsive" src="<?php echo $oForm->template_desktop_form_thumbnail ?>" alt="">
                                                <img class="img-responsive" src="<?php echo $oForm->template_desktop_tab_thumbnail ?>" alt="">
                                            </div>

                                            <div class="col-sm-9">

                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="row">
                                                            <div class="col-md-8"><h4><strong><?php echo $oForm->form_name ?></strong></h4></div>
                                                        </div>

                                                        <div class="place-option">

                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <strong> Place Across My Site</strong>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <input type="checkbox" class="myforms-checkbox" name="checkbox_<?php echo $oForm->form_id; ?>" data-formid="<?php echo $oForm->form_id; ?>" <?php echo( $formSettings['form_status'] ) ? 'checked': ''; ?>>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <?php
                                                                    if ($cUsMC_API_getDeliveryOptions) {

                                                                        $cUs_jsonDelivery = json_decode($cUsMC_API_getDeliveryOptions);
                                                                        if(!empty($cUs_jsonDelivery->mailchimp)){
                                                                            $cUs_jsonDeliveryMC = $cUs_jsonDelivery->mailchimp; ?>
                                                                            <img src="<?php echo plugins_url('assets/img/mailchimp_enabled.png', dirname(__FILE__)); ?>" class="img-responsive pull-right tooltips" data-placement="left" title="MailChimp Sync Enabled with <?php echo $cUs_jsonDeliveryMC->mailchimp_unique_list_id; ?> List ID"  />
                                                                        <?php

                                                                        }else{ ?>
                                                                            <a href="<?php echo cUsMC_CRED_URL; ?>&confirmed=1&redir_url=<?php echo urlencode(cUsMC_PARTNER_URL . '/'.$partnerID . '/en/forms/gt/'.$oForm->form_id.'/formSubmissionSettings/softwareIntegrations?d=mailchimp'); ?>" target="_blank"><img src="<?php echo plugins_url('assets/img/mailchimp_disabled.png', dirname(__FILE__)); ?>" class="img-responsive pull-right tooltips" data-placement="left" title="MailChimp Sync is disabled, To Activate click here."  /></a>
                                                                        <?php
                                                                        }

                                                                    }

                                                                    ?>
                                                                </div>
                                                            </div>

                                                        </div>


                                                    </div>

                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="scode-box">
                                                            <strong>Form Shortcodes</strong>

                                                            <p>(Optional: You can use shortcodes instead of the Form Placement section to place your code)</p>

                                                            <p>
                                                            <h5> Tab Form </h5>
                                                            [show-mailchimp-form formkey=”<?php echo $oForm->form_key; ?>" version=”tab"]
                                                            <h5> Inline Form </h5>
                                                            [show-mailchimp-form formkey=”<?php echo $oForm->form_key; ?>" version="inline"]
                                                            </p>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="row">
                                                    <div class="col-md-5 col-md-offset-7">
                                                        <div class="btn btn-warning pull-right preview-form" data-formkey="<?php echo $oForm->form_key ?>">
                                                            <i class="icon-popup"></i> Preview this Form
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="save_message save_message_<?php echo $oForm->form_id; ?>">
                                            <p>Saving your settings...</p>
                                        </div>

                                    </div>
                                    <input type="hidden" name="form_key" value="<?php echo $oForm->form_key; ?>">
                                    <input type="hidden" name="form_type" value="<?php echo $oForm->form_type; ?>">
                                    <input type="hidden" name="form_id" value="<?php echo $oForm->form_id; ?>">
                                    <input type="hidden" name="action" value="cUsMC_setDefaulFormKeyByID">
                                    <input type="hidden" name="updated" value="<?php echo (isset( $formSettings['updated'] )) ? $formSettings['updated'] : 0 ; ?>">
                                </form>
                                <!-- FORM ITEM END -->

                                <?php
                                $nCF++;
                                //END IF ALLOWED TYPES

                            }


                        }

                        ?>

                    </div>
                    <?php
                    break;
            } //endswitch;

            if ($nCF <= 1) {
                ?>
                <a href="<?php echo cUsMC_PARTNER_URL; ?>/index.php?loginName=<?php echo $cUs_API_Account; ?>&userPsswd=<?php echo urlencode($cUs_API_Key); ?>&confirmed=1&redir_url=<?php echo urlencode(trim($createform)) . $form_Type; ?>" target="_blank" class="deep_link_action">Add New Form <span>+</span></a>
                <?php
            }
        }
    //}

//}
?>

<script>
    //PLUGIN cUsMC_myjq ENVIROMENT (cUsMC_myjq)
    var cUsMC_myjq = jQuery.noConflict();
    //ON READY DOM LOADED
    cUsMC_myjq(document).ready(function($) {

        $(".myforms-checkbox").bootstrapSwitch({
            onColor: 'success',
            offColor: 'danger',
            size: 'small',
            onSwitchChange: function(event, state) {
                var status = (state) ? 1 : 0 ;
                var formId = $(this).attr('data-formid');
                var oFormData = $('#cus_form_' + formId).serialize();
                oFormData += '&form_status=' + status;

                $('.save_message_'+formId).fadeIn();

                //AJAX POST CALL
                $.ajax({ type: "POST", dataType:'json', url: ajax_object.ajax_url, data: oFormData,
                    success: function(data) {

                        switch(data.status){

                            //USER CRATED SUCCESS
                            case 1:

                                if(status){
                                    var message = '<p> Your form is now global and placed across your entire site.<br/> <strong>Settings in Form/Page Selection Panel will remain the same.</strong></p>';
                                    $('#cus_form_'+formId+ ' > .cu_form-item').addClass('active');
                                }else{
                                    var message = '<p> Your form is no longer global. <br/> <strong>Settings in Form/Page Selection Panel will remain the same.</strong></p>';
                                    $('#cus_form_'+formId+ ' > .cu_form-item').removeClass('active');
                                }

                                $('.save_message_'+formId).html(message).show().delay(5000).fadeOut();

                                break;
                        }

                        $('.loadingMessage').fadeOut();


                    },
                    fail: function(){ //AJAX FAIL
                        message = '<p>Unfortunately there has being an error during the application. If the problem continues, contact us at support@contactus.com.</a></p>';
                    },
                    async: false
                });


            }
        });

        try {
            $(".preview-form").on('click', function(){

                var formKey = $(this).attr('data-formkey');
                //console.log(formKey);
                var form_Preview = '<h2>Form Preview</h2><br/><iframe id="formPreview" width="100%" style="min-height:540px;max-height:800px;" src="<?php echo cUsMC_ENV_URL;?>/'+ formKey+'/">';

                bootbox.alert(form_Preview);

            });

        } catch (err) {
            console.log(err);
        }

    });//ready
</script>