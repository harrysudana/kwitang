<?php
include 'inc/header.php';
?>

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <?php
      echo '<div class="page-header">'
        .'<h1>'.$current_sct->title.'</h1>'
        .'</div>';

      foreach ($content['data'] as $value) {
        $_url = content_url($current_sct, $value);
        echo '<div class="media">'
            .'  <h4><a href="'.$_url.'">'.$value->title.'</a></h4>'
            .'  <p class="aji-metadata">'
            .'    <small>'.kdate(from_gmt($value->pub_date)).' | '.$value->author.( ! empty ($value->counter) ? ' | Dibaca '.number_format($value->counter, 0).' kali':'').'</small>'
            .'  </p>'
            .'  <p>'.$value->description.'</p>'
            .'</div>';
      }

      $page_number  = isset ($page_number) ? $page_number : 1;
      $item_perpage = isset ($item_perpage) ? $item_perpage : kconfig ('system', 'item_perpage', 10);
      $current_page = $content['page_number'];
      $total_page   = $content['total_page'];

      $page_before = ($current_page < $total_page) ? '' : ' disabled';
      $page_after  = ($current_page > 1) ? '' : ' disabled';

      echo '<ul class="pager">
              <li class="previous'.$page_before.'"><a href="'.index_url($current_sct, $current_page + 1, $item_perpage).'">&laquo; Lama</a></li>
              <li>Halaman: '.$current_page.' / '.$total_page.'</li>
              <li class="next'.$page_after.'"><a href="'.index_url($current_sct, $current_page - 1, $item_perpage).'">Baru &raquo;</a></li>
            </ul>';

      ?>
    </div>
    <div class="col-md-4">
      <?php
      echo print_widget('sidebar');
      ?>
    </div>
  </div>
</div>

<?php
include 'inc/footer.php';
