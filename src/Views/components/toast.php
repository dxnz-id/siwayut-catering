<?php
$flashes = $flashes ?? [];

// Collect flash messages from session (consumed here)
foreach (['success', 'error', 'info', 'warning'] as $type) {
    $msg = \App\Core\Session::getFlash($type);
    if ($msg) {
        $flashes[] = ['type' => $type, 'message' => $msg];
    }
}
?>
<div id="toast-container"
     class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none"
     data-flash='<?= json_encode($flashes, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>'></div>
