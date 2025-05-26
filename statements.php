<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['user_id'])) {
        $data = [
            "carNumber" => $_POST["carNumber"],
            "description" => $_POST["description"],
            "status" => "new",
            "user_id" => $_SESSION['user_id']
        ];

        try {
            $connection = new PDO("mysql:host=localhost;dbname=demka_db", "root", '');
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = 'INSERT INTO statements(carNumber, description, status, user_id) 
                    VALUES (:carNumber, :description, :status, :user_id)';
            
            $statement = $connection->prepare($sql);
            $result = $statement->execute($data);
            
            if ($result) {
                header("Location: application.php");
                exit();
            }
        } catch(PDOException $e) {
            $error = $e->getMessage();
        }
    } else {
        $error = "Пользователь не авторизован";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <title>Форма Заявления</title>

    <style>
        :root {
            --primary: #2c3e50;     /* Темно-синий */
            --secondary: #3498db;   /* Голубой */
            --background: #f8f9fa;  /* Светлый фон */
            --text: #2d3436;        /* Основной текст */
            --border: #ced4da;      /* Границы */
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: var(--background);
        }

        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        h1, h2 {
            color: var(--primary);
            margin-bottom: 2rem;
        }

        h1 {
            font-size: 2.5rem;
            text-align: center;
        }

        h2 {
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            color: var(--text);
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        input, textarea {
            width: 100%;
            padding: 0.875rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: var(--secondary);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        button {
            background: var(--secondary);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        button:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .auth-link {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text);
        }

        .auth-link a {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 500;
        }

        .auth-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .container {
                margin: 1rem;
                padding: 1.5rem;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            h2 {
                font-size: 1.5rem;
            }
        }



        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }

        .error {
            background: #fee;
            color: #e74c3c;
            padding: 1rem;
            border-radius: 6px;
            margin: 1rem 0;
            border: 1px solid #e74c3c;
        }

        .registration-card {
            margin: 2rem auto;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
            outline: none;
        }

        .btn-primary {
            background-color: #3498db;
            padding: 1rem;
            font-size: 1rem;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .text-center {
            text-align: center;
        }

        .mb-2 {
            margin-bottom: 2rem;
        }

        .w-100 {
            width: 100%;
        }

        .logo {
            color: var(--text);
            text-align: center;
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



    <div class="header">
        <nav class="nav container">
            <div class="logo">Нарушениям.Нет</div>
                <div class="registration-card">
        <form method="POST" class="auth-form">
            <h2 class="text-center mb-2">Новое заявление</h2>
            
            <?php if(isset($error)): ?>
            <div class="error"><?= $error ?></div>
            <?php endif; ?>

            <div class="form-group">
                <label>Номер машины</label>
                <input type="text" class="form-control" name="carNumber" required 
                       placeholder="A123BC">
            </div>

            <div class="form-group">
                <label>Описание нарушения</label>
                <textarea class="form-control" name="description" rows="4" required 
                          placeholder="Опишите подробности нарушения"></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100">Отправить</button>
        </form>
    </div>
        </nav>
    </div>
</body>
</html>