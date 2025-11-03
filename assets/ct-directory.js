// Lightweight fetch+swap for instant filtering & paginated results
function ctdir_collectQuery(form){
  const fd = new FormData(form);
  const p  = new URLSearchParams();
  for (const [k,v] of fd.entries()){
    if (v !== '' && v !== null) p.append(k, v);
  }
  return p.toString();
}

async function ctdir_fetch(form, resetPage){
  const wrap = form.closest('.ctdir');
  if (!wrap) return;
  const url = new URL(window.location.href);
  const qs  = new URLSearchParams(ctdir_collectQuery(form));
  if (resetPage) qs.set('pg', '1');
  url.search = qs.toString();

  wrap.classList.add('is-loading');
  try{
    const res = await fetch(url.toString(), { credentials: 'same-origin' });
    const html = await res.text();
    const tmp  = document.createElement('div');
    tmp.innerHTML = html;
    const nw = tmp.querySelector('.ctdir');
    if (!nw) { form.submit(); return; }
    wrap.replaceWith(nw);
    history.pushState({}, '', url.toString());
  }finally{}
}

function ctdir_submitNow(form, resetPage=true){
  if (window._ctDir && _ctDir.ajax) {
    ctdir_fetch(form, resetPage);
  } else {
    form.requestSubmit ? form.requestSubmit() : form.submit();
  }
}

// Change = apply immediately
document.addEventListener('change', function(e){
  var f = e.target.closest('.ctdir-filter-form, .ctdir-hero-form');
  if (f) ctdir_submitNow(f, true);
});

// Debounced typing for keyword
var ctdir_t;
document.addEventListener('keyup', function(e){
  if (!e.target.matches('input[type=text]')) return;
  var f = e.target.closest('.ctdir-filter-form');
  if (!f) return;
  clearTimeout(ctdir_t);
  ctdir_t = setTimeout(function(){ ctdir_submitNow(f, true); }, 500);
});

// Accordion toggle + chevrons
document.addEventListener('click', function(e){
  var btn = e.target.closest('.ctdir-filter .acc > button');
  if (!btn) return;
  e.preventDefault();
  var acc = btn.closest('.acc');
  var p   = acc.querySelector('.panel');
  var open = acc.classList.toggle('open');
  p.style.display = open ? 'block' : 'none';
});

// Pagination via AJAX
document.addEventListener('click', function(e){
  var a = e.target.closest('.ctdir-pagination a');
  if (!a) return;
  e.preventDefault();
  var c = a.closest('.ctdir');
  if (!c) { window.location = a.href; return; }
  var f = c.querySelector('.ctdir-filter-form');
  if (!f) { window.location = a.href; return; }
  // ensure hidden pg input exists and set it from link
  var m = a.href.match(/(?:[?&])pg=(\d+)/);
  var pg = f.querySelector('input[name=pg]') || (function(){ var i=document.createElement('input'); i.type='hidden'; i.name='pg'; f.appendChild(i); return i; })();
  pg.value = m ? m[1] : '1';
  ctdir_submitNow(f, false);
});

// Reset button
document.addEventListener('click', function(e){
  var btn = e.target.closest('.ctdir-reset');
  if (!btn) return;
  e.preventDefault();
  var f = btn.closest('form');
  if (!f) return;
  f.reset();
  var pg = f.querySelector('input[name=pg]');
  if (pg) pg.value = 1;
  ctdir_submitNow(f, true);
});
