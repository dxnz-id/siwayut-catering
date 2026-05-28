<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold font-display text-text">Add Event</h1>
    <a href="/events" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-white/6 text-text border-border hover:bg-white/10 hover:text-text">
        &larr; Back to Events
    </a>
</div>

<div class="bg-[#18181b] border border-border rounded-xl overflow-hidden">
    <div class="p-6">
        <form method="POST" action="/events">
            <?= \App\Core\Csrf::field() ?>

            <?php component('form/input', [
                'name' => 'name',
                'label' => 'Event Name (e.g., Idul Fitri 2026)',
                'value' => old('name'),
                'required' => true
            ]); ?>

            <div class="grid grid-cols-2 gap-4">
                <?php component('form/input', [
                    'name' => 'start_date',
                    'label' => 'Start Date',
                    'type' => 'date',
                    'value' => old('start_date'),
                    'required' => true
                ]); ?>

                <?php component('form/input', [
                    'name' => 'end_date',
                    'label' => 'End Date',
                    'type' => 'date',
                    'value' => old('end_date'),
                    'required' => true
                ]); ?>
            </div>

            <?php component('form/select', [
                'name' => 'status',
                'label' => 'Status',
                'options' => [
                    'active' => 'Active',
                    'inactive' => 'Inactive'
                ],
                'value' => old('status', 'active'),
                'required' => true
            ]); ?>

            <div class="mt-6">
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-primary text-white border-primary hover:bg-primary-hover hover:border-primary-hover hover:shadow-[0_0_15px_var(--color-gold-glow)] hover:text-white">Save Event</button>
            </div>
        </form>
    </div>
</div>
