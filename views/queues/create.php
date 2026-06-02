<?php require BASE_PATH . '/views/layouts/header.php'; ?>
<?php
/** @var array<int, array<string, mixed>> $courts */
/** @var array<string, mixed> $selected */
?>
<div class="queue-create-shell">
<div class="card queue-create-card">
    <div class="card-header bg-white queue-create-card__header">
        <h5 class="mb-0 fw-semibold"><i class="fa-solid fa-table-tennis-paddle-ball me-2 text-primary"></i>Add Match to Queue</h5>
    </div>
    <div class="card-body queue-create-card__body">
        <form method="POST" action="<?= url('queues/store') ?>" id="queueForm" class="queue-create-form-clean"><?= csrf_field() ?>
            <section class="queue-form-section">
                <label class="form-label fw-semibold">Court *</label>
                <select name="court_id" class="form-select" required>
                    <option value="">Select court</option>
                    <?php foreach ($courts as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= old('court_id') == (string) $c['id'] ? 'selected' : '' ?>><?= e($c['court_number']) ?></option>
                    <?php endforeach; ?>
                </select>
            </section>

            <section class="queue-side-panel queue-form-section">
                <div class="queue-side-panel__title">Players List</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Player 1 *</label>
                        <?php
                        $lookupPrefix = 'player1';
                        $hiddenName = 'player_id';
                        $hiddenId = 'playerId';
                        $lookupSelected = $selected['player'] ?? null;
                        require BASE_PATH . '/views/partials/player_lookup.php';
                        ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Player 2 *</label>
                        <?php
                        $lookupPrefix = 'player2';
                        $hiddenName = 'player2_id';
                        $hiddenId = 'opponentPlayerId';
                        $lookupSelected = $selected['opponent'] ?? null;
                        require BASE_PATH . '/views/partials/player_lookup.php';
                        ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Player 3 (optional)</label>
                        <?php
                        $lookupPrefix = 'partner1';
                        $hiddenName = 'partner_player_id';
                        $hiddenId = 'partnerPlayerId';
                        $lookupRequired = false;
                        $lookupSelected = $selected['partner'] ?? null;
                        require BASE_PATH . '/views/partials/player_lookup.php';
                        unset($lookupRequired);
                        ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Player 4 (optional)</label>
                        <?php
                        $lookupPrefix = 'partner2';
                        $hiddenName = 'player4_id';
                        $hiddenId = 'opponentPartnerPlayerId';
                        $lookupRequired = false;
                        $lookupSelected = $selected['opponent_partner'] ?? null;
                        require BASE_PATH . '/views/partials/player_lookup.php';
                        unset($lookupRequired);
                        ?>
                    </div>
                </div>
            </section>

            <p class="text-muted small queue-create-help mb-0">
                Search by name or player code. Shows skill level when selected.
                <a href="<?= url('players/create') ?>">Add new player</a>
            </p>

            <section class="queue-form-section queue-form-section--details">
            <div class="row g-3 align-items-start">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Priority</label>
                    <select name="priority" class="form-select">
                        <option value="0" <?= old('priority') === '0' ? 'selected' : '' ?>>Normal</option>
                        <option value="5" <?= old('priority') === '5' ? 'selected' : '' ?>>High</option>
                        <option value="10" <?= old('priority') === '10' ? 'selected' : '' ?>>Urgent</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Notes</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Optional"><?= old('notes') ?></textarea>
                </div>
            </div>
            <div class="row g-3 mt-0">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Rental minutes</label>
                    <input
                        name="rental_minutes"
                        type="number"
                        min="1"
                        class="form-control"
                        value="<?= e(old('rental_minutes', setting('rental_default_minutes', '60') ?? '60')) ?>"
                    >
                    <div class="form-text">When you press Start/Serve, timer will use this duration.</div>
                </div>
                <div class="col-md-8 d-flex align-items-end">
                    <div class="form-text">
                        Extend time follows the value in Settings > Rental Timer > Extend step.
                    </div>
                </div>
            </div>
            </section>
            <div class="queue-create-actions">
            <button type="submit" class="btn btn-primary px-4">
                <i class="fas fa-plus me-1"></i> Add Match to Queue
            </button>
            </div>
        </form>
    </div>
</div>
</div>
<script src="<?= url('assets/js/player-lookup.js') ?>"></script>
<script>
(function () {
    const form = document.getElementById('queueForm');
    const player1Input = document.getElementById('playerId');
    const player2Input = document.getElementById('opponentPlayerId');
    const player3Input = document.getElementById('partnerPlayerId');
    const player4Input = document.getElementById('opponentPartnerPlayerId');

    form.addEventListener('submit', function (e) {
        const ids = [
            player1Input.value,
            player2Input.value,
            player3Input.value,
            player4Input.value,
        ];
        if (!ids[0] || !ids[1]) {
            e.preventDefault();
            alert('Please select at least Player 1 and Player 2.');
            return;
        }
        const unique = new Set(ids.filter(Boolean));
        if (unique.size !== ids.filter(Boolean).length) {
            e.preventDefault();
            alert('All players must be different.');
        }
    });

    <?php foreach ([
        ['player1', $selected['player'] ?? null],
        ['partner1', $selected['partner'] ?? null],
        ['player2', $selected['opponent'] ?? null],
        ['partner2', $selected['opponent_partner'] ?? null],
    ] as [$prefix, $p]): ?>
    <?php if (!empty($p)): ?>
    PlayerLookup.restore('<?= $prefix ?>', <?= (int) $p['id'] ?>, <?= json_encode($p['player_name'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= json_encode($p['player_code'] ?? '', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= json_encode(skill_level_label($p['skill_level'] ?? 'intermediate'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= (int) ($p['games_played'] ?? 0) ?>);
    <?php endif; ?>
    <?php endforeach; ?>
})();
</script>
<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
