<?php
include 'inc/header.php';
?>

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <?php
        echo '<div class="page-header">'
            .'  <h1>'.$content->title.'</h1>'
            .'  <small class="text-muted">'.kdate(from_gmt($content->pub_date)).' | '.$content->author.'</small>'
            .'</div>';

        if ( ! empty ($content->foto)) {
          echo '<div><center>'
              .'  <img src="'.base_url($content->foto).'" alt="">'
              .'</center></div>'
              .'<br>';
        }

        echo '<div>'.$content->body.'</div>';
        echo '<iframe width="100%" height="380" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?q='.$content->lat.','.$content->lng.'&amp;hl=id&amp;z=6&amp;output=embed"></iframe>';
        echo '<small><a href="https://maps.google.com/maps?q='.$content->lat.','.$content->lng.'&z=4" target="_blank"><span class="glyphicon glyphicon-map-marker"></span> Peta lebih besar &raquo;</a></small>';

      ?>
      <hr>
      <?php
        print_widget('sharer', array('data_text' => $content->title, 'data_url' => current_url()));
      ?>

      <h3><?php echo $current_sct->title.' lainnya:'; ?></h3>
      <?php
        $data = get_content_page($current_sct->name, array('item_perpage' => 6));
        if ( ! empty ($data['content']['data'])) {
          echo '<ul>';
          foreach ($data['content']['data'] as $content) {
            echo '<li><a href="'.content_url($current_sct, $content).'">'.$content->title.'</a></li>';
          }
          echo '</ul>';
        }
      ?>
      <br><br>
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
