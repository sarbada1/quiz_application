<?php if (isset($_SESSION['message'])): ?>
    <div id="alert" class="alert alert-<?= $_SESSION['status'] ?>" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <?= $_SESSION['message'] ?>
    </div>
    <?php unset($_SESSION['message']);
    unset($_SESSION['status']); ?>
<?php endif; ?>

<h1>List Tags</h1>
<div class="row">
    <div class="breadcrumb">
        <a href="<?= $url('admin/tag/list') ?>">Tags</a>
        <i class="fas fa-chevron-right"></i>
        <a href="#" style="margin-left: 7px;cursor:default">List</a>
    </div>
    <div>
        <button class='success mb-5'><a href="<?= $url('admin/tag/add') ?>">Add Tag</a></button>
    </div>
</div>

<table id="tagTable" class="display">
    <thead>
        <tr>
            <th>SN</th>
            <th>Name</th>
            <th>Slug</th>
            <th>Categories</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php $i = 1;
        foreach ($tags as $tag): ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($tag['name']) ?></td>
                <td><?= htmlspecialchars($tag['slug']) ?></td>
                <td>
                    <span class="badge badge-info"><?= $tag['category_count'] ?? 0 ?> categories</span>
                    <button class="primary manage-categories-btn" data-tag-id="<?= $tag['id'] ?>" 
                            data-tag-name="<?= htmlspecialchars($tag['name']) ?>">
                        Manage Categories
                    </button>
                </td>
                <td>
                    <button class="primary"><a href="<?= $url('admin/tag/edit/' . $tag['id']) ?>">Edit</a></button>
                    <button class="danger"><a href="<?= $url('admin/tag/delete/' . $tag['id']) ?>" onclick="return confirm('Are you sure to delete?')">Delete</a></button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Modal for Managing Categories -->
<div id="manageCategoriesModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Manage Categories for <span id="tagNameSpan"></span></h2>
            <span class="close">&times;</span>
        </div>
    <div class="modal-body">
    <form id="categoryAssociationForm">
        <input type="hidden" id="tagId" name="tagId">
        
        <div class="alert alert-info">
            <p><strong>Note:</strong> Only top-level categories are shown. When you select a top-level category, 
            all its child categories will be automatically associated with this tag.</p>
        </div>
        
        <div class="search-container mb-3">
            <input type="text" id="categorySearch" placeholder="Search top-level categories...">
        </div>
        
        <div class="category-list">
            <div class="loading">Loading top-level categories...</div>
        </div>
        
        <button type="submit" class="success mt-3">Save Associations</button>
    </form>
</div>
    </div>
</div>

<!-- Add required CSS for DataTables -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

<!-- Add required JS for DataTables -->
<script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

<style>
    /* DataTables Styling */
    #tagTable {
        width: 100%;
        margin-top: 20px;
    }
    
    #tagTable_filter input {
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-left: 10px;
    }
    
    #tagTable_length select {
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    
    .action-btn {
        margin-right: 5px;
    }
    
    .action-btn a {
        text-decoration: none;
        color: white;
        display: block;
    }
    
    #tagTable thead th {
        background-color: #f5f5f5;
    }
    
    #tagTable tbody tr:hover {
        background-color: #f9f9f9;
    }
    
    /* Modal Styling */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.4);
    }
    
    .modal-content {
        background-color: #fefefe;
        margin: 10% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 70%;
        max-width: 800px;
        border-radius: 5px;
        position: relative;
    }
    
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }
    
    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
    
    .modal-header {
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
        margin-bottom: 20px;
    }
    
    .category-list {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #ddd;
        padding: 10px;
        border-radius: 5px;
    }
    
    .category-item {
        display: flex;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #eee;
    }
    
    .category-item:last-child {
        border-bottom: none;
    }
    
    .category-item label {
        margin-left: 10px;
        flex: 1;
    }
    
    .parent-info {
        color: #777;
        font-size: 0.85em;
        margin-left: 10px;
    }
    
    #categorySearch {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    
    .loading {
        text-align: center;
        padding: 20px;
        color: #777;
    }
</style>

<script>
    // Initialize DataTable
    $(document).ready(function() {
        $('#tagTable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            columnDefs: [
                { orderable: false, targets: [3, 4] } // Disable sorting on categories and actions columns
            ],
            language: {
                search: "Search tags:",
                lengthMenu: "Show _MENU_ tags per page",
                info: "Showing _START_ to _END_ of _TOTAL_ tags",
                infoEmpty: "No tags found",
                emptyTable: "No tags available"
            }
        });
        
        // Get modal elements
        const modal = document.getElementById("manageCategoriesModal");
        const closeBtn = document.getElementsByClassName("close")[0];
        
        // Close modal when clicking X
        closeBtn.onclick = function() {
            modal.style.display = "none";
        };
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        };
        
        // Open modal and load categories when Manage Categories button is clicked
        $('.manage-categories-btn').on('click', function() {
            const tagId = $(this).data('tag-id');
            const tagName = $(this).data('tag-name');
            
            // Set tag name and ID in the modal
            $('#tagNameSpan').text(tagName);
            $('#tagId').val(tagId);
            
            // Show modal
            modal.style.display = "block";
            
            // Load categories for this tag
            loadCategoriesForTag(tagId);
        });
        
        // Handle category search
        $('#categorySearch').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();
            
            $('.category-item').each(function() {
                const categoryText = $(this).text().toLowerCase();
                if (categoryText.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
        
        // Handle form submission
        $('#categoryAssociationForm').on('submit', function(e) {
            e.preventDefault();
            
            const tagId = $('#tagId').val();
            const selectedCategories = [];
            
            $('input[name="categories"]:checked').each(function() {
                selectedCategories.push($(this).val());
            });
            
            // Send AJAX request to save associations
            $.ajax({
                url: '<?= $url('admin/tag/associate-categories') ?>',
                type: 'POST',
                data: {
                    tagId: tagId,
                    categories: selectedCategories
                },
                success: function(response) {
                    // Parse response if it's a JSON string
                    let result;
                    try {
                        result = JSON.parse(response);
                    } catch (e) {
                        result = response;
                    }
                    
                    // Show success message
                    alert(result.message || 'Categories updated successfully!');
                    
                    // Close modal
                    modal.style.display = "none";
                },
                error: function(xhr) {
                    let errorMsg = 'An error occurred while saving category associations.';
                    
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMsg = response.message || errorMsg;
                    } catch (e) {
                        console.error(e);
                    }
                    
                    alert(errorMsg);
                }
            });
        });
    });
    
    // Function to load categories for a specific tag
    function loadCategoriesForTag(tagId) {
        const categoryListContainer = $('.category-list');
        
        // Show loading indicator
        categoryListContainer.html('<div class="loading">Loading categories...</div>');
        
        // Fetch categories from server
        $.ajax({
            url: '<?= $url('admin/tag/get-categories-for-tag/') ?>' + tagId,
            type: 'GET',
            success: function(response) {
                // Parse response if it's a JSON string
                let data;
                try {
                    data = JSON.parse(response);
                } catch (e) {
                    data = response;
                }
                
                if (data.categories && data.categories.length > 0) {
                    let html = '';
                    
                    // Generate checkboxes for all categories
                    data.categories.forEach(function(category) {
                        const isAssociated = data.associatedCategoryIds.includes(parseInt(category.id));
                        const parentInfo = category.parent_name ? 
                            `<span class="parent-info">(Parent: ${category.parent_name})</span>` : 
                            '<span class="parent-info">(Top Level)</span>';
                        
                        html += `
                            <div class="category-item">
                                <input type="checkbox" name="categories" id="category-${category.id}" 
                                       value="${category.id}" ${isAssociated ? 'checked' : ''}>
                                <label for="category-${category.id}">
                                    ${category.name} ${parentInfo}
                                </label>
                            </div>
                        `;
                    });
                    
                    categoryListContainer.html(html);
                } else {
                    categoryListContainer.html('<div class="empty-message">No categories available</div>');
                }
            },
            error: function() {
                categoryListContainer.html('<div class="error-message">Failed to load categories</div>');
            }
        });
    }
</script>