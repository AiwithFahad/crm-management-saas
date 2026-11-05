<?php require_once __DIR__ . '/../includes/auth.php'; header('Content-Type: application/json');
$user = require_login(); if (!is_post()) { http_response_code(405); exit(json_encode(['error'=>'POST required'])); }
verify_csrf(); $pdo=db(); $tid=require_tenant();
$dealId=(int)($_POST['deal_id']??0); $stageId=(int)($_POST['stage_id']??0);
// verify stage belongs to tenant (isolation)
$s=$pdo->prepare('SELECT id FROM deal_stages WHERE id=? AND tenant_id=?'); $s->execute([$stageId,$tid]); if(!$s->fetch()){http_response_code(422); exit(json_encode(['error'=>'Invalid stage']));}
$s=$pdo->prepare('UPDATE deals SET stage_id=? WHERE id=? AND tenant_id=?'); $s->execute([$stageId,$dealId,$tid]);
if($s->rowCount()){ audit_log($pdo,'deal_stage_update','deals',$dealId); echo json_encode(['ok'=>true]); } else { http_response_code(404); echo json_encode(['error'=>'Deal not found']); }
