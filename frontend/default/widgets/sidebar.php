<?php

for ($i=1;$i<=4;$i++) {
  $_side = kconfig ('system', 'sidelist'.$i);
  $_side_count = kconfig ('system', 'sidelist'.$i.'_count');

  if ( ! empty ($_side)) {
    echo __listbox($_side, $_side_count);
  }

  if ($i == 2) {
    echo kconfig ('system', 'side_html1');
  }
}

echo kconfig ('system', 'side_html2');
