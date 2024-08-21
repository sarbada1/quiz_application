<aside class="sidebar">
    <div class="sidebar-header">
        <h2>Admin Panel</h2>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li><a href="#"><i class="fa-home"></i> Dashboard</a></li>
            <li class="has-submenu">
                <a href="#" class="submenu-toggle">
                    <span><i class="fa-chalkboard-teacher"></i> Category</span>
                    <span class="arrow">▼</span>
                </a>
                <ul class="submenu">
                    <li><a href="/admin/category/add"><i class="fa-plus"></i> Add</a></li>
                    <li><a href="/admin/category/list"><i class="fa-eye"></i> View</a></li>
                </ul>
            </li>
            <?php
            if ($_SESSION['role'] == 1) {
            ?>
                <li class="has-submenu">
                    <a href="#" class="submenu-toggle">
                        <span><i class="fa-chalkboard-teacher"></i> Question Type</span>
                        <span class="arrow">▼</span>
                    </a>
                    <ul class="submenu">
                        <li><a href="/admin/questiontype/add"><i class="fa-plus"></i> Add</a></li>
                        <li><a href="/admin/questiontype/list"><i class="fa-eye"></i> View</a></li>
                    </ul>
                </li>
                <li class="has-submenu">
                    <a href="#" class="submenu-toggle">
                        <span><i class="fa-chalkboard-teacher"></i> Teacher</span>
                        <span class="arrow">▼</span>
                    </a>
                    <ul class="submenu">
                        <li><a href="/admin/teacher/add"><i class="fa-plus"></i> Add</a></li>
                        <li><a href="/admin/teacher/list"><i class="fa-eye"></i> View</a></li>
                    </ul>
                </li>
            <?php } ?>
            <li class="has-submenu">
                <a href="#" class="submenu-toggle">
                    <span><i class="fa-chalkboard-teacher"></i> Quiz</span>
                    <span class="arrow">▼</span>
                </a>
                <ul class="submenu">
                    <li><a href="/admin/quiz/add"><i class="fa-plus"></i> Add</a></li>
                    <li><a href="/admin/quiz/list"><i class="fa-eye"></i> View</a></li>
                </ul>
            </li>
            <li class="has-submenu">
                <a href="#" class="submenu-toggle">
                    <span><i class="fa-chalkboard-teacher"></i> Question</span>
                    <span class="arrow">▼</span>
                </a>
                <ul class="submenu">
                    <li><a href="/admin/question/add"><i class="fa-plus"></i> Add</a></li>
                    <li><a href="/admin/question/list"><i class="fa-eye"></i> View</a></li>
                </ul>
            </li>
            <li><a href="#"><i class="fa-cog"></i> Settings</a></li>
        </ul>
    </nav>
</aside>