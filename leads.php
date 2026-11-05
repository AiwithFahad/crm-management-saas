<?php require_once __DIR__ . '/includes/auth.php'; $user=require_role(['tenant_admin','manager','employee']); $pdo=db(); check_tenant_active($pdo); $tid=require_tenant(); $base=''; 
$active='leads'; $pageTitle='Leads';
if (isset($_GET['export'])) { $s=$pdo->prepare('SELECT id,name,email,phone,source,status,score,created_at FROM leads WHERE tenant_id=? ORDER BY id DESC'); $s->execute([$tid]); export_csv($s->fetchAll(),'leads.csv'); }
if (is_post()) { verify_csrf(); $act=$_POST['action']??'';
  if ($act==='delete') $pdo->prepare('DELETE FROM leads WHERE id=? AND tenant_id=?')->execute([(int)$_POST['id'],$tid]);
  elseif ($act==='convert') { $s=$pdo->prepare('SELECT * FROM leads WHERE id=? AND tenant_id=?'); $s->execute([(int)$_POST['id'],$tid]); $l=$s->fetch();
    if($l){ $pdo->prepare('INSERT INTO customers(tenant_id,name,email,phone,company,status,created_at) VALUES(?,?,?,?,?,"active",NOW())')->execute([$tid,$l['name'],$l['email'],$l['phone'],$l['company']]); $pdo->prepare('UPDATE leads SET status="converted" WHERE id=?')->execute([$l['id']]); log_activity($pdo,'Converted lead '.$l['name'].' to customer'); } flash('success','Lead converted to customer.'); }
  else { $data=[$_POST['name']??'',$_POST['email']??null,$_POST['phone']??null,$_POST['company']??null,$_POST['source']??'website',$_POST['status']??'new',max(0,min(100,(int)($_POST['score']??0))),$_POST['notes']??null];
    if(!empty($_POST['id'])) $pdo->prepare('UPDATE leads SET name=?,email=?,phone=?,company=?,source=?,status=?,score=?,notes=? WHERE id=? AND tenant_id=?')->execute([...$data,(int)$_POST['id'],$tid]);
    else $pdo->prepare('INSERT INTO leads(tenant_id,name,email,phone,company,source,status,score,notes,created_at) VALUES(?,?,?,?,?,?,?,?,?,NOW())')->execute([$tid,...$data]);
    flash('success','Lead saved.'); }
  redirect('leads.php'); }
$q=trim($_GET['q']??''); $sql='SELECT * FROM leads WHERE tenant_id=?'; $args=[$tid]; if($q!==''){$sql.=' AND (name LIKE ? OR email LIKE ?)';$args[]="%$q%";$args[]="%$q%";} $sql.=' ORDER BY score DESC,id DESC';
$s=$pdo->prepare($sql);$s->execute($args);$rows=$s->fetchAll(); $edit=null; if(!empty($_GET['edit'])){$s=$pdo->prepare('SELECT * FROM leads WHERE id=? AND tenant_id=?');$s->execute([(int)$_GET['edit'],$tid]);$edit=$s->fetch();}
include __DIR__ . '/includes/header.php'; ?>
<div class="card p-3 mb-3"><form class="row g-2" method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= e($edit['id']??'') ?>">
<?php foreach(['name','email','phone','company'] as $f): ?><div class="col-md-3"><input class="form-control" name="<?= $f ?>" placeholder="<?= ucfirst($f) ?>" value="<?= e($edit[$f]??'') ?>" <?= $f==='name'?'required':'' ?>></div><?php endforeach; ?>
<div class="col-md-2"><select class="form-select" name="source"><?php foreach(['website','referral','ads','cold','social'] as $c): ?><option <?= ($edit['source']??'')===$c?'selected':'' ?>><?= $c ?></option><?php endforeach; ?></select></div>
<div class="col-md-2"><select class="form-select" name="status"><?php foreach(['new','contacted','qualified','lost','converted'] as $c): ?><option <?= ($edit['status']??'new')===$c?'selected':'' ?>><?= $c ?></option><?php endforeach; ?></select></div>
<div class="col-md-2"><input class="form-control" type="number" min="0" max="100" name="score" placeholder="Score 0-100" value="<?= e($edit['score']??'50') ?>"></div>
<div class="col-md-4"><input class="form-control" name="notes" placeholder="Notes" value="<?= e($edit['notes']??'') ?>"></div>
<div class="col-md-2"><button class="btn btn-primary w-100"><?= $edit?'Update':'Add' ?> lead</button></div></form></div>
<form class="row g-2 mb-3" method="get"><div class="col-md-6"><input class="form-control" name="q" value="<?= e($q) ?>" placeholder="Search leads"></div><div class="col-md-2"><button class="btn btn-outline-primary w-100">Search</button></div><div class="col-md-2"><a class="btn btn-outline-success w-100" href="?export=1">Export CSV</a></div></form>
<div class="card p-0 overflow-auto"><table class="table table-hover mb-0"><thead><tr><th>Name</th><th>Contact</th><th>Source</th><th>Status</th><th>Score</th><th></th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td><?= e($r['name']) ?><div class="text-muted small"><?= e($r['company']) ?></div></td><td><?= e($r['email']) ?><div class="small text-muted"><?= e($r['phone']) ?></div></td><td><?= e($r['source']) ?></td><td><span class="badge bg-info text-dark"><?= e($r['status']) ?></span></td><td><div class="progress" style="width:90px"><div class="progress-bar" style="width:<?= (int)$r['score'] ?>%"><?= (int)$r['score'] ?></div></div></td>
<td class="text-end"><a class="btn btn-sm btn-outline-primary" href="?edit=<?= $r['id'] ?>">Edit</a>
<form method="post" class="d-inline"><?= csrf_field() ?><input type="hidden" name="action" value="convert"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="btn btn-sm btn-outline-success">Convert</button></form>
<form method="post" class="d-inline"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="btn btn-sm btn-outline-danger" data-confirm="Delete lead?">Delete</button></form></td></tr><?php endforeach; ?></tbody></table></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
