
//PLUGIN $ ENVIROMENT ($)
var cUsMC_myjq = jQuery.noConflict();

cUsMC_myjq(window).error(function(e){
    e.preventDefault();
});

//ON READY DOM LOADED
cUsMC_myjq(document).ready(function($) {
    
    try{
        
        //LOADING UI BOX
        $( ".cUsMC_preloadbox" ).delay(1000).fadeOut()
        $('.tooltips').tooltip();
        
        //UI TABS
        //$( "#cUsMC_tabs" ).tabs({active: false});
        //$( "#menuWrapper" ).tabs({active: false});
        //$('#tabs').tabs();
        //console.log('tabs loaded')

        //UNBIND UI TABS LINK ON CLICK
        $("li.gotohelp a").unbind('click');

        //colorbox window
        $(".tooltip_formsett").colorbox({iframe:true, innerWidth:'75%', innerHeight:'80%'});

        cUsMC_myjq('.sign-in').click(function() {
            cUsMC_myjq('.signup-form').slideToggle('slow');
            cUsMC_myjq('.login-form').slideToggle('slow');
        });

        $(".custom-checkbox").bootstrapSwitch({
            onColor: 'success',
            offColor: 'danger',
            size: 'small'
        });

       
    }catch(err){
        console.log(err);
        $('.advice_notice').html('Error - please update your WordPress  to the latest version. If the problem continues, contact us at support@contactus.com.: ' + err ).slideToggle().delay(2000).fadeOut(2000);
    }
    
    //TOOLTIPS
    try{
        //JQ UI TOOLTIPS
        $(".setLabels").tooltip();
    }catch(err){
        $('.advice_notice').html('Error - please update your WordPress version to the latest version. If the problem continues, contact us at support@contactus.com. ' + err ).slideToggle().delay(2000).fadeOut(2000);
    }

    //UNLINK ACCOUNT AND DELETE PLUGIN OPTIONS AND SETTINGS
    cUsMC_myjq('.LogoutUser').click(function(){

        bootbox.confirm("Do you want to unlink your account? <span class='loading'></span>", function(result) {
            if(result){
                cUsMC_myjq('.loading').fadeIn();
                cUsMC_myjq.ajax({ type: "POST", url: ajax_object.ajax_url, data: {action:'cUsMC_logoutUser'},
                    success: function(data) {
                        cUsMC_myjq('.loading').fadeOut();
                        location.reload();
                    }
                });
            }
        });

    });


    
});//ON LOAD