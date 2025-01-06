<?php
setcookie('sessid_clinabs', '', time() - 6307200000000 * 300000, '/', hostname, true);
setcookie('sessid_clinabs_uid', '', time() - 630720000000 * 30000, '/', hostname, true);

header('Location: /login');