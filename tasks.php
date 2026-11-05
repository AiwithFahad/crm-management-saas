<?php require_once __DIR__ . '/includes/auth.php'; $user=require_role(['tenant_admin','manager','employee']); $pdo=db(); check_tenant_active($pdo); $tid=require_tenant(); $base=''; 
$active='tasks'; $pageTitle='Tasks';
if (is_post()) { verify_csrf();
  if(($_POST['action']??'')==='delete') $pdo->prepare('DELETE FROM tasks WHERE id=? AND tenant_id=?')->execute([(int)$_POST['id'],$tid]);
  elseif(($_POST['action']??'')==='toggle') $pdo->prepare('UPDATE tasks SET status=IF(status="done","todo","done") WHERE id=? AND tenant_id=?')->execute([(int)$_POST['id'],$tid]);
  else { $data=[$_POST['title']??'',$_POST['description']??null,(int)($_POST['assigned_to']??0)?:null,$_POST['priority']??'medium',$_POST['status']??'todo',$_POST['due_date']??null,!empty($_POST['is_recurring'])?1:0];
    if(!empty($_POST['id'])) $pdo->prepare('UPDATE tasks SET title=?,description=?,assigned_to=?,priority=?,status=?,due_date=?,is_recurring=? WHERE id=? AND tenant_id=?')->execute([...$data,(int)$_POST['id'],$tid]);
    else $pdo->prepare('INSERT INTO tasks(tenant_id,title,description,assigned_to,priority,status,due_date,is_recurring,created_by,created_at) VALUES(?,?,?,?,?,?,?,?,?,NOW())')->execute([$tid,...$data,$user['id']]);
    flash('success','Task saved.'); } redirect('tasks.php'); }
$s=$pdo->prepare('SELECT t.*,u.name assignee FROM tasks t LEFT JOIN users u ON u.id=t.assigned_to WHERE t.tenant_id=? ORDER BY t.due_date IS NULL,t.due_date'); $s->execute([$tid]); $rows=$s->fetchAll();
$s=$pdo->prepare('SELECT id,name FROM users WHERE tenant_id=? ORDER BY name'); $s->execute([$tid]); $users=$s->fetchAll(); $edit=null; if(!empty($_GET['edit'])){$s=$pdo->prepare('SELECT * FROM tasks WHERE id=? AND tenant_id=?');$s->execute([(int)$_GET['edit'],$tid]);$edit=$s->fetch();}
include __DIR__ . '/includes/header.php'; ?>
<div class="card p-3 mb-3"><form class="row g-2" method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= e($edit['id']??'') ?>">
<div class="col-md-3"><input class="form-control" name="title" placeholder="Task title" required value="<?= e($edit['title']??'') ?>"></div>
<div class="col-md-2"><select class="form-select" name="assigned_to"><option value="">Assign…</option><?php foreach($users as $u): ?><option value="<?= $u['id'] ?>" <?= ($edit['assigned_to']??'')==$u['id']?'selected':'' ?>><?= e($u['name']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-2"><select class="form-select" name="priority"><?php foreach(['low','medium','high','urgent'] as $c): ?><option <?= ($edit['priority']??'medium')===$c?'selected':'' ?>><?= $c ?></option><?php endforeach; ?></select></div>
<div class="col-md-2"><select class="form-select" name="status"><?php foreach(['todo','in_progress','done'] as $c): ?><option <?= ($edit['status']??'todo')===$c?'selected':'' ?>><?= $c ?></option><?php endforeach; ?></select></div>
<div class="col-md-2"><input class="form-control" type="date" name="due_date" value="<?= e($edit['due_date']??'') ?>"></div>
<div class="col-md-1"><button class="btn btn-primary w-100"><?= $edit?'Update':'Add' ?></button></div>
<div class="col-md-10"><input class="form-control" name="description" placeholder="Description" value="<?= e($edit['description']??'') ?>"></div><div class="col-md-2 form-check pt-2"><input class="form-check-input" type="checkbox" name="is_recurring" id="rec" <?= !empty($edit['is_recurring'])?'checked':'' ?>><label for="rec">Recurring</label></div></form></div>
<div class="card p-0 overflow-auto"><table class="table table-hover mb-0"><thead><tr><th>Task</th><th>Assignee</th><th>Priority</th><th>Status</th><th>Due</th><th></th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr class="<?= $r['status']==='done'?'text-decoration-line-through text-muted':'' ?>"><td><?= e($r['title']) ?></td><td><?= e($r['assignee']??'—') ?></td><td><span class="badge bg-<?= $r['priority']==='urgent'?'danger':($r['priority']==='high'?'warning':'secondary') ?>"><?= e($r['priority']) ?></span></td><td><?= e($r['status']) ?></td><td><?= e($r['due_date']??'') ?></td>
<td class="text-end"><form method="post" class="d-inline"><?= csrf_field() ?><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="btn btn-sm btn-outline-success">Toggle done</button></form>
<a class="btn btn-sm btn-outline-primary" href="?edit=<?= $r['id'] ?>">Edit</a><form method="post" class="d-inline"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="btn btn-sm btn-outline-danger" data-confirm="Delete task?">Delete</button></form></td></tr><?php endforeach; ?></tbody></table></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
