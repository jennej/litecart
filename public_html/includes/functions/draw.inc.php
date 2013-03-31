<?php

  function draw_img($image, $width, $height, $title, $params) {
    return '<img src="'. $image .'" width="'. $width .'" height="'. $height .'" border="0"'. ($params ? ' '.$params : '') .'/>';
  }
  
  function draw_listing_category($category) {
    global $system;
    
    $output = '<li class="category hover-light">' . PHP_EOL
            . '  <a class="link" href="'. $system->document->href_link(WS_DIR_HTTP_HOME .'category.php', array('category_id' => $category['id'])) .'">' . PHP_EOL
            . '    <div class="image" style="position: relative;">' . PHP_EOL
            . '    <img src="'. $system->functions->image_resample(FS_DIR_HTTP_ROOT . WS_DIR_IMAGES . $category['image'], FS_DIR_HTTP_ROOT . WS_DIR_CACHE, 340, 180, 'CROP') .'" width="340" height="180" alt="'. $category['name'] .'" title="'. $category['name'] .'" />' . PHP_EOL
            . '      <div class="footer" style="position: absolute; bottom: 0;">' . PHP_EOL
            . '        <div class="title">'. $category['name'] .'</div>' . PHP_EOL
            . '        <div class="description">'. $category['short_description'] .'</div>' . PHP_EOL
            . '      </div>' . PHP_EOL
            . '    </div>' . PHP_EOL
            . '  </a>' . PHP_EOL
            . '</li>' . PHP_EOL;
    
    return $output;
  }

  function draw_listing_manufacturer($manufacturer) {
    global $system;
    
    $output = '<li class="manufacturer hover-light">' . PHP_EOL
            . '  <a class="link" href="'. $system->document->href_link('manufacturer.php', array('manufacturer_id' => $manufacturer['id'])) .'">' . PHP_EOL
            . '    <div class="image" style="position: relative;">' . PHP_EOL
            . '      <img src="'. $system->functions->image_resample(FS_DIR_HTTP_ROOT . WS_DIR_IMAGES . $manufacturer['image'], FS_DIR_HTTP_ROOT . WS_DIR_CACHE, 215, 60, 'FIT_ONLY_BIGGER_USE_WHITESPACING') .'" width="215" height="60" alt="'. $manufacturer['name'] .'" title="'. $manufacturer['name'] .'" /><br />' . PHP_EOL
            . '    </div>' . PHP_EOL
            . '    <div class="title">'. $manufacturer['name'] .'</div>' . PHP_EOL
            . '  </a>' . PHP_EOL
            . '</li>' . PHP_EOL;
    
    return $output;
  }

  function draw_listing_product($product) {
    global $system;
    
    if ($product['campaign_price']) {
      $sticker = '<img src="'. WS_DIR_IMAGES .'stickers/campaign_48x48.png" width="48" height="48" alt="" title="'. $system->language->translate('title_on_sale', 'On Sale') .'" style="position: absolute; top: 10px; left: '. ($product['date_created'] > date('Y-m-d', strtotime('-1 month')) ? '30px' : '10px') .';" />';
    } else if ($product['date_created'] > date('Y-m-d', strtotime('-1 month'))) {
      $sticker = '<img src="'. WS_DIR_IMAGES .'stickers/new_48x48.png" width="48" height="48" alt="" title="'. $system->language->translate('title_new', 'New') .'" style="position: absolute; top: 0; left: 0;" />';
    } else {
      $sticker = '';
    }
    
    $output = '<li class="product hover-light" style="position: relative">' . PHP_EOL
            . '  <a class="link" href="'. $system->document->href_link(WS_DIR_HTTP_HOME . 'product.php', array('product_id' => $product['id']), array('category_id')) .'">' . PHP_EOL
            . '    <div class="image" style="position: relative;">'. PHP_EOL
            . '      <img src="'. $system->functions->image_resample(FS_DIR_HTTP_ROOT . WS_DIR_IMAGES . $product['image'], FS_DIR_HTTP_ROOT . WS_DIR_CACHE, 150, 150, 'FIT_USE_WHITESPACING') .'" width="150" height="150" alt="'. htmlspecialchars($product['name']) .'" title="'. htmlspecialchars($product['name']) .'" />' . PHP_EOL
            . '      ' . $sticker . PHP_EOL
            . '    </div>' . PHP_EOL
            . '    <div class="name">'. $product['name'] .'</div>' . PHP_EOL
            . '    <div class="manufacturer">'. $product['manufacturer_name'] .'</div>' . PHP_EOL
            . '    <div class="price">'. ($product['campaign_price'] ? '<s class="old-price">'. $system->currency->format($system->tax->calculate($product['price'], $product['tax_class_id'])) .'</s> <strong class="special-price">'. $system->currency->format($system->tax->calculate($product['campaign_price'], $product['tax_class_id'])) .'</strong>' : '<span class="price">'. $system->currency->format($system->tax->calculate($product['price'], $product['tax_class_id'])) .'</span>') .'</div>' . PHP_EOL
            . '  </a>' . PHP_EOL
            . (($product['image']) ? '  <a href="'. WS_DIR_IMAGES . $product['image'] .'" class="fancybox" data-fancybox-group="product-listing" title="'. htmlspecialchars($product['name']) .'"><img src="'. WS_DIR_IMAGES .'icons/16x16/preview.png" alt="" width="16" height="16" class="zoomable" style="position: absolute; top: 15px; right: 15px;" /></a>' . PHP_EOL : '')
          //. '  <div style="text-align: center;" class="buy_now">'.  $system->functions->form_draw_form_begin('buy_now_form') . $system->functions->form_draw_hidden_field('product_id', $product['id']) . $system->functions->form_draw_button('add_cart_product', $system->language->translate('title_add_to_cart', 'Add To Cart'), 'submit') . $system->functions->form_draw_form_end() .'</div>' . PHP_EOL
            . '</li>' . PHP_EOL;
    
    return $output;
  }
  
  function draw_fancybox($selector='a.fancybox', $params=array()) {
    global $system;
    
    $default_params = array(
      'hideOnContentClick' => true,
      'showCloseButton'    => true,
      'speedIn'            => 200,
      'speedOut'           => 200,
      'transitionIn'       => 'elastic',
      'transitionOut'      => 'elastic',
      'titlePosition'      => 'inside'
    );
    
    foreach (array_keys($default_params) as $key) {
      if (!isset($params[$key])) $params[$key] = $default_params[$key];
    }
    ksort($params);
    
    if (empty($system->document->snippets['head_tags']['fancybox'])) {
      $system->document->snippets['head_tags']['fancybox'] = '<script src="'. WS_DIR_EXT .'fancybox/jquery.fancybox-1.3.4.pack.js"></script>' . PHP_EOL
                                                           . '<link rel="stylesheet" href="{snippet:template_path}styles/fancybox.css" media="screen" />';
    }
    
    $system->document->snippets['javascript']['fancybox-'.$selector] = '  $(document).ready(function() {' . PHP_EOL
                                                                     . '    $("a").each(function() {' . PHP_EOL
                                                                     . '      $(this).attr("rel", $(this).attr("data-fancybox-group"));' . PHP_EOL
                                                                     . '    }); ' . PHP_EOL
                                                                     . '    $("a.fancybox").on("hover", function() { ' . PHP_EOL // Fixes ajax content
                                                                     . '      $'. ($selector ? '("'. $selector .'")' : '') .'.fancybox({' . PHP_EOL;
    
    foreach (array_keys($params) as $key) {
      switch (gettype($params[$key])) {
        case 'boolean':
          $system->document->snippets['javascript']['fancybox-'.$selector] .= '        "'. $key .'" : '. ($params[$key] ? 'true' : 'false') .',' . PHP_EOL;
          break;
        case 'integer':
          $system->document->snippets['javascript']['fancybox-'.$selector] .= '        "'. $key .'" : '. $params[$key] .',' . PHP_EOL;
          break;
        case 'string':
          $system->document->snippets['javascript']['fancybox-'.$selector] .= '        "'. $key .'" : "'. $params[$key] .'",' . PHP_EOL;
          break;
      }
    }
    
    $system->document->snippets['javascript']['fancybox-'.$selector] = rtrim($system->document->snippets['javascript']['fancybox-'.$selector], ','.PHP_EOL) . PHP_EOL;

    $system->document->snippets['javascript']['fancybox-'.$selector] .= '      });' . PHP_EOL
                                                                      . '    });' . PHP_EOL
                                                                      . '  });';
  }
  
  function draw_pagination($pages) {
    global $system;
    
    $pages = ceil($pages);
  
    if ($pages < 2) return false;
    
    if ($_GET['page'] < 2) $_GET['page'] = 1;
    
    if ($_GET['page'] > 1) $system->document->snippets['head_tags']['prev'] = '<link rel="prev" href="'. htmlspecialchars($system->document->link('', array('page' => $_GET['page']-1), true)) .'" />';
    if ($_GET['page'] < $pages) $system->document->snippets['head_tags']['next'] = '<link rel="next" href="'. htmlspecialchars($system->document->link('', array('page' => $_GET['page']+1), true)) .'" />';
    
    $link = $_SERVER['REQUEST_URI'];
    $link = preg_replace('/page=[0-9]/', '', $link);
    
    while (strstr($link, '&&')) $link = str_replace('&&', '&', $link);
    
    if (!strpos($link, '?')) $link = $link . '?';
    
    $html = '<nav class="pagination">'. PHP_EOL
          . '  <ul class="navigation-horizontal">' . PHP_EOL;
    
    if ($_GET['page'] > 1) {
      //$html .= '    <li><a class="page button" href="'. $system->document->href_link('', array('page' => 1), true) .'">'. $system->language->translate('title_first', 'First') .'</a></li>' . PHP_EOL;
      $html .= '    <li><a class="page button" href="'. $system->document->href_link('', array('page' => $_GET['page']-1), true) .'">'. $system->language->translate('title_previous', 'Previous') .'</a></li>' . PHP_EOL;
    } else {
      //$html .= '    <li><span class="page button disabled" href="'. $system->document->href_link('', array('page' => 1), true) .'">'. $system->language->translate('title_first', 'First') .'</span></li>' . PHP_EOL;
      $html .= '    <li><a class="page button disabled" href="'. $system->document->href_link('', array('page' => $_GET['page']-1), true) .'">'. $system->language->translate('title_previous', 'Previous') .'</a></li>' . PHP_EOL;
    }
    
    for ($i=1; $i<=$pages; $i++) {
      
      if ($i < $pages-5) {
        if ($i > 1 && $i < $_GET['page'] - 1 && $_GET['page'] > 4) {
          $rewind = round(($_GET['page']-1)/2);
          $html .= '    <li><a class="page button" href="'. $system->document->href_link('', array('page' => $rewind), true) .'">'. (($rewind == $_GET['page']-2) ? $rewind : '...') .'</a></li>' . PHP_EOL;
          $i = $_GET['page'] - 1;
          if ($i > $pages-4) $i = $pages-4;
        }
      }
      
      if ($i > 5) {  
        if ($i > $_GET['page'] + 1 && $i < $pages) {
          $forward = round(($_GET['page']+1+$pages)/2);
          $html .= '    <li><a class="page button" href="'. $system->document->href_link('', array('page' => $forward), true) .'">'. (($forward == $_GET['page']+2) ? $forward : '...') .'</a></li>' . PHP_EOL;
          $i = $pages;
        }
      }
    
      if ($i == $_GET['page']) {
        $html .= '    <li><span class="page button active">'. $i .'</span></li>' . PHP_EOL;
      } else {
        $html .= '    <li><a class="page button" href="'. $system->document->href_link('', array('page' => $i), true) .'">'. $i .'</a></li>' . PHP_EOL;
      }
    }
    
    if ($_GET['page'] < $pages) {
      $html .= '    <li><a class="page button" href="'. $system->document->href_link('', array('page' => $_GET['page']+1), true) .'">'. $system->language->translate('title_next', 'Next') .'</a></li>' . PHP_EOL;
      //$html .= '    <li><a class="page button" href="'. $system->document->href_link('', array('page' => $pages), true) .'">'. $system->language->translate('title_last', 'Last') .'</a></li>' . PHP_EOL;
    } else {
      $html .= '    <li><span class="page button disabled">'. $system->language->translate('title_next', 'Next') .'</span></li>' . PHP_EOL;
      //$html .= '    <li><span class="page button disabled">'. $system->language->translate('title_last', 'Last') .'</span></li>' . PHP_EOL;
    }
    
    $html .= '  </ul>'
           . '</nav>';
    
    return $html;
  }
  
?>