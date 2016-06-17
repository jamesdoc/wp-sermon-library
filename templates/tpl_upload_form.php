<?php
  // Appears in the CPT to allow uploading of an MP3 file

  $btn_label = "Upload Audio File";
  if (isset($audio['url'])) {
    $btn_label = "Replace Audio File";
  }

?>

<div class="js-sl_upload_form sl_upload_form">

  <p>
    <input id="js-sl_sermon_audio" type="hidden" name="sl_sermon_audio" value="" />
    <button class="js-sl_sermon_audio__button button" data-uploader_title="Upload Sermon" data-uploader_button_text="Add Sermon">
      <?php echo $btn_label; ?>
    </button>
  </p>

  <?php if (isset($audio)) : ?>
    <p>
      <strong>Current audio:</strong>
      <span class="js-sl_audio_preview__title"><?php echo $audio['title']; ?></span>
      (<span class="js-sl_audio_preview__length"><?php echo $audio['length_formatted']; ?></span>)
    </p>
    <audio class="js-sl_audio_preview sl_upload_form__audio_player" preload="none" <?php if(isset($audio['url'])) { echo "controls"; } ?>>
      <source src="<?php if(isset($audio['url'])) { echo $audio['url']; } ?>" type="audio/mp3" />
    </audio>
  <?php endif; // end if audio already... ?>

  <small>TODO: Add in 'how to compress' tip</small>

</div>
