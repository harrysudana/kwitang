<?php if ( ! defined ('FRONT_PATH')) exit ('Kwitang ERROR..!!!');

$js_files = array(asset_url ('ace/ace.js'),
                  asset_url ('ace/theme-dawn.js'),
                  asset_url ('ace/mode-php.js'),
                  asset_url ('ace/mode-javascript.js'),
                  asset_url ('ace/mode-css.js'),
                  asset_url ('filetree/jqueryFileTree.js'));
$css_files = array(asset_url ('filetree/jqueryFileTree.css'));

$csrf_protection = $this->config->item('csrf_protection');
$csrf_token_name = $this->config->item('csrf_token_name');
$csrf_cookie_name = $this->config->item('csrf_cookie_name');

include 'header.php';
?>
<style type="text/css" media="screen">
    .editor {
        margin: 0;
        position: relative;
        height: 440px;
        width: 100%;
        padding: 0;
    }
    #myTab {
        margin-bottom: 2px;
    }
    #info {
        display: none;
        position: fixed;
        top: 80px;
        right: 20px;
    }
</style>

<div class="page-header">
    <h1>FrontEnd: <?php echo ucfirst(kconfig ('system', 'frontend', 'default')); ?></h1>
</div>

<div class="row-fluid">
    <div class="span3">
        <?php
        // tanpa ada style disini, behavior ace editor aneh, isi file ter-load namun tidak nampil,
        // ketika klik kanan inspect element baru dia muncul, dan jika dillihat di source code,
        // isi ace editor ditambahkan tepat sebelum </body>
        ?>
        <div id="folderTheme" style="background-color: #F5F5F5;border: 1px solid #aaa; padding: 10px;"></div>
    </div>

    <div class="span8">
        <ul class="nav nav-tabs" id="myTab"></ul>
        <div class="tab-content" id="tab_content"></div>
        <p><i class="icon-info-sign"></i> Please select file from the left menu.</p>
    </div>
</div>

<div id="info" class="alert">
    <span id="info-text">Loading...</span>
</div>

<?php
    $path = kconfig ('system', 'frontend', 'default');
    $path = str_replace('\\', '/', $path);
    $path = $path.'/';
?>


<link rel="stylesheet" href="<?php echo asset_url ('filetree/jqueryFileTree.css'); ?>">
<script type="text/javascript" href="<?php echo asset_url ('filetree/jqueryFileTree.js'); ?>"></script>
<script type="text/javascript">
    var editor;
    var aced = new Array();
    var aced_num = 0;

    $(document).ready( function() {

        $("#folderTheme").fileTree({
            <?php
                echo '"root": "'.$path.'",'
                    .'"script": "'.site_url ('admin/filetree').'"';
                if ($csrf_protection) {
                    echo ',"csrf_token_name": "'.$csrf_token_name.'"'
                        .',"csrf_cookie_name": "'.$csrf_cookie_name.'"';
                }
            ?>
        }, function(file) {
            loadFile(file);
        });

        $("#info").click(function() { $(this).fadeOut(); });
    });

    function loadFile(file)
    {
        var newtab = true;

        // cek apakah sudah dibuka
        var i = 0;
        $('#myTab li').each(function() {
            if (file == $(this).find('.tips').attr('title')) {
                $('#myTab li:eq(' + i + ') a').tab('show');
                newtab = false;
            }
            i = i + 1;
        });

        if (newtab == true) {
            $("#info-text").text('Loading file ' + basename(file));
            $("#info").fadeIn();
            $.post("<?php echo site_url ('admin/file_get'); ?>", {
                    'filename' : file
                    <?php
                        if ($csrf_protection) {
                            echo ',"'.$csrf_token_name.'" : getCookie("'.$csrf_cookie_name.'")';
                        }
                    ?>
                },
                function(data) {
                    $('#myTab').append('<li><a data-toggle="tab" href="#acetab_' + aced_num + '"><span class="tips" title="' + file + '">' + basename(file) + '</span></a></li>');
                    $('#tab_content').append('<div class="tab-pane" id="acetab_' + aced_num +'">'
                                           + '<?php echo str_replace("\n",'',form_open( site_url ('admin/file_save'))); ?>'
                                           + '<input type="hidden" name="filename" class="filename" value="' + file + '">'
                                           + '<div id="editor' + aced_num + '" class="editor" rel="' + aced_num + '"></div>'
                                           + '<p>Nama File: ' + file + '</p>'
                                           + '<input type="button" value=" Simpan File " class="btn btn-primary" onclick="simpanFile(this)">'
                                           + '<?php echo form_close(); ?>'
                                           + '</div>');

                    aced[aced_num] = ace.edit("editor" + aced_num);
                    aced[aced_num].setTheme("ace/theme/dawn");
                    aced[aced_num].getSession().setTabSize(4);
                    aced[aced_num].getSession().setUseSoftTabs(true);
                    aced[aced_num].setReadOnly(false);
                    ext = file.substr(file.lastIndexOf('.') + 1);

                    if(ext == 'php') {
                        var PhpMode = require("ace/mode/php").Mode;
                        aced[aced_num].getSession().setMode(new PhpMode());
                    } else if(ext == 'js') {
                        var JavascriptMode = require("ace/mode/javascript").Mode;
                        aced[aced_num].getSession().setMode(new JavascriptMode());
                    } else if(ext == 'css') {
                        var CssMode = require("ace/mode/css").Mode;
                        aced[aced_num].getSession().setMode(new CssMode());
                    }

                    aced[aced_num].getSession().setValue(data);

                    // activate
                    $('#myTab li:eq(' + aced_num + ') a').tab('show');

                    aced_num = aced_num + 1;

                    $("#info").animate({'opacity':1}, 1000).fadeOut('slow');
                }
          );
        }
    }

    function simpanFile(btn)
    {
        var frm = $(btn).parent();

        var f_action = frm.attr('action');
        var filename = frm.find('.filename').val();
        var num      = frm.find('.editor').attr('rel');
        var content  = aced[num].getSession().getValue();

        $("#info-text").text('Proses menyimpan berkas: ' + basename(filename));
        $("#info").fadeIn();
        $.post(f_action,
            {
                "filename": filename,
                "file_content": content
                <?php
                    if ($csrf_protection) {
                        echo ',"'.$csrf_token_name.'" : getCookie("'.$csrf_cookie_name.'")';
                    }
                ?>
            },
            function(data) {
                $("#info-text").text('');
                $("#info").hide();
                notify(basename(filename) + ': ' + data);
            }
      );
    }

    function basename(path)
    {
        return path.replace(/\\/g,'/').replace( /.*\//, '');
    }

    window.onbeforeunload = function() {
        // TODO: cek semua editor, jika ada yang diubah dan belum disimpan, cegah untuk menutup halaman
        return "Tutup Halaman Editor ?";
    }
</script>

<?php
include 'footer.php';
