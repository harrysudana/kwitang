<?php if ( ! defined ('FRONT_PATH')) exit ('Kwitang ERROR..!!!');

include 'header.php';
if ( ! empty ($folders_check)) {
    echo '<div class="row-fluid">'
        .'    <div class="span12">'
        .'        <div class="alert">'
        .'            <button type="button" class="close" data-dismiss="alert">&times;</button>'
        .'            <p>Silakan perbaiki permission pada folder berikut:</p>';
    foreach ($folders_check as $fc) {
        echo '            <span class="label label-important">'.$fc.'</span> ';
    }
    echo '        </div>'
        .'    </div>'
        .'</div>';
}
?>

<div id="helpScreen" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h2>Selamat datang di KwitangCMS</h2>
        <p>Berikut adalah panduan singkat KwitangCMS:</p>
    </div>
    <div class="modal-body">
        <div class="accordion" id="helpContent">
            <?php
                if (is_admin()) {
            ?>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#helpContent" href="#cBackend">
                        <h4>&raquo; Atur Data Konten</h4>
                    </a>
                </div>
                <div id="cBackend" class="accordion-body collapse">
                    <div class="accordion-inner">
                        <ol>
                            <li>
                                <h5>
                                    <a href="<?php echo site_url('admin/content_type'); ?>">Tipe-Konten</a>
                                    <i class="icon-info-sign tips pull-right" title="Modul yang berisi jenis konten, misal: Article, Photo (gallery), Video dll."></i>
                                </h5>
                                <p>Langkah pertama adalah mengaktifkan jenis konten yang akan disediakan pada website. </p>
                            </li>
                            <li>
                                <h5><a class="" href="<?php echo site_url('admin/structure'); ?>">Susun Arsitektur Informasi</a></h5>
                                <p>Di KwitangCMS Anda harus menuangkan Arsitektur Informasi yang digunakan pada Website dalam sebuah bentuk hirarki.</p>
                                <p>Pada setiap cabang Struktur yang Anda buat, Anda dapat mengaitkan satu tipe-konten atau lebih, serta menentukan bagaimana konten tersebut ditampilkan melalui <i>view file</i>.</p>
                            </li>
                            <li>
                                <h5><a class="" href="<?php echo site_url('admin/roles'); ?>">Hak Akses</a> (untuk Tim)</h5>
                                <p>Atur Tim Anda sesuai dengan bidangnya. Disini Anda dapat membuat grup pengguna dan mengatur hak akses terhadap bagian mana dari Structure yang telah Anda buat sebelumnya.</p>
                                <p>Selanjutnya Anda dapat menambahkan <a href="<?php echo site_url('admin/user'); ?>">Akun Pengguna</a> baru untuk masing-masing anggota Tim.</p>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#helpContent" href="#cFrontend">
                        <h4>&raquo; Sesuaikan Penampilan</h4>
                    </a>
                </div>
                <div id="cFrontend" class="accordion-body collapse">
                    <div class="accordion-inner">
                        <ul>
                            <li>
                                <h5><a class="" href="<?php echo site_url('admin/common'); ?>">Atur Penampilan Situs</a></h5>
                                <p>Ubah Nama situs, Slogan, Logo dan lain-lain disini</p>
                                <p>Anda juga dapat mengubah teks yang ditampilkan pada dashboard disini. Gunakan sebagai papan informasi, atau catatan tentang situs Anda.</p>
                            </li>
                            <li>
                                <h5><a class="" href="<?php echo site_url('admin/frontend_editor.'); ?>">Edit kode sumber tampilan</a></h5>
                                <p>Percanggih situs dengan menambahkan script Anda disini.</p>
                                <p>Dengan modal mengerti HTML, CSS dan PHP dasar, dan memahami bagaimanga KwitangCMS menggunakan file views dan widgets untuk membuat halaman. Anda dapat dengan mudah mengubah tampilan sesuai kebutuhan Anda.</p>
                                <p>
                                    <span class="label label-warning"> Perhatian! </span><br>
                                    <small>Kesalahan pada script yang ditambahkan dapat membuat situs tidak berguna.</small>
                                </p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <?php
                } // enf of is_admin
            ?>
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#helpContent" href="#cCreateContent">
                        <h4>&raquo; Buat Sebuah Konten</h4>
                    </a>
                </div>
                <div id="cCreateContent" class="accordion-body collapse">
                    <div class="accordion-inner">
                        <p>Konten pada situs ini di susun dalam sebuah Struktur. Struktur informasi disajikan sebagai menu di bagian atas pada setiap halaman.</p>
                        <p>Telusur bagian mana yang hendak Anda tambah/ubah kemudian klik menu tersebut untuk memilihnya. Anda akan dibawa ke halaman editor untuk Struktur informasi dimaksud.</p>
                        <p>Temukan panduan lebih lanjut pada halaman tersebut.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn">Close</a>
    </div>
</div>

<?php
$dashboard_text = kconfig ('system', 'dashboard_text');
if ( ! empty ($dashboard_text)) {
    echo '<div class="well">'.$dashboard_text.'</div>';
}

$CI =& get_instance();

$col_count = 0;
echo '  <div class="row-fluid">';

// ARTICLE
// -----------------------------------------------------------------------------
$is_installed = $CI->kwitang->ctIsInstalled('Article');
if ($is_installed) {
    $q = $CI->db->query('select s.name,s.structure_id,a.sct_id,a.id,a.title,a.slug,a.author,a.pub_date,
                        a.thumbnail,a.description,a.counter,a.lang
                        from ct_articles a
                        left join sct s on a.sct_id=s.id
                        right join structure st on s.structure_id=st.id
                        where s.id is not null and a.active=1
                        order by pub_date desc LIMIT 5');
    if ($q) {
        $col_count++;
        echo '  <div class="span6">
                    <h4>New Article:</h4>
                    <table class="table table-condensed table-hover">';
        foreach ($q->result() as $value) {
            echo '  <tr>
                        <td>
                            <small><i>'.kdate($value->pub_date).' - '.$value->author.' - hit: '.number_format($value->counter, 0).'</i></small>
                            <div class="pull-right">';
            if ( priv('approve', $value->structure_id)) {
                echo '<a href="'.site_url('admin/content/'.$value->structure_id.'/'.$value->sct_id.'/edit/'.$value->id).'" class="btn btn-mini"><i class="icon icon-pencil"></i></a>';
            }
            echo '              <a target="_blank" href="'.content_url($value->name, array($value->id, $value->slug)).'" class="btn btn-mini"><i class="icon icon-eye-open"></i></a>
                            </div>';
            if ( ! empty($value->thumbnail)) {
                echo '<img class="pull-left img-polaroid" style="margin: 0 8px 2px 0;width:114px" src="'.base_url($value->thumbnail).'" alt="">';
            }
            echo '          <h5>'.$value->title.'</h5>
                            <p>'.$value->description.'</p>
                        </td>
                    </tr>';
        }
        echo '      </table>
                </div>';
    }
}
echo (($col_count%2) == 0) ? '</div><div class="row-fluid">' : '';

// PHOTO
// -----------------------------------------------------------------------------
$is_installed = $CI->kwitang->ctIsInstalled('Photo');
if ($is_installed) {
    $q = $CI->db->query('select s.name,s.structure_id,a.sct_id,a.id,a.title,a.author,a.pub_date,
                        a.foto1,a.foto2,a.foto3,a.foto4,a.description,a.counter
                        from ct_photo a
                        left join sct s on a.sct_id=s.id
                        right join structure st on s.structure_id=st.id
                        where s.id is not null and a.active=1
                        order by pub_date desc LIMIT 5');
    if ($q) {
        $col_count++;
        echo '  <div class="span6">
                    <h4>New Photo:</h4>
                    <table class="table table-condensed table-hover">';
        foreach ($q->result() as $value) {
            /*
            $tmp = ! empty($value->foto1) ? '<img class="pull-left img-polaroid" style="margin: 0 8px 2px 0;width:104px" src="'.base_url($value->foto1).'" alt="">' : '';
            $tmp .= ! empty($value->foto2) ? '<img class="pull-left img-polaroid" style="margin: 0 8px 2px 0;width:104px" src="'.base_url($value->foto2).'" alt="">' : '';
            $tmp .= ! empty($value->foto3) ? '<img class="pull-left img-polaroid" style="margin: 0 8px 2px 0;width:104px" src="'.base_url($value->foto3).'" alt="">' : '';
            $tmp .= ! empty($value->foto4) ? '<img class="pull-left img-polaroid" style="margin: 0 8px 2px 0;width:104px" src="'.base_url($value->foto4).'" alt="">' : '';
            */
            echo '  <tr>
                        <td>
                            <small>'.kdate($value->pub_date).' - '.$value->author.' - hit: '.number_format($value->counter, 0).'</small>
                            <div class="pull-right">';
            if ( priv('approve', $value->structure_id)) {
                echo '<a href="'.site_url('admin/content/'.$value->structure_id.'/'.$value->sct_id.'/edit/'.$value->id).'" class="btn btn-mini"><i class="icon icon-pencil"></i></a>';
            }
            echo '              <a target="_blank" href="'.content_url($value->name, array($value->id)).'" class="btn btn-mini"><i class="icon icon-eye-open"></i></a>
                            </div>
                            <h5>'.$value->title.'</h5>
                            <p>'.$value->description.'</p>
                        </td>
                    </tr>';
        }
        echo '      </table>
                </div>';
    }
}
echo (($col_count%2) == 0) ? '</div><div class="row-fluid">' : '';


// NEW USER
// -----------------------------------------------------------------------------
$q = $CI->db->query('select * from user where created > \''.date ('Y-m-d', to_gmt() - (7*24*60*60)).'\' order by created desc LIMIT 5');
if ($q && $q->num_rows() > 0) {

    $col_count++;
    echo '  <div class="span6">
                <h4>New User <small><i>(Last 7 days)</i></small></h4>
                <table class="table table-condensed table-hover">
                    <tr>
                        <th>Registered</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Active</th>
                    </tr>';
    foreach ($q->result() as $value) {
        echo '<tr>
                <td><small>'.kdate($value->created).'</small></td>
                <td>'.((is_admin()) ? '<a href="'.site_url('admin/user_edit/'.$value->username).'">'.$value->fullname.'</a>' : $value->fullname).'</td>
                <td>'.$value->email.'</td>
                <td>'.($value->active==1 ? '<i class="icon icon-ok icon-green"></i>': '<i class="icon icon-remove icon-red"></i>').'</td>
              </tr>';
    }
    echo '      </table>
            </div>';
}
echo (($col_count%2) == 0) ? '</div><div class="row-fluid">' : '';


// LOGIN ACTIVITY
// -----------------------------------------------------------------------------
$q = $CI->db->query('SELECT * FROM (
    SELECT u.username,us.login_time,us.ip_address,us.last_activity, us.user_agent
    FROM user_session us
    LEFT JOIN user u ON us.user_id=u.id
    ORDER BY last_activity DESC
) t
GROUP BY t.username
ORDER BY last_activity DESC LIMIT 5');

if ($q) {
    $col_count++;
    echo '  <div class="span6">
                <h4>Login Activity</h4>
                <table class="table table-condensed table-hover">
                <tr>
                    <th>Username</th>
                    <th>IP Address</th>
                    <th>Last activity</th>
                    <th>Login time</th>
                </tr>';
    foreach ($q->result() as $value) {
        echo '<tr>
                <td><span class="tips" title="'.htmlentities($value->user_agent).'">'.$value->username.'</span></td>
                <td>'.$value->ip_address.'</td>
                <td>'.date('Y-m-d H:i:s', from_gmt($value->last_activity)).'</td>
                <td>'.date('Y-m-d H:i:s', from_gmt($value->login_time)).'</td>
              </tr>';
    }
    echo '      </table>
            </div>';
}

echo '</div>';

include 'footer.php';
