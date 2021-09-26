/**
 * Created by Younes DRO
 * 
 */
;
(function ($) {



    $(document).ready(function () {

        $(document).on('click', 'button.supp-post', function (e) {
            //e.preventDefault();

           // console.log( $(this).data("id") );
          
            
            var postid = $(this).data("id");
          
            var $this = $(this);
            $.ajax({
                type: 'POST',
                url: dro_ajax_obj.ajaxurl,
                data: {
                    'action': 'dro_ajax_request',
                    'post': postid,
                    'nonce': dro_ajax_obj.nonce
                },
                beforeSend: function () {
                   
                   $this.append(' Deleting....');
                    $this.addClass('sending');
                    //console.log($this);
                    
                },
                success: function ( data ) {
                     $this.removeClass('sending');
                     $this.parent().parent().fadeOut();
                },
                error: function (errorThrown) {
                    console.log(errorThrown);
                }
            });
        });
    });

})(jQuery);


