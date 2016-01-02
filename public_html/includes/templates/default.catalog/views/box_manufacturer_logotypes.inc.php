<ul id="manufacturer-logotypes" class="list-inline">
  <?php foreach ($logotypes as $logotype) { ?>
  <li>
    <a href="<?php echo htmlspecialchars($logotype['link']); ?>">
      <img src="<?php echo htmlspecialchars($logotype['image']); ?>" alt="" title="<?php echo htmlspecialchars($logotype['title']); ?>" style="margin: 0px 15px;">
    </a>
  </li>
  <?php } ?>
</ul>
