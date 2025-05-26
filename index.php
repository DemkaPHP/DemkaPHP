<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        "login" => $_POST["login"],
        "password" => $_POST["password"],
        "email" => $_POST["email"],
        "phone" => $_POST["phone"],
        "FCS" => $_POST["FCS"],
    ];

    try {
        $connection = new PDO("mysql:host=localhost;dbname=demka_db", "root", '');
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = 'INSERT INTO users(login, password, email, FCS, phone) 
                VALUES (:login, :password, :email, :FCS, :phone)';
        
        $statement = $connection->prepare($sql);
        $result = $statement->execute($data);
        
        if ($result) {
            header('Location: autho.php');
            exit();
        }
    } catch(PDOException $e) {
        echo '<div class="error">Ошибка регистрации: ' . $e->getMessage() . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --background: #f8fafc;
            --text: #1e293b;
            --border: #cbd5e1;
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
            align-items: center;
            justify-content: center;
            background-color: var(--background);
            padding: 1rem;
        }

        .registration-card {
            background: white;
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 480px;
        }

        h1 {
            color: var(--text);
            font-size: 1.875rem;
            font-weight: 600;
            margin-bottom: 2rem;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            color: var(--text);
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        input {
            width: 100%;
            padding: 0.875rem;
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.2s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        button {
            width: 100%;
            padding: 1rem;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        button:hover {
            background-color: var(--primary-hover);
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #64748b;
        }

        .login-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .registration-card {
                padding: 1.5rem;
            }
            
            h1 {
                font-size: 1.5rem;
            }
            
            input, button {
                padding: 0.75rem;
            }
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
            margin-top: 8px;
        }

        .form-control::placeholder {
            color: #95a5a6;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
            outline: none;
        }

        .link {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .link:hover {
            text-decoration: underline;
        }

        .error {
            background: #fee;
            color: var(--danger-color);
            padding: 1rem;
            border-radius: 6px;
            margin: 1rem auto;
            max-width: 400px;
            border: 1px solid var(--danger-color);
        }

        .w-100 {
            width: 100%;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInput = document.getElementById('phone');
            
            phoneInput.addEventListener('input', function(e) {
                let number = e.target.value.replace(/\D/g, '');
                
                
                if(number.startsWith('8') && number.length > 1) {
                    number = '7' + number.substring(1);
                }
                
               
                let formatted = '+7';
                if(number.length > 1) {
                    formatted += ' (' + number.substring(1, 4);
                }
                if(number.length >= 5) {
                    formatted += ') ' + number.substring(4, 7);
                }
                if(number.length >= 8) {
                    formatted += '-' + number.substring(7, 9);
                }
                if(number.length >= 10) {
                    formatted += '-' + number.substring(9, 11);
                }
                
                
                e.target.value = formatted;
            });

           
            phoneInput.addEventListener('change', function(e) {
                e.target.value = e.target.value.replace(/[^\d+()-\s]/g, '');
            });
        });
    </script>

</head>
<body>
    <div class="registration-card">
        <form method="POST" class="auth-form">
            <h2 class="text-center mb-2">Регистрация</h2>
            
            <?php if(isset($e)): ?>
            <div class="error-message"><?= $e->getMessage() ?></div>
            <?php endif; ?>

            <div class="form-group">
                <label>ФИО</label>
                <input type="text" class="form-control" name="FCS" required 
                    pattern="^[А-Яа-яЁё\s\-]{2,}$"
                    title="Только русские буквы, пробелы и дефисы"
                    placeholder="Иванов Иван Иванович"
                    oninput="this.setCustomValidity('')"
                    oninvalid="this.setCustomValidity('Используйте только русские буквы, пробелы и дефисы')">
            </div>


            <div class="form-group">
                <label>Логин</label>
                <input type="text" class="form-control" name="login" required 
                       placeholder="Придумайте логин">
            </div>

            <div class="form-group">
                <label>Пароль</label>
                <input type="password" class="form-control" name="password" required 
                       placeholder="Не менее 8 символов">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" name="email" required 
                       placeholder="example@mail.ru">
            </div>

            <div class="form-group">
                <label>Телефон</label>
                <input type="tel" class="form-control" id="phone" name="phone" required 
                       placeholder="+7 (999) 999-99-99" maxlength="18" minlength="5">
            </div>

            <button type="submit" class="btn btn-primary w-100">Зарегистрироваться</button>
            
            <p class="text-center mt-2">
                Уже есть аккаунт? <a href="autho.php" class="link">Войти</a>
            </p>
        </form>
    </div>
</body>
</html>