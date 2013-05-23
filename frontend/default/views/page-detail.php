<?php
include 'inc/header.php';
?>

<div class="container">
  <div class="row">
    <div class="col-md-12">
      <?php
        echo '<div class="page-header">'
            .'<h1>'.$content->title.'</h1>'
            .'</div>';

        if ( ! empty ($content->foto)) {
          echo '<div><center>'
              .'  <img src="'.base_url($content->foto).'" alt="">'
              .'</center></div>'
              .'<hr>';
        }

        echo '<div>'.$content->body.'</div>';
      ?>
      <hr>
      <?php
        print_widget('sharer', array('data_text' => $content->title, 'data_url' => current_url()));
      ?>
      <br><br>
    </div>
  </div>
</div>

<?php
include 'inc/footer.php';
