<?php
/**
 * ============================================================================
 *  LiteBansU - Secure Password Hash Generator
 * ============================================================================
 *
 *  Plugin Name:   LiteBansU
 *  Description:   A modern, secure, and responsive web interface for LiteBans punishment management system.
 *  Version:       3.0
 *  Market URI:    https://builtbybit.com/resources/litebansu-litebans-website.69448/
 *  Author URI:    https://yamiru.com
 *  License:       MIT
 *  License URI:   https://opensource.org/licenses/MIT
 *
 *  This tool generates a secure BCRYPT password hash for use in your .env file.
 *  ?? WARNING: Remove this file after generating your hash to prevent misuse.
 * ============================================================================
 */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Hash Generator - LiteBansU</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --primary: #007bff;
            --light: #f8f9fa;
            --dark: #343a40;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--light);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .box {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }

        h1 {
            text-align: center;
            color: var(--dark);
            margin-bottom: 1rem;
        }

        p.notice {
            font-size: 0.95rem;
            background: #fff3cd;
            padding: 1rem;
            border: 1px solid #ffeeba;
            border-radius: 0.5rem;
            color: #856404;
            margin-bottom: 1.5rem;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input[type="password"] {
            padding: 0.8rem;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }

        button {
            padding: 0.8rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        .output {
            margin-top: 1.5rem;
            background: #e9ecef;
            padding: 1rem;
            border-radius: 0.5rem;
            font-family: monospace;
            word-break: break-all;
            position: relative;
        }

        .copy-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #6c757d;
            color: white;
            border: none;
            padding: 4px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.8rem;
        }

        .copy-btn:hover {
            background: #343a40;
        }

        .footer {
            margin-top: 2rem;
            font-size: 0.85rem;
            text-align: center;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>Generate BCRYPT Password Hash</h1>
        <p class="notice">
            Enter your password below to generate a secure BCRYPT hash.<br>
            Use the result in your <code>.env</code> file like this:<br>
            <strong>ADMIN_PASSWORD=</strong><code>[generated hash]</code><br><br>
            ?? <strong>Important:</strong> For security, delete this file after using it.
        </p>

        <form method="post">
            <input type="password" name="password" placeholder="Enter password" required>
            <button type="submit">Generate Hash</button>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["password"])) {
            $password = $_POST["password"];
            $options = ['cost' => 10];
            $hash = password_hash($password, PASSWORD_BCRYPT, $options);
            echo "<div class='output' id='hashOutput'>
                    <strong>ADMIN_PASSWORD=</strong><span id='hashText'>$hash</span>
                    <button class='copy-btn' onclick='copyHash()'>Copy</button>
                  </div>";
        }
        ?>

        <div class="footer">
            &copy; <?= date("Y") ?> LiteBansU - built by <a href="https://yamiru.com" target="_blank">Yamiru</a>
        </div>
    </div>

    <script>
        function copyHash() {
            const text = document.getElementById("hashText").innerText;
            navigator.clipboard.writeText("ADMIN_PASSWORD=" + text).then(() => {
                alert("Hash copied to clipboard.");
            });
        }
    </script>
</body>
</html>
