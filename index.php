<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function escape($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

$formData = json_decode($_COOKIE['form_data'] ?? '{}', true);
$errors = json_decode($_COOKIE['form_errors'] ?? '{}', true);

if (isset($_COOKIE['form_errors'])) {
    setcookie('form_errors', '', time() - 3600, '/');
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Форма регистрации</title>
</head>
<body>
    <?php if (!empty($errors)): ?>
        <div class="error-message">
            <h3>Ошибки:</h3>
            <?php foreach ($errors as $field => $message): ?>
                <p><?= escape($message) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="submit.php">
        <div class="<?= isset($errors['name']) ? 'error' : '' ?>">
            <label>ФИО:
                <input type="text" name="name" 
                    value="<?= escape($formData['name'] ?? '') ?>"
                    pattern="^[a-zA-Zа-яА-ЯёЁ\s]{1,150}$"
                    title="Только буквы и пробелы (макс. 150 символов)"
                    required>
            </label>
        </div>

        <div class="<?= isset($errors['phone']) ? 'error' : '' ?>">
            <label>Телефон:
                <input type="tel" name="phone" 
                    value="<?= escape($formData['phone'] ?? '') ?>"
                    pattern="^\+7\d{10}$"
                    title="Формат: +7XXXXXXXXXX"
                    required>
            </label>
        </div>

        <div class="<?= isset($errors['email']) ? 'error' : '' ?>">
            <label>Email:
                <input type="email" name="email" 
                    value="<?= escape($formData['email'] ?? '') ?>"
                    required>
            </label>
        </div>

        <div class="<?= isset($errors['birthdate']) ? 'error' : '' ?>">
            <label>Дата рождения:
                <input type="date" name="birthdate" 
                    value="<?= escape($formData['birthdate'] ?? '') ?>"
                    max="<?= date('Y-m-d', strtotime('-10 years')) ?>"
                    required>
            </label>
        </div>

        <fieldset class="<?= isset($errors['gender']) ? 'error' : '' ?>">
            <legend>Пол:</legend>
            <label>
                <input type="radio" name="gender" value="male"
                    <?= ($formData['gender'] ?? '') == 'male' ? 'checked' : '' ?> required> Мужской
            </label>
            <label>
                <input type="radio" name="gender" value="female"
                    <?= ($formData['gender'] ?? '') == 'female' ? 'checked' : '' ?>> Женский
            </label>
        </fieldset>

        <div class="<?= isset($errors['languages']) ? 'error' : '' ?>">
            <label>Любимые языки:
                <select name="languages[]" multiple required>
                    <option value="1" <?= in_array(1, $formData['languages'] ?? []) ? 'selected' : '' ?>>Pascal</option>
                    <option value="2" <?= in_array(2, $formData['languages'] ?? []) ? 'selected' : '' ?>>C</option>
                    <option value="3" <?= in_array(3, $formData['languages'] ?? []) ? 'selected' : '' ?>>C++</option>
                    <option value="4" <?= in_array(4, $formData['languages'] ?? []) ? 'selected' : '' ?>>JavaScript</option>
                    <option value="5" <?= in_array(5, $formData['languages'] ?? []) ? 'selected' : '' ?>>PHP</option>
                    <option value="6" <?= in_array(6, $formData['languages'] ?? []) ? 'selected' : '' ?>>Python</option>
                    <option value="7" <?= in_array(7, $formData['languages'] ?? []) ? 'selected' : '' ?>>Java</option>
                    <option value="8" <?= in_array(8, $formData['languages'] ?? []) ? 'selected' : '' ?>>Haskell</option>
                    <option value="9" <?= in_array(9, $formData['languages'] ?? []) ? 'selected' : '' ?>>Clojure</option>
                    <option value="10" <?= in_array(10, $formData['languages'] ?? []) ? 'selected' : '' ?>>Prolog</option>
                    <option value="11" <?= in_array(11, $formData['languages'] ?? []) ? 'selected' : '' ?>>Scala</option>
                    <option value="12" <?= in_array(12, $formData['languages'] ?? []) ? 'selected' : '' ?>>Go</option>
                </select>
            </label>
        </div>

        <div class="<?= isset($errors['bio']) ? 'error' : '' ?>">
            <label>Биография:
                <textarea name="bio" rows="4" required><?= escape($formData['bio'] ?? '') ?></textarea>
            </label>
        </div>

        <div class="<?= isset($errors['agree']) ? 'error' : '' ?>">
            <label>
                <input type="checkbox" name="agree" value="1"
                    <?= ($formData['agree'] ?? 0) ? 'checked' : '' ?> required> Согласен с условиями
            </label>
        </div>

        <button type="submit">Отправить</button>
    </form>
</body>
</html>