<?php if ( ! defined ('FRONT_PATH')) exit ('Kwitang ERROR..!!!');

include 'header.php';
?>
<div id="preview" style="border: 1px solid #ccc;">
    <div style="padding: 4px 12px; background-color: #bbbbbb;" class="form-inline">
        Theme: <?php
        echo '<select name="frontend" class="input-medium" id="frontend">';
        foreach ($frontends as $t) {
            echo '<option value="'.site_url ('app/preview/'.$t).'">'.$t.'</optiion>';
        }
        echo '</select>';
        ?>
        <input type="button" class="btn btn-info" value="Fullscreen" onclick="previewFull(this)">
    </div>
    <iframe id="iframePreview" src="<?php echo site_url ('app/preview/default'); ?>" width="100%" frameborder="0" style="height:450px;"></iframe>
</div>

<script type="text/javascript">
    $('#frontend').change(function() {
       $("#iframePreview").attr("src", $(this).val());
    });

    function previewFull(sender)
    {
        var wHe = $(window).height();

        if ( $("#preview").css('position') == 'absolute') {
            $("#preview").css({'position' : 'relative', 'z-index' : 1});
            $(sender).val('Fullscreen');
        } else {
            $("#preview").css({'position' : 'absolute', 'top': 0, 'left' : 0, 'right' : 0, 'bottom' : 0, 'z-index':9999});
            $("#iframePreview").css('height', (wHe - 60) + 'px');
            $(sender).val('Minimize');
        }
    }
</script>

<?php
include 'footer.php';
