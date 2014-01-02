<?php
  functions::draw_fancybox('.fancybox-region', array(
    'centerOnScroll' => true,
    'hideOnContentClick' => false,
    'modal' => false,
    'speedIn' => 600,
    'transitionIn' => 'fade',
    'transitionOut' => 'fade',
    'type' => 'ajax',
    'scrolling' => 'false',
  ));
?>
<div id="region">
  <table>
    <tr>
      <td>
        <div class="language"><img src="<?php echo WS_DIR_IMAGES .'icons/languages/'. language::$selected['code'] .'.png'; ?>" alt="<?php echo language::$selected['name']; ?>" title="<?php echo language::$selected['name']; ?>" /></div>
        <div class="currency" title="<?php echo currency::$selected['name']; ?>"><?php echo currency::$selected['code']; ?></div>
        <div class="country" title="<?php echo functions::reference_get_country_name(customer::$data['country_code']); ?>"><?php echo functions::reference_get_country_name(customer::$data['country_code']); ?></div>
      </td>
      <td><a class="fancybox-region" href="<?php echo document::href_link(WS_DIR_HTTP_HOME . 'select_region.php'); ?>"><?php echo language::translate('title_select_region', 'Select Region'); ?></a></td>
    </tr>
  </table>
</div>