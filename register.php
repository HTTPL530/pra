<?php
$host = 'localhost';
$db = 'postgres';
$user = 'postgres';
$pass = '12345';

try {
    $conn = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Хеширование пароля

    $stmt = $conn->prepare("INSERT INTO diplom.users (username, password) VALUES (:username, :password)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);

    if ($stmt->execute()) {
        echo "<div class='success'>Регистрация успешна! Теперь вы можете <a href='login.php'>войти</a>.</div>";
    } else {
        echo "<div class='error'>Ошибка регистрации.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="avtor.css">
    <title>Регистрация</title>
</head>
<body>
    <div class="registration-container">
        <h1>Регистрация</h1>
        <form action="" method="POST">
            <input type="text" name="username" placeholder="Имя пользователя" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit" name="register">Зарегистрироваться</button>
        </form>
        <p>Уже есть аккаунт? <a href="login.php">Войти</a></p>
    </div>
</body>
</html>
