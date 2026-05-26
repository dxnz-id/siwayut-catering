<div class="content-header">
    <h1 class="content-title">Edit Catering Menu</h1>
    <a href="/menus" class="btn btn-secondary">Back</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="/menus/<?= e($menu['id']) ?>" method="POST" enctype="multipart/form-data">
            <?= \App\Core\Csrf::field() ?>

            <?php component('form/input', [
                'name' => 'name',
                'label' => 'Menu Name *',
                'value' => old('name', $menu['name']),
                'required' => true
            ]); ?>

            <?php component('form/textarea', [
                'name' => 'description',
                'label' => 'Description',
                'value' => old('description', $menu['description']),
                'rows' => 4
            ]); ?>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <?php 
                component('form/select', [
                    'name' => 'category_id',
                    'label' => 'Category *',
                    'options' => array_column($categories, 'name', 'id'),
                    'selected' => old('category_id', $menu['category_id']),
                    'placeholder' => '-- Select Category --',
                    'required' => true
                ]); 
                ?>

                <?php component('form/select', [
                    'name' => 'event_id',
                    'label' => 'Hari Raya / Event',
                    'options' => array_column($events ?? [], 'name', 'id'),
                    'selected' => old('event_id', $menu['event_id'] ?? ''),
                    'placeholder' => '-- Select Event --',
                    'required' => true
                ]); ?>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <?php component('form/select', [
                    'name' => 'status',
                    'label' => 'Status *',
                    'options' => ['active' => 'Active', 'inactive' => 'Inactive'],
                    'selected' => old('status', $menu['status']),
                    'required' => true
                ]); ?>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <?php component('form/input', [
                    'name' => 'price',
                    'label' => 'Price (Rp) *',
                    'type' => 'number',
                    'value' => old('price', floor((float)$menu['price'])),
                    'required' => true
                ]); ?>

                <?php component('form/input', [
                    'name' => 'minimum_portions',
                    'label' => 'Minimum Portions *',
                    'type' => 'number',
                    'value' => old('minimum_portions', $menu['minimum_portions']),
                    'min' => '1',
                    'required' => true
                ]); ?>
            </div>

            <div class="form-group">
                <label for="image" class="form-label">Menu Image (Optional)</label>
                <?php if ($menu['image']): ?>
                    <div style="margin-bottom: 0.5rem;">
                        <img src="<?= str_starts_with($menu['image'], 'http') ? e($menu['image']) : '/uploads/' . e($menu['image']) ?>" alt="Current Image" style="width: 100px; height: 100px; object-fit: cover; border-radius: var(--radius); border: 1px solid var(--color-border);">
                    </div>
                <?php endif; ?>
                <input type="file" id="image" name="image" class="form-input" accept="image/jpeg,image/png,image/webp">
                <small style="color: var(--color-text-muted); margin-top: 0.25rem; display: block;">Supported formats: JPG, PNG, WEBP. Max 5MB. Leave empty to keep current image.</small>
                <?php if ($err = error('image')): ?>
                <div class="form-error"><?= e($err) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
