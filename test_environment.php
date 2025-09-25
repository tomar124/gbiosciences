<?php
// test_twig_version.php
// require_once('config.php');
// require_once(DIR_SYSTEM . 'library/template/Twig/Autoloader.php');
// \Twig_Autoloader::register();

// // Check Twig version
// if (defined('\Twig_Environment::VERSION')) {
//     echo "Twig Version: " . \Twig_Environment::VERSION . "\n<br>";
// } else {
//     echo "Twig Version: Unknown (likely 1.x)\n<br>";
// }

// // Check how Twig handles whitespace
// $loader = new \Twig_Loader_Array(array(
//     'test1' => 'a{% if true %}b{% endif %}c',
//     'test2' => 'a {% if true %}b{% endif %} c',
//     'test3' => 'a{% if true %} b{% endif %} c',
//     'test4' => 'a{{- test -}}c',
// ));

// $twig = new \Twig_Environment($loader, array('autoescape' => false));

// echo "Test 1 (no spaces): " . $twig->render('test1', array('test' => 'b')) . "\n<br>";
// echo "Test 2 (spaces outside): " . $twig->render('test2', array('test' => 'b')) . "\n<br>";
// echo "Test 3 (spaces inside): " . $twig->render('test3', array('test' => 'b')) . "\n<br>";

// // Server info
// echo "\n<br>Server: " . $_SERVER['SERVER_SOFTWARE'] . "\n<br>";
// echo "OS: " . PHP_OS . "\n<br>";

?>

<?php
$url = "https://api.opencart.com/"; // replace with actual endpoint
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$result = curl_exec($ch);
if ($result === false) {
    echo "cURL Error: " . curl_error($ch);
} else {
    echo "SUCCESS: Got " . strlen($result) . " bytes";
}
curl_close($ch);
