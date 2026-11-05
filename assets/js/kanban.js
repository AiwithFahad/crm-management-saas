document.querySelectorAll('.deal-card').forEach(card=>{
 card.addEventListener('dragstart',e=>e.dataTransfer.setData('text/plain',card.dataset.id));
});
document.querySelectorAll('.kanban-col').forEach(col=>{
 col.addEventListener('dragover',e=>e.preventDefault());
 col.addEventListener('drop',async e=>{e.preventDefault();const id=e.dataTransfer.getData('text/plain');const card=document.querySelector(`[data-id="${id}"]`);col.querySelector('.col-body').appendChild(card);
  const fd=new FormData();fd.append('deal_id',id);fd.append('stage_id',col.dataset.stage);fd.append('_csrf',document.querySelector('meta[name=csrf]')?.content||'');
  const r=await fetch('api/deal_stage_update.php',{method:'POST',body:fd}); if(!r.ok) alert('Stage update failed');});
});
