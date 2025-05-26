<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои заявки</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <script src="js/bootstrap.bundle.min.js" defer></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .violation-card {
            margin: 1.5rem 0;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            background: #fff;
            transition: transform 0.2s, box-shadow 0.2s;
            border-left: 4px solid transparent;
        }
        
        .violation-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        
        .status-badge {
            padding: 0.5rem 1.25rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .status-badge::before {
            content: '';
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: rgba(255,255,255,0.8);
        }
        
        .status-new { 
            background: linear-gradient(135deg, #60a5fa, #3b82f6);
            color: white; 
        }
        
        .status-confirmed { 
            background: linear-gradient(135deg, #34d399, #10b981);
            color: white; 
        }
        
        .status-rejected { 
            background: linear-gradient(135deg, #f87171, #ef4444);
            color: white; 
        }
        
        .divider {
            border-top: 1px solid #e5e7eb;
            margin: 1rem 0;
        }
        
        .container h2 {
            font-weight: 600;
            color: #1f2937;
            padding-bottom: 0.75rem;
            position: relative;
        }
        
        .container h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: #3b82f6;
            border-radius: 2px;
        }
        
        .car-number {
            font-size: 1.25rem;
            color: #1f2937;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-dark navbar-dark shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="statements.php">
                <img src="img/car.jpg" alt="Logo" width="75" height="75" class="me-2 rounded-circle">
                <span class="fs-4">Нарушениям.Нет</span>
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="application.php">Мои заявки</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <?php
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit();
        }

        try {
            $connection = new PDO("mysql:host=localhost;dbname=demka_db", "root", "");
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $user_id = $_SESSION['user_id'];
            $sql = "SELECT * FROM statements WHERE user_id = :user_id";
            $stmt = $connection->prepare($sql);
            $stmt->execute([':user_id' => $user_id]);

            if ($stmt->rowCount() > 0) {
                echo '<h2 class="mb-4">Мои заявления о нарушениях</h2>';
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $status_class = 'status-' . $row['status'];
                    $status_text = match($row['status']) {
                        'new' => 'На рассмотрении',
                        'confirmed' => 'Подтверждено',
                        'rejected' => 'Отклонено',
                        default => 'Неизвестный статус'
                    };
        ?>
                    <div class="violation-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="mb-0">Номер авто: <?= htmlspecialchars($row['carNumber']) ?></h5>
                            <span class="status-badge <?= $status_class ?>">
                                <?= $status_text ?>
                            </span>
                        </div>

                        <p class="mb-0"><?= nl2br(htmlspecialchars($row['description'])) ?></p>
                    </div>
        <?php
                }
            } else {
                echo '<div class="alert alert-info">У вас нет активных заявлений</div>';
            }
        } catch (PDOException $e) {
            echo '<div class="alert alert-danger">Ошибка: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        ?>
    </div>
</body>
</html>