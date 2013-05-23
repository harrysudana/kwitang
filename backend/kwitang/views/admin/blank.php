<?php if ( ! defined ('FRONT_PATH')) exit ('Kwitang ERROR..!!!');

include 'header.php';

function blank_ul($data) {
    $ret = '<ul>';
    foreach ($data as $s) {
        $ret.= '<li>'
            .'<a href="'.site_url('admin/content/'.$s->id).'">'.var_lang($s->title).'</a>';
        if ( ! empty ($s->childs)) {
            $ret.= blank_ul($s->childs);
        }
        $ret.= '</li>';
    }
    $ret.= '</ul>';
    return $ret;
}

if ( ! empty ($current_structure->childs)) {
    echo '<div class="page-header">'
        .'    <h2>Silakan Pilih Menu di Bawah Ini</h2>'
        .'</div>';
    echo '<div class="row-fluid">'
        .'    <div class="span12">';
    echo blank_ul($current_structure->childs);
    echo '    </div>'
        .'</div>';
} else {
    echo '<div class="page-header">'
        .'    <h2>Struktur masih kosong.</h2>'
        .'</div>';

}

include 'footer.php';
