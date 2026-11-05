// CRM Pro — premium UI behaviours: theme, collapsible sidebar, global search filter.
(function(){
  var root=document.documentElement, body=document.body;
  function applyTheme(t){
    if(t==='dark'){ root.setAttribute('data-theme','dark'); body.classList.add('dark'); }
    else { root.removeAttribute('data-theme'); body.classList.remove('dark'); }
  }
  applyTheme(localStorage.getItem('crm-theme')||'light');
  document.getElementById('themeToggle')?.addEventListener('click',function(){
    var dark = root.getAttribute('data-theme')!=='dark';
    applyTheme(dark?'dark':'light');
    localStorage.setItem('crm-theme', dark?'dark':'light');
    var ic=this.querySelector('i'); if(ic) ic.className = dark ? 'bi bi-sun' : 'bi bi-moon';
  });
  // Sync toggle icon with saved theme on load
  var tgl=document.getElementById('themeToggle');
  if(tgl && root.getAttribute('data-theme')==='dark'){ var ic=tgl.querySelector('i'); if(ic) ic.className='bi bi-sun'; }

  // Collapsible sidebar (desktop), persisted
  if(localStorage.getItem('crm-sb')==='1') body.classList.add('sb-collapsed');
  document.getElementById('sidebarToggle')?.addEventListener('click',function(){
    body.classList.toggle('sb-collapsed');
    localStorage.setItem('crm-sb', body.classList.contains('sb-collapsed')?'1':'0');
  });

  // Global search: live-filters sidebar navigation labels (decorative quick filter)
  var gs=document.getElementById('globalSearch');
  if(gs){
    gs.addEventListener('input',function(){
      var q=gs.value.trim().toLowerCase();
      document.querySelectorAll('.sidebar .nav-link').forEach(function(a){
        a.style.display = (!q || a.textContent.toLowerCase().includes(q)) ? '' : 'none';
      });
    });
    gs.addEventListener('keydown',function(e){
      if(e.key==='Enter'){ e.preventDefault(); var first=document.querySelector('.sidebar .nav-link:not([style*="none"])'); if(first) first.click(); }
    });
  }
})();
document.querySelectorAll('[data-confirm]')?.forEach(el=>el.addEventListener('click',ev=>{if(!confirm(el.dataset.confirm))ev.preventDefault();}));
