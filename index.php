<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$errors = [];
$old_values = [];

if (isset($_COOKIE['form_errors'])) {
    $errors = unserialize($_COOKIE['form_errors']);
    setcookie('form_errors', '', time() - 3600, '/'); 
}

if (isset($_COOKIE['form_data'])) {
    $old_values = unserialize($_COOKIE['form_data']);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .error { color: red; font-size: 0.9em; margin: 3px 0; }
        .error-field { border: 2px solid red !important; }
        .success { color: green; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $field => $message): ?>
                    <div class="error"><?= htmlspecialchars($message) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success']) && $_GET['success'] === '1'): ?>
            <div class="success">Данные успешно сохранены!</div>
        <?php endif; ?>

        <form action="submit.php" method="POST">
       
            <label>ФИО (только буквы и пробелы):
                <input type="text" name="name" 
                       value="<?= htmlspecialchars($old_values['name'] ?? '') ?>" 
                       class="<?= isset($errors['name']) ? 'error-field' : '' ?>">
            </label>

            <label>Телефон (формат +7XXXXXXXXXX):
                <input type="tel" name="phone" 
                       value="<?= htmlspecialchars($old_values['phone'] ?? '') ?>" 
                       class="<?= isset($errors['phone']) ? 'error-field' : '' ?>">
            </label>

            <label>Email:
                <input type="email" name="email" 
                       value="<?= htmlspecialchars($old_values['email'] ?? '') ?>" 
                       class="<?= isset($errors['email']) ? 'error-field' : '' ?>">
            </label>

            <label>Дата рождения:
                <input type="date" name="birthdate" 
                       value="<?= htmlspecialchars($old_values['birthdate'] ?? '') ?>" 
                       class="<?= isset($errors['birthdate']) ? 'error-field' : '' ?>">
            </label>

            <fieldset class="<?= isset($errors['gender']) ? 'error-field' : '' ?>">
                <legend>Пол:</legend>
                <label>
                    <input type="radio" name="gender" value="male" 
                        <?= isset($old_values['gender']) && $old_values['gender'] === 'male' ? 'checked' : '' ?>> Мужской
                </label>
                <label>
                    <input type="radio" name="gender" value="female" 
                        <?= isset($old_values['gender']) && $old_values['gender'] === 'female' ? 'checked' : '' ?>> Женский
                </label>
            </fieldset>

            <label>Любимый язык программирования (выберите один или несколько):
                <select name="languages[]" multiple 
                    class="<?= isset($errors['languages']) ? 'error-field' : '' ?>">
                    <?php
                    $allowedLanguages = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala', 'Go'];
                    $selectedLanguages = $old_values['languages'] ?? [];
                    foreach ($allowedLanguages as $lang): ?>
                        <option value="<?= $lang ?>" 
                            <?= in_array($lang, $selectedLanguages) ? 'selected' : '' ?>>
                            <?= $lang ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>Биография (только английские символы):
                <textarea name="bio" rows="4" 
                    class="<?= isset($errors['bio']) ? 'error-field' : '' ?>"><?= htmlspecialchars($old_values['bio'] ?? '') ?></textarea>
            </label>

            <label>
                <input type="checkbox" name="agree" 
                    <?= isset($old_values['agree']) ? 'checked' : '' ?>> Согласен с условиями
            </label>

            <button type="submit">Сохранить</button>
        </form>
    </div>
</body>
</html>