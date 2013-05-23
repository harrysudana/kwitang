<?php

function sct_dropdown($select_name, $structure_array, $select_value = '', $select_attr = '', $deep = 1)
{
    if ($deep == 1) {
        $retval = '<select name="'.$select_name.'" '.$select_attr.'>'
                 .'<option value="">...</option>';
    } else {
        $retval = '';
    }

    if (! empty($structure_array)) {
        foreach ($structure_array as $structure_item) {
            $space_left = str_repeat('&nbsp;', (($deep-1)*3));
            $retval.= '<optgroup label="'.$space_left.'&rArr; '.var_lang($structure_item->title).'">';
            $scts = get_structure_sct($structure_item->id);
            if (! empty($scts)) {
                foreach ($scts as $sct_item) {
                    $selected = '';
                    if ($sct_item->name == $select_value) {
                        $selected = ' selected="selected"';
                    }
                    $retval.= '<option value="'.$sct_item->name.'"'.$selected.'>'.$space_left.var_lang($sct_item->title).' ('.$sct_item->content_type.')</option>';
                }
            }
            if (! empty($structure_item->childs)) {
                $retval.= sct_dropdown($select_name, $structure_item->childs, $select_value, $select_attr, $deep+1);
            }
            $retval.= '</optgroup>';
        }
    }

    if ($deep == 1) {
        $retval.= '</select>';
    }

    return $retval;
}

if (empty($structure) or ! is_array($structure)) {
    $structure = get_structure();
}

?>

<ul class="nav nav-tabs">
    <li class="active"><a href="#home" data-toggle="tab">Home</a></li>
    <li><a href="#sidebar" data-toggle="tab">Sidebar</a></li>
    <li><a href="#footer" data-toggle="tab">Footer</a></li>
    <li><a href="#lainnya" data-toggle="tab">Lainnya</a></li>
</ul>

<div class="tab-content">
    <div class="tab-pane active" id="home">
        <h3>Halaman Beranda</h3>
        <div class="control-group">
            <label class="control-label">Headline</label>
            <div class="controls">
                <?php
                echo sct_dropdown('def_fe[headline]', $structure, kconfig('def_fe', 'headline'), 'id="headline" data-placement="left" class="tips" title="headline"')
                .' Jml:'
                .form_input('def_fe[headline_count]', kconfig('def_fe', 'headline_count', 6), 'id="headline_count" data-placement="left" class="tips input-digit" data-min="1" title="headline_count"')
                .' Start:'
                .form_input('def_fe[headline_start]', kconfig('def_fe', 'headline_start', 0), 'id="headline_start" data-placement="left" class="tips input-digit" data-min="0" title="headline_start"');
                ?>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label">Content Box</label>
            <div class="controls">
                <table class="table">
                    <?php
                    for ($i=1; $i<9; $i+=2) {
                        $j = $i+1;
                        echo '<tr>
                        <td>'
                        .sct_dropdown('def_fe[listdata'.$i.']', $structure, kconfig('def_fe', 'listdata'.$i), 'id="listdata'.$i.'" data-placement="left" class="tips" title="listdata'.$i.'"')
                        .' <small>Jml:</small> '.form_input('def_fe[listdata'.$i.'_count]', kconfig('def_fe', 'listdata'.$i.'_count', 3), 'id="listdata'.$i.'_count" data-placement="left" class="tips input-digit" data-min="1" title="listdata'.$i.'_count"')
                        .' <small>Start:</small> '.form_input('def_fe[listdata'.$i.'_start]', kconfig('def_fe', 'listdata'.$i.'_start', 0), 'id="listdata'.$i.'_start" data-placement="left" class="tips input-digit" data-min="0" title="listdata'.$i.'_start"').'
                        </td>
                        <td>'
                        .sct_dropdown('def_fe[listdata'.$j.']', $structure, kconfig('def_fe', 'listdata'.$j), 'id="listdata'.$j.'" data-placement="left" class="tips" title="listdata'.$j.'"')
                        .' <small>Jml:</small> '.form_input('def_fe[listdata'.$j.'_count]', kconfig('def_fe', 'listdata'.$j.'_count', 3), 'id="listdata'.$j.'_count" data-placement="left" class="tips input-digit" data-min="1" title="listdata'.$j.'_count"')
                        .' <small>Start:</small> '.form_input('def_fe[listdata'.$j.'_start]', kconfig('def_fe', 'listdata'.$j.'_start', 0), 'id="listdata'.$j.'_start" data-placement="left" class="tips input-digit" data-min="0" title="listdata'.$j.'_start"').'
                        </td>
                        </tr>';
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>

    <div class="tab-pane" id="sidebar">
        <h3>Sidebar (Kanan)</h3>
        <p>Tampil di semua halaman.</p>

        <?php
        for ($i=1; $i<5; $i++) {
            echo '<p>Nama SCT: '.sct_dropdown('def_fe[sidelist'.$i.']', $structure, kconfig('def_fe', 'sidelist'.$i), 'id="sidelist'.$i.'" data-placement="left" class="tips" title="sidelist'.$i.'"')
            .' Jml: '.form_input('def_fe[sidelist'.$i.'_count]', kconfig('def_fe', 'sidelist'.$i.'_count', 3), 'id="sidelist'.$i.'_count" data-placement="left" class="tips input-digit" data-min="1" title="sidelist'.$i.'_count"')
            .' Start: '.form_input('def_fe[sidelist'.$i.'_start]', kconfig('def_fe', 'sidelist'.$i.'_start', 0), 'id="sidelist'.$i.'_start" data-placement="left" class="tips input-digit" data-min="0" title="sidelist'.$i.'_start"')
            .'</p>'
            .form_textarea('def_fe[side_html'.$i.']', kconfig('def_fe', 'side_html'.$i), 'id="side_html'.$i.'" class="simple-editor tips" title="side_html'.$i.'"')
            .'<br>';
        }
        ?>
    </div>

    <div class="tab-pane" id="footer">
        <h3>Footer</h3>
        <?php
        echo form_textarea('def_fe[footnotes]', kconfig('def_fe', 'footnotes'), 'class="editor"');
        ?>
    </div>

    <div class="tab-pane" id="lainnya">
        <h3>Lainnya</h3>

        <div class="control-group">
            <label class="control-label">CSS Style</label>
            <div class="controls">
                <?php
                $_options = array(
                    '' => 'Bootstrap Default',
                    'amelia' => 'Amelia',
                    'cerulean' => 'Cerulen',
                    'cosmo' => 'Cosmo',
                    'cupid' => 'Cupid',
                    'cyborg' => 'Cyborg',
                    'flatly' => 'Flatly',
                    'journal' => 'Journal',
                    'lumen' => 'Lumen',
                    'readable' => 'Readable',
                    'simplex' => 'Simplex',
                    'slate' => 'Slate',
                    'spacelab' => 'Spacelab',
                    'superhero' => 'Superhero',
                    'united' => 'United',
                    'yeti' => 'Yeti'
                    );
                echo form_dropdown('def_fe[css_style]', $_options, kconfig('def_fe', 'css_style', 'united'));
                ?>
            </div>
            <small>*Menggunakan css style dari <a href="http://bootswatch.com">bootswatch.com</a></small>
        </div>
        <div class="control-group">
            <label class="control-label">Page Max-Width</label>
            <div class="controls">
                <?php
                echo form_input('def_fe[default_maxwidth]', kconfig('def_fe', 'default_maxwidth', 'auto'), 'id="default_maxwidth" data-placement="left" class="tips input-mini" title="default_maxwidth"');
                ?>
                px&nbsp;&nbsp;<small>(ex: 970, default=auto)</small>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">Navbar Style</label>
            <div class="controls">
                <?php
                $navbar = kconfig('def_fe', 'default_navbar', 'navbar-default');
                if ($navbar == 'navbar-default') {
                    echo '<input type="radio" name="def_fe[default_navbar]" value="navbar-default" checked="checked" /> Default ';
                    echo '<input type="radio" name="def_fe[default_navbar]" value="navbar-inverse" /> Inverse';
                } else {
                    echo '<input type="radio" name="def_fe[default_navbar]" value="navbar-default" /> Default ';
                    echo '<input type="radio" name="def_fe[default_navbar]" value="navbar-inverse" checked="checked" /> Inverse';
                }
                ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">SearchBox</label>
            <div class="controls">
                <strong>Top: </strong>
                <?php
                $searchbox = kconfig('def_fe', 'default_search_top', 'no');
                if ($searchbox == 'yes') {
                    echo '<input type="radio" name="def_fe[default_search_top]" value="yes" checked="checked" /> Yes ';
                    echo '<input type="radio" name="def_fe[default_search_top]" value="no" /> No';
                } else {
                    echo '<input type="radio" name="def_fe[default_search_top]" value="yes" /> Yes ';
                    echo '<input type="radio" name="def_fe[default_search_top]" value="no" checked="checked" /> No';
                }
                ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Navbar: </strong>
                <?php
                $searchbox = kconfig('def_fe', 'default_search_nav', 'yes');
                if ($searchbox == 'yes') {
                    echo '<input type="radio" name="def_fe[default_search_nav]" value="yes" checked="checked" /> Yes ';
                    echo '<input type="radio" name="def_fe[default_search_nav]" value="no" /> No';
                } else {
                    echo '<input type="radio" name="def_fe[default_search_nav]" value="yes" /> Yes ';
                    echo '<input type="radio" name="def_fe[default_search_nav]" value="no" checked="checked" /> No';
                }
                ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">Panel border</label>
            <div class="controls">
                <?php
                $panel = kconfig('def_fe', 'default_panel', '');
                if (empty($panel)) {
                    echo '<input type="radio" name="def_fe[default_panel]" value="" checked="checked" /> Default ';
                    echo '<input type="radio" name="def_fe[default_panel]" value="panel-noborder" /> No Border';
                } else {
                    echo '<input type="radio" name="def_fe[default_panel]" value="" /> Default ';
                    echo '<input type="radio" name="def_fe[default_panel]" value="panel-noborder" checked="checked" /> No Border';
                }
                ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">CSS Script</label>
            <div class="controls">
                <?php
                echo form_textarea('def_fe[default_css_override]', kconfig('def_fe', 'default_css_override', ''), 'id="default_css_override" style="width:90%"');
                ?>
            </div>
        </div>
        <hr>

        <div class="control-group">
            <label class="control-label">Facebook AppID</label>
            <div class="controls">
                <?php
                echo form_input('def_fe[fb_appid]', kconfig('def_fe', 'fb_appid'), 'id="fb_appid" data-placement="left" class="tips input-medium" title="fb_appid"');
                ?>
            </div>
        </div>

    </div>
</div>

<br>
<div class="well">
    <strong>Start:</strong> Indeks awal data yang akan ditampilkan, dimulai dengan 0 (nol) untuk data pertama <br>
    <strong>Jml:</strong> Jumlah data yang ditampilkan
</div>

