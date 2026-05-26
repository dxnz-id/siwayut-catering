<div class="content-header">
    <h1 class="content-title">Catering Menu Management</h1>
    <a href="/menus/create" class="btn btn-primary">Add Menu</a>
</div>

<div class="card">
    <?php if (empty($menus)): ?>
    <div class="empty-state">
        <p>No menus added yet.</p>
    </div>
    <?php else: ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Menu Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Min Portions</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($menus as $menu): ?>
                <tr>
                    <td><?= e($menu['id']) ?></td>
                    <td>
                        <?php if ($menu['image']): ?>
                            <img src="/uploads/<?= e($menu['image']) ?>" alt="<?= e($menu['name']) ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                        <?php else: ?>
                            <div style="width: 40px; height: 40px; background: var(--color-border); border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--color-text-muted); font-size: 0.75rem;">No Img</div>
                        <?php endif; ?>
                    </td>
                    <td style="font-weight: 500;"><?= e($menu['name']) ?></td>
                    <td><?= e($katMap[$menu['category_id']] ?? 'Unknown') ?></td>
                    <td style="color: var(--color-success); font-weight: 500;">Rp <?= number_format((float)$menu['price'], 0, ',', '.') ?></td>
                    <td><?= e($menu['minimum_portions']) ?> portions</td>
                    <td>
                        <?php if ($menu['status'] === 'active'): ?>
                            <span class="badge" style="background: var(--color-success); color: white;">Active</span>
                        <?php else: ?>
                            <span class="badge" style="background: var(--color-danger); color: white;">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="table-actions">
                            <a href="/menus/<?= e($menu['id']) ?>/edit" class="btn btn-secondary btn-sm">Edit</a>
                            <form action="/menus/<?= e($menu['id']) ?>/delete" method="POST" style="margin: 0;" onsubmit="return confirm('Are you sure you want to delete this menu?');">
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

    <?php if ($pagination['last_page'] > 1): ?>
    <div class="pagination">
        <div class="pagination-info">
            Showing page <?= (int)$pagination['current_page'] ?> of <?= (int)$pagination['last_page'] ?> (Total: <?= (int)$pagination['total'] ?> menus)
        </div>
        <div class="pagination-links">
            <a href="?page=<?= max(1, $pagination['current_page'] - 1) ?>" class="pagination-link<?= $pagination['current_page'] <= 1 ? ' disabled' : '' ?>">&laquo; Prev</a>
            <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
            <a href="?page=<?= $i ?>" class="pagination-link<?= $i === $pagination['current_page'] ? ' active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <a href="?page=<?= min($pagination['last_page'], $pagination['current_page'] + 1) ?>" class="pagination-link<?= $pagination['current_page'] >= $pagination['last_page'] ? ' disabled' : '' ?>">Next &raquo;</a>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>
