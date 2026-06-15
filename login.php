<?php
// إعدادات حماية الكوكيز والجلسة
ini_set('session.cookie_secure', 0);     // اجعلها 1 عند الرفع على Coolify (HTTPS) و 0 للمحلي
ini_set('session.cookie_httponly', 1);   
ini_set('session.use_only_cookies', 1);
session_start();

// 1. الاتصال بقاعدة بيانات PostgreSQL
$host     = 'localhost'; 
$port     = '5432'; // المنفذ الافتراضي لـ Postgres
$db_name  = 'alassil';
$username = 'postgres'; // المستخدم الافتراضي لـ Postgres غالباً
$password = 'password_here'; // اكتب كلمة مرور Postgres الخاصة بك هنا

try {
    // تم تغيير المحرك هنا إلى pgsql
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("فشل الاتصال بقاعدة بيانات PostgreSQL: " . $e->getMessage());
}

// 2. استقبال البيانات من نموذج الـ HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $id_input   = isset($_POST['national_id']) ? trim($_POST['national_id']) : '';
    $pass_input = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (!empty($id_input) && !empty($pass_input)) {
        
        // جلب المستخدم بناءً على رقم الهوية
        $stmt = $conn->prepare("SELECT * FROM users WHERE national_id = :national_id LIMIT 1");
        $stmt->bindParam(':national_id', $id_input);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // التحقق من كلمة المرور (هنا بدون تشفير كما طلبنا للتجربة، أو يمكنك إرجاع password_verify)
            if ($pass_input === $user['password']) {
                
                // تسجيل جلسة النجاح
                $_SESSION['user_id']     = $user['id'];
                $_SESSION['national_id'] = $user['national_id'];
                
                // التوجيه إلى لوحة التحكم
                header("Location: dashboard.php");
                exit();
                
            } else {
                echo "<h3 style='color:red; text-align:center; margin-top:50px;'>كلمة المرور غير صحيحة.</h3>";
            }
        } else {
            echo "<h3 style='color:red; text-align:center; margin-top:50px;'>رقم الهوية غير مسجل بالنظام.</h3>";
        }
        
    } else {
        echo "<h3 style='color:red; text-align:center; margin-top:50px;'>برجاء ملء جميع الحقول.</h3>";
    }
}
?>