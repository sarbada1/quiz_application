<div class="container">
    <h1>Bulk Question Management</h1>
    
    <?php if (isset($_SESSION['message'])): ?>
        <div id="alert" class="alert alert-<?= $_SESSION['status'] ?>" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <?= $_SESSION['message'] ?>
        </div>
        <?php unset($_SESSION['message']); unset($_SESSION['status']); ?>
    <?php endif; ?>

    <div class="filter-section mb-4">
        <div class="card">
            <div class="card-header">
                <h5>Filter Questions</h5>
            </div>
            <div class="card-body">
                <form id="filterForm" method="get" action="<?= $url('admin/question/bulk-manage') ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tag_filter">Filter by Tag:</label>
                                <select name="tag_filter" id="tag_filter" class="form-control select2">
                                    <option value="">All Tags</option>
                                    <?php foreach ($tags as $tag): ?>
                                        <option value="<?= $tag['id'] ?>" <?= ($selectedTag == $tag['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($tag['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="category_filter">Filter by Category:</label>
                                <select name="category_filter" id="category_filter" class="form-control select2">
                                    <option value="">All Categories</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" <?= ($selectedCategory == $category['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="<?= $url('admin/question/bulk-manage') ?>" class="btn btn-secondary">Reset Filters</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="stats-section mb-4">
        <div class="row">
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Questions</h5>
                        <h2 class="mb-0"><?= $totalQuestions ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Tags</h5>
                        <h2 class="mb-0"><?= count($tags) ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Categories</h5>
                        <h2 class="mb-0"><?= count($categories) ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($questionsByTag)): ?>
        <div class="alert alert-info">
            No questions found matching your filter criteria.
        </div>
    <?php else: ?>
        <div class="accordion" id="questionAccordion">
            <?php foreach ($questionsByTag as $tagId => $tagData): ?>
                <div class="card mb-3">
                    <div class="card-header" id="heading<?= $tagId ?>">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="tag-info">
                                <h5 class="mb-0">
                                    <button class="btn btn-link collapsed accordion-toggle" type="button" 
                                            data-toggle="collapse" data-target="#collapse<?= $tagId ?>" 
                                            aria-expanded="false" aria-controls="collapse<?= $tagId ?>">
                                        <i class="fas fa-plus-circle toggle-icon"></i>
                                        <?= htmlspecialchars($tagData['name']) ?>
                                        <span class="badge badge-primary ml-2"><?= count($tagData['questions']) ?> Questions</span>
                                    </button>
                                </h5>
                            </div>
                            <div class="bulk-actions">
                                <button type="button" class="btn btn-sm btn-warning bulk-edit-btn" 
                                       data-tag-id="<?= $tagId ?>" data-tag-name="<?= htmlspecialchars($tagData['name']) ?>">
                                    Bulk Edit
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="collapse<?= $tagId ?>" class="collapse" aria-labelledby="heading<?= $tagId ?>" 
                         data-parent="#questionAccordion">
                        <div class="card-body">
                            <div class="tag-categories mb-3">
                                <strong>Categories Used:</strong>
                                <?php foreach ($tagData['categories'] as $category): ?>
                                    <span class="badge badge-info"><?= htmlspecialchars($category['name']) ?> (<?= $category['count'] ?>)</span>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-striped question-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px">ID</th>
                                            <th>Question</th>
                                            <th style="width: 180px">Category</th>
                                            <th style="width: 100px">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="questionBody<?= $tagId ?>">
                                        <!-- Questions will be loaded here via AJAX pagination -->
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination controls for this tag -->
                            <div class="pagination-container mt-3" id="pagination<?= $tagId ?>">
                                <nav aria-label="Question pagination">
                                    <ul class="pagination justify-content-center pagination-sm">
                                        <!-- Pagination links will be generated by JavaScript -->
                                    </ul>
                                </nav>
                                <div class="text-center">
                                    <select class="page-size-select" id="pageSize<?= $tagId ?>">
                                        <option value="10">10 per page</option>
                                        <option value="25">25 per page</option>
                                        <option value="50">50 per page</option>
                                        <option value="100">100 per page</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Bulk Edit Modal -->
<div class="modal fade" id="bulkEditModal" tabindex="-1" role="dialog" aria-labelledby="bulkEditModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkEditModalLabel">Edit Questions for <span id="tagNameSpan"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="bulkEditForm">
                    <input type="hidden" id="bulkTagId" name="tag_id">
                    
                    <div class="form-group">
                        <label for="bulkCategoryId">Change Category:</label>
                        <select id="bulkCategoryId" name="category_id" class="form-control" required>
                            <option value="" disabled selected>Select a category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="confirm" name="confirm" required>
                            <label class="custom-control-label" for="confirm">
                                I understand this will update ALL questions under this tag
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="bulkUpdateBtn">Update Questions</button>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />

<style>
    .accordion .card-header {
        background-color: #f8f9fa;
        cursor: pointer;
    }
    
    .accordion .btn-link {
        color: #343a40;
        text-decoration: none;
        font-weight: 600;
        width: 100%;
        text-align: left;
        padding: 0;
    }
    
    .accordion .btn-link:hover,
    .accordion .btn-link:focus {
        text-decoration: none;
    }
    
    .accordion .btn-link .toggle-icon {
        transition: transform 0.3s;
        margin-right: 10px;
    }
    
    .accordion .btn-link:not(.collapsed) .toggle-icon {
        transform: rotate(45deg);
    }
    
    .tag-categories .badge {
        margin-right: 5px;
        padding: 6px 10px;
    }
    
    .question-table {
        font-size: 14px;
    }
    
    .select2-container .select2-selection--single {
        height: 38px;
        padding: 6px 12px;
    }
    
    .pagination-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }
    
    .page-size-select {
        padding: 5px;
        border-radius: 4px;
        border: 1px solid #ddd;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
    }
    
    .loading-spinner {
        display: flex;
        justify-content: center;
        padding: 20px 0;
    }
</style>

<script>
    // Store all questions by tag for local pagination
    const allQuestionsByTag = {};
    
    <?php foreach ($questionsByTag as $tagId => $tagData): ?>
        // Export all questions for this tag to JavaScript for client-side pagination
        allQuestionsByTag[<?= $tagId ?>] = <?= json_encode($tagData['questions']) ?>;
    <?php endforeach; ?>
    
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2();
        
        // Initialize pagination for each tag when accordion is opened
        $('.accordion-toggle').on('click', function() {
            const target = $(this).data('target');
            const tagId = target.replace('#collapse', '');
            
            // Check if we need to initialize pagination (first open)
            if ($(target).data('initialized') !== true) {
                initPagination(tagId);
                $(target).data('initialized', true);
            }
        });
        
        // Handle page size change
        $(document).on('change', '.page-size-select', function() {
            const tagId = this.id.replace('pageSize', '');
            initPagination(tagId);
        });
        
        // Bulk Edit Button Click Handler
        $('.bulk-edit-btn').on('click', function() {
            const tagId = $(this).data('tag-id');
            const tagName = $(this).data('tag-name');
            
            $('#bulkTagId').val(tagId);
            $('#tagNameSpan').text(tagName);
            $('#bulkCategoryId').val('').trigger('change'); // Reset category selector
            $('#confirm').prop('checked', false); // Uncheck confirmation
            
            $('#bulkEditModal').modal('show');
        });
        
        // Bulk Update Button Click Handler
        $('#bulkUpdateBtn').on('click', function() {
            const form = $('#bulkEditForm')[0];
            
            if (!form.checkValidity()) {
                // If form is not valid, trigger HTML5 validation
                $('<input type="submit">').hide().appendTo(form).click().remove();
                return;
            }
            
            const tagId = $('#bulkTagId').val();
            const categoryId = $('#bulkCategoryId').val();
            
            // Disable button and show loading state
            const $btn = $(this);
            const originalText = $btn.text();
            $btn.text('Updating...').prop('disabled', true);
            
            // Send AJAX request to update categories
            $.ajax({
                url: '<?= $url('admin/question/bulk-update-category') ?>',
                type: 'POST',
                data: {
                    tag_id: tagId,
                    category_id: categoryId
                },
                success: function(response) {
                    try {
                        const result = typeof response === 'string' ? JSON.parse(response) : response;
                        
                        if (result.success) {
                            // Show success message and reload the page
                            alert(result.message || 'Questions updated successfully');
                            location.reload();
                        } else {
                            // Show error message
                            alert(result.error || 'An error occurred while updating questions');
                            $btn.text(originalText).prop('disabled', false);
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                        alert('An unexpected error occurred');
                        $btn.text(originalText).prop('disabled', false);
                    }
                },
                error: function() {
                    alert('Failed to update questions. Please try again.');
                    $btn.text(originalText).prop('disabled', false);
                }
            });
        });
        
        // Auto-expand accordion if we have filters active
        if ('<?= $selectedTag ?>' || '<?= $selectedCategory ?>') {
            const collapseElements = $('.collapse');
            collapseElements.collapse('show');
            
            // Initialize pagination for each expanded accordion
            setTimeout(() => {
                collapseElements.each(function() {
                    const tagId = $(this).attr('id').replace('collapse', '');
                    initPagination(tagId);
                    $(this).data('initialized', true);
                });
            }, 300);
        }
    });
    
    /**
     * Initialize pagination for a specific tag's questions
     */
    function initPagination(tagId) {
        const questions = allQuestionsByTag[tagId] || [];
        const pageSize = parseInt($('#pageSize' + tagId).val() || 10);
        const totalPages = Math.max(1, Math.ceil(questions.length / pageSize));
        
        // Set current page to 1 initially
        renderPage(tagId, 1, pageSize);
        renderPagination(tagId, 1, totalPages);
    }
    
    /**
     * Render a specific page of questions for a tag
     */
    function renderPage(tagId, page, pageSize) {
        const questions = allQuestionsByTag[tagId] || [];
        const startIndex = (page - 1) * pageSize;
        const endIndex = Math.min(startIndex + pageSize, questions.length);
        const tableBody = $('#questionBody' + tagId);
        
        // Clear the table
        tableBody.empty();
        
        // If no questions, show a message
        if (questions.length === 0) {
            tableBody.append(`
                <tr>
                    <td colspan="4" class="text-center">No questions found for this tag</td>
                </tr>
            `);
            return;
        }
        
        // Add questions for this page
        for (let i = startIndex; i < endIndex; i++) {
            const question = questions[i];
            tableBody.append(`
                <tr data-question-id="${question.id}">
                    <td>${question.id}</td>
                    <td>
                        ${escapeHtml(question.question_text.substring(0, 100))}
                        ${question.question_text.length > 100 ? '...' : ''}
                    </td>
                    <td>
                        ${escapeHtml(question.category_name || 'Uncategorized')}
                    </td>
                    <td>
                        <a href="${'<?= $url('admin/question/edit/') ?>' + question.id}" 
                           class="btn btn-sm btn-primary">Edit</a>
                    </td>
                </tr>
            `);
        }
    }
    
    /**
     * Render pagination controls for a specific tag
     */
    function renderPagination(tagId, currentPage, totalPages) {
        const pagination = $('#pagination' + tagId + ' .pagination');
        pagination.empty();
        
        // Don't show pagination if there's only 1 page
        if (totalPages <= 1) {
            return;
        }
        
        // Previous button
        pagination.append(`
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage - 1}" data-tag-id="${tagId}">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        `);
        
        // Page numbers
        const maxPages = 5;
        const start = Math.max(1, currentPage - Math.floor(maxPages / 2));
        const end = Math.min(totalPages, start + maxPages - 1);
        
        for (let i = start; i <= end; i++) {
            pagination.append(`
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}" data-tag-id="${tagId}">${i}</a>
                </li>
            `);
        }
        
        // Next button
        pagination.append(`
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage + 1}" data-tag-id="${tagId}">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        `);
        
        // Add click handlers for pagination
        pagination.find('a.page-link').on('click', function(e) {
            e.preventDefault();
            
            const page = parseInt($(this).data('page'));
            const tagId = $(this).data('tag-id');
            const pageSize = parseInt($('#pageSize' + tagId).val() || 10);
            const totalPages = Math.ceil((allQuestionsByTag[tagId] || []).length / pageSize);
            
            // Ensure page is within valid range
            if (page < 1 || page > totalPages || isNaN(page)) {
                return;
            }
            
            renderPage(tagId, page, pageSize);
            renderPagination(tagId, page, totalPages);
        });
    }
    
    /**
     * Escape HTML special characters to prevent XSS
     */
    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
</script>