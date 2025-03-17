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
<div class="container">
    <h2>Import Questions</h2>
    
    <!-- Download template button -->
    <div class="mb-4">
        <a href="<?= $url('admin/question/template/download') ?>" class="btn btn-primary">
            Download CSV Template
        </a>
    </div>

    <div class="mb-4">
        <h4>Instructions:</h4>
        <ol>
            <li>Download the CSV template using the button above</li>
            <li>Open the template in Excel or similar spreadsheet software</li>
            <li>Fill in your questions following the sample format</li>
            <li>Save as CSV (Comma delimited)</li>
            <li>Upload the saved CSV file below</li>
        </ol>
    </div>

    <!-- Import form -->
    <form method="post" action="<?= $url('admin/question/import') ?>" enctype="multipart/form-data">
        <div class="form-group">
            <label for="csv_file">Upload Questions CSV File</label>
            <input type="file" name="csv_file" accept=".csv" required class="form-control">
        </div>
        <button type="submit" class="btn btn-primary mt-3">Import Questions</button>
    </form>
</div>