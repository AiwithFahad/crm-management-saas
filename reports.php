<?php require_once __DIR__ . '/includes/auth.php'; $user=require_role(['tenant_admin','manager','employee']); $pdo=db(); check_tenant_active($pdo); $tid=require_tenant(); $base=''; 
$active='reports'; $pageTitle='Reports';
$from=$_GET['from']??date('Y-m-01'); $to=$_GET['to']??date('Y-m-d');
function rep(PDO $pdo,string $sql,array $a){$s=$pdo->prepare($sql);$s->execute($a);return $s->fetchAll();}
$sales=rep($pdo,'SELECT st.name stage,COUNT(*) deals,COALESCE(SUM(d.value),0) value FROM deals d JOIN deal_stages st ON st.id=d.stage_id WHERE d.tenant_id=? AND DATE(d.created_at) BETWEEN ? AND ? GROUP BY st.name',[$tid,$from,$to]);
$leadSrc=rep($pdo,'SELECT source,COUNT(*) total,SUM(status="converted") converted FROM leads WHERE tenant_id=? GROUP BY source',[$tid]);
$rev=rep($pdo,'SELECT DATE_FORMAT(d.created_at,"%Y-%m") month,COALESCE(SUM(d.value),0) revenue FROM deals d JOIN deal_stages st ON st.id=d.stage_id WHERE d.tenant_id=? AND st.name="Won" GROUP BY month ORDER BY month',[$tid]);
if(isset($_GET['export'])) export_csv(array_merge($sales,$leadSrc,$rev),'report.csv');
include __DIR__ . '/includes/header.php'; ?>
<form class="row g-2 mb-3" method="get"><div class="col-md-3"><input class="form-control" type="date" name="from" value="<?= e($from) ?>"></div><div class="col-md-3"><input class="form-control" type="date" name="to" value="<?= e($to) ?>"></div><div class="col-md-2"><button class="btn btn-outline-primary w-100">Filter</button></div><div class="col-md-2"><a class="btn btn-outline-success w-100" href="?export=1&from=<?= e($from) ?>&to=<?= e($to) ?>">Export CSV</a></div></form>
<div class="row g-3"><div class="col-lg-4"><div class="card p-3"><h6>Sales by stage</h6><table class="table"><tr><th>Stage</th><th>Deals</th><th>Value</th></tr><?php foreach($sales as $r): ?><tr><td><?= e($r['stage']) ?></td><td><?= $r['deals'] ?></td><td><?= money($r['value']) ?></td></tr><?php endforeach; ?></table></div></div>
<div class="col-lg-4"><div class="card p-3"><h6>Leads by source</h6><table class="table"><tr><th>Source</th><th>Total</th><th>Converted</th></tr><?php foreach($leadSrc as $r): ?><tr><td><?= e($r['source']) ?></td><td><?= $r['total'] ?></td><td><?= $r['converted'] ?></td></tr><?php endforeach; ?></table></div></div>
<div class="col-lg-4"><div class="card p-3"><h6>Won revenue by month</h6><canvas id="revChart" height="160"></canvas><table class="table mt-2"><?php foreach($rev as $r): ?><tr><td><?= e($r['month']) ?></td><td><?= money($r['revenue']) ?></td></tr><?php endforeach; ?></table></div></div></div>
<?php $extraJs='<script>makeChart("revChart","line",'.json_encode(array_column($rev,'month')).','.json_encode(array_map('floatval',array_column($rev,'revenue'))).',"Revenue");</script>'; include __DIR__ . '/includes/footer.php'; ?>
