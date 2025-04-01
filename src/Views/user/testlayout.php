<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz application</title>
    <link rel="stylesheet" href="<?= $url('src/Views/user/css/custom.css') ?>">
    <!-- <link rel="stylesheet" href="<?= $url('src/Views/user/css/style.css') ?>"> -->
    <link rel="stylesheet" href="<?= $url('src/Views/user/css/mockteststyle.css') ?>">
    <!-- Add this to your page head or at the top of exam.php -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
</head>

<body>

    <?php echo $content; ?>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <script src="<?= $url('src/Views/user/js/script.js') ?>"></script>
</body>

</html>