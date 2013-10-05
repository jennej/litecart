<?php
  
  $order_statuses = array();
  $orders_status_query = $system->database->query(
    "select id from ". DB_TABLE_ORDER_STATUSES ." where is_sale;"
  );
  while ($order_status = $system->database->fetch($orders_status_query)) {
    $order_statuses[] = (int)$order_status['id'];
  }
  
  $stats = array();
  
// Total Sales
  $orders_query = $system->database->query(
    "select sum(payment_due) as total_sales from ". DB_TABLE_ORDERS ."
    where order_status_id in ('". implode("', '", $order_statuses) ."');"
  );
  $orders = $system->database->fetch($orders_query);
  $stats['total_sales'] = $orders['total_sales'];
  
// Total Sales Year
  $orders_query = $system->database->query(
    "select sum(payment_due) as total_sales_year from ". DB_TABLE_ORDERS ."
    where order_status_id in ('". implode("', '", $order_statuses) ."')
    and date_created >= '". date('Y-m-d H:i:s', mktime(0, 0, 0, 1, 1, date('Y'))) ."';"
  );
  $orders = $system->database->fetch($orders_query);
  $stats['total_sales_year'] = $orders['total_sales_year'];
  
// Total Sales Month
  $orders_query = $system->database->query(
    "select sum(payment_due) as total_sales_month from ". DB_TABLE_ORDERS ."
    where order_status_id in ('". implode("', '", $order_statuses) ."')
    and date_created >= '". date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), 1, date('Y'))) ."';"
  );
  $orders = $system->database->fetch($orders_query);
  $stats['total_sales_month'] = $orders['total_sales_month'];
  
// Average order
  $orders_query = $system->database->query(
    "select count(id) as num_orders, sum(payment_due) as total_sales from ". DB_TABLE_ORDERS ."
    where order_status_id in ('". implode("', '", $order_statuses) ."');"
  );
  $orders = $system->database->fetch($orders_query);
  @$stats['average_order_amount'] = $orders['total_sales'] / $orders['num_orders'];
  
// Num customers
  $customers_query = $system->database->query(
    "select count(id) as num_customers from ". DB_TABLE_CUSTOMERS .";"
  );
  $customers = $system->database->fetch($customers_query);
  $stats['num_customers'] = $customers['num_customers'];
  
?>
<div class="widget">
  <table width="100%" class="dataTable">
    <tr class="header">
      <th colspan="2" align="left"><?php echo $system->language->translate('title_statistics', 'Statistics'); ?></th>
    </tr>
    <tr class="odd">
      <td nowrap="nowrap" align="left"><?php echo $system->language->translate('title_total_sales', 'Total Sales'); ?>:</td>
      <td nowrap="nowrap" align="right"><?php echo $system->currency->format($stats['total_sales'], false, false, $system->settings->get('store_currency_code')); ?></td>
    </tr>
    <tr class="even">
      <td nowrap="nowrap" align="left"><?php echo $system->language->translate('title_total_sales', 'Total Sales') .' '. date('Y'); ?>:</td>
      <td nowrap="nowrap" align="right"><?php echo $system->currency->format($stats['total_sales_year'], false, false, $system->settings->get('store_currency_code')); ?></td>
    </tr>
    <tr class="odd">
      <td nowrap="nowrap" align="left"><?php echo $system->language->translate('title_total_sales', 'Total Sales') .' '. strftime('%B'); ?>:</td>
      <td nowrap="nowrap" align="right"><?php echo $system->currency->format($stats['total_sales_month'], false, false, $system->settings->get('store_currency_code')); ?></td>
    </tr>
    <tr class="even">
      <td nowrap="nowrap" align="left"><?php echo $system->language->translate('title_average_order_amount', 'Average Order Amount'); ?>:</td>
      <td nowrap="nowrap" align="right"><?php echo $system->currency->format($stats['average_order_amount'], false, false, $system->settings->get('store_currency_code')); ?></td>
    </tr>
    <tr class="odd">
      <td nowrap="nowrap" align="left"><?php echo $system->language->translate('title_number_of_customers', 'Number of Customers'); ?>:</td>
      <td nowrap="nowrap" align="right"><?php echo (int)$stats['num_customers']; ?></td>
    </tr>
  </table>
</div>