<?php
include 'inc/header.php';

echo '  <div class="container">
            <div class="page-header">
                <h1>Hasil Pencarian: "'.$_GET['q'].'"</h1>
            </div>
            <div class="row">
                <div class="col-md-12">';
if ( ! empty ($result['Article']['data'])) {
    echo '<ul class="media-list">';
    foreach ($result['Article']['data'] as $value) {
        echo '<li class="media">'
                .'  <h4 class="media-heading"><a href="'.content_url($value->sct_name, $value).'">'.$value->title.'</a></h4>';
        if ( ! empty ($value->thumbnail)) {
            echo '<a class="pull-left" href="'.content_url($value->sct_name, $value).'">'
                .'<img class="media-object" src="'.base_url($value->thumbnail).'" alt="" width="75">'
                .'</a>';
        }
        echo '<div class="media-body">'
            .'  <small class="text-muted">'.kdate(from_gmt($value->pub_date)).'</small>'
            .'  <p>'.word_limiter($value->description, 14).'</p>'
            .'</div>'
            .'</li>';
    }
    echo '</ul>';
} else {
    echo '<p>Tidak menemukan hasil.</p>';
}
echo '          </div>
            </div>
        </div>';


include 'inc/footer.php';
