<?php
/** @var string $lookupPrefix */
/** @var string $hiddenName */
/** @var string $hiddenId */
/** @var array<string, mixed>|null $selected */
/** @var array<string, mixed>|null $lookupSelected */
$oldVal = isset($_SESSION['old'][$hiddenName]) ? (string) $_SESSION['old'][$hiddenName] : '';
$lookupRequired = $lookupRequired ?? true;
?>
<div class="player-lookup position-relative" data-lookup-prefix="<?= e($lookupPrefix) ?>">
    <input type="hidden" name="<?= e($hiddenName) ?>" id="<?= e($hiddenId) ?>" value="<?= e($oldVal) ?>"<?= $lookupRequired ? ' required' : '' ?>>
    <div class="player-search-wrap">
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control player-search-input" placeholder="Search by name or player code..." autocomplete="off">
        </div>
        <div class="list-group player-results d-none" role="listbox"></div>
    </div>
    <div class="alert alert-primary py-2 px-3 mt-2 mb-0 d-none player-selected d-flex justify-content-between align-items-center gap-2">
        <div class="min-w-0">
            <div class="player-selected-name fw-semibold text-truncate"></div>
            <div class="player-selected-meta text-muted small"></div>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary player-clear-btn flex-shrink-0">Change</button>
    </div>
</div>
