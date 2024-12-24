
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz application</title>
    <link rel="stylesheet" href="/src/Views/user/css/style.css">
    <link rel="stylesheet" href="/src/Views/user/css/custom.css">
</head>

<body>
    <div class="layout-container">
    <?php include __DIR__ . '/partials/header.php'; ?>
    
    <?php echo $content; ?>
    <?php include __DIR__ . '/partials/footer.php'; ?>

    </div>
  
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="/src/Views/user/js/script.js"></script>
</body>

</html>