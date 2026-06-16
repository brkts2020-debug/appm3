<?php
// 1. جلب بيانات الاتصال من بيئة Coolify (Environment Variables)
$host     = getenv('DB_HOST');
$port     = getenv('DB_PORT'); // يجب أن يكون 5432
$db_name  = getenv('DB_DATABASE'); // في حالتك غالباً 'bnm302'
$username = getenv('DB_USERNAME'); // 'postgres'
$password = getenv('DB_PASSWORD');

// 2. إعداد الاتصال باستخدام PDO
try {
    // دمج البيانات في سلسلة الاتصال
    $dsn = "pgsql:host=$host;port=$port;dbname=$db_name";
    
    // إنشاء كائن الاتصال
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // 3. التحقق من بيانات الدخول (مثال لنموذج تسجيل دخول)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_input = $_POST['username'] ?? '';
        $pass_input = $_POST['password'] ?? '';

        // استعلام التحقق (تأكد من تعديل اسم الجدول 'users' ليطابق جدولك)
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $user_input]);
        $user = $stmt->fetch();

        // مقارنة كلمة المرور (نستخدم المقارنة المباشرة بما أن بياناتك مخزنة كنص عادي في قاعدة البيانات)
        if ($user && $pass_input === $user['password']) {
            echo "<h1>تم تسجيل الدخول بنجاح!</h1>";
        } else {
            echo "<h1>اسم المستخدم أو كلمة المرور خطأ</h1>";
        }
    }
} catch (PDOException $e) {
    // في حال فشل الاتصال، هذا السطر سيخبرك بالسبب بالضبط في المتصفح
    echo "<h1>خطأ في الاتصال بقاعدة البيانات:</h1> " . $e->getMessage();
}
?>
