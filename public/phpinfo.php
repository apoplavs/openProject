<?php
$mysqlnd = function_exists('mysqli_fetch_all');

if ($mysqlnd) {
    echo 'mysqlnd enabled!'.PHP_EOL;
} else {
	echo 'mysqlnd DISABLED!'.PHP_EOL;
}
echo "<br><br><br>";
// $pdo = new PDO('mysql:dbname=toecyd;host=127.0.0.1', 'root', '1');
$pdo = new PDO('mysql:dbname=toecydto_toecyd;host=127.0.0.1', 'toecydto', 'I9Aw3wv63m');
if (strpos($pdo->getAttribute(PDO::ATTR_CLIENT_VERSION), 'mysqlnd') !== false) {
    echo 'PDO MySQLnd enabled!'.PHP_EOL;
} else {
	echo 'PDO MySQLnd DISABLED!'.PHP_EOL;
}

//phpinfo();
?>