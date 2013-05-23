<div class="row">
  <div class="col-md-3">
    <h4 class="text-right">Bagikan :</h4>
  </div>
  <div class="col-md-9">
    <ul class="nav nav-tabs">
      <?php
        $fb_appid = kconfig ('system', 'fb_appid');
        if ( ! empty ($fb_appid)) {
          echo '<li style="padding:12px 12px 0 0">
                  <div class="fb-share-button" data-href="'.$data_url.'" data-type="button_count"></div>
                </li>';
        } else {
          echo '  <li>
                    <a href="#" onclick="window.open(\'https://www.facebook.com/sharer/sharer.php?u=\'+encodeURIComponent(location.href), \'facebook-share-dialog\', \'width=626,height=436\'); return false;">
                      <img src="'.asset_url('img/fb-bagikan.png').'" alt="">
                    </a>
                  </li>';
        }
      ?>
      <li style="padding: 12px 0 0;">
        <a href="https://twitter.com/share" class="twitter-share-button" data-text="<?php echo substr($data_text, 0, 139); ?>" data-lang="id">Tweet</a>
        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
      </li>
    </ul>
  </div>
</div>
