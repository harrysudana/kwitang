<?php
include 'inc/header.php';
?>

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <?php
      echo '<div class="page-header">'
          .'<h1>'.$current_sct->title.'</h1>'
          .'</div>'
          .'<ul class="media-list">';

      foreach ($content['data'] as $value) {
        echo '<li class="media">'
            .'  <h4><a href="'.content_url($current_sct, $value).'">'.$value->title.'</a></h4>'
            .'  <div class="pull-left" style="padding: 0 10px 10px 0;margin-bottom: 10px;border-bottom:1px dotted #ddd;;border-right:1px dotted #ddd;">';

        if ($value->date_start == $value->date_end) {
          $tgl = date('d-m-Y', strtotime($value->date_start));
          echo '<strong>'.$tgl.'</strong><br>';
        } else {
          $tgla = date('d-m-Y', strtotime($value->date_start));
          $tglb = date('d-m-Y', strtotime($value->date_start));
          echo '<strong>'.$tgla.' s/d '.$tglb.'</strong><br>';
        }

        if ( ! empty ($value->time)) echo $value->time.'<br>';
        if ( ! empty ($value->venue))  echo $value->venue.'<br>';

        echo '  </div>'
            .'  <div><p>'.$value->description.'</p></div>'
            .'  <div class="clearfix"></div>'
            .'</li>';
      }
      echo '</ul>';

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
