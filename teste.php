<?php echo 'PHP: '.htmlspecialchars(PHP_VERSION).'<br>SQLite: '.(extension_loaded('pdo_sqlite')?'LIGADO':'DESLIGADO');
