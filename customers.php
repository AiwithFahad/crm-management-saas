<?php require_once __DIR__ . '/includes/auth.php'; $user=require_role(['tenant_admin','manager','employee']); $pdo=db(); check_tenant_active($pdo); $tid=require_tenant(); $base=''; 
$active='customers'; $pageTitle='Customers';
if (isset($_GET['export'])) { $s=$pdo->prepare('SELECT id,name,email,phone,company,category,status,created_at FROM customers WHERE tenant_id=? ORDER BY id DESC'); $s->execute([$tid]); export_csv($s->fetchAll(),'customers.csv'); }
if (is_post()) { verify_csrf(); $act=$_POST['action']??'';
  if ($act==='delete') { $pdo->prepare('DELETE FROM customers WHERE id=? AND tenant_id=?')->execute([(int)$_POST['id'],$tid]); flash('success','Customer deleted.'); }
  elseif ($act==='import' && !empty($_FILES['csv']['tmp_name'])) { $h=fopen($_FILES['csv']['tmp_name'],'r'); $head=fgetcsv($h); $n=0;
    while($row=fgetcsv($h)){ if(count($row)<2) continue; $pdo->prepare('INSERT INTO customers(tenant_id,name,email,phone,company,category,status,created_at) VALUES(?,?,?,?,?,?,?,NOW())')->execute([$tid,$row[0]??'',$row[1]??null,$row[2]??null,$row[3]??null,$row[4]??'general','active']); $n++; } fclose($h); flash('success',"Imported $n customers."); }
  else { $data=[$_POST['name']??'',$_POST['email']??null,$_POST['phone']??null,$_POST['company']??null,$_POST['category']??'general',$_POST['status']??'active',$_POST['notes']??null];
    if (!empty($_POST['id'])) { $pdo->prepare('UPDATE customers SET name=?,email=?,phone=?,company=?,category=?,status=?,notes=? WHERE id=? AND tenant_id=?')->execute([...$data,(int)$_POST['id'],$tid]); flash('success','Customer updated.'); }
    else { $limits=subscription_limits($pdo); $c=$pdo->prepare('SELECT COUNT(*) FROM customers WHERE tenant_id=?'); $c->execute([$tid]); if($c->fetchColumn()>=$limits['max_customers']){flash('error','Customer limit reached for your plan. Upgrade to add more.'); redirect('customers.php');}
      $pdo->prepare('INSERT INTO customers(tenant_id,name,email,phone,company,category,status,notes,created_at) VALUES(?,?,?,?,?,?,?,?,NOW())')->execute([$tid,...$data]); flash('success','Customer added.'); log_activity($pdo,'Added customer '.$data[0]); } }
  redirect('customers.php'); }
$q=trim($_GET['q']??''); $status=$_GET['status']??'';
$sql='SELECT * FROM customers WHERE tenant_id=?'; $args=[$tid]; if($q!==''){$sql.=' AND (name LIKE ? OR email LIKE ? OR company LIKE ?)'; $args[]="%$q%";$args[]="%$q%";$args[]="%$q%";} if($status!==''){$sql.=' AND status=?';$args[]=$status;} $sql.=' ORDER BY id DESC';
$s=$pdo->prepare($sql); $s->execute($args); $rows=$s->fetchAll(); $edit=null; if(!empty($_GET['edit'])){$s=$pdo->prepare('SELECT * FROM customers WHERE id=? AND tenant_id=?');$s->execute([(int)$_GET['edit'],$tid]);$edit=$s->fetch();}
include __DIR__ . '/includes/header.php'; ?>
<div class="card p-3 mb-3"><form class="row g-2" method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= e($edit['id']??'') ?>">
<?php foreach([['name','Name',1],['email','Email',0],['phone','Phone',0],['company','Company',0]] as [$f,$l,$r]): ?><div class="col-md-3"><input class="form-control" name="<?= $f ?>" placeholder="<?= $l ?>" value="<?= e($edit[$f]??'') ?>" <?= $r?'required':'' ?>></div><?php endforeach; ?>
<div class="col-md-2"><select class="form-select" name="category"><?php foreach(['general','vip','partner'] as $c): ?><option <?= ($edit['category']??'')===$c?'selected':'' ?>><?= $c ?></option><?php endforeach; ?></select></div>
<div class="col-md-2"><select class="form-select" name="status"><?php foreach(['active','inactive'] as $c): ?><option <?= ($edit['status']??'active')===$c?'selected':'' ?>><?= $c ?></option><?php endforeach; ?></select></div>
<div class="col-md-8"><input class="form-control" name="notes" placeholder="Notes" value="<?= e($edit['notes']??'') ?>"></div>
<div class="col-md-2"><button class="btn btn-primary w-100"><?= $edit?'Update':'Add' ?> customer</button></div></form>
<form class="row g-2 mt-2" method="post" enctype="multipart/form-data"><?= csrf_field() ?><input type="hidden" name="action" value="import"><div class="col-md-4"><input class="form-control" type="file" name="csv" accept=".csv" required></div><div class="col-md-2"><button class="btn btn-outline-secondary w-100">Import CSV</button></div><div class="col-md-3 text-muted small pt-2">CSV columns: name,email,phone,company,category</div></form></div>
<form class="row g-2 mb-3" method="get"><div class="col-md-5"><input class="form-control" name="q" value="<?= e($q) ?>" placeholder="Search name, email, company"></div>
<div class="col-md-2"><select class="form-select" name="status"><option value="">All status</option><option <?= $status==='active'?'selected':'' ?>>active</option><option <?= $status==='inactive'?'selected':'' ?>>inactive</option></select></div>
<div class="col-md-2"><button class="btn btn-outline-primary w-100">Filter</button></div><div class="col-md-2"><a class="btn btn-outline-success w-100" href="?export=1">Export CSV</a></div></form>
<div class="card p-0 overflow-auto"><table class="table table-hover mb-0"><thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Company</th><th>Category</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td><?= e($r['name']) ?></td><td><?= e($r['email']) ?></td><td><?= e($r['phone']) ?></td><td><?= e($r['company']) ?></td><td><?= e($r['category']) ?></td><td><span class="badge bg-success"><?= e($r['status']) ?></span></td>
<td class="text-end"><a class="btn btn-sm btn-outline-primary" href="?edit=<?= $r['id'] ?>">Edit</a>
<form method="post" class="d-inline"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="btn btn-sm btn-outline-danger" data-confirm="Delete this customer?">Delete</button></form></td></tr><?php endforeach; ?>
</tbody></table></div><?php include __DIR__ . '/includes/footer.php'; ?>
