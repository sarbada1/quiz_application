<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Questions Management</h2>
        </div>
        <div class="col-md-4 text-right">
            <a href="<?= $url('admin/question/add') ?>" class="btn btn-success">
                <i class="fas fa-plus"></i> Add New Question
            </a>
            <a href="<?= $url('admin/question/bulk-manage') ?>" class="btn btn-info ml-2">
                <i class="fas fa-tasks"></i> Bulk Manage
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Filter Questions</h5>
        </div>
        <div class="card-body">
            <form id="filterForm" method="GET" class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="category"><i class="fas fa-folder"></i> Category:</label>
                        <select name="category" id="category" class="form-control select2">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= $selectedCategory == $category['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name'] ?? '') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tag"><i class="fas fa-tag"></i> Tag:</label>
                        <select name="tag" id="tag" class="form-control select2">
                            <option value="">All Tags</option>
                            <?php foreach ($tags as $tag): ?>
                                <option value="<?= $tag['id'] ?>" <?= ($selectedTag == $tag['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tag['name'] ?? '') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-group w-100">
                        <button type="submit" class="btn btn-primary mr-2">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                        <a href="<?= $url('admin/question/list') ?>" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Question List</h5>
                <span class="badge badge-primary"><?= count($questions) ?> Questions</span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="questionsTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th width="40">#</th>
                            <th>Question</th>
                            <th width="150">Category</th>
                            <th width="80">Marks</th>
                            <th width="100">Answers</th>
                            <th width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        foreach ($questions as $question): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td>
                                    <div class="question-text">
                                        <?= htmlspecialchars($question['question_text'] ?? '') ?>
                                    </div>
                                    <?php if (!empty($question['tag_names'])): ?>
                                        <div class="question-tags mt-1">
                                            <?php foreach (explode(',', $question['tag_names'] ?? '') as $tagName): ?>
                                                <?php if (!empty(trim($tagName))): ?>
                                                    <span class="badge badge-info"><?= htmlspecialchars(trim($tagName)) ?></span>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($question['category_name'])): ?>
                                        <span class="badge badge-secondary">
                                            <?= htmlspecialchars($question['category_name'] ?? 'Uncategorized') ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Uncategorized</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center"><?= $question['marks'] ?? 1 ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= $url('admin/answer/add/' . $question['id']) ?>" 
                                           class="btn btn-sm btn-outline-primary" 
                                           data-toggle="tooltip" title="Add Answers">
                                            <i class="fas fa-plus-circle"></i>
                                        </a>
                                        <a href="<?= $url('admin/answer/list/' . $question['id']) ?>" 
                                           class="btn btn-sm btn-outline-info" 
                                           data-toggle="tooltip" title="View Answers">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= $url('admin/question/edit/' . $question['id']) ?>" 
                                           class="btn btn-sm btn-outline-primary"
                                           data-toggle="tooltip" title="Edit Question">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= $url('admin/question/delete/' . $question['id']) ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Are you sure you want to delete this question?')"
                                           data-toggle="tooltip" title="Delete Question">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($questions)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        No questions found matching your criteria.
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add needed CSS and JS libraries -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    /* Enhanced styles for better UI */
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: none;
        margin-bottom: 2rem;
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
    
    .select2-container .select2-selection--single {
        height: 38px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    
    .badge {
        font-size: 85%;
        font-weight: 500;
        padding: 0.4em 0.6em;
    }
    
    .badge-info {
        background-color: #17a2b8;
    }
    
    .badge-secondary {
        background-color: #6c757d;
    }
    
    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }
    
    .badge-primary {
        background-color: #007bff;
    }
    
    .question-text {
        max-width: 500px;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    
    .question-tags .badge {
        margin-right: 3px;
    }
    
    .table th {
        background-color: #f8f9fa;
        font-weight: 500;
        border-top: none;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.04);
    }
    
    .btn-group .btn {
        margin: 0 1px;
    }
    
    /* Custom DataTables styling */
    div.dataTables_wrapper div.dataTables_filter {
        text-align: right;
        margin-bottom: 15px;
    }
    
    div.dataTables_wrapper div.dataTables_filter input {
        margin-left: 0.5em;
        width: 250px;
        padding: 6px 12px;
        border: 1px solid #ced4da;
        border-radius: 4px;
    }
    
    div.dataTables_wrapper div.dataTables_info {
        padding-top: 0.85em;
    }
    
    div.dataTables_wrapper div.dataTables_paginate {
        margin-top: 15px;
    }
    
    div.dataTables_wrapper div.dataTables_paginate ul.pagination {
        margin: 2px 0;
        white-space: nowrap;
        justify-content: flex-end;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 4px !important;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #007bff !important;
        border-color: #007bff !important;
        color: white !important;
    }
    
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 4px 8px;
    }
</style>

<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            width: '100%'
        });
        
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // Initialize DataTable
        $('#questionsTable').DataTable({
            "paging": true,
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "language": {
                "search": "Search questions:",
                "lengthMenu": "Show _MENU_ questions per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ questions",
                "infoEmpty": "Showing 0 to 0 of 0 questions",
                "emptyTable": "No questions available",
                "zeroRecords": "No matching questions found"
            },
            "dom": '<"top"lf>rt<"bottom"ip><"clear">'
        });
    });
</script>