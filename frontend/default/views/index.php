<?php
include 'inc/header.php';
?>

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <?php
      $_sct_headline = kconfig ('system', 'headline');
      $_sct_headline_count = kconfig ('system', 'headline_count', 6);
      $_sct_headline_start = kconfig ('system', 'headline_start', 0);

      if ( ! empty ($_sct_headline)) {
        $_data = get_content_page($_sct_headline, array('item_perpage'=> $_sct_headline_count, 'offset' => $_sct_headline_start));

        if ( ! empty ($_data['content']['data'])) {
          $_indicator = '';
          $_item = '';
          $_i = 0;
          foreach ($_data['content']['data'] as $value) {
            $_indicator .= '<li data-target="#news-carousel" data-slide-to="'.$_i.'"'.($_i==0?' class="active"':'').'></li>';
            $_item .= '<div class="item '.($_i==0?'active ':'').'">'
                     .'  <img class="img-responsive" src="'.base_url($value->foto).'" alt="'.$value->foto_description.'">'
                     .'  <div class="carousel-caption">'
                     .'    <h4><a href="'.content_url($_data['sct'], $value).'">'.$value->title.'</a></h4>'
                     //.'    <p><small>'.kdate(from_gmt($value->pub_date)).' - '.$value->author.'</small></p>'
                     .'    <p>'.$value->description.'</p>'
                     .'  </div>'
                     .'</div>';
            $_i++;
          }
          echo '<div id="news-carousel" class="carousel slide" data-ride="carousel">
                  <ol class="carousel-indicators">'.$_indicator.'</ol>
                  <div class="carousel-inner">'.$_item.'</div>

                  <a class="left carousel-control" href="#news-carousel" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                  </a>
                  <a class="right carousel-control" href="#news-carousel" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right"></span>
                  </a>
                </div>';
        }
      }

      for ($i=1;$i<=8;$i+=2) {
        $j = $i+1;
        $list1      = kconfig ('system', 'listdata'.$i);
        $list1count = kconfig ('system', 'listdata'.$i.'_count', 3);
        $list1start = kconfig ('system', 'listdata'.$i.'_start', 0);
        $list2      = kconfig ('system', 'listdata'.$j);
        $list2count = kconfig ('system', 'listdata'.$j.'_count', 3);
        $list2start = kconfig ('system', 'listdata'.$j.'_start', 0);
        if ( ! empty ($list1) OR ! empty ($list2)) {
          echo '<div class="row">
                  <div class="col-md-6" style="margin-left:0">'.__listbox($list1, $list1count, $list1start).'</div>
                  <div class="col-md-6">'.__listbox($list2, $list2count, $list2start).'</div>
                </div>';
        }
      }
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
