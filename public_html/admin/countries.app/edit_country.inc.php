<?php
  
  if (isset($_GET['country_code'])) {
    $country = new ctrl_country($_GET['country_code']);
  } else {
    $country = new ctrl_country();
  }
  
  if (!$_POST) {
    foreach ($country->data as $key => $value) {
      $_POST[$key] = $value;
    }
  }

  
  // Save data to database
  if (isset($_POST['save'])) {

    if (empty($_POST['iso_code_2'])) $system->notices->add('errors', $system->language->translate('error_missing_code', 'You must enter a code'));
    if (empty($_POST['iso_code_3'])) $system->notices->add('errors', $system->language->translate('error_missing_code', 'You must enter a code'));
    if (empty($_POST['name'])) $system->notices->add('errors', $system->language->translate('error_must_enter_name', 'You must enter a name'));
    
    if (empty($_POST['zones'])) $_POST['zones'] = array();
    
    if (!$system->notices->get('errors')) {
    
      $fields = array(
        'status',
        'iso_code_2',
        'iso_code_3',
        'name',
        'domestic_name',
        'address_format',
        'postcode_required',
        'currency_code',
        'phone_code',
        'zones',
      );
      
      foreach ($fields as $field) {
        if (isset($_POST[$field])) $country->data[$field] = $_POST[$field];
      }
      
      $country->save();
      
      $system->notices->add('success', $system->language->translate('success_changes_saved', 'Changes saved'));
      header('Location: '. $system->document->link('', array('doc' => 'countries'), true, array('country_id')));
      exit;
    }
  }
  
  if (isset($_POST['delete'])) {

    $country->delete();
    
    $system->notices->add('success', $system->language->translate('success_post_deleted', 'Post deleted'));
    header('Location: '. $system->document->link('', array('doc' => 'countries'), true, array('country_id')));
    exit();
  }

?>
  <h1 style="margin-top: 0px;"><img src="<?php echo WS_DIR_ADMIN . $_GET['app'] .'.app/icon.png'; ?>" width="32" height="32" style="vertical-align: middle; margin-right: 10px;" /><?php echo (!empty($country->data['id'])) ? $system->language->translate('title_edit_country', 'Edit Country') : $system->language->translate('title_add_new_country', 'Add New Country'); ?></h1>
  
  <?php echo $system->functions->form_draw_form_begin(false, 'post', false, true); ?>
  
    <table>
      <tr>
        <td align="left" nowrap="nowrap"><strong><?php echo $system->language->translate('title_status', 'Status'); ?></strong><br />
          <?php echo $system->functions->form_draw_radio_button('status', '1', isset($_POST['status']) ? $_POST['status'] : '1'); ?> <?php echo $system->language->translate('title_enabled', 'Enabled'); ?>
          <?php echo $system->functions->form_draw_radio_button('status', '0', isset($_POST['status']) ? $_POST['status'] : '1'); ?> <?php echo $system->language->translate('title_disabled', 'Disabled'); ?>
        </td>
      </tr>
      <tr>
        <td align="left" nowrap="nowrap"><strong><?php echo $system->language->translate('title_code', 'Code'); ?> (ISO 3166-1 alpha-2)</strong><br />
          <?php echo $system->functions->form_draw_text_field('iso_code_2', true, 'data-size="tiny"'); ?> <a href="http://en.wikipedia.org/wiki/ISO_3166-1_alpha-2" target="_blank"><?php echo $system->language->translate('title_reference', 'Reference'); ?>
        </td>
      </tr>
      <tr>
        <td align="left" nowrap="nowrap"><strong><?php echo $system->language->translate('title_code', 'Code'); ?> (ISO 3166-1 alpha-3)</strong><br />
          <?php echo $system->functions->form_draw_text_field('iso_code_3', true, 'data-size="tiny"'); ?> <a href="http://en.wikipedia.org/wiki/ISO_3166-1_alpha-3" target="_blank"><?php echo $system->language->translate('title_reference', 'Reference'); ?>
        </td>
      </tr>
      <tr>
        <td align="left" nowrap="nowrap"><strong><?php echo $system->language->translate('title_name', 'Name'); ?></strong><br />
          <?php echo $system->functions->form_draw_text_field('name', true); ?>
        </td>
      </tr>
      <tr>
        <td align="left" nowrap="nowrap"><strong><?php echo $system->language->translate('title_domestic_name', 'Domestic Name'); ?></strong><br />
          <?php echo $system->functions->form_draw_text_field('domestic_name', true); ?>
        </td>
      </tr>
      <tr>
        <td align="left" nowrap="nowrap"><strong><?php echo $system->language->translate('title_address_format', 'Address Format'); ?> (<a id="address-format-hint" href="#">?</a>)</strong> <a href="http://www.addressdoctor.com/en/countries-data/address-formats.html" target="_blank"><?php echo $system->language->translate('title_reference', 'Reference')?></a><br />
          <?php echo $system->functions->form_draw_textarea('address_format', true, 'data-size="medium" style="height: 150px;"'); ?>
          <script type="text/javascript">
            $("#address-format-hint").click(function() {
              alert(
                "<?php echo $system->language->translate('title_syntax', 'Syntax'); ?>:\n\n" +
                "%company, %firstname, %lastname, \n" +
                "%address1, %address2\n" +
                "%postcode %city\n" + 
                "%zone_code, %zone_name\n" +
                "%country_code_2, %country_code_3, %country_name\n"
              );
            });
          </script>
        </td>
      </tr>
      <tr>
        <td align="left" nowrap="nowrap"><strong><?php echo $system->language->translate('title_postcode_required', 'Postcode Required'); ?></strong><br />
          <?php echo $system->functions->form_draw_radio_button('postcode_required', '1', isset($_POST['postcode_required']) ? $_POST['postcode_required'] : '1'); ?> <?php echo $system->language->translate('title_yes', 'Yes'); ?>
          <?php echo $system->functions->form_draw_radio_button('postcode_required', '0', isset($_POST['postcode_required']) ? $_POST['postcode_required'] : '1'); ?> <?php echo $system->language->translate('title_no', 'No'); ?>
        </td>
      </tr>
      <tr>
        <td align="left" nowrap="nowrap"><strong><?php echo $system->language->translate('title_currency_code', 'Currency Code'); ?></strong><br />
          <?php echo $system->functions->form_draw_text_field('currency_code', true, 'data-size="tiny"'); ?>
        </td>
      </tr>
      <tr>
        <td align="left" nowrap="nowrap"><strong><?php echo $system->language->translate('title_phone_country_code', 'Phone Country Code'); ?></strong><br />
          <?php echo $system->functions->form_draw_text_field('phone_code', true, 'data-size="tiny"'); ?>
        </td>
      </tr>
    </table>
    
    <h2><?php echo $system->language->translate('title_zones', 'Zones'); ?></h2>
    <table width="100%" class="dataTable" id="table-zones">
      <tr class="header">
        <th align="left" style="vertical-align: text-top" nowrap="nowrap"><?php echo $system->language->translate('title_id', 'ID'); ?></th>
        <th align="left" style="vertical-align: text-top" nowrap="nowrap"><?php echo $system->language->translate('title_code', 'Code'); ?></th>
        <th align="left" style="vertical-align: text-top" nowrap="nowrap"><?php echo $system->language->translate('title_name', 'Name'); ?></th>
        <th align="center" style="vertical-align: text-top" nowrap="nowrap">&nbsp;</th>
      </tr>
  <?php
      if (!empty($_POST['zones'])) {
        foreach (array_keys($_POST['zones']) as $key) {
  ?>
      <tr>
        <td align="left"><?php echo $system->functions->form_draw_hidden_field('zones['. $key .'][id]', true); ?><?php echo $_POST['zones'][$key]['id']; ?></td>
        <td align="left"><?php echo $system->functions->form_draw_hidden_field('zones['. $key .'][code]', true); ?><?php echo $_POST['zones'][$key]['code']; ?></td>
        <td align="left"><?php echo $system->functions->form_draw_hidden_field('zones['. $key .'][name]', true); ?><?php echo $_POST['zones'][$key]['name']; ?></td>
        <td align="right"><a id="remove-zone" href="#"><img src="<?php echo WS_DIR_IMAGES; ?>icons/16x16/remove.png" width="16" height="16" title="<?php echo $system->language->translate('title_remove', 'Remove'); ?>" /></a></td>
      </tr>
  <?php
        }
      }
  ?>
      <tr>
        <td align="left">&nbsp;</td>
        <td align="left"><?php echo $system->functions->form_draw_text_field('zone[code]', ''); ?></td>
        <td align="left"><?php echo $system->functions->form_draw_text_field('zone[name]', ''); ?></td>
        <td align="right"><?php echo $system->functions->form_draw_button('add_zone', $system->language->translate('title_add', 'Add'), 'button'); ?></td>
      </tr>
    </table>
    
    <script type="text/javascript">
      $("body").on("click", "#remove-zone", function(event) {
        event.preventDefault();
        $(this).closest('tr').remove();
      });
      
      $("select[name='country[code]']").change(function(){
        $('body').css('cursor', 'wait');
        $.ajax({
          url: '<?php echo WS_DIR_AJAX .'zones.json.php'; ?>?country_code=' + $(this).val(),
          type: 'get',
          cache: true,
          async: true,
          dataType: 'json',
          error: function(jqXHR, textStatus, errorThrown) {
            alert(jqXHR.readyState + '\n' + textStatus + '\n' + errorThrown.message);
          },
          success: function(data) {
            $('select[name=\'zone[code]\']').html('');
            if ($('select[name=\'zone[code]\']').attr('disabled')) $('select[name=\'zone[code]\']').removeAttr('disabled');
            if (data) {
              $('select[name=\'zone[code]\']').append('<option value="">-- '+ '<?php echo $system->language->translate('title_all_zones', 'All Zones'); ?>' +' --</option>');
              $.each(data, function(i, zone) {
                $('select[name=\'zone[code]\']').append('<option value="'+ zone.code +'">'+ zone.name +'</option>');
              });
            } else {
              $('select[name=\'zone[code]\']').attr('disabled', 'disabled');
            }
          },
          complete: function() {
            $('body').css('cursor', 'auto');
          }
        });
      });
      
      var new_zone_i = <?php echo isset($_POST['zones']) ? count($_POST['zones']) : '0'; ?>;
      $("button[name=add_zone]").click(function(event) {
        event.preventDefault();
        if ($("select[name='country[code]']").find("option:selected").val() == "") return;
        new_zone_i++;
        var output = '    <tr>'
                   + '      <td align="left"><?php echo str_replace(PHP_EOL, '', $system->functions->form_draw_hidden_field('zones[new_zone_i][id]', '')); ?></td>'
                   + '      <td align="left"><?php echo str_replace(PHP_EOL, '', $system->functions->form_draw_hidden_field('zones[new_zone_i][code]', 'new_zone_code')); ?>new_zone_code</td>'
                   + '      <td align="left"><?php echo str_replace(PHP_EOL, '', $system->functions->form_draw_hidden_field('zones[new_zone_i][name]', 'new_zone_name')); ?>new_zone_name</td>'
                   + '      <td align="right"><a id="remove-zone" href="#"><img src="<?php echo WS_DIR_IMAGES; ?>icons/16x16/remove.png" width="16" height="16" title="<?php echo $system->language->translate('title_remove', 'Remove'); ?>" /></a></td>'
                   + '    </tr>';
        output = output.replace(/new_zone_i/g, 'new_' + new_zone_i);
        output = output.replace(/new_zone_code/g, $("input[name='zone[code]']").val());
        output = output.replace(/new_zone_name/g, $("input[name='zone[name]']").val());
        $("#table-zones tr:last").before(output);
      });
    </script>
  
    <p><?php echo $system->functions->form_draw_button('save', $system->language->translate('title_save', 'Save'), 'submit', '', 'save'); ?> <?php echo $system->functions->form_draw_button('cancel', $system->language->translate('title_cancel', 'Cancel'), 'button', 'onclick="history.go(-1);"', 'cancel'); ?> <?php echo (isset($country->data['id'])) ? $system->functions->form_draw_button('delete', $system->language->translate('title_delete', 'Delete'), 'submit', 'onclick="if (!confirm(\''. $system->language->translate('text_are_you_sure', 'Are you sure?') .'\')) return false;"', 'delete') : false; ?></p>
  
<?php echo $system->functions->form_draw_form_end(); ?>