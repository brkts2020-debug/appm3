<?php

// إعدادات حماية الكوكيز والجلسة
ini_set('session.cookie_secure', 1); // يعمل مع HTTPS
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

session_start();


// ==========================
// الاتصال بقاعدة البيانات
// ==========================

$host     = '72.62.150.128';
$port     = '3000';
$db_name  = 'bnm302';
$username = 'postgres';
$password = 'qOHdThn8X49rdf885hfn7Yd88uW646KlFgrxNhKlEKMvWMbQTHpo9jOzAxRuEORN';

try {

    $conn = new PDO(
        "pgsql:host=$host;port=$port;dbname=$db_name",
        $username,
        $password
    );

    $conn->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {

    die("
    <h3 style='color:red;text-align:center;margin-top:50px;'>
        فشل الاتصال بقاعدة البيانات
    </h3>
    ");
}


// ==========================
// تسجيل الدخول
// ==========================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // استقبال البيانات من الفورم
    $id_input = trim($_POST['national_id'] ?? '');
    $pass_input = trim($_POST['password'] ?? '');

    // التحقق من أن الحقول ليست فارغة
    if (!empty($id_input) && !empty($pass_input)) {

        // البحث عن المستخدم
        $stmt = $conn->prepare("
            SELECT user_id, username, password_hash
            FROM users
            WHERE username = :username
            LIMIT 1
        ");

        $stmt->bindParam(':username', $id_input);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // التحقق من كلمة المرور (نص عادي)
        if ($user && $pass_input === $user['password_hash']) {

            // حماية إضافية للجلسة
            session_regenerate_id(true);

            // حفظ بيانات المستخدم
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];

            // تحويل لصفحة لوحة التحكم
            header("Location: dashboard.php");
            exit();

        } else {

            echo "
            <h3 style='color:red;text-align:center;margin-top:50px;'>
                بيانات تسجيل الدخول غير صحيحة
            </h3>";
        }

    } else {

        echo "
        <h3 style='color:red;text-align:center;margin-top:50px;'>
            برجاء ملء جميع الحقول
        </h3>";
    }
}

?>
