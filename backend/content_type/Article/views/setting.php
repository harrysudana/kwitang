<div class="page-header">
    <h2>Setting "<?php echo $current_sct->title; ?>"</h2>
</div>

<div class="row-fluid">
    <div class="span12">
        <?php
        $act_url = 'admin/setting_ct/Article/setting_save/'.$current_structure->id.'/'.$current_sct->id;
        echo form_open(site_url($act_url));
        echo form_hidden('structure_id', $current_structure->id);
        echo form_hidden('sct_id', $current_sct->id);

        $langs = json_decode(kconfig ('system', 'langs'));
        $lang_options = array();
        foreach ($langs as $value) {
            $lang_options[$value->code] = $value->name;
        }

        if (! empty($sct_config)) {
            foreach ($sct_config as $key => $value) {
                if(substr($key, 0, 8) == 'feed_url') {
                    $feeds_url[substr($key, 9)] = $value;
                }
                if(substr($key, 0, 9) == 'feed_lang') {
                    $feeds_lang[substr($key, 10)] = $value;
                }
                if(substr($key, 0, 9) == 'feed_name') {
                    $feeds_name[substr($key, 10)] = $value;
                }
            }
        }

        echo '<table class="table table-condensed">
                <tr>
                    <th></th>
                    <th></th>
                    <th>RSS Feed URL</th>
                    <th>Bahasa</th>
                    <th>Sumber <i class="icon-question-sign tips" title="Situs web asal"></i></th>
                </tr>';
        for ($i= 1; $i<=12; $i++) {
            $val = ! empty($feeds_url[$i]) ? $feeds_url[$i] : '';
            echo '<tr>'
                .'  <td>' . $i . '</td>'
                .'  <td>:</td>'
                .'  <td>'.form_input('feed_url[' . $i . ']', $val, 'class="input-xxlarge" maxlength="255"') . '</td>'
                .'  <td>'.form_dropdown('feed_lang['.$i.']', $lang_options, (isset($feeds_lang[$i]) ? $feeds_lang[$i] : ''), 'class="input-medium"').'</td>'
                .'  <td>'.form_input('feed_name['.$i.']', (isset($feeds_name[$i]) ? $feeds_name[$i] :''), 'class="input-medium"').'</td>'
                .'</tr>';
        }
        echo '</table>';

        echo '  <div class="well">
                    <a href="' . site_url('admin/content/'
                        .$current_sct->structure_id . '/'
                        .$current_sct->id) . '" class="btn btn-link pull-left"> &laquo; Kembali ke ' . $current_sct->title . '</a>
                    <button type="submit" class="btn btn-primary pull-right"><span>Simpan</span></button>
                    <div class="clearfix"></div>
                </div>';
        echo form_close();

        ?>
    </div>
</div>
