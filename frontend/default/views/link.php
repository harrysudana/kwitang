<?php
include 'inc/header.php';
?>

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <?php
      echo '<div class="page-header">'
          .'<h1>'.var_lang($current_sct->title).'</h1>'
          .'</div>'
          .'<ul class="media-list">';

      foreach ($content['data'] as $value) {
        $link_kanan = '<a class="pull-right" href="'.content_url($current_sct, $value).'"><span class="glyphicon glyphicon-link"></span></a>';
        echo '<li class="media">';
        if ( ! empty ($value->image)) {
          echo '  <a class="pull-left" href="'.$value->url.'" target="_blank">
                    <img class="media-object" width="120" src="'.base_url($value->image).'" alt="">
                  </a>
                  <div class="media-body">
                    <h4 class="media-heading"><a href="'.$value->url.'" target="_blank">'.$value->title.'</a>'.$link_kanan.'</h4>
                    <p>'.$value->url.'</p>
                  </div>';
        } else {
          echo '  <h4 class="media-heading"><a href="'.$value->url.'" target="_blank">'.$value->title.'</a>'.$link_kanan.'</h4>';
        }

        echo '</li>';
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
