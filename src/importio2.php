<?php
$result=file_get_contents("https://api.import.io/store/connector/a2b81069-a3e1-496a-aacc-4502e2c1f5e7/_query?input=webpage/url:http%3A%2F%2Fwww.marca.com&&_apikey=17a6f7f8afb54428be774948ec7e1fbdcc1e058e9a9683f935c75b7ee57114079f66fca23cd3d94b880a06c52ff797ee100dfdb9b28606c2cf7bfa2164c41459ab062747ca2c5f3750547bb6a5d7bb8d");
echo print_r(json_decode($result),1);
exit();
?>
