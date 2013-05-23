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
        $_url = content_url($current_sct, $value);
        echo '<li class="media media-dual media-dual-video">'
            .'  <h4 class="media-heading"><a href="'.$_url.'">'.$value->title.'</a></h4>';
        if ( ! empty ($value->youtube_id)) {
          echo '  <iframe width="100%" height="216" src="http://www.youtube.com/embed/'.$value->youtube_id.'" frameborder="0" allowfullscreen="true"></iframe>';
        } elseif ( ! empty ($value->video_file)){
          $ext = pathinfo($value->video_file, PATHINFO_EXTENSION);
          switch (strtolower($ext)) {
            case 'flv': $tmp_mime = 'video/x-flv'; break;
            default: $tmp_mime = 'video/mp4'; break;
          }
          echo '<video class="video-js vjs-default-skin" poster="'.base_url($value->image).'" width="100%" height="216" controls preload="none" data-setup="{}">
                  <source src="'.base_url($value->video_file).'" type="'.$tmp_mime.'" />
                </video>';
        }
        echo '  <div class="media-body">'
            .'    <small class="text-muted">'.kdate(from_gmt($value->pub_date)).' | '.$value->author.( ! empty ($value->counter) ? ' | dilihat '.number_format($value->counter, 0).' kali':'').'</small>'
            .'    <p>'.$value->description.'</p>'
            .'  </div>'
            .'</li>';
      }
      echo '</ul>
            <div class="clearfix"></div>';

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
      print_widget('sidebar');
      ?>
    </div>
  </div>
</div>

<?php
include 'inc/footer.php';
