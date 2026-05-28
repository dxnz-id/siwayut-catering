<?php $flashSuccess = $success ?? \App\Core\Session::getFlash('success'); ?>
<?php $flashError = $error ?? \App\Core\Session::getFlash('error'); ?>

<?php if ($flashSuccess): ?>
<div class="flex items-center gap-2 px-4 py-3 rounded-lg text-sm mb-6 bg-success-bg text-[#6ee7b7] border border-success-border">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
    <?= \App\Core\View::e($flashSuccess) ?>
</div>
<?php endif; ?>

<?php if ($flashError): ?>
<div class="flex items-center gap-2 px-4 py-3 rounded-lg text-sm mb-6 bg-error-bg text-[#fca5a5] border border-error-border">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
    <?= \App\Core\View::e($flashError) ?>
</div>
<?php endif; ?>
