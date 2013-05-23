<?php

/**
 * Digunakan terutama untuk file download
 */
function cleanup_basename($filename) {
  $filename = basename($filename);
  $filename = str_replace('_', ' ', $filename);
  $filename = ucwords(strtolower($filename));

  return $filename;
}

function __listbox($sct_name, $jml_item = 6, $offset = 0) {

  $tmp_result = '';
  $tmp_sct = get_sct($sct_name);

  if ( ! empty ($tmp_sct)) {
    switch ($tmp_sct->content_type) {
      case 'Article'  : $tmp_data = get_content_page($sct_name, array('item_perpage'=>$jml_item, 'offset' => $offset));
                        $tmp_result = __listbox_article($tmp_data); break;
      case 'ArticleDoc': $tmp_data = get_content_page($sct_name, array('item_perpage'=>$jml_item, 'offset' => $offset));
                        $tmp_result = __listbox_articledoc($tmp_data); break;
      case 'Agenda'   : $tmp_data   = get_content_page($sct_name, array('item_perpage'=>$jml_item, 'offset' => $offset, 'orders' => array('date_start' => 'asc')));
                        $tmp_result = __listbox_agenda($tmp_data); break;
      case 'Photo'    : $tmp_data = get_content_page($sct_name, array('item_perpage'=>$jml_item, 'offset' => $offset));
                        $tmp_result = __listbox_photo($tmp_data); break;
      case 'Video'    : $tmp_data = get_content_page($sct_name, array('item_perpage'=>$jml_item, 'offset' => $offset));
                        $tmp_result = __listbox_video($tmp_data); break;
      case 'Link'     : $tmp_data = get_content_page($sct_name, array('item_perpage'=>$jml_item, 'offset' => $offset));
                        $tmp_result = __listbox_link($tmp_data); break;
    }
  }

  return $tmp_result;
}

function __listbox_article($tmp_data) {
  $tmp_result = ' <div class="panel panel-default">
                    <div class="panel-heading" style="position:relative">
                      <h3 class="panel-title">
                        <a href="'.index_url($tmp_data['sct']).'">'.$tmp_data['sct']->title.'</a>
                        <a href="'.index_url($tmp_data['sct']).'" style="position:absolute;right:15px"><span class="glyphicon glyphicon-link"></span></a>
                      </h3>

                    </div>
                    <div class="panel-body">
                      <ul class="media-list">';

  if ( ! empty ($tmp_data['content']['data'])) {
    foreach ($tmp_data['content']['data'] as $value) {
      $tmp_result .= '<li class="media">';

      $tmp_result .= '  <div class="media-body">
                          <h4 class="media-heading"><a href="'.content_url($tmp_data['sct'], $value).'">'.character_limiter($value->title, 68).'</a></h4>';
      if ( ! empty ($value->thumbnail)) {
        $tmp_result .= '<a class="pull-left" href="'.content_url($tmp_data['sct'], $value).'">
                          <img class="media-object" src="'.base_url($value->thumbnail).'" alt="" width="75">
                        </a>';
      }

      $tmp_result .= '    <small class="text-muted">'.kdate(from_gmt($value->pub_date)).' | dilihat '.number_format($value->counter, 0).' kali</small>
                          <p>'.$value->description.'</p>
                        </div>
                      </li>';
    }
  }

  $tmp_result .= '    </ul>
                    </div>
                  </div>';

  return $tmp_result;
}


/**
* Digunakan oleh __listbox_agenda
*/
function __date_str($str_date) {
  $_days = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
    //$_months = array('','Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');

  $_dstart = strtotime($str_date);
  $ds_day = date('w', $_dstart);
  $ds_month = date('n', $_dstart);

  $ds_str = '<div class="agenda-date">'
  .'  <div class="day">'.$_days[$ds_day].'</div> '
  .'  <div class="datemonth">'.date('d', $_dstart).'/'.date('m', $_dstart).'</div> '
    //.'  <div class="date">'.date('d', $_dstart).'</div> '
    //.'  <div class="month_str">'.$_months[$ds_month].'</div> '
    //.'  <div class="month">'.date('m', $_dstart).'</div> '
  .'  <div class="year">'.date('Y', $_dstart).'</div> '
  .'</div>';

  return $ds_str;
}


function __listbox_agenda($_pdata) {
  $tmp_result = ' <div class="panel panel-default">
                    <div class="panel-heading" style="position:relative">
                      <h3 class="panel-title">
                        <a href="'.index_url($_pdata['sct']).'">'.var_lang($_pdata['sct']->title).'</a>
                        <a href="'.index_url($_pdata['sct']).'" style="position:absolute;right:15px"><span class="glyphicon glyphicon-time"></span></a>
                      </h3>
                    </div>
                    <div class="panel-body">
                      <ul class="media-list">';
  if ( ! empty ($_pdata['content']['data'])) {
    foreach ($_pdata['content']['data'] as $value) {
      $_url = content_url($_pdata['sct'], $value);
      $tmp_result.= ' <li class="media">
                        <h4 class="media-heading"><a href="'.$_url.'">'.$value->title.'</a></h4>
                        <div class="media-body">
                          <small class="text-muted">'.kdate(from_gmt($value->pub_date)).' | dilihat '.number_format($value->counter, 0).' kali</small>
                          <div class="media-date">';

      if ($value->date_start == $value->date_end) {
        $tmp_result .= __date_str($value->date_start);
      } else {
        $_dstart = __date_str($value->date_start);
        $_dend   = __date_str($value->date_end);

        $tmp_result .= $_dstart.' '.$_dend;
      }

      $tmp_result.= '       <div class="agenda-time">'.$value->time.'</div>';
      $tmp_result.= '       <div class="agenda-venue">'.$value->venue.'</div>
                          </div>
                        </div>
                      </li>';
    }
  }
  $tmp_result .= '    </ul>
                    </div>
                  </div>';

  return $tmp_result;
}


function __listbox_link($tmp_data) {
  $tmp_result = ' <div class="panel panel-default">
                    <div class="panel-heading" style="position:relative">
                      <h3 class="panel-title">
                        <a href="'.index_url($tmp_data['sct']).'">'.$tmp_data['sct']->title.'</a>
                        <a href="'.index_url($tmp_data['sct']).'" style="position:absolute;right:15px"><span class="glyphicon glyphicon-random"></span></a>
                      </h3>
                    </div>
                    <div class="panel-body">
                      <ul>';

  if ( ! empty ($tmp_data['content']['data'])) {
    foreach ($tmp_data['content']['data'] as $value) {
      $tmp_result.= ' <li>
                        <a href="'.$value->url.'" target="_blank">'.$value->title.'</a>
                      </li>';
    }
  }
  $tmp_result .= '    </ul>
                    </div>
                  </div>';

  return $tmp_result;
}

function __listbox_photo($tmp_data) {
  $tmp_result = ' <div class="panel panel-default">
                    <div class="panel-heading" style="position:relative">
                      <h3 class="panel-title">
                        <a href="'.index_url($tmp_data['sct']).'">'.$tmp_data['sct']->title.'</a>
                        <a href="'.index_url($tmp_data['sct']).'" style="position:absolute;right:15px"><span class="glyphicon glyphicon-camera"></span></a>
                      </h3>
                    </div>
                    <div class="panel-body">
                      <ul class="media-list">';
  if ( ! empty ($tmp_data['content']['data'])) {
    foreach ($tmp_data['content']['data'] as $value) {
      $tmp_result .= '  <li class="media">
                          <h4 class="media-heading"><a href="'.content_url($tmp_data['sct'], $value).'">'.$value->title.'</a></h4>
                          <div class="media-body">';

      if ( ! empty ($value->foto1)) {
        $tmp_result .= '<a class="pull-left" href="'.content_url($tmp_data['sct'], $value).'">'
                      .'<img class="media-objecttanggal" src="'.base_url($value->foto1).'" alt="" width="75">'
                      .'</a>';
      }
      if ( ! empty ($value->foto2)) {
        $tmp_result .= '<a class="pull-left" href="'.content_url($tmp_data['sct'], $value).'">'
                      .'<img class="media-objecttanggal" src="'.base_url($value->foto2).'" alt="" width="75">'
                      .'</a>';
      }
      if ( ! empty ($value->foto3)) {
        $tmp_result .= '<a class="pull-left" href="'.content_url($tmp_data['sct'], $value).'">'
                      .'<img class="media-objecttanggal" src="'.base_url($value->foto3).'" alt="" width="75">'
                      .'</a>';
      }
      $tmp_result .= '      <div class="clearfix"></div>
                            <small class="text-muted">'.kdate(from_gmt($value->pub_date)).' | dilihat '.number_format($value->counter, 0).' kali</small>
                            <p>'.word_limiter($value->description, 14).'</p>
                          </div>
                        </li>';
    }
  }
  $tmp_result .= '    </ul>
                    </div>
                  </div>';

  return $tmp_result;
}

function __listbox_articledoc($tmp_data) {
  $tmp_result = ' <div class="panel panel-default">
                    <div class="panel-heading" style="position:relative">
                      <h3 class="panel-title">
                        <a href="'.index_url($tmp_data['sct']).'">'.$tmp_data['sct']->title.'</a>
                        <a href="'.index_url($tmp_data['sct']).'" style="position:absolute;right:15px"><span class="glyphicon glyphicon-download-alt"></span></a>
                      </h3>

                    </div>
                    <div class="panel-body">
                      <ul class="media-list">';

  if ( ! empty ($tmp_data['content']['data'])) {
    foreach ($tmp_data['content']['data'] as $value) {
      $tmp_result .= '<li class="media">';

      $tmp_result .= '  <div class="media-body">
                          <h4 class="media-heading"><a href="'.content_url($tmp_data['sct'], $value).'">'.character_limiter($value->title, 68).'</a></h4>';
      if ( ! empty ($value->thumbnail)) {
        $tmp_result .= '<a class="pull-left" href="'.content_url($tmp_data['sct'], $value).'">
                          <img class="media-object" src="'.base_url($value->thumbnail).'" alt="" width="75">
                        </a>';
      }

      $tmp_result .= '    <small class="text-muted">'.kdate(from_gmt($value->pub_date)).' | dilihat '.number_format($value->counter, 0).' kali</small>
                          <p>'.$value->description.'</p>
                        </div>
                      </li>';
    }
  }

  $tmp_result .= '    </ul>
                    </div>
                  </div>';

  return $tmp_result;
}

function __listbox_video($tmp_data) {
  $tmp_result = ' <div class="panel panel-default">
                    <div class="panel-heading" style="position:relative">
                      <h3 class="panel-title">
                        <a href="'.index_url($tmp_data['sct']).'">'.$tmp_data['sct']->title.'</a>
                        <a href="'.index_url($tmp_data['sct']).'" style="position:absolute;right:15px"><span class="glyphicon glyphicon-film"></span></a>
                      </h3>
                    </div>
                    <div class="panel-body">
                      <ul class="media-list">';
  if ( ! empty ($tmp_data['content']['data'])) {
    foreach ($tmp_data['content']['data'] as $value) {
      $tmp_result.= '<li class="media">';
      if ( ! empty ($value->youtube_id)) {
        $tmp_result.= '<iframe width="100%" height="240" src="http://www.youtube.com/embed/'.$value->youtube_id.'" frameborder="0" allowfullscreen="true"></iframe>';
      }
      $tmp_result.= '  <h4 class="media-heading"><a href="'.content_url($tmp_data['sct'], $value).'">'.$value->title.'</a></h4>';
    }
  }
  $tmp_result .= '    </ul>
                    </div>
                  </div>';

  return $tmp_result;
}
