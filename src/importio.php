<?php
$result=file_get_contents("https://api.import.io/store/connector/b5075f26-32f4-43f2-b726-5e7d4731be6d/_query?input=webpage/url:http%3A%2F%2Fwww.ikea.com%2Fus%2Fen%2Fsearch%2F%3Fquery%3Dchair&&_apikey=17a6f7f8afb54428be774948ec7e1fbdcc1e058e9a9683f935c75b7ee57114079f66fca23cd3d94b880a06c52ff797ee100dfdb9b28606c2cf7bfa2164c41459ab062747ca2c5f3750547bb6a5d7bb8d");
echo print_r(json_decode($result),1);
exit();
?>
