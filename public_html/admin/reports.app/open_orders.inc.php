<?php
  $_GET['date_from'] = !empty($_GET['date_from']) ? date('Y-m-d 00:00:00', strtotime($_GET['date_from'])) : null;
  $_GET['date_to'] = !empty($_GET['date_to']) ? date('Y-m-d 23:59:59', strtotime($_GET['date_to'])) : date('Y-m-d H:i:s');

  if ($_GET['date_from'] > $_GET['date_to']) list($_GET['date_from'], $_GET['date_to']) = array($_GET['date_to'], $_GET['date_from']);

  $date_first_order = database::fetch(database::query("select min(date_created) from ". DB_TABLE_ORDERS ." limit 1;"));
  $date_first_order = $date_first_order['min(date_created)'];
  if (empty($date_first_order)) $date_first_order = date('Y-m-d 00:00:00');
  if ($_GET['date_from'] < $date_first_order) $_GET['date_from'] = $date_first_order;

  if ($_GET['date_from'] > date('Y-m-d H:i:s')) $_GET['date_from'] = date('Y-m-d H:i:s');
  if ($_GET['date_to'] > date('Y-m-d H:i:s')) $_GET['date_to'] = date('Y-m-d H:i:s');

  if (!isset($_GET['page'])) $_GET['page'] = 1;
?>

<style>
.border-left {
  border-left: 1px #999 dashed;
}
</style>

<div style="float: right; display: inline;">
  <?php echo functions::form_draw_form_begin('filter_form', 'get'); ?>
    <?php echo functions::form_draw_hidden_field('app'); ?>
    <?php echo functions::form_draw_hidden_field('doc'); ?>
    <table>
      <tr>
        <td><?php echo language::translate('title_item_name', 'Item Name'); ?>: <?php echo functions::form_draw_search_field('name'); ?></td>
        <td><?php echo language::translate('title_date_period', 'Date Period'); ?>: <?php echo functions::form_draw_date_field('date_from'); ?> - <?php echo functions::form_draw_date_field('date_to'); ?></td>
        <td><?php echo functions::form_draw_button('filter', language::translate('title_filter_now', 'Filter')); ?></td>
      </tr>
    </table>
  <?php echo functions::form_draw_form_end(); ?>
</div>

<h1 style="margin-top: 0px;"><?php echo $app_icon; ?> <?php echo language::translate('title_open_orders', 'Open Orders'); ?></h1>

<table width="100%" align="center" class="dataTable">
  <tr class="header">
    <th width="50%"><?php echo language::translate('title_product', 'Product'); ?></th>
    <th style="text-align: center;"><?php echo language::translate('title_quantity', 'Quantity'); ?></th>
    <th style="text-align: center;"><?php echo language::translate('title_date', 'Date'); ?></th>
    <th style="text-align: center;"><?php echo language::translate('title_sales', 'Sales'); ?></th>
    <th style="text-align: center;"><?php echo language::translate('title_tax', 'Tax'); ?></th>
  </tr>
<?php

  $order_statuses = array();
  $orders_status_query = database::query(
    "select id from ". DB_TABLE_ORDER_STATUSES ." where is_active;"
  );

  while ($order_status = database::fetch($orders_status_query)) {
      $order_statuses[] = (int)$order_status['id'];
  }


  $order_items_query = database::query(
    "select
      oi.name,
      sum(oi.quantity) as total_quantity,
      sum(oi.price) as total_sales,
      sum(oi.tax) as total_tax,
      oi.options as options,
      oi.option_stock_combination as option_stock_combination, 
      o.shipping_option_id as shipping_option_id,
      GROUP_CONCAT(DISTINCT o.shipping_option_name SEPARATOR '    \n<br> ') as shipping_option_name,
      substring_index(o.shipping_option_id, '@', -1) as shipping_option_date
    from ". DB_TABLE_ORDERS_ITEMS ." oi
    left join ". DB_TABLE_ORDERS ." o on (o.id = oi.order_id)
    where o.order_status_id in ('". implode("', '", $order_statuses) ."')
    and o.date_created >= '". date('Y-m-1 00:00:00', strtotime($_GET['date_from'])) ."'
    and o.date_created <= '". date('Y-m-t 23:59:59', strtotime($_GET['date_to'])) ."'
    ". (!empty($_GET['name']) ? "and oi.name like '%". database::input($_GET['name']) ."%'" : "") ."
    group by shipping_option_date, oi.product_id, oi.options
    order by o.shipping_option_id, total_quantity, oi.name desc;"
  );

  if (database::num_rows($order_items_query) > 0) {

    if ($_GET['page'] > 1) database::seek($order_items_query, (settings::get('data_table_rows_per_page') * ($_GET['page']-1)));

    $page_items = 0;
    while ($order_item = database::fetch($order_items_query)) {
?>
  <tr class="row">
        <td><?php echo $order_item['name']; ?> <?php echo $order_item['options']; ?>

<?php
        $order_action = new mod_order_action();
        $order_action_options = $order_action->options();
        /* JEN TODO= print out the options pretty-like instead of a bunch of garbage like right now
        if (!empty($order_item['options'])) {
            echo '      <br />' . PHP_EOL
               . '      <table>' . PHP_EOL;
            foreach (array_keys($order_item['options']) as $field) {
                echo '        <tr>' . PHP_EOL
                                    . '          <td style="padding-left: 10px;">'. $field .'</td>' . PHP_EOL
                                    . '          <td>';
                if (is_array($order_item['options'][$field])) {
                    foreach (array_keys($order_item['options'][$field]) as $k) {
                        echo "<div>jen!</div>" .functions::form_draw_text_field($order_item[options][$field][$k],
                                                                                true,
                                                                                !empty($order_item['option_stock_combination']) ? 'readonly="readonly"' : '');
                    }
                } else {
                    echo "jennnnn". functions::form_draw_text_field($order_item[options][$field], true, !empty($order_item['option_stock_combination']) ? 'readonly="readonly"' : '');
                }
                echo '          </td>' . PHP_EOL
                   . '        </tr>' . PHP_EOL;
            }
            echo '      </table>' . PHP_EOL;
        } else {
            echo functions::form_draw_hidden_field($order_item[options], '');
        }
        */
        ?>

        </td>
    <td style="text-align: center;" class="border-left"><?php echo (float)$order_item['total_quantity']; ?></td>
    <td style="text-align: center;" class="border-left"><?php echo $order_item['shipping_option_date']; ?>
        <br><small> <?php echo $order_item['shipping_option_name']; ?></small>
        </td>
    <td style="text-align: right;" class="border-left"><?php echo currency::format($order_item['total_sales'], false, false, settings::get('store_currency_code')); ?></td>
    <td style="text-align: right;" class="border-left"><?php echo currency::format($order_item['total_tax'], false, false, settings::get('store_currency_code')); ?></td>
  </tr>
<?php
      if (++$page_items == settings::get('data_table_rows_per_page')) break;
    }
  }
?>
</table>

<?php echo functions::draw_pagination(ceil(database::num_rows($order_items_query)/settings::get('data_table_rows_per_page'))); ?>