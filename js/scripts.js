jQuery(document).ready( function( $ ) {

    $('#upload_image_button').click(function() {

        formfield = $('#poster_url').attr('name');
        tb_show( '', 'media-upload.php?type=image&amp;TB_iframe=true' );
        return false;
    });

    window.send_to_editor = function(html) {

        imgurl = $(html).attr('src');
        $('#poster_url').val(imgurl);
        tb_remove();
    }

});