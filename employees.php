<?php require_once __DIR__ . '/includes/auth.php'; $user=require_role(['tenant_admin','manager','employee']); $pdo=db(); check_tenant_active($pdo); $tid=require_tenant(); $base=''; 
$active='employees'; $pageTitle='Employees'; require_role(['tenant_admin','manager']);
if (is_post()) { verify_csrf();
  if(($_POST['action']??'')==='delete') $pdo->prepare('DELETE FROM employees WHERE id=? AND tenant_id=?')->execute([(int)$_POST['id'],$tid]);
  elseif(($_POST['action']??'')==='dept') { $pdo->prepare('INSERT INTO departments(tenant_id,name,created_at) VALUES(?,?,NOW())')->execute([$tid,trim($_POST['dept_name']??'')]); flash('success','Department added.'); }
  else { $data=[$_POST['name']??'',$_POST['email']??null,$_POST['phone']??null,(int)($_POST['department_id']??0)?:null,$_POST['job_title']??null,(float)($_POST['salary']??0),$_POST['status']??'active'];
    if(!empty($_POST['id'])) $pdo->prepare('UPDATE employees SET name=?,email=?,phone=?,department_id=?,job_title=?,salary=?,status=? WHERE id=? AND tenant_id=?')->execute([...$data,(int)$_POST['id'],$tid]);
    else $pdo->prepare('INSERT INTO employees(tenant_id,name,email,phone,department_id,job_title,salary,status,created_at) VALUES(?,?,?,?,?,?,?,?,NOW())')->execute([$tid,...$data]);
    flash('success','Employee saved.'); } redirect('employees.php'); }
$s=$pdo->prepare('SELECT * FROM departments WHERE tenant_id=? ORDER BY name'); $s->execute([$tid]); $depts=$s->fetchAll();
$s=$pdo->prepare('SELECT emp.*,d.name dept FROM employees emp LEFT JOIN departments d ON d.id=emp.department_id WHERE emp.tenant_id=? ORDER BY emp.name'); $s->execute([$tid]); $rows=$s->fetchAll(); $edit=null; if(!empty($_GET['edit'])){$s=$pdo->prepare('SELECT * FROM employees WHERE id=? AND tenant_id=?');$s->execute([(int)$_GET['edit'],$tid]);$edit=$s->fetch();}
include __DIR__ . '/includes/header.php'; ?>
<div class="row g-3"><div class="col-lg-8"><div class="card p-3 mb-3"><form class="row g-2" method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= e($edit['id']??'') ?>">
<?php foreach(['name','email','phone','job_title'] as $f): ?><div class="col-md-3"><input class="form-control" name="<?= $f ?>" placeholder="<?= ucfirst(str_replace('_',' ',$f)) ?>" value="<?= e($edit[$f]??'') ?>" <?= $f==='name'?'required':'' ?>></div><?php endforeach; ?>
<div class="col-md-3"><select class="form-select" name="department_id"><option value="">Department…</option><?php foreach($depts as $d): ?><option value="<?= $d['id'] ?>" <?= ($edit['department_id']??'')==$d['id']?'selected':'' ?>><?= e($d['name']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-3"><input class="form-control" type="number" step="0.01" name="salary" placeholder="Salary" value="<?= e($edit['salary']??'') ?>"></div>
<div class="col-md-3"><select class="form-select" name="status"><option>active</option><option <?= ($edit['status']??'')==='inactive'?'selected':'' ?>>inactive</option></select></div>
<div class="col-md-3"><button class="btn btn-primary w-100"><?= $edit?'Update':'Add' ?> employee</button></div></form></div>
<div class="card p-0 overflow-auto"><table class="table mb-0"><thead><tr><th>Name</th><th>Dept</th><th>Title</th><th>Status</th><th></th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><?= e($r['name']) ?><div class="small text-muted"><?= e($r['email']) ?></div></td><td><?= e($r['dept']??'—') ?></td><td><?= e($r['job_title']) ?></td><td><?= e($r['status']) ?></td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="?edit=<?= $r['id'] ?>">Edit</a> <form method="post" class="d-inline"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="btn btn-sm btn-outline-danger" data-confirm="Delete employee?">Delete</button></form></td></tr><?php endforeach; ?></tbody></table></div></div>
<div class="col-lg-4"><div class="card p-3"><h6>Departments</h6><ul class="list-group mb-3"><?php foreach($depts as $d): ?><li class="list-group-item"><?= e($d['name']) ?></li><?php endforeach; ?></ul>
<form method="post" class="d-flex gap-2"><?= csrf_field() ?><input type="hidden" name="action" value="dept"><input class="form-control" name="dept_name" placeholder="New department" required><button class="btn btn-primary">Add</button></form></div></div></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
