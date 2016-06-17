<?php
  // Appears in the CPT to allow uploading of an MP3 file
?>

<div class="sl_upload_form">

  <?php if (isset($audio)) : ?>
    <p>
      <strong>Current audio:</strong>
      <?php echo $audio['title']; ?>
      (<?php echo $audio['length_formatted']; ?>)
    </p>
    <audio preload="none" controls>
      <source src="<?php echo $audio['url'] ?>" type="audio/mp3" />
    </audio>
  <?php endif; // end if audio already... ?>

  <p>
    <input id="js-sl_sermon_audio" type="hidden" name="sl_sermon_audio" value="" />
    <button class="js-sl_sermon_audio__button button insert-media add_media">
      Pick audio file
    </button>
  </p>

</div>
