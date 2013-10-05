<nav id="site-links">
  <ul class="list-horizontal">
<?php
  $pages_query = $system->database->query(
    "select p.id, pi.title from ". DB_TABLE_PAGES ." p
    left join ". DB_TABLE_PAGES_INFO ." pi on (p.id = pi.page_id and pi.language_code = '". $system->language->selected['code'] ."')
    where status
    and find_in_set('menu', dock)
    order by p.priority, pi.title;"
  );
  while ($page = $system->database->fetch($pages_query)) {
    echo '    <li><a href="'. $system->document->href_link(WS_DIR_HTTP_HOME . 'information.php', array('page_id' => $page['id'])) .'">'. $page['title'] .'</a></li>' . PHP_EOL;
  }
?>
    
    <?php if (empty($system->customer->data['id'])) { ?>
    <li><a href="<?php echo $system->document->link(WS_DIR_HTTP_HOME . 'login.php'); ?>"><?php echo $system->language->translate('title_login', 'Login'); ?></a></li>
    <?php } else { ?>
    <li><a href="<?php echo $system->document->link(WS_DIR_HTTP_HOME . 'order_history.php'); ?>"><?php echo $system->language->translate('title_order_history', 'Order History'); ?></a></li>
    <li><a href="<?php echo $system->document->link(WS_DIR_HTTP_HOME . 'edit_account.php'); ?>"><?php echo $system->language->translate('title_edit_account', 'Edit Account'); ?></a></li>
    <li><a href="<?php echo $system->document->link(WS_DIR_HTTP_HOME . 'logout.php'); ?>"><?php echo $system->language->translate('title_logout', 'Logout'); ?></a></li>
    <?php } ?>

    <li><a href="<?php echo $system->document->href_link(WS_DIR_HTTP_HOME . 'customer_service.php'); ?>"><?php echo $system->language->translate('title_customer_service', 'Customer Service'); ?></a></li>
  </ul>
</nav>