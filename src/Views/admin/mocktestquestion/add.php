<div class="category-allocation-summary">
    <h4>Category Allocation</h4>
    <div class="card-deck">
        <?php foreach ($categoryAllocations as $catId => $allocation): ?>
            <?php
            $currentCount = $existingQuestionsByCategory[$catId] ?? 0;
            $isFull = $currentCount >= $allocation['number_of_questions'];
            ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($allocation['name']) ?></h5>
                    <p class="card-text">
                        Questions: <span id="category_counter_<?= $catId ?>" class="category-counter <?= $isFull ? 'full' : '' ?>">
                            <?= $currentCount ?>/<?= $allocation['number_of_questions'] ?>
                        </span>
                        <button type="button" class="btn btn-sm btn-primary edit-allocation"
                            data-category-id="<?= $catId ?>"
                            data-category-name="<?= htmlspecialchars($allocation['name']) ?>"
                            data-current-questions="<?= $allocation['number_of_questions'] ?>"
                            data-current-marks="<?= $allocation['marks_allocated'] ?>">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </p>
                    <p class="card-text">Total marks: <?= $allocation['marks_allocated'] ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal for editing category allocation -->
<div class="modal fade" id="editAllocationModal" tabindex="-1" role="dialog" aria-labelledby="editAllocationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAllocationModalLabel">Edit Category Allocation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editAllocationForm">
                    <input type="hidden" id="edit_category_id" name="category_id">
                    <input type="hidden" id="edit_quiz_id" name="quiz_id" value="<?= $quiz['id'] ?>">

                    <div class="form-group">
                        <label for="edit_category_name">Category</label>
                        <input type="text" class="form-control" id="edit_category_name" readonly>
                    </div>

                    <div class="form-group">
                        <label for="edit_number_of_questions">Number of Questions</label>
                        <input type="number" class="form-control" id="edit_number_of_questions" name="number_of_questions" required min="1">
                    </div>

                    <div class="form-group">
                        <label for="edit_marks_allocated">Total Marks</label>
                        <input type="number" class="form-control" id="edit_marks_allocated" name="marks_allocated" required min="1">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveAllocationBtn">Save Changes</button>
            </div>
        </div>
    </div>
</div>




<div class="container">
    <h2>Add Questions to Real Exam: <?= htmlspecialchars($quiz['title']) ?></h2>
    <div class="row">
        <div class="breadcrumb">
            <a href="<?= $url('admin/quiz/list') ?>">Real Exam</a>
            <i class="fas fa-chevron-right"></i>
            <a href="<?= $url('admin/realexam/add/<?= $quiz[') ?>"id'] ?>" style="margin-left: 7px;cursor:default">Questions</a>
            <i class="fas fa-chevron-right"></i>
            <a href="#" style="margin-left: 7px;cursor:default">Add</a>
        </div>
    </div>
    <div class="mb-4">
        <h4>Instructions:</h4>
        <ol>
            <li>Use the filter options below to narrow down the list of questions by category.</li>
            <li>Select the desired category from the dropdown menu.</li>
            <li>Click the "Filter" button to apply the filters and display the relevant questions.</li>
            <li>To add questions to the real exam, check the boxes next to the questions you want to include.</li>
            <li>Once you have selected the questions, they will be automatically added to the real exam.</li>
        </ol>
    </div>
    <div class="mb-4">
        <h4>Filter Questions</h4>
        <form id="filterForm" method="get" action="">
            <div class="form-group">
                <label for="category_id">Category:</label>
                <select name="category_id" id="category_id" class="form-control">
                    <option value="">--Select Category--</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= $categoryId == $category['id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mb-5">Filter</button>
        </form>
    </div>

    <form method="post" action="<?= $url('admin/realexam/add/<?= $quiz[') ?>"id'] ?>">
        <table class="table">
            <thead>
                <tr>
                    <th>S.N</th>
                    <th>Question</th>
                    <th>Category</th>
                    <th>Add</th>
                </tr>
            </thead>
            <tbody>

                <?php
                if ($questions) {

                    $i = 1;
                    foreach ($questions as $question): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($question['question_text']) ?></td>
                            <td><?= htmlspecialchars($question['category_name']) ?></td>
                            <td>
                                <input type="checkbox"
                                    id="question_<?= $question['id'] ?>"
                                    data-category="<?= $question['category_id'] ?>"
                                    <?= in_array($question['id'], $existingQuestions) ? 'checked' : '' ?>
                                    onclick="toggleQuestion(<?= $question['id'] ?>, <?= $quiz['id'] ?>)">
                            </td>
                        </tr>
                    <?php endforeach;
                } else {
                    ?>
                    <tr>
                        <td colspan="4">No questions found</td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </form>

</div>

<div class="pagination">
    <?php if ($currentPage > 1): ?>
        <a href="?page=<?= $currentPage - 1 ?>&category_id=<?= $categoryId ?>">&laquo; Previous</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>&category_id=<?= $categoryId ?>" <?= $i == $currentPage ? 'class="active"' : '' ?>><?= $i ?></a>
    <?php endfor; ?>

    <?php if ($currentPage < $totalPages): ?>
        <a href="?page=<?= $currentPage + 1 ?>&category_id=<?= $categoryId ?>">Next &raquo;</a>
    <?php endif; ?>
</div>
<script>
    function toggleQuestion(questionId, quizId) {
        var isChecked = document.getElementById('question_' + questionId).checked;
        var action = isChecked ? 'add' : 'remove';
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var response = JSON.parse(this.responseText);

                if (!response.success) {
                    alert(response.message);
                    if (response.limit_exceeded) {
                        document.getElementById('question_' + questionId).checked = false;
                    }
                } else {
                    updateCategoryCounter(questionId, action);
                }
            }
        };
        xmlhttp.open("/", "<?= $url('ajax/toggle-question/') ?>" + action + "/" + questionId + "/" + quizId, true);
        xmlhttp.send();
    }

    function updateCategoryCounter(questionId, action) {
        var categoryId = document.getElementById('question_' + questionId).getAttribute('data-category');
        var counterElement = document.getElementById('category_counter_' + categoryId);

        if (counterElement) {
            var current = parseInt(counterElement.innerText.split('/')[0]);
            var max = parseInt(counterElement.innerText.split('/')[1]);

            if (action === 'add') {
                counterElement.innerText = (current + 1) + '/' + max;
            } else if (action === 'remove') {
                counterElement.innerText = (current - 1) + '/' + max;
            }

            if ((action === 'add' && current + 1 >= max) ||
                (action === 'remove' && current - 1 < max)) {
                counterElement.parentElement.className =
                    (current + 1 >= max) ? 'category-counter full' : 'category-counter';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Edit allocation button click handler
        const editButtons = document.querySelectorAll('.edit-allocation');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const categoryId = this.getAttribute('data-category-id');
                const categoryName = this.getAttribute('data-category-name');
                const currentQuestions = this.getAttribute('data-current-questions');
                const currentMarks = this.getAttribute('data-current-marks');

                // Populate the modal fields
                document.getElementById('edit_category_id').value = categoryId;
                document.getElementById('edit_category_name').value = categoryName;
                document.getElementById('edit_number_of_questions').value = currentQuestions;
                document.getElementById('edit_marks_allocated').value = currentMarks;

                // Show the modal
                $('#editAllocationModal').modal('show');
            });
        });

        // Save allocation button click handler
        document.getElementById('saveAllocationBtn').addEventListener('click', function() {
            const formData = new FormData(document.getElementById('editAllocationForm'));
            const categoryId = formData.get('category_id');
            const quizId = formData.get('quiz_id');
            const numQuestions = formData.get('number_of_questions');
            const marksAllocated = formData.get('marks_allocated');

            if (!numQuestions || !marksAllocated) {
                alert('Please fill in all required fields');
                return;
            }

            // Send AJAX request to update allocation
            const xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState == 4) {
                    if (this.status == 200) {
                        try {
                            const response = JSON.parse(this.responseText);
                            if (response.success) {
                                // Update the UI
                                const counterElement = document.getElementById('category_counter_' + categoryId);
                                const currentQuestions = parseInt(counterElement.innerText.split('/')[0]);
                                counterElement.innerText = currentQuestions + '/' + numQuestions;

                                // Update the data attributes
                                const editButton = document.querySelector('.edit-allocation[data-category-id="' + categoryId + '"]');
                                editButton.setAttribute('data-current-questions', numQuestions);
                                editButton.setAttribute('data-current-marks', marksAllocated);

                                // Update marks display
                                editButton.closest('.card-body').querySelector('p.card-text:last-child').innerText = 'Total marks: ' + marksAllocated;

                                // Hide the modal
                                $('#editAllocationModal').modal('hide');

                                // Show success message
                                alert('Category allocation updated successfully');
                            } else {
                                alert(response.message || 'Failed to update allocation');
                            }
                        } catch (e) {
                            alert('Error processing response');
                        }
                    } else {
                        alert('Error updating allocation');
                    }
                }
            };

            xhr.open('POST', '/ajax/update-category-allocation', true);
            xhr.send(formData);
        });
    });
</script>
<style>
    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }

    .pagination a {
        margin: 0 5px;
        padding: 10px 15px;
        text-decoration: none;
        color: #007bff;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .pagination a.active {
        background-color: #007bff;
        color: white;
        border: 1px solid #007bff;
    }

    .pagination a:hover {
        background-color: #0056b3;
        color: white;
        border: 1px solid #0056b3;
    }

    .category-counter {
        font-weight: bold;
        padding: 3px 8px;
        border-radius: 10px;
        background-color: #dff0d8;
    }

    .category-counter.full {
        background-color: #f2dede;
    }

    .card-deck {
        display: flex;
        flex-wrap: wrap;
        margin-right: -15px;
        margin-left: -15px;
    }

    .card-deck .card {
        flex: 0 0 calc(33.333% - 30px);
        margin: 0 15px 20px;
    }
</style>