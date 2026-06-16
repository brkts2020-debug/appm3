<?php
// جلب المتغيرات التي قمت بوضعها في إعدادات المنصة
$host     = getenv('DB_HOST');
$port     = getenv('DB_PORT');
$db_name  = getenv('DB_DATABASE');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$db_name";
    $conn = new PDO($dsn, $username, $password);
    echo "<h1>اتصال ناجح! تم الربط بقاعدة البيانات.</h1>";
} catch (PDOException $e) {
    echo "<h1>فشل الاتصال:</h1> " . $e->getMessage();
}
?>
