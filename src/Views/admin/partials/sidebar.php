<aside class="sidebar">
    <div class="sidebar-header">
        <h2>Admin Panel</h2>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li><a href="<?= $url('admin') ?>"><i class="fas fa-home"></i> Dashboard</a></li>



            <?php if ($_SESSION['role'] == 1) { ?>
                <li class="has-submenu">
                    <a href="#" class="submenu-toggle">
                        <span><i class="fas fa-list-alt"></i> Category Type</span>
                        <span class="arrow">▼</span>
                    </a>
                    <ul class="submenu">
                        <li><a href="<?= $url('admin/category-type/add') ?>"><i class="fas fa-plus"></i> Add</a></li>
                        <li><a href="<?= $url('admin/category-type/list') ?>"><i class="fas fa-eye"></i> View</a></li>
                    </ul>
                </li>
                <li class="has-submenu">
                    <a href="#" class="submenu-toggle">
                        <span><i class="fas fa-list-alt"></i> Category</span>
                        <span class="arrow">▼</span>
                    </a>
                    <ul class="submenu">
                        <li><a href="<?= $url('admin/category/add') ?>"><i class="fas fa-plus"></i> Add</a></li>
                        <li><a href="<?= $url('admin/category/list') ?>"><i class="fas fa-eye"></i> View</a></li>
                    </ul>
                </li>
                <li class="has-submenu">
                    <a href="#" class="submenu-toggle">
                        <span><i class="fas fa-tags"></i> Tags</span>
                        <span class="arrow">▼</span>
                    </a>
                    <ul class="submenu">
                        <li><a href="<?= $url('admin/tag/add') ?>"><i class="fas fa-plus"></i> Add</a></li>
                        <li><a href="<?= $url('admin/tag/list') ?>"><i class="fas fa-eye"></i> View</a></li>
                    </ul>
                </li>

                <li class="has-submenu">
                    <a href="#" class="submenu-toggle">
                        <span><i class="fas fa-question-circle"></i> Question Type</span>
                        <span class="arrow">▼</span>
                    </a>
                    <ul class="submenu">
                        <li><a href="<?= $url('admin/questiontype/add') ?>"><i class="fas fa-plus"></i> Add</a></li>
                        <li><a href="<?= $url('admin/questiontype/list') ?>"><i class="fas fa-eye"></i> View</a></li>
                    </ul>
                </li>
                <li class="has-submenu">
                    <a href="#" class="submenu-toggle">
                        <span><i class="fas fa-layer-group"></i> Level</span>
                        <span class="arrow">▼</span>
                    </a>
                    <ul class="submenu">
                        <li><a href="<?= $url('admin/level/add') ?>"><i class="fas fa-plus"></i> Add</a></li>
                        <li><a href="<?= $url('admin/level/list') ?>"><i class="fas fa-eye"></i> View</a></li>
                    </ul>
                </li>
                <li class="has-submenu">
                    <a href="#" class="submenu-toggle">
                        <span><i class="fas fa-chalkboard-teacher"></i> Teacher</span>
                        <span class="arrow">▼</span>
                    </a>
                    <ul class="submenu">
                        <li><a href="<?= $url('admin/teacher/add') ?>"><i class="fas fa-plus"></i> Add</a></li>
                        <li><a href="<?= $url('admin/teacher/list') ?>"><i class="fas fa-eye"></i> View</a></li>
                    </ul>
                </li>
            <?php } ?>
            <?php if ($_SESSION['role'] == 2) { ?>


                <li class="has-submenu">
                    <a href="#" class="submenu-toggle">
                        <span><i class="fas fa-question"></i> Exam</span>
                        <span class="arrow">▼</span>
                    </a>
                    <ul class="submenu">
                        <li><a href="<?= $url('admin/create/quiz') ?>"><i class="fas fa-plus"></i> Quiz</a></li>
                        <li><a href="<?= $url('admin/create/mocktest') ?>"><i class="fas fa-eye"></i> Mocktest</a></li>
                        <li><a href="<?= $url('admin/create/previous') ?>"><i class="fas fa-eye"></i> Previous Year</a></li>
                        <li><a href="<?= $url('admin/create/real_exam') ?>"><i class="fas fa-eye"></i> Real Exam</a></li>
                    </ul>
                </li>

            <?php } ?>
            <li><a href="<?= $url('admin/student/list') ?>"><i class="fas fa-user-graduate"></i> Students</a></li>
            <li class="has-submenu">
                <a href="#" class="submenu-toggle">
                    <span><i class="fas fa-trophy"></i> Quiz</span>
                    <span class="arrow">▼</span>
                </a>
                <ul class="submenu">
                    <li><a href="<?= $url('admin/quiz/add') ?>"><i class="fas fa-plus"></i> Add</a></li>
                    <li><a href="<?= $url('admin/quiz/list') ?>"><i class="fas fa-eye"></i> View</a></li>
                </ul>
            </li>
            <li class="has-submenu">
                <a href="#" class="submenu-toggle">
                    <span><i class="fas fa-question"></i> Question</span>
                    <span class="arrow">▼</span>
                </a>
                <ul class="submenu">
                    <li><a href="<?= $url('admin/question/add') ?>"><i class="fas fa-plus"></i> Add</a></li>
                    <li><a href="<?= $url('admin/question/list') ?>"><i class="fas fa-eye"></i> View</a></li>
                    <li><a href="<?= $url('admin/question/word') ?>"><i class="fas fa-eye"></i> Import</a></li>
                </ul>
            </li>

            <li><a href="<?= $url('admin/reports') ?>"><i class="fas fa-cog"></i> Report</a></li>
        </ul>
    </nav>
</aside>