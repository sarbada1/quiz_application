<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="/src/Views/admin/css/style.css">
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


</head>

<body>
    <div class="layout-container">
        <?php include __DIR__ . '/partials/sidebar.php'; ?>
        <div class="content-wrapper">
            <?php include __DIR__ . '/partials/header.php'; ?>
            <main class="main-content">
                <div class="card">
                    <?php echo $content; ?>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <script src="/src/Views/admin/js/script.js"></script>
    <script src="/src/Views/admin/js/quiz-sets.js"></script>
    
</body>

</html>