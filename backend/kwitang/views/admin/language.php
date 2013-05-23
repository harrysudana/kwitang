<?php if ( ! defined ('FRONT_PATH')) exit ('CyberCMS Error...!');

$csrf_protection = $this->config->item('csrf_protection');
$csrf_token_name = $this->config->item('csrf_token_name');
$csrf_cookie_name = $this->config->item('csrf_cookie_name');

include 'header.php';
?>
<div class="page-header">
    <h2 class="pull-left"><?php echo lang('k_language');?></h2>
    <div class="clearfix"></div>
</div>

<div class="row-fluid">
    <div class="span4">
        <div class="form-horizontal">
            <div class="control-group">
                <p>Tambah Bahasa</p>
                <label class="control-label">Kode</label>
                <div class="controls">
                    <?php
                        echo form_input('lang_code', '', 'id="lang_code" class="input-mini"');
                    ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">Nama</label>
                <div class="controls">
                    <?php
                        echo form_input('lang_name', '', 'id="lang_name" class="input-medium"');
                    ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">Right to Left</label>
                <div class="controls">
                    <?php
                        echo form_radio('lang_rtl', '1', false, 'class="lang_rtl"').' Ya';
                        echo form_radio('lang_rtl', '0', true, 'class="lang_rtl"').' Tidak';
                    ?>
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <a href="#" onclick="addBahasa();" class="btn btn-primary"><i class="icon-plus icon-white"></i> Tambah Bahasa</a>
                </div>
            </div>
        </div>
    </div>
    <div class="span8">
        <?php
        $langs = kconfig ('system', 'langs');
        $langs_arr = json_decode($langs);
        ?>
        <table id="tdata" class="table table-striped table-bordered table-hover" cellpadding="0" cellspacing="0" border="0">
            <thead>
                <tr>
                    <th>Utama</th>
                    <th>Kode</th>
                    <th>Nama Bahasa</th>
                    <th>RTL</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $utama = kconfig ('system', 'lang_default', 'id');
                    if ( ! empty ($langs_arr)) {
                        foreach ($langs_arr as $lang) {
                            echo '<tr>'
                                .'  <td>'.($utama == $lang->code?'<i class="icon-ok icon-green"></i>':'').'</td>'
                                .'  <td>'.$lang->code.'</td>'
                                .'  <td>'.$lang->name.'</td>'
                                .'  <td>'.($lang->rtl?'Ya':'Tidak').'</td>'
                                .'</tr>';
                        }
                    }
                ?>
            </tbody>
        </table>
        <div class="clearfix"></div>
        <div id="setdefault-div" style="display:none">
            <a href="#" id="btn_default" class="btn btn-success">Jadikan Bahasa utama</a>
            <a href="#" id="btn_del" class="btn btn-danger">Hapus yang dipilih</a>
        </div>
    </div>
</div>

<script type="text/javascript">
var dtable;
<?php
    if ( ! empty ($langs_arr)) {
        echo 'var langs = JSON.parse(\''.$langs.'\');';
    } else {
        echo 'var langs = new Array();';
    }
?>

$(document).ready(function() {
    dtable = $('#tdata').dataTable({
        "bSort": true,
        "bPaginate": false,
        "bFilter" : false,
        "aaSorting": [[ 1, "asc" ]],
        "aoColumnDefs": [
            { "sWidth": "50", "bSortable": false, "sClass":"txt-center", "aTargets": [ 0 ] },
            { "sWidth": "80", "aTargets": [ 1 ] },
            { "sWidth": "80", "aTargets": [ 3 ] }
        ]
    });
    $("#tdata tbody").click(function(event) {
        $(dtable.fnSettings().aoData).each(function (){
            $(this.nTr).removeClass('row_selected');
        });
        $(event.target.parentNode).addClass('row_selected');
        $("#setdefault-div").fadeIn();
    });
    $("#btn_default").click(function() {
        var anSelected = fnGetSelected( dtable );
        if ( typeof anSelected[0] !== 'undefined') {
            var tmp = $(anSelected[0]).find('td');
            var lang_default_new =$(tmp[1]).text();

            $.post(
                '<?php echo site_url('admin/common_update'); ?>',
                {
                    <?php
                    if ($csrf_protection) {
                        echo '"'.$csrf_token_name.'" : getCookie("'.$csrf_cookie_name.'"),';
                    }
                    ?>
                    lang_default : lang_default_new
                },
                function(data) {
                    if (data.status) {
                        var aTrs = dtable.fnGetNodes();
                        for ( var i=0 ; i<aTrs.length ; i++ ) {
                            var val = '';
                            if ( $(aTrs[i]).hasClass('row_selected')) {
                                val = '<i class="icon-ok icon-green"></i>';
                            }
                            var tdx = $(aTrs[i]).find('td');
                            $(tdx[0]).html(val);
                        }
                        notify(data.message);
                    } else {
                        notify(data.message);
                    }
                }
            );
        } else {
            notify('Anda belum menentukan bahasa yang akan dijadikan Bahasa Utama<br>Klik pada tabel untuk memilih salah satu.');
        }
    });
    $("#btn_del").click(function() {
        var anSelected = fnGetSelected( dtable );
        if ( typeof anSelected[0] !== 'undefined') {
            var tmp           = $(anSelected[0]).find('td');
            var lang_code_del = $(tmp[1]).text();

            var index = 0;
            for (index in langs) {
                if (langs[index].code == lang_code_del) {
                    break;
                }
            }
            langs.splice(index,1);

            var post_data = {<?php
                                if ($csrf_protection) {
                                    echo '"'.$csrf_token_name.'" : getCookie("'.$csrf_cookie_name.'"),';
                                }
                            ?>};

            var langs_json = JSON.stringify(langs);
            if (langs_json == "[]") {
                post_data.lang_default = "";
                post_data.langs        = "";
            } else {
                post_data.langs = langs_json;
            }

            $.post(
                '<?php echo site_url('admin/common_update'); ?>',
                post_data,
                function(data) {
                    if (data.status) {
                        dtable.fnDeleteRow( anSelected[0] );
                        notify(data.message);
                    } else {
                        notify(data.message);
                    }
                }
            );
        } else {
            notify('Anda belum menentukan bahasa yang akan dihapus<br>Klik pada tabel untuk memilih salah satu.');
        }
    });
});

function addBahasa() {
    var lang_code = $('#lang_code').val().trim();
    var lang_name = $('#lang_name').val().trim();
    if (lang_code == '') {
        alert('Silakan masukkan kode Bahasa');
        $('#lang_code').focus();
        return;
    }
    if (lang_name == '') {
        alert('Silakan masukkan Nama Bahasa');
        $('#lang_name').focus();
        return;
    }
    for (i in langs) {
        if (langs[i].code == lang_code) {
            notify('Kode Bahasa sudah digunakan, silakan gunakan kode yang lain.');
            $('#lang_code').focus();
            return;
        }
    }

    lang_rtl = $('.lang_rtl:checked').val();
    lang_rtl = lang_rtl == 1 ? true : false;

    var newlang  = new Object();
    newlang.code = lang_code;
    newlang.name = lang_name;
    newlang.rtl  = lang_rtl;
    langs.push(newlang);
    var langs_json = JSON.stringify(langs);
    if (langs_json == "[]") {
        langs_json = "";
    }

    $.post(
        '<?php echo site_url('admin/common_update'); ?>',
        {
            <?php
            if ($csrf_protection) {
                echo '"'.$csrf_token_name.'" : getCookie("'.$csrf_cookie_name.'"),';
            }
            ?>
            langs : langs_json
        },
        function(data) {
            if (data.status) {
                lang_rtl_text = lang_rtl ? 'Ya' : 'Tidak';
                dtable.fnAddData(['',lang_code, lang_name, lang_rtl_text])
                notify(data.message);
                $('#lang_code').val('');
                $('#lang_name').val('');
            } else {
                notify(data.message);
            }
        }
    );
}

/* Get the rows which are currently selected */
function fnGetSelected( oTableLocal )
{
    var aReturn = new Array();
    var aTrs = oTableLocal.fnGetNodes();

    for ( var i=0 ; i<aTrs.length ; i++ )
    {
        if ( $(aTrs[i]).hasClass('row_selected') )
        {
            aReturn.push( aTrs[i] );
        }
    }
    return aReturn;
}
</script>

<?php
include 'footer.php';
