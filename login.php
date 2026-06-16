<?php
// جلب بيانات الاتصال من متغيرات البيئة في Coolify
$host     = getenv('DB_HOST');
$port     = getenv('DB_PORT') ?: '5432';
$db_name  = getenv('DB_DATABASE');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');

try {
    // إنشاء الاتصال
    $dsn = "pgsql:host=$host;port=$port;dbname=$db_name";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // معالجة طلب تسجيل الدخول
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_input = $_POST['username'] ?? '';
        $pass_input = $_POST['password'] ?? '';

        // استعلام التحقق من قاعدة البيانات
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $user_input]);
        $user = $stmt->fetch();

        // المقارنة (تأكد أن كلمة السر في قاعدة البيانات هي نص عادي كما في صورتك)
        if ($user && $pass_input === $user['password']) {
            // نجاح: التوجيه إلى لوحة التحكم
            header("Location: dashboard.html");
            exit(); 
        } else {
            echo "<h1>اسم المستخدم أو كلمة المرور غير صحيحة</h1>";
            echo '<a href="index.html">العودة للصفحة السابقة</a>';
        }
    }
} catch (PDOException $e) {
    echo "<h1>خطأ في الاتصال بقاعدة البيانات:</h1> " . $e->getMessage();
}
?>
