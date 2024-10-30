<?php

// loginAlreadyUser handler function...
/*
 * Method in charge to login user via ajax post request vars
 * @since 1.0
 * @return array jSon encoded array
 */
add_action('wp_ajax_cUsMC_loginAlreadyUser', 'cUsMC_loginAlreadyUser_callback');
function cUsMC_loginAlreadyUser_callback() {
    
    $cUsMC_api = new cUsComAPI_MC();
    $cUs_email = filter_input(INPUT_POST, 'username',FILTER_SANITIZE_STRING);
    $cUs_pass = filter_input(INPUT_POST, 'password',FILTER_SANITIZE_STRING);
    
    //API CALL TO getAPICredentials
    $cUsMC_API_credentials = $cUsMC_api->getAPICredentials($cUs_email, $cUs_pass); //api hook;

    //print_r($cUsMC_API_credentials);
    
    if($cUsMC_API_credentials){
        $cUs_json = json_decode($cUsMC_API_credentials);
        
        //SWITCH API STATUS RESPONSE
        switch ( $cUs_json->status  ) {
            case 'success':
                
                $cUs_API_Account    = $cUs_json->api_account;
                $cUs_API_Key        = $cUs_json->api_key;
                
                if(strlen(trim($cUs_API_Account)) && strlen(trim($cUs_API_Key))){
                    
                    $aryUserCredentials = array(
                        'API_Account' => $cUs_API_Account,
                        'API_Key'     => $cUs_API_Key
                    );
                    
                    
                    $cUsMC_API_getKeysResult = $cUsMC_api->getFormKeysData($cUs_API_Account, $cUs_API_Key); //api hook;

                    //print_r($cUsMC_API_getKeysResult);
                    
                    //$old_options = get_option('contactus_settings'); //GET THE OLD OPTIONS
                    
                    $cUs_jsonKeys = json_decode($cUsMC_API_getKeysResult);
                
                    if($cUs_jsonKeys->status == 'success' ){
                        
                        $postData = array( 'email' => $cUs_email);
                        update_option('cUsMC_settings_userData', $postData);
                        
                        $cUsMC_deeplinkview = $cUsMC_api->get_deeplink( $cUs_jsonKeys->data );
                        
                        // get a default deeplink
                        update_option('cUsMC_settings_default_deep_link_view', $cUsMC_deeplinkview ); // DEFAULT FORM KEYS

                        //print_r($cUs_jsonKeys->data);

                        foreach ($cUs_jsonKeys->data as $oForms => $oForm) {
                            if ($oForm->default == 1 && cUsMC_allowedFormType($oForm->form_type)){ //GET DEFAULT FORM KEY
                               $defaultFormKey = $oForm->form_key;
                               $form_type = $oForm->form_type;
                               $deeplinkview   = $oForm->deep_link_view;
                               $defaultFormId  = $oForm->form_id;
                               break;
                            }
                        } 
                            
                        if(!strlen($defaultFormKey)){
                                //echo 2; //NO ONE NEWSLETTER FORM
                                
                                $aryResponse = array(
                                    'status' => 2,
                                    'cUs_API_Account' 	=> $cUs_API_Account,
                                    'cUs_API_Key' 	=> $cUs_API_Key,
                                    'deep_link_view'	=> $cUsMC_deeplinkview
                                );
                                
                               
                        }else{
                            
                            $aryFormOptions = array('tab_user' => 1,'cus_version' => 'tab'); //DEFAULT SETTINGS / FIRST TIME
                            
                            update_option('cUsMC_FORM_settings', $aryFormOptions );//UPDATE FORM SETTINGS
                            update_option('cUsMC_settings_form_key', $defaultFormKey);//DEFAULT FORM KEYS
                            update_option('cUsMC_settings_form_keys', $cUs_jsonKeys); // ALL FORM KEYS
                            update_option('cUsMC_settings_form_id', $defaultFormId); // DEFAULT FORM KEYS
                            update_option('cUsMC_settings_default_deep_link_view', $deeplinkview); // DEFAULT FORM KEYS
                            update_option('cUsMC_settings_userCredentials', $aryUserCredentials);
                            delete_option('cUsMC_settings_userData');

                            $formSettings['form_status'] = 1;
                            $formSettings['form_key'] = $defaultFormKey;
                            $formSettings['form_id'] = $defaultFormId;
                            $formSettings['form_type'] = $form_type;
                            $formSettings['updated'] = 1;
                            update_option('cUsMC_settings_form_'.$defaultFormId, $formSettings);
                            
                            $aryResponse = array('status' => 1);
                            
                        }

                            //echo 1;
                        
                    }else{
                        //{"status":"error","error":"No valid form keys"}
                        $aryResponse = array('status' => 3, 'message' => $cUs_jsonKeys->error);
                    } 
                    
                }else{
                    $aryResponse = array('status' => 3, 'message' => $cUs_json->error);
                }
                
                break;

            case 'error':
                $aryResponse = array('status' => 3, 'message' => $cUs_json->error);
                break;
        }
    }
    
    echo json_encode($aryResponse);
    
    die();
}


// cUsMC_verifyCustomerEmail handler function...
/*
 * Method in charge to verify if the email exist via ajax post request vars
 * @since 2.0
 * @return string Value status to switch
 */
add_action('wp_ajax_cUsMC_verifyCustomerEmail', 'cUsMC_verifyCustomerEmail_callback');
function cUsMC_verifyCustomerEmail_callback() {
    
    if ( !strlen(filter_input(INPUT_POST, 'email',FILTER_VALIDATE_EMAIL)) ){      echo 'Missing/Invalid Email, is required field';   die();
    }else{
        
        $cUsMC_api = new cUsComAPI_MC(); //CONTACTUS.COM API

        $cUsMC_API_EmailResult = $cUsMC_api->verifyCustomerEmail(filter_input(INPUT_POST, 'email')); //EMAIL VERIFICATION
        if($cUsMC_API_EmailResult) {
            $cUsMC_jsonEmail = json_decode($cUsMC_API_EmailResult);
            
            switch ($cUsMC_jsonEmail->result){
                case 0 :
                    echo 'true';
                    break;
                case 1 :
                    echo 'false';
                    break;
            }
            
        }else{
            echo 'Unfortunately there has being an error during the application, please try again';
            exit();
        }
         
    }

    //echo json_encode($aryResponse);

    die();
}


// cUsMC_createCustomer handler function...
/*
 * Method in charge to create a contactus.com user via ajax post request vars
 * @since 2.0
 * @return string Value status to switch
 */
add_action('wp_ajax_cUsMC_createCustomer', 'cUsMC_createCustomer_callback');
function cUsMC_createCustomer_callback() {
    
    $cUsMC_userData = get_option('cUsMC_settings_userData'); //get the saved user data
    
    if      (  !strlen( filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING) ) ){      echo 'Missing First Name, is required field';      die();
    }elseif  ( !strlen( filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING) ) ){      echo 'Missing Last Name, is required field';       die();
    }elseif  ( !strlen( filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) ) ){      echo 'Missing/Invalid Email, is required field';   die();
    }elseif  ( !strlen( filter_input(INPUT_POST, 'website', FILTER_SANITIZE_STRING) ) ){    echo 'Missing Website, is required field';         die();
    }else{
        
        $cUsMC_api = new cUsComAPI_MC(); //CONTACTUS.COM API
        
        $postData = array(
            'fname' => filter_input(INPUT_POST, 'first_name'),
            'lname' => filter_input(INPUT_POST, 'last_name'),
            'email' => filter_input(INPUT_POST, 'email'),
            'website' => filter_input(INPUT_POST, 'website'),
            'phone' => preg_replace('/[^0-9]+/i', '', filter_input(INPUT_POST, 'phone')),
            'Template_Desktop_Form' => cUsMC_FORM_TPL,
            'Template_Desktop_Tab' => cUsMC_TAB_TPL,
            'Main_Category' => filter_input(INPUT_POST, 'CU_category',FILTER_SANITIZE_STRING),
            'Sub_Category' => filter_input(INPUT_POST, 'CU_subcategory',FILTER_SANITIZE_STRING),
            'Goals' => filter_input(INPUT_POST, 'CU_goals',FILTER_SANITIZE_STRING)
        );
        
        $cUsMC_API_result = $cUsMC_api->createCustomer($postData, filter_input(INPUT_POST, 'password'));
        if($cUsMC_API_result) {

            $cUs_json = json_decode($cUsMC_API_result);

            switch ( $cUs_json->status  ) {

                case 'success':
                    
                    echo 1;//GREAT
                    update_option('cUsMC_settings_form_key', $cUs_json->form_key ); //finally get form key form contactus.com // SESSION IN
                    $aryFormOptions = array( //DEFAULT SETTINGS / FIRST TIME
                        'tab_user'          => 1,
                        'cus_version'       => 'tab'
                    ); 
                    update_option('cUsMC_settings_FORM', $aryFormOptions );//UPDATE FORM SETTINGS
                    update_option('cUsMC_settings_userData', $postData);
                    
                    $cUs_API_Account    = $cUs_json->api_account;
                    $cUs_API_Key        = $cUs_json->api_key;
                    
                    $aryUserCredentials = array(
                        'API_Account' => $cUs_API_Account,
                        'API_Key'     => $cUs_API_Key
                    );
                    update_option('cUsMC_settings_userCredentials', $aryUserCredentials);
                    
                    // ********************************
                    // get here the default deeplink after creating customer
                    $cUsMC_API_getKeysResult = $cUsMC_api->getFormKeysData($cUs_API_Account, $cUs_API_Key); //api hook;
                    
                    $cUs_jsonKeys = json_decode( $cUsMC_API_getKeysResult );
                    $cUsMC_deeplinkview = $cUsMC_api->get_deeplink( $cUs_jsonKeys->data );
                    // get the default contact form deeplink
                    if( strlen( $cUsMC_deeplinkview ) ){
                        update_option('cUsMC_settings_default_deep_link_view', $cUsMC_deeplinkview ); // DEFAULT FORM KEYS
                    }
                    // save the form id for this donation new user
                    update_option( 'cUsMC_settings_form_id', $cUs_jsonKeys->data[0]->form_id );

                    $formSettings['form_status'] = 1;
                    $formSettings['form_key'] = $cUs_json->form_key;
                    $formSettings['form_id'] = $cUs_jsonKeys->data[0]->form_id;
                    $formSettings['form_type'] = $cUs_jsonKeys->data[0]->form_type;
                    $formSettings['updated'] = 1;
                    update_option('cUsMC_settings_form_'.$cUs_jsonKeys->data[0]->form_id, $formSettings);

                break;

                case 'error':

                    if($cUs_json->error == 'Email exists'){
                        echo 2;//ALREDY CUS USER
                        //$cUsMC_api->resetData(); //RESET DATA
                    }else{
                        //ANY ERROR
                        echo $cUs_json->error;
                        //$cUsMC_api->resetData(); //RESET DATA
                    }
                    
                break;


            }
            
        }else{
             //echo 3;//API ERROR
             echo $cUsMC_API_result;
             // $cUsMC_api->resetData(); //RESET DATA
        }
        
         
    }
    
    die();
}


// LoadDefaultKey handler function...
/*
 * Method in charge to set default form key by user via ajax post request vars
 * @since 2.0
 * @return array jSon encoded array
 */
add_action('wp_ajax_cUsMC_LoadDefaultKey', 'cUsMC_LoadDefaultKey_callback');
function cUsMC_LoadDefaultKey_callback() {
    
    $cUsMC_api = new cUsComAPI_MC();
    $cUsMC_userData = get_option('cUsMC_settings_userData'); //get the saved user data
    $cUs_email = $cUsMC_userData['email'];
    $cUs_pass = $cUsMC_userData['credential'];
    
    $cUsMC_API_result = $cUsMC_api->getFormKeysData($cUs_email, $cUs_pass); //api hook;
    if($cUsMC_API_result){
        $cUs_json = json_decode($cUsMC_API_result);

        switch ( $cUs_json->status  ) {
            case 'success':
                
                foreach ($cUs_json->data as $oForms => $oForm) {
                    if ($oForms !='status' && $oForm->form_type == 0 && $oForm->default == 1){//GET DEFAULT NEWSLETTER FORM KEY
                       $defaultFormKey = $oForm->form_key;
                    }
                }
                
                update_option('cUsMC_settings_form_key', $defaultFormKey);
                
                echo 1;
                break;

            case 'error':
                echo $cUs_json->error;
                //$cUsMC_api->resetData(); //RESET DATA
                break;
        }
    }
    
    die();
}

// cUsMC_setDefaulFormKey handler function...
/*
 * Method in charge to set default form key in all WP environment via ajax post request vars
 * @since 4.0.1
 * @return atring Status value array
 */
add_action('wp_ajax_cUsMC_setDefaulFormKey', 'cUsMC_setDefaulFormKey_callback');
function cUsMC_setDefaulFormKey_callback() {
    
    if(isset($_REQUEST['formKey'])){
       update_option('cUsMC_settings_form_key', $_REQUEST['formKey']);
       echo 1;//GREAT
    }
    
    die();
}


/*
 * Method in charge to set default form key in all WP environment via ajax post request vars
 * @since 4.0.1
 * @return atring Status value array
 */
add_action('wp_ajax_cUsMC_setDefaulFormKeyByID', 'cUsMC_setDefaulFormKeyByID_callback');
function cUsMC_setDefaulFormKeyByID_callback() {

    if(isset($_POST['form_id'])){
       unset($_POST['action']);
       update_option('cUsMC_settings_form_'.$_POST['form_id'], $_POST);
        $aryResponse = array('status' => 1);
    }

    echo json_encode($aryResponse);

    die();
}

/*
 * Method in charge to set default form key in all WP environment via ajax post request vars
 * @since 4.0.1
 * @return atring Status value array
 */
add_action('wp_ajax_cUsMC_setPageSettings', 'cUsMC_setPageSettings_callback');
function cUsMC_setPageSettings_callback() {

    if(isset($_POST['page_id'])){
        unset($_POST['action']);

        $pageID = $_POST['page_id'];
        $pageSettings = get_post_meta( $pageID, 'cUsMC_FormByPage_settings', true );

        $pageSettings['form_key'] = $_REQUEST['form-key'];

        if($_REQUEST['cus_version'] == 'tab'){
            $pageSettings['tab_user'] = $_REQUEST['form_status'];
            $pageSettings['form_status'] = $_REQUEST['form_status'];
            $pageSettings['cus_version'] = $_REQUEST['cus_version'];
        }

        if($_REQUEST['cus_version'] == 'inline'){

            $pageSettings['form_status_inline'] = $_REQUEST['form_status'];
            $pageSettings['cus_version_inline'] = $_REQUEST['form_version'];

            cUsMC_inline_shortcode_cleaner_by_ID($pageID); //RESET SC
            if($_REQUEST['form_status']){
                cUsMC_inline_shortcode_add($pageID); //ADDING SHORTCODE FOR INLINE PAGES
            }

        }

        update_post_meta($pageID, 'cUsMC_FormByPage_settings', $pageSettings);//SAVE DATA ON POST TYPE PAGE METAS
        $aryResponse = array('status' => 1);
    }

    echo json_encode($aryResponse);

    die();
}

/*
 * Method in charge to set default form key in all WP environment via ajax post request vars
 * @since 4.0.1
 * @return atring Status value array
 */
add_action('wp_ajax_cUsMC_setFormKeyByPage', 'cUsMC_setFormKeyByPage_callback');
function cUsMC_setFormKeyByPage_callback() {

    if(isset($_POST['pageID'])){
        unset($_POST['action']);

        $pageID = $_POST['pageID'];

        //print_r($_POST);
        //exit;



        if(!empty( $_REQUEST['form_key'] )){

            if($pageID == 'home'){
                $getHomePage    = get_option('cUsMC_HOME_settings');
                $getHomePage['form_key'] = $_REQUEST['form_key'];
                update_option('cUsMC_HOME_settings', $getHomePage);
            }else{
                $pageSettings = get_post_meta( $pageID, 'cUsMC_FormByPage_settings', true );
                $pageSettings['form_key'] = $_REQUEST['form_key'];
                update_post_meta($pageID, 'cUsMC_FormByPage_settings', $pageSettings);//SAVE DATA ON POST TYPE PAGE METAS
            }

            $aryThumbs = get_option('cUsMC_settings_form_thumbs');
            $aryThumbs = $aryThumbs[ $_REQUEST['form_key'] ];
            $thumb = $aryThumbs['thumb'];
        }

        $aryResponse = array('status' => 1, 'thumb' => $thumb);
    }

    echo json_encode($aryResponse);

    die();
}

/*
 * Method in charge to set default form key in all WP environment via ajax post request vars
 * @since 4.0.1
 * @return atring Status value array
 */
add_action('wp_ajax_cUsMC_setPageSettingsHome', 'cUsMC_setPageSettingsHome_callback');
function cUsMC_setPageSettingsHome_callback() {

    if(isset($_POST['page_id'])){
        unset($_POST['action']);

        $pageID = $_POST['page_id'];
        $getHomePage    = get_option('cUsMC_HOME_settings');
        $thumb = '';

        if(!empty($getHomePage)){
           update_option('cUsMC_HOME_settings', $getHomePage);
           $getHomePage    = get_option('cUsMC_HOME_settings');
        }else{
            $getHomePage = array();
        }

        if(!empty( $_REQUEST['form-key'] )){
            $getHomePage['form_key'] = $_REQUEST['form-key'];
            $getHomePage['tab_user'] = $_REQUEST['tab_user'];
            $getHomePage['cus_version'] = $_REQUEST['cus_version'];

            update_option('cUsMC_HOME_settings', $getHomePage);

            $aryThumbs = get_option('cUsMC_settings_form_thumbs');
            $aryThumbs = $aryThumbs[ $_REQUEST['form-key'] ];
            $thumb = $aryThumbs['thumb'];
        }

        $aryResponse = array('status' => 1, 'thumb' => $thumb);
    }

    echo json_encode($aryResponse);

    die();
}

// cUsMC_createCustomer handler function...
/*
 * Method in charge to update user form templates via ajax post request vars
 * @since 2.0
 * @return string Value status to switch
 */
add_action('wp_ajax_cUsMC_UpdateTemplates', 'cUsMC_UpdateTemplates_callback');
function cUsMC_UpdateTemplates_callback() {
    
    $cUsMC_userData = get_option('cUsMC_settings_userData'); //get the saved user data
    
    if      ( !strlen($cUsMC_userData['email']) ){      echo 'Missing/Invalid Email, is required field';   die();
    }elseif  ( !strlen($_REQUEST['Template_Desktop_Form']) ){    echo 'Missing Form Template';         die();
    }elseif  ( !strlen($_REQUEST['Template_Desktop_Tab']) ){    echo 'Missing Tab Template';         die();
    }else{
        
        $cUsMC_api = new cUsComAPI_MC(); //CONTACTUS.COM API
        $form_key       = get_option('cUsMC_settings_form_key');
        $postData = array(
            'email' => $cUsMC_userData['email'],
            'credential' => $cUsMC_userData['credential'],
            'Template_Desktop_Form' => $_REQUEST['Template_Desktop_Form'],
            'Template_Desktop_Tab' => $_REQUEST['Template_Desktop_Tab']
        );
        
        $cUsMC_API_result = $cUsMC_api->updateFormSettings($postData, $form_key);
        if($cUsMC_API_result) {

            $cUs_json = json_decode($cUsMC_API_result);

            switch ( $cUs_json->status  ) {

                case 'success':
                    echo 1;//GREAT

                break;

                case 'error':
                    //ANY ERROR
                    echo $cUs_json->error;
                    //$cUsMC_api->resetData(); //RESET DATA
                break;


            }
            
        }else{
             //echo 3;//API ERROR
             echo $cUs_json->error;
             // $cUsMC_api->resetData(); //RESET DATA
        }
         
    }
    
    die();
}

/*
 * Method in charge to chage user form templates via ajax post request vars
 * @since 2.0
 * @return string Value status to switch
 */
add_action('wp_ajax_cUsMC_changeFormTemplate', 'cUsMC_changeFormTemplate_callback');
function cUsMC_changeFormTemplate_callback() {
    
    $cUsMC_userData = get_option('cUsMC_settings_userCredentials'); //get the saved user data
   
    if      ( !strlen($cUsMC_userData['API_Account']) ){     echo 'Missing API Account';   die();
    }elseif  ( !strlen($cUsMC_userData['API_Key']) ){         echo 'Missing Form Key';         die();
    }elseif  ( !strlen($_REQUEST['Template_Desktop_Form']) ){    echo 'Missing Form Template';         die();
    }elseif  ( !strlen($_REQUEST['form_key']) ){    echo 'Missing Form Key';         die();
    }else{
        
        $cUsMC_api = new cUsComAPI_MC(); //CONTACTUS.COM API
        $form_key = $_REQUEST['form_key'];
        
        $postData = array(
            'API_Account'       => $cUsMC_userData['API_Account'],
            'API_Key'           => $cUsMC_userData['API_Key'],
            'Template_Desktop_Form' => $_REQUEST['Template_Desktop_Form']
        );
        
        $cUsMC_API_result = $cUsMC_api->updateFormSettings($postData, $form_key);
        if($cUsMC_API_result) {

            $cUs_json = json_decode($cUsMC_API_result);

            switch ( $cUs_json->status  ) {

                case 'success':
                    echo 1;//GREAT

                break;

                case 'error':
                    //ANY ERROR
                    echo $cUs_json->error;
                    //$cUsMC_api->resetData(); //RESET DATA
                break;


            } 
        }else{
             //echo 3;//API ERROR
             echo $cUs_json->error;
             // $cUsMC_api->resetData(); //RESET DATA
        } 
        
         
    } 
    
    die();
}

/*
 * Method in charge to update user tab templates via ajax post request vars
 * @since 2.0
 * @return string Value status to switch
 */
add_action('wp_ajax_cUsMC_changeTabTemplate', 'cUsMC_changeTabTemplate_callback');
function cUsMC_changeTabTemplate_callback() {
    
    $cUsMC_userData = get_option('cUsMC_settings_userCredentials'); //get the saved user data
   
    if       ( !strlen($cUsMC_userData['API_Account']) ){       echo 'Missing API Account';   die();
    }elseif  ( !strlen($cUsMC_userData['API_Key']) ){           echo 'Missing Form Key';      die();
    }elseif  ( !strlen($_REQUEST['Template_Desktop_Tab']) ){    echo 'Missing Tab Template';  die();
    }elseif  ( !strlen($_REQUEST['form_key']) ){                echo 'Missing Form Key';      die();
    }else{
        
        $cUsMC_api = new cUsComAPI_MC(); //CONTACTUS.COM API
        $form_key = $_REQUEST['form_key'];
        
        $postData = array(
            'API_Account'       => $cUsMC_userData['API_Account'],
            'API_Key'           => $cUsMC_userData['API_Key'],
            'Template_Desktop_Tab' => $_REQUEST['Template_Desktop_Tab']
        );
        
        $cUsMC_API_result = $cUsMC_api->updateFormSettings($postData, $form_key);
        if($cUsMC_API_result) {

            $cUs_json = json_decode($cUsMC_API_result);

            switch ( $cUs_json->status  ) {

                case 'success':
                    echo 1;//GREAT

                break;

                case 'error':
                    //ANY ERROR
                    echo $cUs_json->error;
                    //$cUsMC_api->resetData(); //RESET DATA
                break;


            } 
        }else{
             //echo 3;//API ERROR
             echo $cUs_json->error;
             // $cUsMC_api->resetData(); //RESET DATA
        } 
        
         
    }
    
    die();
}



// save custom selected pages handler function...
/*
 * Method in charge to save form settings via ajax post request vars
 * @since 2.0
 * @return string Value status to switch
 */
add_action('wp_ajax_cUsMC_saveCustomSettings', 'cUsMC_saveCustomSettings_callback');
function cUsMC_saveCustomSettings_callback() {
    
    $aryFormOptions = array( //DEFAULT SETTINGS / FIRST TIME
        'tab_user'          => $_REQUEST['tab_user'],
        'cus_version'       => $_REQUEST['cus_version']
    ); 
    update_option('cUsMC_settings_FORM', $aryFormOptions );//UPDATE FORM SETTINGS
    
    cUsMC_page_settings_cleaner();
    
    delete_option( 'cUsMC_settings_inlinepages' );
    delete_option( 'cUsMC_settings_tabpages' );
   
    
    die();
}

// save custom selected pages handler function...
/*
 * Method in charge to remove page settings via ajax post request vars
 * @since 2.0
 * @return string Value status to switch
 */
add_action('wp_ajax_cUsMC_deletePageSettings', 'cUsMC_deletePageSettings_callback');
function cUsMC_deletePageSettings_callback() {
    
    $pageID = $_REQUEST['pageID'];
    
    delete_post_meta($pageID, 'cUsMC_FormByPage_settings');//reset values
    cUsMC_inline_shortcode_cleaner_by_ID($pageID); //RESET SC
    
    $aryTabPages = get_option('cUsMC_settings_tabpages');
    $aryTabPages = cUsMC_removePage($pageID,$aryTabPages);
    update_option( 'cUsMC_settings_tabpages', $aryTabPages); //UPDATE OPTIONS
            
    $aryInlinePages = get_option('cUsMC_settings_inlinepages');
    $aryInlinePages = cUsMC_removePage($pageID,$aryInlinePages);
    update_option( 'cUsMC_settings_inlinepages', $aryInlinePages); //UPDATE OPTIONS
    
    die();
}

// save custom selected pages handler function...
/*
 * Method in charge to update user page settings from page selection via ajax post request vars
 * @since 2.0
 * @return string Value status to switch
 */
add_action('wp_ajax_cUsMC_changePageSettings', 'cUsMC_changePageSettings_callback');
function cUsMC_changePageSettings_callback() {
    
    $pageID = $_REQUEST['pageID'];
    delete_post_meta($pageID, 'cUsMC_FormByPage_settings');//reset values
    cUsMC_inline_shortcode_cleaner_by_ID($pageID); //RESET SC
    $aryTabPages = get_option('cUsMC_settings_tabpages');
    $aryInlinePages = get_option('cUsMC_settings_inlinepages');
    
    switch ($_REQUEST['cus_version']){
        case 'tab':
            
            $tabUser = 1;
            
            $aryTabPages[] = $pageID;
            $aryTabPages = array_unique($aryTabPages);
            update_option('cUsMC_settings_tabpages', $aryTabPages); //UPDATE OPTIONS
            
            if(!empty($aryInlinePages)){
                $aryInlinePages = cUsMC_removePage($pageID,$aryInlinePages);
                update_option( 'cUsMC_settings_inlinepages', $aryInlinePages); //UPDATE OPTIONS
            }
            
            echo 1;
            
            break;
        case 'inline':
            
            $tabUser = 0;
            
            $aryInlinePages[] = $pageID;
            $aryInlinePages = array_unique($aryInlinePages);
            update_option( 'cUsMC_settings_inlinepages', $aryInlinePages); //UPDATE OPTIONS
            
            if(!empty($aryTabPages)){
                $aryTabPages = cUsMC_removePage($pageID,$aryTabPages);
                update_option( 'cUsMC_settings_tabpages', $aryTabPages); //UPDATE OPTIONS
            }
            
            cUsMC_inline_shortcode_add($pageID); //ADDING SHORTCODE FOR INLINE PAGES
            
            echo 1;
            
            break;
    } 
    
    $aryFormOptions = array( //DEFAULT SETTINGS / FIRST TIME
        'tab_user'          => $tabUser,
        'form_key'          => $_REQUEST['form_key'],   
        'cus_version'       => $_REQUEST['cus_version']
    );
    
    if($pageID != 'home'){
        update_post_meta($pageID, 'cUsMC_FormByPage_settings', $aryFormOptions);//SAVE DATA ON POST TYPE PAGE METAS
    }else{
       update_option('cUsMC_HOME_settings', $aryFormOptions );//UPDATE FORM SETTINGS
    }
    
    die();
}

/*
 * Method in charge to remove user guide
 * @since 2.0
 * @return string Value status to switch
 */
add_action('wp_ajax_cUsMC_disable_introjs', 'cUsMC_disable_introjs_callback');
function cUsMC_disable_introjs_callback() {
    update_option( 'cUsMC_settings_intro_hints', 0); //UPDATE OPTIONS

    die();
}

/*
 * Method in charge to remove page settings via ajax post request vars
 * @since 2.0
 * @return string Value status to switch
 */
function cUsMC_removePage($valueToSearch, $arrayToSearch){
    $key = array_search($valueToSearch,$arrayToSearch);
    if($key!==false){
        unset($arrayToSearch[$key]);
    }
    return $arrayToSearch;
}

// logoutUser handler function...
/*
 * Method in charge to remove wp options saved with this plugin via ajax post request vars
 * @since 1.0
 * @return string Value status to switch
 */
add_action('wp_ajax_cUsMC_logoutUser', 'cUsMC_logoutUser_callback');
function cUsMC_logoutUser_callback() {
    
    $cUsMC_api = new cUsComAPI_MC();
    $cUsMC_api->resetData(); //RESET DATA
    
    delete_option( 'cUsMC_settings_api_key' );
    delete_option( 'cUsMC_settings_form_key' );
    delete_option( 'cUsMC_settings_list_Name' );
    delete_option( 'cUsMC_settings_list_ID' );
    
    echo 'Deleted.... User data'; //none list
    
    die();
}