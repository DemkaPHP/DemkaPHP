<?php
session_start();

// Проверка авторизации администратора
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_form.php");
    exit();
}

// Обработка изменения статуса
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['statement_id'])) {
    try {
        $connection = new PDO("mysql:host=localhost;dbname=demka_db", "root", "");
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $statement_id = $_POST['statement_id'];
        $new_status = $_POST['status'];
        
        $sql = "SELECT 
                    s.id,
                    s.carNumber,
                    s.description,
                    s.status,
                    s.created_at,
                    u.FCS AS user_fcs
                FROM statements s
                INNER JOIN users u ON s.user_id = u.id";

        $stmt = $connection->prepare($sql);
        $stmt->execute([':status' => $new_status, ':id' => $statement_id]);
        
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } catch (PDOException $e) {
        die("Ошибка обновления: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ панель</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-dark" data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="../img/car.jpg" alt="Logo" width="75" height="75" class="d-inline-block align-text-center">
                Нарушениям.Нет
            </a>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="stm_DB.php">Заявления</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <span class="navbar-text text-white me-3">
                        Вы вошли как: <?= htmlspecialchars($_SESSION['username']) ?>
                    </span>
                    <a href="logout.php" class="btn btn-danger">Выйти</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>База данных заявлений</h1>
        <p>Управление статусами заявок</p>
        
        <?php 
        try {
            $connection = new PDO("mysql:host=localhost;dbname=demka_db", "root", "");
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $sql = "SELECT * FROM statements";
            $result = $connection->query($sql);

            if ($result && $result->rowCount() > 0) {
        ?>
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Номер автомобиля</th>
                            <th>Описание</th>
                            <th>Статус</th>
                            <th>ФИО заявителя</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($result as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['carNumber']) ?></td>
                            <td><?= htmlspecialchars($row['description']) ?></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="statement_id" value="<?= htmlspecialchars($row['id']) ?>">
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="new" <?= $row['status'] == 'new' ? 'selected' : '' ?>>Новое</option>
                                        <option value="confirmed" <?= $row['status'] == 'confirmed' ? 'selected' : '' ?>>Подтверждено</option>
                                        <option value="rejected" <?= $row['status'] == 'rejected' ? 'selected' : '' ?>>Отклонено</option>
                                    </select>
                                </form>
                            </td>
                             <td><?= htmlspecialchars($row['user_fcs'] ?? 'N/A') ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="statement_id" value="<?= htmlspecialchars($row['id']) ?>">
                                    <button type="submit" class="btn btn-sm btn-primary">Обновить</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
        <?php 
            } else {
                echo "<div class='alert alert-info'>Заявления не найдены</div>";
            }
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>Ошибка подключения: ".htmlspecialchars($e->getMessage())."</div>";
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>