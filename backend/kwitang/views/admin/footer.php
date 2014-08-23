<?php if ( ! defined ('FRONT_PATH')) exit ('Kwitang ERROR..!!!'); ?>
    <div class="row-fluid">
        <div class="footer">
            <?php
                $foot_text = kconfig ('system', 'admin_footer_text');
                if ( empty ($foot_text)) {
                    $foot_text = lang ('k_admin_footer_text');
                }

                echo $foot_text;
            ?>
        </div>
    </div>

</div> <!-- end of .container -->

</body>
</html>
