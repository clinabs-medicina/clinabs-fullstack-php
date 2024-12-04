<?php
file_put_contents('ipBlock.txt', $_SERVER['REMOTE_ADDR'] . "\n", FILE_APPEND);
http_response_code(403);