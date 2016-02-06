<div id="category-tree">
  <h3><?php echo language::translate('title_categories', 'Categories'); ?></h3>
  <ul class="nav nav-pills nav-stacked">
<?php
  if (!function_exists('custom_draw_category_tree')) {
    function custom_draw_category_tree($categories, $indent=0) {
      foreach ($categories as $category) {
        echo '  <li class="category-'. $category['id'] . (!empty($category['active']) ? ' active' : '') .'"><a href="'. htmlspecialchars($category['link']) .'">'. $category['name'] .'</a>';
        if (!empty($category['subcategories'])) {
          echo '<ul class="nav nav-pills nav-stacked">' . PHP_EOL;
          echo PHP_EOL . custom_draw_category_tree($category['subcategories'], $indent+1);
          echo '</ul>' . PHP_EOL;
        }
        echo '  </li>' . PHP_EOL;
      }
      
    }
  }
  custom_draw_category_tree($categories);
?>
  </ul>
</div>