<div class="content-header">
    <h1 class="content-title"><?= htmlspecialchars($title ?? 'Edit Category') ?></h1>
    <a href="/categories" class="btn btn-secondary">&larr; Back to Categories</a>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <form action="/categories/<?= $category['id'] ?>" method="POST">
            <?= \App\Core\Csrf::field() ?>
            
            <?php component('form/input', [
                'name' => 'name',
                'label' => 'Category Name',
                'value' => old('name', $category['name']),
                'required' => true
            ]); ?>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Category</button>
                <a href="/categories" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
