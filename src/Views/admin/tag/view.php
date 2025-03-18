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

<table>
    <thead>
        <tr>
            <th>SN</th>
            <th>Name</th>
            <th>Slug</th>
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
                    <button class="primary"><a href="<?= $url('admin/tag/edit/' . $tag['id']) ?>">Edit</a></button>
                    <button class="danger"><a href="<?= $url('admin/tag/delete/' . $tag['id']) ?>" onclick="return confirm('Are you sure to delete?')">Delete</a></button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>