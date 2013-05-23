<?php
include 'inc/header.php';
?>

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <?php
        echo '<div class="page-header">'
            .'<h1>'.$content->title.'</h1>'
            .'  <small class="text-muted">'.kdate(from_gmt($content->pub_date)).' | '.$content->author.( ! empty ($content->counter) ? ' | Dibaca '.number_format($content->counter, 0).' kali':'').'</small>'
            .'</div>';

        if ($content->date_start == $content->date_end) {
          $tanggal = kdate(strtotime($content->date_start));
        } else {
          $tanggal = kdate(strtotime($content->date_start))
                          .' - '
                          .kdate(strtotime($content->date_end));
        }
        echo '<table class="table table-striped">'
            .'  <tr>'
            .'    <th>Tanggal / <small>Date</small></th>'
            .'    <td>'.$tanggal.'</td>'
            .'  </tr>'
            .'  <tr>'
            .'    <th>Waktu / <small>Time</small></th>'
            .'    <td>'.$content->time.'</td>'
            .'  </tr>'
            .'  <tr>'
            .'    <th>Lokasi / <small>Venue</small></th>'
            .'    <td>'.$content->venue.'</td>'
            .'  </tr>'
            .'</table>';

        echo '<div>'.$content->body.'</div>';
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
        foreach ($data['content']['data'] as $value) {
          echo '<li><a href="'.content_url($current_sct, $value).'">'.$value->title.'</a></li>';
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
