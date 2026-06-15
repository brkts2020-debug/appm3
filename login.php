<?php
// إعدادات حماية الكوكيز والجلسة للـ Production (HTTPS)
ini_set('session.cookie_secure', 1);     // تعمل فقط عبر HTTPS الصادر من Coolify
ini_set('session.cookie_httponly', 1);   
ini_set('session.use_only_cookies', 1);
session_start();

// 1. الاتصال بقاعدة بيانات PostgreSQL الخارجية
$host     = '72.62.150.128'; 
$port     = '3000';          
$db_name  = 'bnm302';        
$username = 'postgres';      
$password = 'QOHdThn8X49rdf885hfn7Yd88uW646KlFgrxNhKlEKMvWMbQTHpo9jOzAxRuEORN'; 

try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
}

// 2. استقبال البيانات من نموذج الـ HTML وفحصها
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // استلام رقم الهوية المكتوب في الفورم، ومطابقته بحقل الـ username في قاعدة البيانات
    $id_input   = isset($_POST['national_id']) ? trim($_POST['national_id']) : '';
    $pass_input = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (!empty($id_input) && !empty($pass_input)) {
        
        // استعلام معدل: البحث داخل حقل username الفعلي بالجدول
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->bindParam(':username', $id_input);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // التحقق من تطابق كلمة المرور باستخدام حقل password_hash الفعلي بالجدول
            // بما أن اسم الحقل password_hash، يفضل استخدام دالة التحقق الآمنة أو مقارنة نصية إذا كنت تخزنها صريحة
            if (password_verify($pass_input, $user['password_hash']) || $pass_input === $user['password_hash']) {
                
                // تسجيل جلسة النجاح باستخدام الحقول الفعلية بالجدول (user_id و username)
                $_SESSION['user_id']  = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                
                // التوجيه التلقائي إلى لوحة التحكم
                header("Location: dashboard.php");
                exit();
                
            } else {
                echo "<h3 style='color:red; text-align:center; margin-top:50px;'>كلمة المرور غير صحيحة.</h3>";
            }
        } else {
            echo "<h3 style='color:red; text-align:center; margin-top:50px;'>المستخدم غير مسجل بالنظام.</h3>";
        }
        
    } else {
        echo "<h3 style='color:red; text-align:center; margin-top:50px;'>برجاء ملء جميع الحقول.</h3>";
    }
}
?>
