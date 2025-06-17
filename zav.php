CREATE TABLE reservations (
    id SERIAL PRIMARY KEY,          -- Уникальный идентификатор записи
    reservation_date DATE NOT NULL, -- Дата бронирования
    reservation_time TIME NOT NULL,  -- Время бронирования
    guest_count INT NOT NULL,        -- Количество гостей
    phone VARCHAR(15) NOT NULL       -- Телефонный номер
);

ALTER TABLE publiс.reservations
ADD COLUMN status VARCHAR(20) DEFAULT 'новая';
<?php
$errors = [];

// Предполагается, что вы уже начали сессию
session_start();
$phone = $_SESSION['phone'] ?? ''; // Получаем номер телефона из сессии

require_once 'bd.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $date = trim($_POST["date"] ?? '');
    $time = trim($_POST["time"] ?? '');
    $guest_count = trim($_POST["guest_count"] ?? '');

    if ($date === '') {
        $errors['date'] = "Дата бронирования обязательна.";
    }
    if ($time === '') {
        $errors['time'] = "Время бронирования обязательно.";
    }
    if ($guest_count === '') {
        $errors['guest_count'] = "Количество гостей обязательно.";
    }

    if (empty($errors)) {
        $sql = "INSERT INTO reservations (reservation_date, reservation_time, guest_count, phone) 
                VALUES (:date, :time, :guest_count, :phone)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':date' => $date,
            ':time' => $time,
            ':guest_count' => $guest_count,
            ':phone' => $phone
        ]);
        header("Location: lk.php?success=1");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Бронирование</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0; padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header {
            background-color: #FF813D;
            color: white;
            padding: 10px 20px;
            display: flex;
            align-items: center;
        }
        header img {
            height: 40px;
            margin-right: 10px;
        }
        .container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .form-container {
            width: 400px;
            padding: 20px;
            background-color: rgba(255, 149, 92, 0.75);
            border-radius: 15px;
            box-shadow: 0 0 10px #fff;
        }
        h2 {
            text-align: center;
            color: white;
            margin-top: 0;
        }
        input[type="date"],
        input[type="time"],
        select {
            width: 96%;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 15px;
        }
        input[type="submit"] {
            background-color: #FF813D;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 15px;
            cursor: pointer;
            width: 100%;
            display: block;
            margin: 0 auto;
        }
        input[type="submit"]:hover {
            background-color: #FF813D94;
        }
        .error {
            color: red;
            font-size: 0.9em;
            margin-top: -8px;
            margin-bottom: 10px;
        }
        .login-link {
            text-align: center;
            margin-top: 15px;
            color: white;
        }
        .login-link a {
            color: #FF813D;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<header>
    <img src="logo.png" alt="Логотип" />
    <h1>Бронирование</h1>
</header>

<div class="container">
    <div class="form-container">
        <h2>Форма бронирования</h2>
        <form method="post" action="">
            <input type="date" name="date" value="<?= htmlspecialchars($date) ?>">
            <div class="error"><?= $errors['date'] ?? '' ?></div>

            <input type="time" name="time" value="<?= htmlspecialchars($time) ?>">
            <div class="error"><?= $errors['time'] ?? '' ?></div>

            <select name="guest_count">
                <option value="">Выберите количество гостей</option>
                <?php for ($i = 1; $i <= 10; $i++): ?>
                    <option value="<?= $i ?>" <?= isset($guest_count) && $guest_count == $i ? 'selected' : '' ?>><?= $i ?></option>
                <?php endfor; ?>
            </select>
            <div class="error"><?= $errors['guest_count'] ?? '' ?></div>

            <input type="submit" value="Забронировать">
        </form>
        <div class="login-link">
            <a href="lk.php">Войти в личный кабинет</a>
        </div>
    </div>
</div>

<script>
    <?php if (isset($_GET['success'])): ?>
        alert('Бронирование успешно оформлено! Спасибо за вашу заявку.');
    <?php endif; ?>
</script>
</body>
</html>
