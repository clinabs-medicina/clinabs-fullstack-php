<?php
file_put_contents('last_sync.json', json_encode(new DateTime(), JSON_PRETTY_PRINT));