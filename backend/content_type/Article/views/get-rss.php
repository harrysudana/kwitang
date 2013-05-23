<div class="page-header">
    <h2>Get Content</h2>
</div>

<div class="row-fluid">
    <div class="span12">
        <?php
            if ( ! empty( $result_feed)) {
                foreach ($result_feed as $r) {
                    echo '<h2>Ambil dari: ' . $r['feed_url'] . '</h2>';
                    if (empty( $r['success']) && empty( $r['failed'])) {
                        echo '<p>Tidak ada data.</p>';
                    } else {
                        echo '<ol>';
                        if ( ! empty( $r['success'])) {
                            foreach ($r['success'] as $s) {
                                echo '<li>'
                                    .'<span style="color: #00ff00">*baru</span> '
                                    . $s . '</li>';
                            }
                        }
                        if ( ! empty( $r['failed'])) {
                            foreach ($r['failed'] as $f) {
                                echo '<li>' . $f . '</li>';
                            }
                        }
                        echo '</ol>';
                    }

                    echo '<hr>';
                }
            } else {
                echo '<p>Tidak ada data yang dapat diambil, silakan cek <a href="'.site_url('admin/setting_ct/Article/setting/'.$current_sct->structure_id.'/'.$current_sct->id).'">setting.</p>';
            }

            echo '<br><hr>';
            echo '<a href="' . site_url('admin/content/'
                             . $current_sct->structure_id . '/'
                             . $current_sct->id) . '" class="btn btn-link"> &laquo; Kembali ke ' . $current_sct->title . '</a>';
        ?>
    </div>
</div>
