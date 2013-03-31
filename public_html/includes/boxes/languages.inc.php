<ul id="languages" class="navigation-horizontal">
<?php
  foreach ($system->language->languages as $language) {
    if ($language['status']) {
      echo '  <li><a href="javascript:set_language(\''. $language['code'] .'\');"><img src="'. WS_DIR_IMAGES .'icons/languages/'. $language['code'] .'.png" alt="'. $language['name'] .'" /></a></li>' . PHP_EOL;
    }
  }
?>
</ul>
<script>
  function set_language(code) {
    var form = $('<?php
      echo str_replace(array("\r", "\n"), '', $system->functions->form_draw_form_begin('language_form', 'post')
                                            . $system->functions->form_draw_hidden_field('set_language', '\'+ code +\'')
                                            . $system->functions->form_draw_form_end()
      );
    ?>');
    $(document.body).append(form);
    form.submit();
  }
</script>