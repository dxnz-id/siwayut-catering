<div class="content-header">
    <h1 class="content-title">Add Catering Menu</h1>
    <a href="/menus" class="btn btn-secondary">Back</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="/menus" method="POST" enctype="multipart/form-data">
            <?= \App\Core\Csrf::field() ?>

            <?php component('form/input', [
                'name' => 'name',
                'label' => 'Menu Name *',
                'required' => true
            ]); ?>

            <?php component('form/textarea', [
                'name' => 'description',
                'label' => 'Description',
                'rows' => 4
            ]); ?>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <?php 
                $catOptions = [];
                foreach ($categories as $kat) {
                    $catOptions[$kat['id']] = $kat['name'];
                }
                component('form/select', [
                    'name' => 'category_id',
                    'label' => 'Category *',
                    'options' => $catOptions,
                    'placeholder' => '-- Select Category --',
                    'required' => true
                ]); 
                ?>

                <?php component('form/select', [
                    'name' => 'status',
                    'label' => 'Status *',
                    'options' => ['active' => 'Active', 'inactive' => 'Inactive'],
                    'required' => true
                ]); ?>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <?php component('form/input', [
                    'name' => 'price',
                    'label' => 'Price (Rp) *',
                    'type' => 'number',
                    'required' => true
                ]); ?>

                <?php component('form/input', [
                    'name' => 'minimum_portions',
                    'label' => 'Minimum Portions *',
                    'type' => 'number',
                    'value' => old('minimum_portions', '1'),
                    'min' => '1',
                    'required' => true
                ]); ?>
            </div>

            <?php component('form/input', [
                'name' => 'image',
                'label' => 'Menu Image (Optional)',
                'type' => 'file',
                'help_text' => 'Supported formats: JPG, PNG, WEBP. Max 5MB.'
            ]); ?>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Menu</button>
            </div>
        </form>
    </div>
</div>
