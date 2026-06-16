<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


// ==========================
// إعدادات قاعدة البيانات
// ==========================

$host     = '72.62.150.128';
$port     = '3000';
$db_name  = 'bnm302';
$username = 'postgres';
$password = 'qOHdThn8X49rdf885hfn7Yd88uW646KlFgrxNhKlEKMvWMbQTHpo9jOzAxRuEORN';


// ==========================
// اختبار الاتصال
// ==========================

try {

    $dsn = "pgsql:host=$host;port=$port;dbname=$db_name";

    $conn = new PDO(
        $dsn,
        $username,
        $password
    );

    $conn->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    echo "
    <div style='
        text-align:center;
        margin-top:50px;
        font-size:24px;
        color:green;
        font-family:Arial;
    '>
        ✅ تم الاتصال بقاعدة البيانات بنجاح
    </div>";

} catch (PDOException $e) {

    echo "
    <div style='
        text-align:center;
        margin-top:50px;
        font-size:22px;
        color:red;
        font-family:Arial;
    '>
        ❌ فشل الاتصال بقاعدة البيانات
        <br><br>
        <strong>رسالة الخطأ:</strong>
        <br>
        " . $e->getMessage() . "
    </div>";
}

?>
