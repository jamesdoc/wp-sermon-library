jQuery(document).ready( function( $ ) {
  // TO DO: Remove jQuery dependancy
  var file_frame;
  $('.js-sl_sermon_audio__button').on('click', function(e) {
    e.preventDefault();

    if (file_frame) {
        file_frame.open();
        return;
    }

    file_frame = wp.media.frames.file_frame = wp.media({
        title: $(this).data('uploader_title'),
        button: {
            text: $(this).data('uploader_button_text'),
        },
        multiple: false
    });

    file_frame.on('select', function(){
        attachment = file_frame.state().get('selection').first().toJSON();
        // TODO: validate on attachment.mime or attachment.type
        var url = attachment.id;
        var field = document.getElementById("js-sl_sermon_audio");
        field.value = url;
    });

    file_frame.open();
  });

});
