<!-- File: src/Views/user/index.php -->
<div class="content-header">
    <h1 class="content-title">Users</h1>
    <a href="/users/create" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Add User
    </a>
</div>

<div class="card">
    <?php if (empty($users)): ?>
    <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" style="margin: 0 auto; opacity: 0.4;"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>
        <p>No users found. Create your first user to get started.</p>
    </div>
    <?php else: ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= \App\Core\View::e($user['id']) ?></td>
                    <td style="font-weight: 500;"><?= \App\Core\View::e($user['name']) ?></td>
                    <td><?= \App\Core\View::e($user['email']) ?></td>
                    <td>
                        <span class="badge badge-<?= $user['role'] === 'admin' ? 'admin' : 'user' ?>">
                            <?= \App\Core\View::e(ucfirst($user['role'])) ?>
                        </span>
                    </td>
                    <td style="color: var(--color-text-muted); font-size: 0.8125rem;">
                        <?= \App\Core\View::e($user['created_at']) ?>
                    </td>
                    <td>
                        <div class="table-actions">
                            <a href="/users/<?= (int)$user['id'] ?>/edit" class="btn btn-secondary btn-sm">Edit</a>
                            <?php if (($currentUser['id'] ?? 0) !== (int)$user['id']): ?>
                            <form method="POST" action="/users/<?= (int)$user['id'] ?>/delete" style="display:inline;">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" class="btn btn-danger btn-sm" data-confirm="Are you sure you want to delete this user?">Delete</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (isset($pagination) && $pagination['last_page'] > 1): ?>
    <div class="pagination">
        <div class="pagination-info">
            Showing page <?= (int)$pagination['current_page'] ?> of <?= (int)$pagination['last_page'] ?> (<?= (int)$pagination['total'] ?> total)
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
