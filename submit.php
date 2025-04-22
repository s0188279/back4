<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Подключение к БД
$servername = "sql207.infinityfree.com";
$username = "if0_38666238";
$password = "Fafafo333yryry";
$dbname = "if0_38666238_form_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Валидация данных
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получение данных с проверкой существования ключей
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $birthdate = isset($_POST['birthdate']) ? $_POST['birthdate'] : '';
    $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
    $bio = isset($_POST['bio']) ? trim($_POST['bio']) : '';
    $agree = isset($_POST['agree']) ? 1 : 0;
    $languages = isset($_POST['languages']) ? $_POST['languages'] : [];

    // ... (код валидации остаётся без изменений) ...

    // Если ошибок нет — сохранить в БД
    try {
        $conn->begin_transaction();

        // Исправленный запрос с 7 параметрами
        $stmt = $conn->prepare("INSERT INTO users (name, phone, email, birthdate, gender, bio, agree) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", 
            $name,
            $phone,
            $email,
            $birthdate,
            $gender,
            $bio,
            $agree // 7-й параметр (integer)
        );
        $stmt->execute();
        $user_id = $stmt->insert_id;
        $stmt->close();

        // Вставка языков
        foreach ($languages as $lang) {
            $stmt = $conn->prepare("INSERT INTO user_languages (user_id, language) VALUES (?, ?)");
            $stmt->bind_param("is", $user_id, $lang);
            $stmt->execute();
            $stmt->close();
        }

        $conn->commit();
        setcookie('form_data', serialize($_POST), time() + 31536000, '/');
        header("Location: index.php?success=1");
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        error_log("Ошибка: " . $e->getMessage());
        die("Произошла ошибка. Пожалуйста, попробуйте позже.");
    }
}

$conn->close();
?>