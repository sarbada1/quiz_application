<div class="container">
    <h2>Questions</h2>
    <div class="mb-4">
        <h4>Filter Questions</h4>
        <div class="filter-container mb-4">
            <form id="categoryFilterForm" method="GET" class="filter-form">
                <div class="form-group">
                    <label for="category">Category:</label>
                    <select name="category" id="category" class="form-control">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" <?= $selectedCategory == $category['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="<?= $url('admin/question/list') ?>" class="btn btn-secondary">Reset</a>
            </form>
        </div>
    </div>

    <table id="questionsTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Question</th>
                <th>Category</th>
                <th>Marks</th>
                <th>Answer</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            foreach ($questions as $question): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($question['question_text']) ?></td>
                    <td><?= htmlspecialchars($question['category_name']) ?></td>
                    <td><?= $question['marks'] ?></td>
                    <td>
                        <a href="<?= $url('admin/answer/add/' . $question['id']) ?>" class="btn-sm btn-primary"> <i class="fas fa-table"></i> </a>
                        <a href="<?= $url('admin/answer/list/' . $question['id']) ?>" class="btn-sm btn-danger"><i class="fas fa-eye"></i></a>
                    </td>
                    <td>
                        <a href="<?= $url('admin/question/edit/' . $question['id']) ?>" class="btn-sm btn-primary"><i class="fas fa-pen"></i></a>
                        <a href="<?= $url('admin/question/delete/' . $question['id']) ?>" class="btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add DataTables CSS and JS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTable with no server-side processing
        var table = $('#questionsTable').DataTable({
            "paging": true,
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "language": {
                "search": "Search questions:"
            }
        });
        
        // Fix for category filter
        $('#categoryFilterForm').on('submit', function(e) {
            var categoryId = $('#category').val();
            // Only prevent default if no category selected
            if (!categoryId) {
                e.preventDefault();
                window.location.href = '<?= $url('admin/question/list') ?>';
            }
            // Otherwise, let the form submit normally with GET parameters
        });
    });
</script>

<style>
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 15px;
    }
    
    .dataTables_wrapper .dataTables_length select {
        min-width: 60px;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.5em 1em;
        margin-left: 5px;
        border-radius: 4px;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #3498db;
        border-color: #3498db;
        color: white !important;
    }
    
    /* Fix icon styles */
    .fas {
        display: inline-block;
        width: 16px;
        text-align: center;
    }
    
    /* Add some button styling */
    .btn-sm {
        display: inline-block;
        padding: 5px 10px;
        margin: 2px;
        border-radius: 3px;
        text-decoration: none;
    }
    
    .btn-primary {
        background-color: #3498db;
        color: white;
    }
    
    .btn-danger {
        background-color: #e74c3c;
        color: white;
    }
</style>