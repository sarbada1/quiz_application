<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Login</title>
  <link rel="stylesheet" href="/src/Views/admin/auth/css/style.css">
</head>
<body>
  <div class="container">
    <div class="login-form">
      <h1>Register</h1>
      <form method="POST" action="/admin/login">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Login</button>
      </form>
      <p>Forgot password?</p>
    </div>
  </div>
  <script src="/src/Views/admin/auth/js/script.js"></script>
</body>
</html>
