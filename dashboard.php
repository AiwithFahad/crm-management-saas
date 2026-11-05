<?php require_once __DIR__ . '/includes/auth.php'; $user=require_role(['tenant_admin','manager','employee']); $pdo=db(); check_tenant_active($pdo); $tid=require_tenant(); $base=''; $active='dashboard'; $pageTitle='Dashboard';
function cnt(PDO $pdo,string $sql,int $tid){$s=$pdo->prepare($sql);$s->execute([$tid]);return $s->fetchColumn();}
$totalCustomers=cnt($pdo,'SELECT COUNT(*) FROM customers WHERE tenant_id=?',$tid);
$totalLeads=cnt($pdo,'SELECT COUNT(*) FROM leads WHERE tenant_id=?',$tid);
$totalDeals=cnt($pdo,'SELECT COUNT(*) FROM deals WHERE tenant_id=?',$tid);
$revenue=cnt($pdo,'SELECT COALESCE(SUM(value),0) FROM deals d JOIN deal_stages st ON st.id=d.stage_id WHERE d.tenant_id=? AND st.name="Won"',$tid);
$won=cnt($pdo,'SELECT COUNT(*) FROM deals d JOIN deal_stages st ON st.id=d.stage_id WHERE d.tenant_id=? AND st.name="Won"',$tid);
$conversion=$totalLeads?round($won/max(1,$totalLeads)*100,1):0;
$pendingTasks=cnt($pdo,'SELECT COUNT(*) FROM tasks WHERE tenant_id=? AND status!="done"',$tid);
$s=$pdo->prepare('SELECT DATE_FORMAT(created_at,"%b") m, COUNT(*) c FROM leads WHERE tenant_id=? GROUP BY MONTH(created_at) ORDER BY MONTH(created_at)'); $s->execute([$tid]); $leadRows=$s->fetchAll();
$s=$pdo->prepare('SELECT * FROM activities WHERE tenant_id=? ORDER BY id DESC LIMIT 8'); $s->execute([$tid]); $acts=$s->fetchAll();
include __DIR__ . '/includes/header.php'; ?>
<div class="row g-3">
<?php foreach ([['Customers',$totalCustomers,'bi-people'],['Leads',$totalLeads,'bi-funnel'],['Deals',$totalDeals,'bi-kanban'],['Revenue',money($revenue),'bi-cash-stack'],['Conversion',$conversion.'%','bi-percent'],['Pending tasks',$pendingTasks,'bi-clock']] as [$l,$v,$i]): ?>
<div class="col-md-4 col-xl-2"><div class="card stat-card p-3"><i class="bi <?= $i ?> fs-4 text-primary"></i><div class="fs-4 fw-bold"><?= e((string)$v) ?></div><div class="text-muted small"><?= e($l) ?></div></div></div><?php endforeach; ?>
</div>
<div class="row g-3 mt-1"><div class="col-lg-8"><div class="card p-3"><h6>Leads by month</h6><canvas id="leadsChart" height="120"></canvas></div></div>
<div class="col-lg-4"><div class="card p-3"><h6>Recent activities</h6><ul class="list-unstyled small mb-0"><?php foreach($acts as $a): ?><li class="border-bottom py-2"><?= e($a['description']) ?> <span class="text-muted">· <?= e($a['created_at']) ?></span></li><?php endforeach; if(!$acts) echo '<li class="py-2 text-muted">No activity yet.</li>'; ?></ul></div></div></div>
<?php $extraJs='<script>makeChart("leadsChart","bar",'.json_encode(array_column($leadRows,'m')).','.json_encode(array_map('intval',array_column($leadRows,'c'))).',"Leads");</script>'; include __DIR__ . '/includes/footer.php'; ?>
