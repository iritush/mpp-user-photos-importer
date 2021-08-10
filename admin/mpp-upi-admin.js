jQuery(document).ready(function(){
  
    var jq = jQuery;

    function import_photos() {

        jq.post(ajaxurl, {action: 'mpp_import_user_photos'}, function (ret) {

            jq('#mpp_import_user_photos-log').append(ret.message + '<br />');
            if (ret.remaining)
            import_photos();

        }, 'json');
    }

    //Start Trigger
    jq('#mpp_import_user_photos-start').click(function () {
        import_photos();
        return false;
    });

});