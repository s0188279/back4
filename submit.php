<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
 
$servername = "sql204.infinityfree.com";
$username = "if0_38861022";
$password = "Fafafo333yryr";
$dbname = "if0_38861022_form_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

$validLangIds = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];

$patterns = [
    'name' => '/^[a-zA-Zа-яА-ЯёЁ\s]{1,150}$/u',
    'phone' => '/^\+7\d{10}$/',
    'email' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
    'bio' => '/^[a-zA-Z\s\.,!?\-()]+$/'
];

$errors = [];
$formData = $_POST;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($formData['name']) || !preg_match($patterns['name'], $formData['name'])) {
        $errors['name'] = 'Некорректное ФИО (только буквы и пробелы)';
    }

    if (empty($formData['phone']) || !preg_match($patterns['phone'], $formData['phone'])) {
        $errors['phone'] = 'Неверный формат: +7XXXXXXXXXX';
    }

    if (empty($formData['email']) || !preg_match($patterns['email'], $formData['email'])) {
        $errors['email'] = 'Некорректный email';
    }

    $maxDate = date('Y-m-d', strtotime('-10 years'));
    if (empty($formData['birthdate']) || $formData['birthdate'] > $maxDate) {
        $errors['birthdate'] = 'Минимальный возраст - 10 лет';
    }

    if (!isset($formData['gender']) || !in_array($formData['gender'], ['male', 'female'])) {
        $errors['gender'] = 'Выберите пол';
    }

    $selectedLangs = $formData['languages'] ?? [];
    if (empty($selectedLangs)) {
        $errors['languages'] = 'Выберите хотя бы один язык';
    } else {
        foreach ($selectedLangs as $langId) {
            if (!in_array($langId, $validLangIds)) {
                $errors['languages'] = 'Недопустимый выбор языков';
                break;
            }
        }
    }

    if (empty($formData['bio']) || !preg_match($patterns['bio'], $formData['bio'])) {
        $errors['bio'] = 'Недопустимые символы';
    }

    if (!isset($formData['agree'])) {
        $errors['agree'] = 'Необходимо согласие';
    }

    if (!empty($errors)) {
        setcookie('form_data', json_encode($formData), time() + 3600, '/');
        setcookie('form_errors', json_encode($errors), time() + 3600, '/');
        header('Location: index.php');
        exit;
    }

    try {
        $agree = $formData['agree'] ? 1 : 0;

        $stmt = $conn->prepare("INSERT INTO users 
            (name, phone, email, birthdate, gender, bio, agree) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
    
        $stmt->bind_param("ssssssi", 
            $formData['name'], 
            $formData['phone'], 
            $formData['email'], 
            $formData['birthdate'], 
            $formData['gender'], 
            $formData['bio'], 
            $agree 
        );
    
        $stmt->execute();
        $userId = $stmt->insert_id;
        $stmt->close();

        foreach ($selectedLangs as $langId) {
            $stmt = $conn->prepare("INSERT INTO user_languages (user_id, language_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $userId, $langId);
            $stmt->execute();
            $stmt->close();
        }

        setcookie('form_data', json_encode($formData), time() + 31536000, '/');
        setcookie('form_errors', '', time() - 3600, '/');
        header('Location: index.php?success=1');
    } catch (Exception $e) {
        error_log("Ошибка БД: " . $e->getMessage());
        die("Ошибка сохранения данных");
    }
}

$conn->close();
?>