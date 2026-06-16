<?php
// بيانات الاتصال (تأكد أنها مطابقة لبيانات Coolify)
$host = "pkk0s8ocscscckgs8osggso8"; // اسم الخدمة الداخلي
$db   = "postgres";
$user = "postgres";
$pass = "uTJoes2W8akpLIZLftpFjLoLG87SPWrSuOrJnTe9NGZz6Ozsd23nU5bLF2BdFKhD";

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
    echo "تم الاتصال! ";
    
    // محاولة إرسال بيانات (اختبار الكتابة)
    $stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
    $stmt->execute(['Test Name', 'test@test.com']);
    
    echo "تم إرسال البيانات بنجاح إلى قاعدة البيانات.";
} catch (PDOException $e) {
    echo "خطأ في الإرسال: " . $e->getMessage();
}
?>
