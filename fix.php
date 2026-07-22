<?php
$c = file_get_contents('database/seeders/ExistingDataSeeder.php');
$c = str_replace('\\"', '"', $c);
file_put_contents('database/seeders/ExistingDataSeeder.php', $c);
