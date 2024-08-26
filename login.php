<?php
// Check if the user is already logged in via cookies
if (isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
    $username = $_COOKIE['username'];
    $password = $_COOKIE['password'];

    $user_file = 'users/' . $username . '.txt';

    if (file_exists($user_file)) {
        $userData = file_get_contents($user_file);
        $lines = explode("\n", $userData);

        if (isset($lines[1])) {
            $storedPassword = trim(explode(':', $lines[1])[1]);

            if ($password === $storedPassword) {
                // Cookies are valid, redirect to start.php
                header('Location: start.php');
                exit;
            } else {
                // Invalid password in cookie, clear cookies
                setcookie('username', '', time() - 3600);
                setcookie('password', '', time() - 3600);
            }
        }
    }
}

// Handle the login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $user_file = 'users/' . $username . '.txt';

    if (file_exists($user_file)) {
        $userData = file_get_contents($user_file);
        $lines = explode("\n", $userData);

        if (isset($lines[1])) {
            $storedPassword = trim(explode(':', $lines[1])[1]);

            if ($password === $storedPassword) {
                // Successful login
                // Set cookies
                $expire = time() + (2 * 24 * 60 * 60); // 2 days
                setcookie('username', $username, $expire);
                setcookie('password', $password, $expire);
                setcookie('expiry_date', time(), $expire);

                header('Location: start.php');
                exit;
            } else {
                echo "<script>alert('Incorrect password');</script>";
            }
        } else {
            echo "<script>alert('Invalid user data');</script>";
        }
    } else {
        echo "<script>alert('Username not found');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <style>
      @import url("https://fonts.googleapis.com/css2?family=Open+Sans:wght@200;300;400;500;600;700&display=swap");

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Open Sans", sans-serif;
    }

    body {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      width: 100%;
      padding: 0;
    }

    body::before {
      content: "";
      position: absolute;
      width: 100%;
      height: 100%;
      background: url("image.jpg");
      background-position: center;
      background-size: cover;
    }

    .wrapper {
      width: 400px;
      border-radius: 8px;
      padding: 30px;
      text-align: center;
      border: 1px solid rgba(255, 255, 255, 0.5);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
    }

    form {
      display: flex;
      flex-direction: column;
    }

    h2 {
      font-size: 2rem;
      margin-bottom: 20px;
      color: #fff;
    }

    .input-field {
      position: relative;
      border-bottom: 2px solid #ccc;
      margin: 15px 0;
    }

    .input-field label {
      position: absolute;
      top: 50%;
      left: 0;
      transform: translateY(-50%);
      color: #fff;
      font-size: 16px;
      pointer-events: none;
      transition: 0.15s ease;
    }

    .input-field input {
      width: 100%;
      height: 40px;
      background: transparent;
      border: none;
      outline: none;
      font-size: 16px;
      color: #fff;
    }

    .input-field input:focus~label,
    .input-field input:valid~label {
      font-size: 0.8rem;
      top: 10px;
      transform: translateY(-120%);
    }

    .forget {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin: 25px 0 35px 0;
      color: #fff;
    }

    #remember {
      accent-color: #f2eeee;
    }

    .forget label {
      display: flex;
      align-items: center;
    }

    .forget label p {
      margin-left: 8px;
    }

    .wrapper a {
      color: #efefef;
      text-decoration: none;
    }

    .wrapper a:hover {
      text-decoration: underline;
    }

    button {
      background: #fff;
      color: #000;
      font-weight: 600;
      border: none;
      padding: 12px 20px;
      cursor: pointer;
      border-radius: 3px;
      font-size: 16px;
      border: 2px solid transparent;
      transition: 0.3s ease;
    }

    button:hover {
      color: #fff;
      border-color: #fff;
      background: rgba(255, 255, 255, 0.15);
    }

    .register {
      text-align: center;
      margin-top: 30px;
      color: #fff;
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <form method="post" action="">
      <h2>Login</h2> 
        <div class="input-field">
        <input type="text" name="username" required>
        <label>Username</label>
      </div>
      <div class="input-field">
        <input type="password" name="password" required>
        <label>Password</label>
      </div>
      <div class="forget">
        <label for="remember">
          <input type="checkbox" id="remember">
          <p>Remember me</p>
        </label>
        <a href="#">Forgot password?</a>
      </div>
      <button type="submit">Log In</button>
      <div class="register">
        <p>Don't have an account? <a href="register.php">Register</a></p>
      </div> 
    </form>
  </div>
</body>
</html>
