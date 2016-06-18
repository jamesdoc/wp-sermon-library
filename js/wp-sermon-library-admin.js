jQuery(document).ready( function( $ ) {
  var file_frame;
  $('.js-sl_sermon_audio__button').on('click', function(e) {
    e.preventDefault();

    $('.js-sl_error').remove();

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

    file_frame.on('select', function() {
      var attachment = file_frame.state().get('selection').first().toJSON();
      sl_return_audio(attachment);
    });

    file_frame.open();
  });

  function sl_return_audio(audio_data) {
    var el_upload_form = $(".js-sl_upload_form");
    if (el_upload_form.length <= 0) { return; }
    el_upload_form = el_upload_form[0]

    // Validate upload is audio (thank you Wordpress)
    if (audio_data.type !== 'audio') {
      $('<div class="error js-sl_error"><p>Please select an audio file</p></div>')
        .prependTo(el_upload_form);
    } else {
      var audio_id_field = document.getElementById("js-sl_sermon_audio");
      audio_id_field.value = audio_data.id;

      $('.js-sl_audio_preview__title').text(audio_data.title);
      $('.js-sl_audio_preview__length').text(audio_data.fileLength);
      var sl_audio_player = $('.js-sl_audio_preview');
      sl_audio_player.attr("controls", "controls");
      sl_audio_player.children("source")
        .attr("src", audio_data.url)
        .attr("type", audio_data.mime);
      sl_audio_player[0].load();
    }
  }

});

