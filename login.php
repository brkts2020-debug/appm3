<?php
// إعدادات حماية الكوكيز والجلسة للـ Production (HTTPS)
ini_set('session.cookie_secure', 1);     // تعمل فقط عبر HTTPS الصادر من Coolify
ini_set('session.cookie_httponly', 1);   // حماية إضافية للجلسة منعاً لسرقتها عبر المتصفح
ini_set('session.use_only_cookies', 1);
session_start();

// 1. الاتصال بقاعدة بيانات PostgreSQL الخارجية (نفس بيانات n8n)
$host     = '72.62.150.128'; // الآي بي الخارجي لقاعدتك
$port     = '3000';          // المنفذ الخارجي المحدد لديك
$db_name  = 'bnm302';        // اسم قاعدة البيانات
$username = 'postgres';      // اسم المستخدم
$password = 'QOHdThn8X49rdf885hfn7Yd88uW646KlFgrxNhKlEKMvWMbQTHpo9jOzAxRuEORN'; // كلمة المرور السرية الخاصة بك

try {
    // الاتصال باستخدام محرك pgsql المخصص لـ PostgreSQL
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("فشل الاتصال بقاعدة بيانات PostgreSQL الخارجية: " . $e->getMessage());
}

// 2. استقبال البيانات من نموذج الـ HTML وفحصها
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $id_input   = isset($_POST['national_id']) ? trim($_POST['national_id']) : '';
    $pass_input = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (!empty($id_input) && !empty($pass_input)) {
        
        // استعلام لجلب المستخدم بناءً على رقم الهوية
        $stmt = $conn->prepare("SELECT * FROM users WHERE national_id = :national_id LIMIT 1");
        $stmt->bindParam(':national_id', $id_input);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // التحقق من تطابق كلمة المرور (مقارنة نصية مباشرة لضمان عملها مع مدخلات n8n الحالية)
            if ($pass_input === $user['password']) {
                
                // تسجيل جلسة النجاح
                $_SESSION['user_id']     = $user['id'];
                $_SESSION['national_id'] = $user['national_id'];
                
                // التوجيه التلقائي إلى لوحة التحكم
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
