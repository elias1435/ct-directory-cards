<?php

/**
 * Plugin Name: CT Directory Filter
 * Description: Directory grid + filters + JSON inspector for CT profiles.
 * Author: Counselingwise
 * Version: 0.4.1
 */

if (!defined('ABSPATH')) exit;

define('CTDIR_PLUGIN_FILE', __FILE__);
define('CTDIR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CTDIR_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * ----------------------------------------
 * Assets (inline CSS/JS)
 * ----------------------------------------
 */
add_action('wp_enqueue_scripts', function () {
	$handle = 'ct-directory-cards';

	/* ---------- Inline CSS (scoped) ---------- */
	$css = <<<'CSS'
/* Layout shell */
.ctdir{width:100%;}
.ctdir .ctdir-cols{display:grid;grid-template-columns:1fr;gap:28px}
@media(min-width:1100px){.ctdir .ctdir-cols{grid-template-columns:320px 1fr}}
.hide-item {display: none !important;}
/* Grid columns safe-guard (never collapse) */
.ctdir .ctdir-grid{display:grid;grid-template-columns:1fr;gap:22px;min-width:0}
@media(min-width:768px){.ctdir .ctdir-grid{grid-template-columns:repeat(2,1fr)}}
@media(min-width:1024px){.ctdir .ctdir-grid{grid-template-columns:repeat(3,1fr)}}

/* Hero */
.ctdir-row-hero{background:#152534;color:#fff;padding:60px 20px;text-align:center}
.ctdir-row-hero .hero-grid{display:grid;gap:12px;grid-template-columns:1fr 1fr 1fr;align-items:end;max-width:800px;margin:0 auto}
.ctdir-row-hero .hero-field{display:flex;flex-direction:column;text-align:left}
.ctdir-row-hero .hero-label{font-size:12px;opacity:.9;margin:0 0 6px;text-transform:uppercase;letter-spacing:.03em}
.ctdir-row-hero select{width:100%;max-width:420px;padding:10px;border-radius:6px;border:1px solid #334;background:#fff;color:#000}
.ctdir-row-hero .hero-checks label{display:inline-flex;align-items:center;gap:6px;margin-right:12px}
@media (max-width:1024px){ .ctdir-row-hero .hero-grid{ grid-template-columns: 1fr 1fr; } }
@media (max-width:640px){ .ctdir-row-hero .hero-grid{ grid-template-columns: 1fr; } }

/* Wrapper */
.ctdir-wrap{background:#fff;padding:50px 10px}

/* Filter accordions */
.ctdir-filter .acc{border:1px solid #ddd;border-radius:8px;margin-bottom:10px}
.ctdir-filter .acc>button{width:100%;text-align:left;background:#f7f7f7;border:0;padding:12px 14px;font-weight:600;cursor:pointer;border-radius:8px;position:relative;padding-right:30px}
.ctdir-filter .acc>button:after{content:"\25BC";position:absolute;right:12px;top:50%;transform:translateY(-50%);font-size:10px;transition:transform .2s ease}
.ctdir-filter .acc.open>button:after{transform:translateY(-50%) rotate(180deg)}
.ctdir-filter .acc .panel{display:none;padding:12px 14px}
.search-fields input{height:40px;padding:10px !important;width:100%;border-radius:25px}
.acc .panel select{width:100%;height:40px;padding:5px;border:1px solid #00000055;border-radius:25px}
.ctdir-filter-form input[name="zip"]{width:100%;margin-bottom:10px;border:1px solid #00000055;height:40px;padding:10px;border-radius:25px}
.ctdir-filter .checklist{display:grid;gap:6px}
.ctdir-filter .checklist label{display:block}
.ctdir-filter .chips{display:flex;flex-wrap:wrap;gap:8px}
.ctdir-filter .chips label{border:1px solid #ddd;padding:6px 10px;border-radius:999px}

/* Cards */
.ctdir-card{box-shadow:0 2px 10px rgba(0,0,0,.08);border-radius:10px;overflow:hidden;text-align:center;background:#fff}
.ctdir-card .hdr{background:#0e2c48;color:#fff;padding:12px 10px;text-transform:uppercase;font-size:13px;letter-spacing:.04em}
.ctdir-card .inner{padding:18px}
.ctdir-card .photo img{width:210px;height:210px;object-fit:cover;border-radius:50%;display:block;margin:0 auto 16px}
.ctdir-card .name{font-size:18px;margin:6px 0}
.ctdir-card .role{opacity:.8;margin-bottom:14px}
.ctdir-card .btn{display:inline-block;background:#ffc65c;color:#1a1a1a;padding:10px 16px;border-radius:24px;text-decoration:none;font-weight:600}

/* Pagination */
.ctdir-pagination{display:flex;gap:6px;justify-content:center;margin-top:22px}
.ctdir-pagination a,.ctdir-pagination span{border:1px solid #ddd;border-radius:4px;padding:6px 10px}

/* Spinner (results only) */
.ctdir .ctdir-cols section{position:relative;min-width:0}
.ctdir .ctdir-cols section.is-loading::after{content:"";position:absolute;inset:0;background:rgba(255,255,255,.6);backdrop-filter:blur(1px);z-index:10}
.ctdir .ctdir-cols section.is-loading::before{content:"";position:absolute;top:50%;left:50%;width:36px;height:36px;margin:-18px 0 0 -18px;border-radius:50%;border:3px solid rgba(0,0,0,.2);border-top-color:#0e2c48;animation:ctdirspin .8s linear infinite;z-index:11}
@keyframes ctdirspin{to{transform:rotate(360deg)}}

/* Dual fee slider */
.pt-big{padding-top: 30px !important;}
.fee-dual{display:flex;align-items:center;gap:4px}
.fee-dual output{display:inline-flex;gap:4px;align-items:center;font-weight:600}
.fee-dual .fee-left,.fee-dual .fee-right{min-width:20px;text-align:center;font-weight:600}
.fee-dual .fee-label{opacity:.7;font-size:10px;text-transform:uppercase;letter-spacing:.04em}
.fee-track{position:relative;flex:1;height:36px}
.fee-track input[type=range]{-webkit-appearance:none;appearance:none;position:absolute;left:0;right:0;top:50%;transform:translateY(-50%);width:100%;height:36px;background:transparent;margin:0;pointer-events:auto;z-index:2}
.fee-track input[type=range]::-webkit-slider-thumb{-webkit-appearance:none;appearance:none;width:18px;height:18px;border-radius:50%;background:#0e2c48;border:2px solid #fff;box-shadow:0 0 0 1px rgba(0,0,0,.2);cursor:pointer;position:relative;z-index:3}
.fee-track input[type=range]::-moz-range-thumb{width:18px;height:18px;border-radius:50%;background:#0e2c48;border:2px solid #fff;box-shadow:0 0 0 1px rgba(0,0,0,.2);cursor:pointer;position:relative;z-index:3}
.fee-track input.on-top{ z-index:4; }
.fee-track::before{content:"";position:absolute;left:0;right:0;top:50%;transform:translateY(-50%);height:6px;border-radius:6px;background:#e9edf3}
.fee-track .fill{position:absolute;left:0;right:0;top:50%;transform:translateY(-50%);height:6px;border-radius:6px;background:#0e2c48;pointer-events:none;z-index:1}
.fee-track .fee-tip{position:absolute; bottom:100%; transform:translate(-50%,-6px); background:#0e2c48; color:#fff; padding:3px 6px; border-radius:4px; font-size:12px; line-height:1; white-space:nowrap; pointer-events:none; box-shadow:0 1px 2px rgba(0,0,0,.2); z-index:12}
.fee-track .fee-tip:after{content:""; position:absolute; left:50%; transform:translateX(-50%); top:100%; width:0; height:0; border-left:6px solid transparent; border-right:6px solid transparent; border-top:6px solid #0e2c48}
.fee-track .fee-tip.stack{ transform:translate(-50%,-24px); }
CSS;

	wp_register_style($handle, false, [], '0.4.1');
	wp_add_inline_style($handle, $css);
	wp_enqueue_style($handle);

	/* ---------- Inline JS (ES5-safe, with guards + coalescing) ---------- */
	$js = <<<'JS'
function matches(el, sel){var p=Element.prototype;var f=p.matches||p.msMatchesSelector||p.webkitMatchesSelector;return f.call(el,sel)}
function closest(el, sel){if(!el)return null;if(el.closest)return el.closest(sel);while(el&&el.nodeType===1){if(matches(el,sel))return el;el=el.parentNode}return null}
function toArray(x){return Array.prototype.slice.call(x||[],0)}
function enc(s){return encodeURIComponent(s)} function dec(s){try{return decodeURIComponent(s)}catch(e){return s}}

var ctdirReqSeq = 0;
var ctdirActiveController = null;

function markLoading(node){var c=closest(node,".ctdir");var s=c?c.querySelector(".ctdir-cols section"):null;if(s)s.classList.add("is-loading")}
function unmarkLoading(node){var c=closest(node,".ctdir");var s=c?c.querySelector(".ctdir-cols section"):null;if(s)s.classList.remove("is-loading")}

/* ----- PARAMS: collect + coalesce hero + sidebar (dedupe) ----- */
function collectParams(container){
  var obj = {};

  function put(k, v, isArrayName){
    if(v==null) return;
    v=String(v);
    if(v.replace(/^\s+|\s+$/g,'')==='') return;
    if(isArrayName || /\[\]$/.test(k)){
      k = k.replace(/\[\]$/,'');
      if(!obj[k]) obj[k]=[];
      if(obj[k].indexOf(v)===-1) obj[k].push(v);
    } else {
      if(typeof obj[k]==='undefined') obj[k]=v;
      else {
        if(Object.prototype.toString.call(obj[k])!=='[object Array]'){ obj[k]=[obj[k]]; }
        if(obj[k].indexOf(v)===-1) obj[k].push(v);
      }
    }
  }

  function readForm(form){
    if(!form) return;
    if(window.FormData){
      var fd=new FormData(form);
      fd.forEach(function(v,k){ put(k,v, /\[\]$/.test(k)); });
    } else {
      toArray(form.elements).forEach(function(el){
        if(!el.name) return;
        if((el.type==='checkbox'||el.type==='radio') && !el.checked) return;
        put(el.name, el.value, /\[\]$/.test(el.name));
      });
    }
  }

  var hero = container.querySelector('.ctdir-hero-form');
  var side = container.querySelector('.ctdir-filter-form');
  readForm(hero);
  readForm(side);

  // Normalize multi keys
  ['therapy','service_area','insurance','values','years','credential','session'].forEach(function(k){
    if (typeof obj[k] !== 'undefined' && Object.prototype.toString.call(obj[k])!=='[object Array]'){
      obj[k] = [String(obj[k])];
    }
  });

  // ---------- HERO OVERRIDES ----------
  // If hero has a non-empty single select value, it *overrides* the sidebar for the same key.
  function heroSelectValue(name){
    var sel = hero ? hero.querySelector('[name="'+name+'"]') : null;
    if (!sel) return '';
    var v = (sel.value||'').trim();
    return v;
  }
  var heroTherapy = heroSelectValue('therapy');         // hero has single "therapy"
  var heroArea    = heroSelectValue('service_area');    // hero has single "service_area"
  if (heroTherapy) obj['therapy'] = [heroTherapy];
  if (heroArea)    obj['service_area'] = [heroArea];

  // pg default
  if(!obj.pg) obj.pg='1';
  return obj;
}

function paramsToQS(obj){
  var parts=[];
  for(var k in obj){ if(!obj.hasOwnProperty(k)) continue;
    var v=obj[k];
    if(v==null) continue;
    if(Object.prototype.toString.call(v)==='[object Array]'){
      for(var i=0;i<v.length;i++) parts.push(enc(k+'[]')+'='+enc(v[i]));
    } else {
      parts.push(enc(k)+'='+enc(v));
    }
  }
  return parts.join('&');
}
function parseQS(search){
  var obj={}; if(!search) return obj;
  if(search.charAt(0)==='?') search=search.slice(1);
  if(!search) return obj;
  var pairs=search.split('&');
  for(var i=0;i<pairs.length;i++){
    if(!pairs[i]) continue;
    var kv=pairs[i].split('='), k=dec(kv[0]||''), v=dec(kv[1]||'');
    var m=k.match(/^(.*)\[\]$/);
    if(m){ var base=m[1]; if(!obj[base]) obj[base]=[]; obj[base].push(v); }
    else { if(typeof obj[k]==='undefined') obj[k]=v; else { if(Object.prototype.toString.call(obj[k])!=='[object Array]') obj[k]=[obj[k]]; obj[k].push(v);} }
  }
  return obj;
}
function syncForms(container, params){
  function setForm(form){
    if(!form) return;
    toArray(form.elements).forEach(function(el){
      if(!el.name) return;
      var isArr = /\[\]$/.test(el.name);
      var key = isArr ? el.name.replace(/\[\]$/,'') : el.name;
      var val = params[key];
      if(isArr){
        var arr = val || [];
        if(Object.prototype.toString.call(arr)!=='[object Array]') arr=[arr];
        if(el.type==='checkbox'||el.type==='radio'){ el.checked = arr.indexOf(String(el.value))>-1; }
        else { el.value = arr[0] || ''; }
      } else {
        if(el.type==='checkbox'||el.type==='radio'){ el.checked = String(el.value)===String(val); }
        else { el.value = (typeof val==='undefined') ? '' : String(val); }
      }
    });
  }
  setForm(container.querySelector('.ctdir-hero-form'));
  setForm(container.querySelector('.ctdir-filter-form'));
}

/* ----- BUILD URLS (with cache-buster), keep seed stable ----- */
function buildQS(container, resetPage){
  var obj = collectParams(container);
  if(resetPage) obj.pg='1';
  try{ var cur=new URL(window.location.href); var seed=cur.searchParams.get('seed'); if(seed && !obj.seed) obj.seed=seed; }catch(e){}
  var displayQS = paramsToQS(obj);
  obj.partial='1';
  obj._=String(Date.now()); // cache-buster to prevent caches stripping partial
  var fetchQS = paramsToQS(obj);
  return {displayQS:displayQS, fetchQS:fetchQS};
}

/* ----- Fetch partial safely; if full page detected -> navigate ----- */
function fetchPartial(container, qs, fallbackDisplayQS){
  var url = window.location.pathname + '?' + qs;
  markLoading(container);

  // cancel any previous request
  if (ctdirActiveController && typeof ctdirActiveController.abort === 'function') {
    try { ctdirActiveController.abort(); } catch(e){}
  }
  var controller = (window.AbortController ? new AbortController() : null);
  ctdirActiveController = controller;
  var mySeq = ++ctdirReqSeq;

  function done(){ unmarkLoading(container); }

  function handleHTML(html){
    // accept only the latest response
    if (mySeq !== ctdirReqSeq) return;

    // Guard: full page returned? navigate instead of injecting
    var isFull = /<html[\s>]/i.test(html) || (/<header[\s>]/i.test(html) && /<footer[\s>]/i.test(html));
    if (isFull){
      var navUrl = window.location.pathname + (fallbackDisplayQS?('?'+fallbackDisplayQS):'');
      window.location.href = navUrl;
      return;
    }
    var box = container.querySelector('#ctdir-results');
    if (box) box.innerHTML = html;
    bindPagination(container);
  }

  // fetch (with abort) or XHR fallback
  if (window.fetch){
    var opt = { headers:{'X-Requested-With':'XMLHttpRequest'} };
    if (controller) opt.signal = controller.signal;
    return fetch(url, opt)
      .then(function(r){ return r.text(); })
      .then(handleHTML)
      .catch(function(err){
        if (err && err.name === 'AbortError') return; // expected
        var navUrl = window.location.pathname + (fallbackDisplayQS?('?'+fallbackDisplayQS):'');
        window.location.href = navUrl;
      })
      .then(done);
  } else {
    try{
      var xhr=new XMLHttpRequest();
      xhr.open('GET', url, true);
      xhr.setRequestHeader('X-Requested-With','XMLHttpRequest');
      xhr.onreadystatechange=function(){
        if(xhr.readyState===4){
          if(xhr.status>=200 && xhr.status<300){ handleHTML(xhr.responseText); }
          else{
            var navUrl = window.location.pathname + (fallbackDisplayQS?('?'+fallbackDisplayQS):'');
            window.location.href = navUrl;
          }
          done();
        }
      };
      xhr.send(null);
    }catch(e){
      done();
      var navUrl = window.location.pathname + (fallbackDisplayQS?('?'+fallbackDisplayQS):'');
      window.location.href = navUrl;
    }
  }
}

/* ---------- One-open helpers ---------- */
function ctdir_closeAll(wrap){
  toArray(wrap.querySelectorAll(".acc")).forEach(function(acc){
    acc.classList.remove("open");
    var p = acc.querySelector(".panel");
    if (p) p.style.display = "none";
  });
}
function ctdir_openThis(acc){
  var wrap = closest(acc, ".ctdir-filter");
  if (!wrap) return;
  ctdir_closeAll(wrap);
  var p = acc.querySelector(".panel");
  acc.classList.add("open");
  if (p) p.style.display = "block";
}


function submitFilters(container, resetPage, push){
  var pair = buildQS(container, !!resetPage);
  var displayUrl = window.location.pathname + (pair.displayQS?('?'+pair.displayQS):'');
  if(push===false){ window.history.replaceState({qs:pair.displayQS}, '', displayUrl); }
  else { window.history.pushState({qs:pair.displayQS}, '', displayUrl); }
  return fetchPartial(container, pair.fetchQS, pair.displayQS);
}

/* ----- Pagination (AJAX) ----- */
function bindPagination(container){
  toArray(container.querySelectorAll('#ctdir-results .ctdir-pagination a')).forEach(function(a){
    a.addEventListener('click', function(e){
      e.preventDefault();
      var base = collectParams(container);
      var m = a.href.match(/(?:[?&])pg=(\d+)/);
      base.pg = m ? m[1] : '1';
      var displayQS = paramsToQS(base);
      window.history.pushState({qs:displayQS}, '', window.location.pathname + (displayQS?('?'+displayQS):''));
      base.partial='1'; base._=String(Date.now());
      fetchPartial(container, paramsToQS(base), displayQS);
    });
  });
}

/* ----- Back/forward support ----- */
window.addEventListener('popstate', function(e){
  var c = document.querySelector('.ctdir'); if(!c) return;
  var qs = (e && e.state && e.state.qs) ? e.state.qs : (window.location.search||'').replace(/^\?/,'');
  var params = parseQS(qs);
  syncForms(c, params);
  var obj = params; obj.partial='1'; obj._=String(Date.now());
  fetchPartial(c, paramsToQS(obj), qs);
});

/* ----- Events ----- */
document.addEventListener('change', function(e){
  var c = closest(e.target, '.ctdir'); if(!c) return;
  if(!closest(e.target, '.ctdir-filter-form') && !closest(e.target, '.ctdir-hero-form')) return;
  submitFilters(c, true, true);
});
var debounce;
document.addEventListener('keyup', function(e){
  if(!matches(e.target, 'input[type=text]')) return;
  var c = closest(e.target, '.ctdir'); if(!c) return;
  if(!closest(e.target, '.ctdir-filter-form')) return;
  clearTimeout(debounce);
  debounce = setTimeout(function(){ submitFilters(c, true, true); }, 400);
});




// --- One-open accordion (on click) ---
document.addEventListener("click", function(e){
  if (!matches(e.target, ".ctdir-filter .acc > button")) return;
  e.preventDefault();

  var acc  = closest(e.target, ".acc");
  if (!acc) return;

  var isOpen = acc.classList.contains("open");
  if (isOpen){
    acc.classList.remove("open");
    var p = acc.querySelector(".panel");
    if (p) p.style.display = "none";
  } else {
    ctdir_openThis(acc);
  }
});






document.addEventListener('click', function(e){
  var btn = closest(e.target, '.ctdir-reset'); if(!btn) return;
  e.preventDefault();
  var c = closest(btn, '.ctdir'); if(!c) return;
  ['.ctdir-hero-form','.ctdir-filter-form'].forEach(function(sel){
    var f=c.querySelector(sel); if(!f) return;
    if(f.reset) f.reset();
    toArray(f.querySelectorAll('select')).forEach(function(s){ s.value=''; });
    toArray(f.querySelectorAll('input[type=text],input[type=number]')).forEach(function(i){ i.value=''; });
    toArray(f.querySelectorAll('input[type=radio],input[type=checkbox]')).forEach(function(i){ i.checked=false; });
    var pg=f.querySelector('input[name=pg]'); if(!pg){ pg=document.createElement('input'); pg.type='hidden'; pg.name='pg'; f.appendChild(pg); } pg.value='1';
    var prof=f.querySelector('input[name="profession"]'); if(prof) prof.value='';
  });
  submitFilters(c, true, true);
});

// --- Auto-open the first active accordion on load ---
document.addEventListener("DOMContentLoaded", function(){
  var wrap = document.querySelector(".ctdir-filter");
  if (!wrap) return;

  var accs = toArray(wrap.querySelectorAll(".acc"));
  var toOpen = null;

  for (var i=0;i<accs.length;i++){
    var acc   = accs[i];
    var panel = acc.querySelector(".panel");
    if (!panel) continue;

    var hasChecked = panel.querySelector("input[type=checkbox]:checked, input[type=radio]:checked");
    var inputs = toArray(panel.querySelectorAll("input[type=text], input[type=number], select"));
    var hasValue = false;
    for (var j=0;j<inputs.length;j++){
      var el = inputs[j];
      if (el.tagName === "SELECT") { if (el.value && el.value !== "") { hasValue = true; break; } }
      else {
        var v = (el.value || "").replace(/^\s+|\s+$/g, "");
        if (v !== "") { hasValue = true; break; }
      }
    }

    if (hasChecked || hasValue){ toOpen = acc; break; }
  }

  if (toOpen) ctdir_openThis(toOpen);
});

// --- When a control changes, auto-open its accordion ---
document.addEventListener("change", function(e){
  var acc = closest(e.target, ".acc");
  if (acc) ctdir_openThis(acc);
});



/* ===== Fee slider (unchanged core) ===== */
(function(){
  function clamp(v,min,max){v=+v;return isNaN(v)?min:Math.min(max,Math.max(min,v))}
  function pct(v,min,max){return ((v-min)/(max-min))*100}
  function initDualFee(el){
    if(!el.__ctdirInited) el.__ctdirInited={};
    var minBound=+el.getAttribute("data-min")||0;
    var maxBound=+el.getAttribute("data-max")||500;
    var step=+el.getAttribute("data-step")||5;
    var rMin=el.querySelector("input.fee-min");
    var rMax=el.querySelector("input.fee-max");
    var fill=el.querySelector(".fill");
    var outL=el.querySelector(".fee-left");
    var outR=el.querySelector(".fee-right");
    var leftVal=outL?outL.querySelector(".fee-val"):null;
    var rightVal=outR?outR.querySelector(".fee-val"):null;
    var track=el.querySelector(".fee-track");
    if(!rMin||!rMax||!fill||!track) return;
    var tipMin=track.querySelector(".fee-tip.min");
    var tipMax=track.querySelector(".fee-tip.max");
    if(!tipMin){tipMin=document.createElement("span");tipMin.className="fee-tip min";track.appendChild(tipMin)}
    if(!tipMax){tipMax=document.createElement("span");tipMax.className="fee-tip max";track.appendChild(tipMax)}
    function sync(src){
      var v1=clamp(parseFloat(rMin.value)||minBound,minBound,maxBound);
      var v2=clamp(parseFloat(rMax.value)||maxBound,minBound,maxBound);
      if(v1>v2-step){ if(src===rMin){ v2=clamp(v1+step,minBound,maxBound); rMax.value=v2; } else { v1=clamp(v2-step,minBound,maxBound); rMin.value=v1; } }
      var p1=pct(v1,minBound,maxBound), p2=pct(v2,minBound,maxBound);
      fill.style.left=p1+"%"; fill.style.right=(100-p2)+"%";
      tipMin.textContent="$"+Math.round(v1); tipMax.textContent="$"+Math.round(v2);
      tipMin.style.left=p1+"%"; tipMax.style.left=p2+"%";
      var close=(p2-p1)<6; tipMin.classList.toggle("stack",close); tipMax.classList.toggle("stack",close);
      if(leftVal) leftVal.textContent=Math.round(minBound);
      if(rightVal) rightVal.textContent=Math.round(maxBound);
    }
    function bringToFront(i){rMin.classList.remove("on-top");rMax.classList.remove("on-top");i.classList.add("on-top")}
    ["pointerdown","mousedown","touchstart"].forEach(function(evt){rMin.addEventListener(evt,function(){bringToFront(rMin)});rMax.addEventListener(evt,function(){bringToFront(rMax)})});
    track.addEventListener("pointerdown",function(e){
      if(e.target===rMin||e.target===rMax) return;
      var rect=track.getBoundingClientRect(); var p=(e.clientX-rect.left)/rect.width;
      var raw=minBound + p*(maxBound-minBound); var val=Math.round(raw/step)*step;
      var curMin=parseFloat(rMin.value)||minBound; var curMax=parseFloat(rMax.value)||maxBound;
      var target=(Math.abs(val-curMin)<=Math.abs(val-curMax))?rMin:rMax;
      target.value=val; bringToFront(target); sync(target); target.dispatchEvent(new Event("change",{bubbles:true}));
    });
    if(!el.__ctdirInited.listeners){ rMin.addEventListener("input",function(){sync(rMin)}); rMax.addEventListener("input",function(){sync(rMax)}); el.__ctdirInited.listeners=true; }
    el.__ctdirRefresh=function(){ if(!rMin.value) rMin.value=rMin.getAttribute("min")||minBound; if(!rMax.value) rMax.value=rMax.getAttribute("max")||maxBound; if(leftVal) leftVal.textContent=Math.round(minBound); if(rightVal) rightVal.textContent=Math.round(maxBound); sync(); };
    el.__ctdirRefresh();
  }
  function initAll(){ toArray(document.querySelectorAll(".fee-dual")).forEach(initDualFee) }
  if(document.readyState==="loading"){ document.addEventListener("DOMContentLoaded", initAll); } else { initAll(); }
})();
JS;

	wp_register_script($handle, '', [], '0.4.1', true);
	wp_add_inline_script($handle, $js);
	wp_enqueue_script($handle);
});


/**
 * ----------------------------------------
 * Helpers
 * ----------------------------------------
 */
function ct_dir_card_image_size()
{
	return 'large';
}
function ct_dir_profile_image_url($post_id)
{
	$post_id = (int)$post_id;
	$photo = get_post_meta($post_id, 'rcp_photo', true);
	if ($photo && filter_var($photo, FILTER_VALIDATE_URL)) return $photo;
	if (function_exists('get_the_post_thumbnail_url')) {
		$img = get_the_post_thumbnail_url($post_id, ct_dir_card_image_size());
		if ($img) return $img;
	}
	if (defined('CT_CORE_PLUGIN_URL')) return CT_CORE_PLUGIN_URL . '/dist/img/profile.png';
	return apply_filters('ct_dir/placeholder_image', 'https://www.gravatar.com/avatar/?d=mp&s=420');
}
function ct_dir_taxonomies()
{
	return array(
		'profession'   => 'ct_profession',
		'service_area' => 'service_area',
		'insurance'    => 'ct_profile_insurance',
		'values'       => 'ct_profile_values',
		'years'        => 'ct_years_of_experience',
		'credential'   => 'type-of-primary-credential',
		'specialties'  => 'specialties-service',
		'modality'     => 'modality',
		'language'     => 'user_language',
		'faith'        => 'faith',
		'therapy'      => 'type-of-therapy',
	);
}
function ct_dir_get_query()
{
	$service_area = isset($_GET['service_area']) ? $_GET['service_area'] : [];
	if (!is_array($service_area) && $service_area !== '') $service_area = array($service_area);
	$therapy = isset($_GET['therapy']) ? $_GET['therapy'] : [];
	if (!is_array($therapy) && $therapy !== '') $therapy = array($therapy);

	$q = array(
		's'            => isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '',
		'pg'           => isset($_GET['pg']) ? max(1, intval($_GET['pg'])) : 1,
		'zip'          => isset($_GET['zip']) ? preg_replace('/[^0-9]/', '', (string)$_GET['zip']) : '',
		'radius'       => isset($_GET['radius']) ? intval($_GET['radius']) : 50,
		'profession'   => isset($_GET['profession']) ? absint($_GET['profession']) : 0,

		'service_area' => array_map('absint', (array)$service_area),
		'therapy'      => array_map('absint', (array)$therapy),

		'insurance'    => array_map('absint', isset($_GET['insurance']) ? (array)$_GET['insurance'] : []),
		'values'       => array_map('absint', isset($_GET['values']) ? (array)$_GET['values'] : []),
		'years'        => array_map('absint', isset($_GET['years']) ? (array)$_GET['years'] : []),
		'credential'   => array_map('absint', isset($_GET['credential']) ? (array)$_GET['credential'] : []),

		'specialties'  => isset($_GET['specialties']) ? absint($_GET['specialties']) : 0,
		'modality'     => isset($_GET['modality']) ? absint($_GET['modality']) : 0,
		'language'     => isset($_GET['language']) ? absint($_GET['language']) : 0,
		'faith'        => isset($_GET['faith']) ? absint($_GET['faith']) : 0,

		'fees_min'     => isset($_GET['fees_min']) ? floatval($_GET['fees_min']) : 0,
		'fees_max'     => isset($_GET['fees_max']) ? floatval($_GET['fees_max']) : 0,

		'session'      => isset($_GET['session']) ? (array)$_GET['session'] : [],
	);

	return $q;
}
function ct_dir_is_unfiltered($q)
{
	return empty($q['s']) && empty($q['zip']) && empty($q['service_area']) && empty($q['insurance']) &&
		empty($q['values']) && empty($q['years']) && empty($q['credential']) && empty($q['specialties']) &&
		empty($q['modality']) && empty($q['language']) && empty($q['faith']) && empty($q['therapy']) &&
		empty($q['session']) && (empty($q['fees_min']) && empty($q['fees_max'])) && empty($q['profession']);
}

/**
 * Build WP_Query based on request.
 */
function ct_dir_build_query($q, $tax)
{
	$paged = max(1, (int)$q['pg']);

	$tax_query = array('relation' => 'AND');
	if (!empty($q['profession']))   $tax_query[] = array('taxonomy' => $tax['profession'],  'field' => 'term_id', 'terms' => array($q['profession']));
	if (!empty($q['service_area'])) $tax_query[] = array('taxonomy' => $tax['service_area'], 'field' => 'term_id', 'terms' => $q['service_area']);
	if (!empty($q['insurance']))    $tax_query[] = array('taxonomy' => $tax['insurance'],   'field' => 'term_id', 'terms' => $q['insurance']);
	if (!empty($q['values']))       $tax_query[] = array('taxonomy' => $tax['values'],      'field' => 'term_id', 'terms' => $q['values']);
	if (!empty($q['years']))        $tax_query[] = array('taxonomy' => $tax['years'],       'field' => 'term_id', 'terms' => $q['years']);
	if (!empty($q['credential']))   $tax_query[] = array('taxonomy' => $tax['credential'],  'field' => 'term_id', 'terms' => $q['credential']);
	if (!empty($q['specialties']))  $tax_query[] = array('taxonomy' => $tax['specialties'], 'field' => 'term_id', 'terms' => array($q['specialties']));
	if (!empty($q['modality']))     $tax_query[] = array('taxonomy' => $tax['modality'],    'field' => 'term_id', 'terms' => array($q['modality']));
	if (!empty($q['language']))     $tax_query[] = array('taxonomy' => $tax['language'],    'field' => 'term_id', 'terms' => array($q['language']));
	if (!empty($q['faith']))        $tax_query[] = array('taxonomy' => $tax['faith'],       'field' => 'term_id', 'terms' => array($q['faith']));
	if (!empty($q['therapy'])) {
		$tax_query[] = array('taxonomy' => $tax['therapy'], 'field' => 'term_id', 'terms' => $q['therapy']);
	}

	$meta_query = array('relation' => 'AND');

	// Fees
	$from = (float)$q['fees_min'];
	$to = (float)$q['fees_max'];
	if ($from || $to) {
		if (!$from && $to) $from = 0;
		if ($from && !$to) $to = 999999;
		$meta_query[] = array('key' => 'rcp_fee', 'value' => array($from, $to), 'type' => 'NUMERIC', 'compare' => 'BETWEEN');
	}

	// In-person / Virtual
	if (!empty($q['session'])) {
		$or = array('relation' => 'OR');
		foreach ((array)$q['session'] as $v) {
			$or[] = array('key' => 'rcp_inperson_virtual', 'value' => $v, 'compare' => 'LIKE');
		}
		$meta_query[] = $or;
	}

	// Distance
	$post__in = array();
	if (!empty($q['zip'])) {
		$post__in = apply_filters('ct_dir/filter_ids_by_distance', array(), $q['zip'], $q['radius']);
		if (empty($post__in)) $post__in = array(0);
	}

	$args = array(
		'post_type'      => 'ct-user-profile',
		'post_status'    => 'publish',
		's'              => $q['s'],
		'posts_per_page' => 12,
		'paged'          => $paged,
	);

	if (count($tax_query) > 1)  $args['tax_query']  = $tax_query;
	if (count($meta_query) > 1) $args['meta_query'] = $meta_query;
	if (!empty($post__in))      $args['post__in']   = $post__in;

	// Seeded random when UNFILTERED
	$remove_orderby_filter = null;
	if (ct_dir_is_unfiltered($q)) {
		$seed = isset($_GET['seed']) ? sanitize_text_field($_GET['seed']) : wp_generate_password(6, false);
		add_filter('posts_orderby', $remove_orderby_filter = function ($orderby) use ($seed) {
			global $wpdb;
			return "RAND('" . esc_sql($seed) . "')";
		});
	}

	$wpq = new WP_Query($args);

	if ($remove_orderby_filter) remove_filter('posts_orderby', $remove_orderby_filter);

	return $wpq;
}

/**
 * ----------------------------------------
 * Shortcode: [ct_directory]
 * ----------------------------------------
 */
add_shortcode('ct_directory', function ($atts = array(), $content = '') {
	$tax  = ct_dir_taxonomies();
	$q    = ct_dir_get_query();

	// Seed only when unfiltered
	if (ct_dir_is_unfiltered($q) && empty($_GET['seed'])) {
		$_GET['seed'] = wp_generate_password(6, false);
	}

	$qry   = ct_dir_build_query($q, $tax);
	$paged = max(1, (int)$q['pg']);

	// Partial path
	if (isset($_GET['partial']) && $_GET['partial'] == '1') {
		$results_tpl = CTDIR_PLUGIN_DIR . 'templates/parts/results.php';
		if (!file_exists($results_tpl)) return '<!-- Missing partial: templates/parts/results.php -->';
		ob_start();
		include $results_tpl;
		return ob_get_clean();
	}

	// Full page path
	ob_start();
	$tpl = CTDIR_PLUGIN_DIR . 'templates/directory.php';
	if (file_exists($tpl)) include $tpl;
	else echo '<p>Template not found: templates/directory.php</p>';
	return ob_get_clean();
});

/**
 * ----------------------------------------
 * REST: Single profile JSON (safe)
 * ----------------------------------------
 */
if (!function_exists('ctdir_register_profile_route')) {
	function ctdir_register_profile_route()
	{
		if (!function_exists('register_rest_route')) return;
		register_rest_route('ct-dir/v1', '/profile', array(
			'methods'             => 'GET',
			'permission_callback' => '__return_true',
			'callback'            => 'ctdir_rest_profile_callback',
		));
	}
	add_action('rest_api_init', 'ctdir_register_profile_route');
}
if (!function_exists('ctdir_rest_profile_callback')) {
	function ctdir_rest_profile_callback($req)
	{
		$meta_single = function ($post_id, $key) {
			$v = get_post_meta($post_id, $key, true);
			$v = maybe_unserialize($v);
			if (is_array($v)) $v = reset($v);
			return trim((string)$v);
		};
		$profile_image = function ($post_id) {
			$photo = get_post_meta($post_id, 'rcp_photo', true);
			if ($photo && filter_var($photo, FILTER_VALIDATE_URL)) return $photo;
			if (function_exists('get_the_post_thumbnail_url')) {
				$img = get_the_post_thumbnail_url($post_id, 'large');
				if ($img) return $img;
			}
			if (defined('CT_CORE_PLUGIN_URL')) return CT_CORE_PLUGIN_URL . '/dist/img/profile.png';
			return 'https://www.gravatar.com/avatar/?d=mp&s=420';
		};

		$id = absint(isset($req['id']) ? $req['id'] : 0);
		$slug = sanitize_title(isset($req['slug']) ? $req['slug'] : '');

		if ($id) {
			$p = get_post($id);
			if (!$p || is_wp_error($p) || $p->post_type !== 'ct-user-profile' || $p->post_status !== 'publish') {
				return new \WP_Error('not_found', 'Profile not found', array('status' => 404));
			}
		}
		if (!$id && $slug) {
			$q = new \WP_Query(array('post_type' => 'ct-user-profile', 'name' => $slug, 'post_status' => 'publish', 'fields' => 'ids', 'posts_per_page' => 1, 'no_found_rows' => true));
			if ($q->have_posts()) $id = (int)$q->posts[0];
			if (!$id && function_exists('get_page_by_path')) {
				$p2 = get_page_by_path($slug, OBJECT, 'ct-user-profile');
				if ($p2 && !is_wp_error($p2) && $p2->post_status === 'publish') $id = (int)$p2->ID;
			}
			if (!$id) {
				global $wpdb;
				$maybe_id = $wpdb->get_var($wpdb->prepare(
					"SELECT ID FROM {$wpdb->posts} WHERE post_type=%s AND post_status='publish' AND post_name=%s LIMIT 1",
					'ct-user-profile',
					$slug
				));
				if ($maybe_id) $id = (int)$maybe_id;
			}
		}
		if (!$id) return new \WP_Error('not_found', 'Profile not found', array('status' => 404));

		$city = $meta_single($id, 'rcp_city');
		$state = $meta_single($id, 'rcp_state');
		if ($state === '' && taxonomy_exists('service_area')) {
			$terms = wp_get_post_terms($id, 'service_area');
			if (!is_wp_error($terms) && !empty($terms)) $state = (string)$terms[0]->name;
		}

		$tax_map = array();
		$maybe_taxes = array('faith', 'user_language', 'modality', 'specialties-service', 'type-of-primary-credential', 'type-of-therapy', 'service_area', 'ct_profile_insurance', 'ct_profile_values', 'ct_years_of_experience', 'ct_profession');
		foreach ($maybe_taxes as $tx) {
			if (!taxonomy_exists($tx)) continue;
			$t = wp_get_post_terms($id, $tx);
			if (is_wp_error($t) || empty($t)) continue;
			$tax_map[$tx] = array_map(function ($term) {
				return array('id' => (int)$term->term_id, 'slug' => $term->slug, 'name' => $term->name);
			}, $t);
		}

		return array(
			'id' => (int)$id,
			'title' => get_the_title($id),
			'url' => get_permalink($id),
			'city' => $city,
			'state' => $state,
			'lat' => $meta_single($id, '_ct_latitude'),
			'lng' => $meta_single($id, '_ct_longitude'),
			'photo' => $profile_image($id),
			'meta' => get_post_meta($id),
			'taxonomies' => $tax_map,
		);
	}
}

/**
 * ----------------------------------------
 * Distance filter using ct-core ZIP→coords
 * ----------------------------------------
 */
add_filter('ct_dir/filter_ids_by_distance', function ($ids, $zip, $miles) {
	$zip   = trim($zip);
	$miles = max(0, (int)$miles);
	if (!$zip || !$miles) return array();

	$lat = $lng = null;
	if (class_exists('CT_Core\GMaps')) {
		$coords = \CT_Core\GMaps::fetch_coordinates_from_zip($zip);
		$lat = isset($coords['lat']) ? (float)$coords['lat'] : null;
		$lng = isset($coords['lng']) ? (float)$coords['lng'] : null;
	}
	if ($lat === null || $lng === null) return array();

	$q = new WP_Query(array(
		'post_type'      => 'ct-user-profile',
		'fields'         => 'ids',
		'posts_per_page' => -1,
		'no_found_rows'  => true,
		'meta_query'     => array(
			array('key' => '_ct_latitude',  'compare' => 'EXISTS'),
			array('key' => '_ct_longitude', 'compare' => 'EXISTS'),
		),
	));

	$keep = array();
	foreach ($q->posts as $pid) {
		$plat = (float) get_post_meta($pid, '_ct_latitude',  true);
		$plng = (float) get_post_meta($pid, '_ct_longitude', true);
		if (!$plat && !$plng) continue;
		$theta = deg2rad($plng - $lng);
		$dist  = acos(sin(deg2rad($lat)) * sin(deg2rad($plat)) + cos(deg2rad($lat)) * cos(deg2rad($plat)) * cos($theta));
		$dist  = rad2deg($dist) * 60 * 1.1515; // miles
		if ($dist <= $miles) $keep[] = $pid;
	}
	return $keep;
}, 10, 3);
