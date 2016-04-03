<?php
  $html = file_get_contents($_GET['sd']);
  $parts = explode('</head>', $html, 2);
  echo $parts[0] . "<script src='/yeti-cms/loader.js'></script></head>" . $parts[1];
?>
