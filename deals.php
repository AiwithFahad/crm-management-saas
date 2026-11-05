<?php require_once __DIR__ . '/includes/auth.php'; $user=require_role(['tenant_admin','manager','employee']); $pdo=db(); check_tenant_active($pdo); $tid=require_tenant(); $base=''; 
$active='deals'; $pageTitle='Sales Pipeline';
if (is_post()) { verify_csrf();
  if(($_POST['action']??'')==='delete') $pdo->prepare('DELETE FROM deals WHERE id=? AND tenant_id=?')->execute([(int)$_POST['id'],$tid]);
  else { $data=[$_POST['title']??'',(int)($_POST['customer_id']??0)?:null,(int)($_POST['stage_id']??0),(float)($_POST['value']??0),$_POST['expected_close']??null,(int)($_POST['probability']??50)];
    if(!empty($_POST['id'])) $pdo->prepare('UPDATE deals SET title=?,customer_id=?,stage_id=?,value=?,expected_close=?,probability=? WHERE id=? AND tenant_id=?')->execute([...$data,(int)$_POST['id'],$tid]);
    else $pdo->prepare('INSERT INTO deals(tenant_id,title,customer_id,stage_id,value,expected_close,probability,created_at) VALUES(?,?,?,?,?,?,?,NOW())')->execute([$tid,...$data]);
    flash('success','Deal saved.'); } redirect('deals.php'); }
$s=$pdo->prepare('SELECT * FROM deal_stages WHERE tenant_id=? ORDER BY sort_order'); $s->execute([$tid]); $stages=$s->fetchAll();
$s=$pdo->prepare('SELECT d.*,c.name customer_name FROM deals d LEFT JOIN customers c ON c.id=d.customer_id WHERE d.tenant_id=? ORDER BY d.value DESC'); $s->execute([$tid]); $deals=$s->fetchAll();
$s=$pdo->prepare('SELECT id,name FROM customers WHERE tenant_id=? ORDER BY name'); $s->execute([$tid]); $customers=$s->fetchAll();
include __DIR__ . '/includes/header.php'; ?><meta name="csrf" content="<?= e(csrf_token()) ?>">
<div class="card p-3 mb-3"><form class="row g-2" method="post"><?= csrf_field() ?>
<div class="col-md-3"><input class="form-control" name="title" placeholder="Deal title" required></div>
<div class="col-md-2"><select class="form-select" name="customer_id"><option value="">Customer…</option><?php foreach($customers as $c): ?><option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-2"><select class="form-select" name="stage_id"><?php foreach($stages as $st): ?><option value="<?= $st['id'] ?>"><?= e($st['name']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-2"><input class="form-control" type="number" step="0.01" name="value" placeholder="Value"></div>
<div class="col-md-2"><input class="form-control" type="date" name="expected_close"></div>
<div class="col-md-1"><button class="btn btn-primary w-100">Add</button></div></form></div>
<div class="kanban"><?php foreach($stages as $st): ?><div class="kanban-col" data-stage="<?= $st['id'] ?>"><h6 class="d-flex justify-content-between"><?= e($st['name']) ?> <span class="badge bg-primary"><?= money(array_sum(array_map(fn($d)=>$d['stage_id']==$st['id']?$d['value']:0,$deals))) ?></span></h6><div class="col-body">
<?php foreach($deals as $d): if($d['stage_id']!=$st['id']) continue; ?><div class="deal-card" draggable="true" data-id="<?= $d['id'] ?>"><strong><?= e($d['title']) ?></strong><div class="small text-muted"><?= e($d['customer_name']??'') ?></div><div class="fw-bold"><?= money($d['value']) ?></div><div class="small"><?= (int)$d['probability'] ?>% · <?= e($d['expected_close']??'') ?></div>
<form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $d['id'] ?>"><button class="btn btn-sm btn-link text-danger p-0" data-confirm="Delete deal?">Delete</button></form></div><?php endforeach; ?>
</div></div><?php endforeach; ?></div>
<?php $extraJs='<script src="assets/js/kanban.js"></script>'; include __DIR__ . '/includes/footer.php'; ?>
