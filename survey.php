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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_survey'])) {
    $question = $_POST['question'];
    $options = $_POST['options'];

    $stmt = $conn->prepare("INSERT INTO diplom.surveys (question) VALUES (:question)");
    $stmt->bindParam(':question', $question);
    $stmt->execute();
    $survey_id = $conn->lastInsertId();

    foreach ($options as $option) {
        if (!empty($option)) {
            $optionsStmt = $conn->prepare("INSERT INTO diplom.options (survey_id, option_text) VALUES (:survey_id, :option_text)");
            $optionsStmt->bindParam(':survey_id', $survey_id);
            $optionsStmt->bindParam(':option_text', $option);
            $optionsStmt->execute();
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vote'])) {
    $option_id = $_POST['option_id'];
    $stmt = $conn->prepare("UPDATE diplom.options SET votes = votes + 1 WHERE id = :option_id");
    $stmt->bindParam(':option_id', $option_id);
    $stmt->execute();
}

$surveys = $conn->query("SELECT * FROM diplom.surveys")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <title>Платформа для опросов</title>
</head>

<body>
    <div class="container">
        <h1>Платформа для опросов</h1>

        <div class="create-survey">
            <h2>Создать новый опрос</h2>
            <form action="" method="POST">
                <label for="question">Вопрос:</label>
                <input type="text" name="question" required>
                <label for="options">Варианты ответов (разделяйте запятыми):</label>
                <input type="text" name="options[]" required>
                <input type="text" name="options[]">
                <input type="text" name="options[]">
                <button type="submit" name="create_survey">Создать опрос</button>
            </form>
        </div>

        <h2>Существующие опросы:</h2>
        <div id="surveys">
            <?php foreach ($surveys as $survey): ?>
                <div class="survey">
                    <h3><?php echo htmlspecialchars($survey['question']); ?></h3>
                    <form action="" method="POST">
                        <?php
                        $optionsStmt = $conn->prepare("SELECT * FROM diplom.options WHERE survey_id = :survey_id");
                        $optionsStmt->bindParam(':survey_id', $survey['id']);
                        $optionsStmt->execute();
                        $options = $optionsStmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($options as $option): ?>
                            <div class="option">
                                <input type="radio" name="option_id" value="<?php echo $option['id']; ?>" required>
                                <?php echo htmlspecialchars($option['option_text']); ?>
                                <span class="votes">(<?php echo $option['votes']; ?> голосов)</span>
                            </div>
                        <?php endforeach; ?>
                        <input type="hidden" name="survey_id" value="<?php echo $survey['id']; ?>">
                        <button type="submit" name="vote">Голосовать</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>