<?php if ( ! defined ('FRONT_PATH')) exit ('Kwitang ERROR..!!!');

include 'header.php';
?>
<div class="page-header">
    <h1>Log Pengguna (<?php echo $username; ?>)</h1>
</div>

<div class="row-fluid">
    <div class="span12">
    <table class="table table-condensed table-striped table-bordered table-hover dataTable">
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Perihal</th>
                <th>Aksi</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach ($userlog as $d) {
            $style = '';
            if ( $d->event == 'delete')
                $style = 'background-color: #FFC7C8;';
            if ( $d->event == 'add')
                $style = 'background-color: #C7FFCE;';
            if ( $d->event == 'update')
                $style = 'background-color: #DCDCFF;';
            echo '<tr style="'.$style.'">
                    <td width="120"><small>'.$d->timestamp.'</small></td>
                    <td width="100"><span class="tips" title="">'.$d->subject.'</span></td>
                    <td width="80"><span class="tips" title="">'.$d->event.'</span></td>
                    <td>' .$d->message.'</td>
                  </tr>';
        }
        ?>
        </tbody>
    </table>

    </div>
</div>

<div class="row-fluid">
    <div class="span12">
        <div class="pagination">
            <ul>
                <?php
                    echo $page_number == 1 ? '<li class="disabled">' : '<li>';
                    echo '<a href="'.site_url ('admin/user_log/'.$username .'/1').'">&laquo;</a></li>';
                    for ($i = 1 ; $i <= $total_page; $i++) {
                        echo ($i == $page_number) ? '<li class="active">' : '<li>';
                        echo '<a href="'.site_url ('admin/user_log/'.$username.'/'.$i).'"> '. $i .' </a>'
                            .'</li>';
                    }
                    echo $page_number == $total_page ? '<li class="disabled">' : '<li>';
                    echo '<a href="'.site_url ('admin/user_log/'.$username.'/'.$total_page).'">&raquo;</a></li>'
                ?>
            </ul>
        </div>
    </div>
</div>

<?php
include 'footer.php';
