<div class="content-header">
    <h1 class="content-title"><?= htmlspecialchars($title ?? 'Menu Categories') ?></h1>
    <a href="/categories/create" class="btn btn-primary">Add Category</a>
</div>

<div class="card">
    <?php if (empty($categories)): ?>
    <div class="empty-state">
        <p>No categories found.</p>
    </div>
    <?php else: ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><?= $cat['id'] ?></td>
                    <td><?= htmlspecialchars($cat['name']) ?></td>
                    <td><?= htmlspecialchars($cat['slug']) ?></td>
                    <td>
                        <div class="table-actions">
                            <a href="/categories/<?= $cat['id'] ?>/edit" class="btn btn-secondary btn-sm">Edit</a>
                            <form action="/categories/<?= $cat['id'] ?>/delete" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
