<?php require BASE_PATH . '/views/layouts/header.php'; ?>
<div class="d-flex justify-content-between mb-3">
    <p class="text-muted mb-0">Manage courts</p>
    <a href="<?= url('courts/create') ?>" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Court</a>
</div>
<form class="row g-2 mb-3" method="GET">
    <div class="col-md-4"><input name="search" class="form-control" placeholder="Search..." value="<?= e($_GET['search'] ?? '') ?>"></div>
    <div class="col-md-3"><select name="status" class="form-select"><option value="">All status</option>
        <?php foreach (['available','occupied','closed'] as $s): ?><option value="<?= $s ?>" <?= ($_GET['status']??'')===$s?'selected':'' ?>><?= ucfirst($s) ?></option><?php endforeach; ?>
    </select></div>
    <div class="col-md-2"><button class="btn btn-primary w-100">Filter</button></div>
</form>
<div class="row g-3">
<?php foreach ($courts as $c): ?>
<div class="col-md-4">
    <div class="card h-100">
        <div class="card-body">
            <div class="d-flex justify-content-between"><h5><?= e($c['court_number']) ?></h5>
                <span class="badge bg-<?= status_badge($c['status']) ?>"><?= e(ucfirst($c['status'])) ?></span></div>
            <p class="mt-2 mb-2">Waiting: <strong><?= (int)$c['waiting_count'] ?></strong> | Done today: <?= (int)$c['today_completed'] ?></p>
            <a href="<?= url('courts/show?id='.$c['id']) ?>" class="btn btn-sm btn-outline-primary">View</a>
            <a href="<?= url('courts/edit?id='.$c['id']) ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
            <form method="POST" action="<?= url('courts/delete') ?>" class="d-inline" onsubmit="return confirm('Delete?')">
                <?= csrf_field() ?><input type="hidden" name="id" value="<?= $c['id'] ?>">
                <button class="btn btn-sm btn-outline-danger">Delete</button>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>
<?php require BASE_PATH . '/views/layouts/footer.php'; ?>
