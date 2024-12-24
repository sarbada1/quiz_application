
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Login</title>
  <link rel="stylesheet" href="/src/Views/admin/auth/css/style.css">
  <!-- <link rel="stylesheet" href="/src/Views/admin/css/style.css">   -->
</head>
<body>
  <div class="container">
    <div class="login-form">
    <?php if (isset($_SESSION['message'])): ?>
    <div id="alert" class="alert alert-<?= $_SESSION['status'] ?>" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <?= $_SESSION['message'] ?>
    </div>
<?php
    unset($_SESSION['message']);
    unset($_SESSION['status']);
endif;
?>
      <h1>Login</h1>
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
  <script src="/src/Views/admin/js/script.js"></script>
</body>
</html>
