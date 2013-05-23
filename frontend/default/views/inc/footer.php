

  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="bg-primary" style="height:8px;margin-bottom:-2px"></div>
        <div class="panel panel-default">
          <div class="panel-body">
            <?php echo kconfig ('system', 'footnotes'); ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="<?php echo asset_url('js/vendor/jquery-1.10.2.min.js'); ?>"><\/script>')</script>
  <script src="<?php echo asset_url('js/vendor/bootstrap.min.js'); ?>"></script>
  <script src="<?php echo asset_url('js/main.js'); ?>"></script>

  <?php
  $tracking_code = kconfig ('system', 'tracking_code');
  if ( ! empty ($tracking_code)) {
    echo '<script type="text/javascript">'.$tracking_code.'</script>';
  }
  ?>
</body>
</html>
