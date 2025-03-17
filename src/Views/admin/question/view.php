<div class="container">
    <h2>Questions</h2>
    <div class="mb-4">
        <h4>Filter Questions</h4>
        <div class="filter-container mb-4">
    <form method="GET" class="filter-form">
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



    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Question</th>
                <th>Category</th>
                <!-- <th>Difficulty</th> -->
                <th>Marks</th>
                <th>Answer</th>
                <!-- <th>Tags</th> -->
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
                    <!-- <td><?= htmlspecialchars($question['difficulty_level']) ?></td> -->
                    <td><?= $question['marks'] ?></td>
                    <!-- <td><?= htmlspecialchars($question['tags'] ?? '') ?></td> -->
                    <td>
                        <a href="<?= $url('admin/answer/add/<?= $question[') ?>"id'] ?>" class="btn flex btn-primary">Add</a>
                        <a href="<?= $url('admin/answer/list/<?= $question[') ?>"id'] ?>"
                            class="btn flex btn-danger">View</a>
                    </td>
                    <td>
                        <a href="<?= $url('admin/question/edit/<?= $question[') ?>"id'] ?>" class="btn flex btn-primary">Edit</a>
                        <a href="<?= $url('admin/question/delete/<?= $question[') ?>"id'] ?>"
                            class="btn flex btn-danger"
                            onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($totalPages > 1): ?>
    <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center">
            <!-- Previous Button -->
            <?php if ($currentPage > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $currentPage - 1 ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php
            $startPage = max(1, $currentPage - 2);
            $endPage = min($totalPages, $currentPage + 2);

            if ($startPage > 1) {
                echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
                if ($startPage > 2) {
                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
            }

            for ($i = $startPage; $i <= $endPage; $i++): ?>
                <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor;

            if ($endPage < $totalPages) {
                if ($endPage < $totalPages - 1) {
                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
                echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . '">' . $totalPages . '</a></li>';
            }
            ?>

            <!-- Next Button -->
            <?php if ($currentPage < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $currentPage + 1 ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>
</div>

<style>
.pagination {
    gap: 5px;
    display: flex;
    list-style: none;
}

.page-link {
    border-radius: 4px;
    padding: 8px 16px;
    color: #2c3e50;
    border: 1px solid #ddd;
}

.page-item.active .page-link {
    background-color: #3498db;
    border-color: #3498db;
}

.page-link:hover {
    background-color: #f8f9fa;
    color: #2c3e50;
}

.page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
  
    background-color: #fff;
}
.page-item.active .page-link {
    background-color: #3498db;
    border-color: #3498db;
    color: white;
    box-shadow: 0 2px 4px rgba(52, 152, 219, 0.2);
}

</style>