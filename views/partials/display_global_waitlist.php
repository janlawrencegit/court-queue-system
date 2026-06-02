<?php
/** @var list<array{court_id: int, court_number: string, position: int, is_next: bool, queue: array<string, mixed>}> $globalWaitlist */
$globalWaitlist = $globalWaitlist ?? [];
?>
<aside class="waitlist-pane" id="waitlist-pane" aria-label="Waitlist">
    <header class="waitlist-pane-hd">
        <h2 class="waitlist-pane-title"><i class="fas fa-list-ul"></i> Waitlist</h2>
        <span class="waitlist-pane-count" id="waitlist-count"><?= count($globalWaitlist) ?> waiting</span>
    </header>
    <div class="waitlist-pane-body" id="waitlist-body">
        <?php if ($globalWaitlist === []): ?>
            <p class="waitlist-pane-empty">No matches waiting. Check the courts for live play.</p>
        <?php else: ?>
            <ul class="global-wl">
                <?php foreach ($globalWaitlist as $item):
                    $queue = $item['queue'];
                    $pos = (int) $item['position'];
                ?>
                <li class="global-wl-item<?= !empty($item['is_next']) ? ' global-wl-item--next' : '' ?>">
                    <div class="global-wl-pos" title="Position on this court">
                        <span class="global-wl-pos-num">#<?= $pos ?></span>
                        <?php if (!empty($item['is_next'])): ?>
                            <span class="global-wl-pos-lbl">Next</span>
                        <?php endif; ?>
                    </div>
                    <div class="global-wl-content">
                        <div class="global-wl-court">
                            <span class="global-wl-court-name"><?= e($item['court_number']) ?></span>
                        </div>
                        <div class="global-wl-meta">
                            <span class="tag tag-qnum"><?= e($queue['queue_number']) ?></span>
                        </div>
                        <div class="global-wl-match">
                            <?php $hideTags = true; require BASE_PATH . '/views/partials/display_queue_slot.php'; unset($hideTags); ?>
                        </div>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</aside>
