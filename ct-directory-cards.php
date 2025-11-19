<?php

/**
 * Plugin Name: CT Directory Filter
 * Description: Directory grid + filters + JSON inspector for CT profiles.
 * Author: Muhammad Elias
 * Version: 0.3.0
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

	/* ---------- Inline CSS (kept minimal & scoped) ---------- */
	$css = '/* minimal CSS for layout */
.ctdir-row-hero{background:#152534;color:#fff;padding:60px 20px;text-align:center}
.ctdir-row-hero select{max-width:420px;width:100%;padding:10px;border-radius:6px;border:1px solid #334}
.ctdir-row-hero a{color:#fff;display:inline-block;margin-top:10px;text-decoration:underline}
.ctdir-wrap{background:#fff;padding:50px 10px}
.ctdir-grid{display:grid;grid-template-columns:1fr;gap:22px}
@media(min-width:900px){.ctdir-grid{grid-template-columns:repeat(3,1fr)}}
.ctdir-cols{display:grid;grid-template-columns:1fr;gap:28px}
@media(min-width:1100px){.ctdir-cols{grid-template-columns:320px 1fr}}
.ctdir-filter .acc{border:1px solid #ddd;border-radius:8px;margin-bottom:10px}
.ctdir-filter .acc>button{width:100%;text-align:left;background:#f7f7f7;border:0;padding:12px 14px;font-weight:600;cursor:pointer;border-radius:8px;position:relative;padding-right:30px}
.ctdir-filter .acc>button:after{content:"\\25BC";position:absolute;right:12px;top:50%;transform:translateY(-50%);font-size:10px;transition:transform .2s ease}
.ctdir-filter .acc.open>button:after{transform:translateY(-50%) rotate(180deg)}
.ctdir-filter .acc .panel{display:none;padding:12px 14px}
.ctdir-card{box-shadow:0 2px 10px rgba(0,0,0,.08);border-radius:10px;overflow:hidden;text-align:center;background:#fff}
.ctdir-card .hdr{background:#0e2c48;color:#fff;padding:12px 10px;text-transform:uppercase;font-size:13px;letter-spacing:.04em}
.ctdir-card .inner{padding:18px}
.ctdir-card .photo img{width:210px;height:210px;object-fit:cover;border-radius:50%;display:block;margin:0 auto 16px}
.ctdir-card .name{font-size:18px;margin:6px 0}
.ctdir-card .role{opacity:.8;margin-bottom:14px}
.ctdir-card .btn{display:inline-block;background:#ffc65c;color:#1a1a1a;padding:10px 16px;border-radius:24px;text-decoration:none;font-weight:600}
.ctdir-pagination{display:flex;gap:6px;justify-content:center;margin-top:22px}
.ctdir-pagination a,.ctdir-pagination span{border:1px solid #ddd;border-radius:4px;padding:6px 10px}
.ctdir-filter .checklist{display:grid;gap:6px}
.ctdir-filter .checklist label{display:block}
.ctdir-filter .chips{display:flex;flex-wrap:wrap;gap:8px}
.ctdir-filter .chips label{border:1px solid #ddd;padding:6px 10px;border-radius:999px}
/* Spinner only on results section */
.ctdir .ctdir-cols section{position:relative}
.ctdir .ctdir-cols section.is-loading::after{content:"";position:absolute;inset:0;background:rgba(255,255,255,.6);backdrop-filter:blur(1px);z-index:10}
.ctdir .ctdir-cols section.is-loading::before{content:"";position:absolute;top:50%;left:50%;width:36px;height:36px;margin:-18px 0 0 -18px;border-radius:50%;border:3px solid rgba(0,0,0,.2);border-top-color:#0e2c48;animation:ctdirspin .8s linear infinite;z-index:11}
@keyframes ctdirspin{to{transform:rotate(360deg)}}
.search-fields input{height:40px;padding:10px !important;width:100%;border-radius:25px}
.acc .panel select{width:100%;height:40px;padding:5px;border:1px solid #00000055;border-radius:25px}
.ctdir-filter-form input[name="zip"]{width:100%;margin-bottom:10px;border:1px solid #00000055;height:40px;padding:10px;border-radius:25px}
/* --- Dual fee slider --- */
.fee-dual{display:flex;align-items:center;gap:4px}
.fee-dual .fee-left,.fee-dual .fee-right{min-width:20px;text-align:center;font-weight:600}
.fee-track{position:relative;flex:1;height:32px}
.fee-track input[type=range]{-webkit-appearance:none;appearance:none;position:absolute;left:0;right:0;top:50%;transform:translateY(-50%);width:100%;height:24px;background:transparent;pointer-events:none;margin:0}
.fee-track input[type=range]::-webkit-slider-thumb{-webkit-appearance:none;appearance:none;width:18px;height:18px;border-radius:50%;background:#0e2c48;border:2px solid #fff;box-shadow:0 0 0 1px rgba(0,0,0,.2);pointer-events:auto;cursor:pointer}
.fee-track input[type=range]::-moz-range-thumb{width:18px;height:18px;border-radius:50%;background:#0e2c48;border:2px solid #fff;box-shadow:0 0 0 1px rgba(0,0,0,.2);pointer-events:auto;cursor:pointer}
/* base track */
.fee-track::before{content:"";position:absolute;left:0;right:0;top:50%;transform:translateY(-50%);height:6px;border-radius:6px;background:#e9edf3}
/* fill between min/max */
.fee-track .fill{position:absolute;left:0;right:0;top:50%;transform:translateY(-50%);height:6px;border-radius:6px;background:#0e2c48}
/* --- Dual fee slider tooltips --- */
.fee-track{position:relative}
.fee-track .fee-tip{
  position:absolute; bottom:100%; transform:translate(-50%,-6px);
  background:#0e2c48; color:#fff; padding:3px 6px; border-radius:4px;
  font-size:12px; line-height:1; white-space:nowrap; pointer-events:none;
  box-shadow:0 1px 2px rgba(0,0,0,.2); z-index:12
}
.fee-track .fee-tip:after{
  content:""; position:absolute; left:50%; transform:translateX(-50%);
  top:100%; width:0; height:0; border-left:6px solid transparent;
  border-right:6px solid transparent; border-top:6px solid #0e2c48;
}
.pt-big{padding-top: 30px !important;}
.fee-track .fee-tip.stack{ transform:translate(-50%,-24px); }
.fee-dual output{display:inline-flex;gap:1px;align-items:center;font-weight:600}
.fee-dual .fee-label{opacity:.7;font-size:10px;text-transform:uppercase;letter-spacing:.04em}
';

	wp_register_style($handle, false, [], '0.3.2');
	wp_add_inline_style($handle, $css);
	wp_enqueue_style($handle);

	/* ---------- Inline JS ---------- */
	$js = 'function ctdir_markLoading(form){
  var section=form.closest(".ctdir")?.querySelector(".ctdir-cols section");
  if(section) section.classList.add("is-loading");
}
function ctdir_submitNow(form,resetPage){
  if(!form) return;
  if(resetPage){
    var pg=form.querySelector("input[name=pg]");
    if(pg) pg.value=1;
  }
  ctdir_markLoading(form);
  form.requestSubmit?form.requestSubmit():form.submit();
}
// Change => submit (hero + sidebar)
document.addEventListener("change",function(e){
  var f=e.target.closest(".ctdir-filter-form,.ctdir-hero-form");
  if(f){ ctdir_submitNow(f,true); }
});
// Debounce search
var ctdir_t;
document.addEventListener("keyup",function(e){
  if(!e.target.matches("input[type=text]")) return;
  var f=e.target.closest(".ctdir-filter-form");
  if(!f) return;
  clearTimeout(ctdir_t);
  ctdir_t=setTimeout(function(){ ctdir_submitNow(f,true); }, 500);
});
// Accordion toggle
document.addEventListener("click",function(e){
  if(e.target.matches(".ctdir-filter .acc > button")){
    e.preventDefault();
    var acc=e.target.closest(".acc");
    var p=acc.querySelector(".panel");
    var open=acc.classList.toggle("open");
    p.style.display=open?"block":"none";
  }
});
// Pagination (preserve filters)
document.addEventListener("click",function(e){
  var a=e.target.closest(".ctdir-pagination a");
  if(!a) return;
  var c=a.closest(".ctdir");
  var f=c?.querySelector(".ctdir-filter-form");
  if(!f) return;
  e.preventDefault();
  var m=a.href.match(/(?:[?&])pg=(\\d+)/);
  var pg=f.querySelector("input[name=pg]")||document.createElement("input");
  if(!pg.parentNode){ pg.type="hidden"; pg.name="pg"; f.appendChild(pg); }
  pg.value=m?m[1]:1;
  ctdir_submitNow(f,false);
});
// Reset button
document.addEventListener("click",function(e){
  var btn=e.target.closest(".ctdir-reset");
  if(!btn) return;
  e.preventDefault();
  var f=btn.closest("form"); if(!f) return;

  f.reset();
  f.querySelectorAll("select").forEach(s=>s.value="");
  f.querySelectorAll("input[type=text],input[type=number]").forEach(i=>i.value="");
  f.querySelectorAll("input[type=radio],input[type=checkbox]").forEach(i=>i.checked=false);
  var prof=f.querySelector("input[name=profession]"); if(prof) prof.value="";
  var pg=f.querySelector("input[name=pg]"); if(!pg){ pg=document.createElement("input"); pg.type="hidden"; pg.name="pg"; f.appendChild(pg); }
  pg.value=1;

  // refresh fee dual slider visuals to defaults
  f.querySelectorAll(".fee-dual").forEach(function(el){
    var minBound = +el.getAttribute("data-min") || 0;
    var maxBound = +el.getAttribute("data-max") || 500;
    var rMin = el.querySelector(".fee-min");
    var rMax = el.querySelector(".fee-max");
    if (rMin){ rMin.value = rMin.getAttribute("min") || minBound; rMin.dispatchEvent(new Event("input", {bubbles:true})); }
    if (rMax){ rMax.value = rMax.getAttribute("max") || maxBound; rMax.dispatchEvent(new Event("input", {bubbles:true})); }
    if (el.__ctdirRefresh) el.__ctdirRefresh();
  });

  ctdir_submitNow(f,true);
});
// Scope spinner on any native submit
document.addEventListener("submit",function(e){
  var s=e.target.closest(".ctdir")?.querySelector(".ctdir-cols section");
  if(s) s.classList.add("is-loading");
}, true);

/* ---- Dual fee slider init (visual sync + constraints + tooltips) ---- */
(function(){
  function clamp(v,min,max){ v=+v; return isNaN(v)?min:Math.min(max, Math.max(min,v)); }
  function pct(v,min,max){ return ((v-min)/(max-min))*100; }

  function initDualFee(el){
    if (!el.__ctdirInited) el.__ctdirInited = {};
    var minBound = +el.getAttribute("data-min") || 0;
    var maxBound = +el.getAttribute("data-max") || 500;
    var step     = +el.getAttribute("data-step") || 5;

    var rMin  = el.querySelector("input.fee-min");
    var rMax  = el.querySelector("input.fee-max");
    var fill  = el.querySelector(".fill");
    var outL  = el.querySelector(".fee-left");
    var outR  = el.querySelector(".fee-right");
    var leftVal  = outL ? outL.querySelector(".fee-val") : null;
    var rightVal = outR ? outR.querySelector(".fee-val") : null;
    var track = el.querySelector(".fee-track");
    if(!rMin || !rMax || !fill || !track) return;

    // tooltips (auto-create)
    var tipMin = track.querySelector(".fee-tip.min");
    var tipMax = track.querySelector(".fee-tip.max");
    if(!tipMin){ tipMin = document.createElement("span"); tipMin.className = "fee-tip min"; track.appendChild(tipMin); }
    if(!tipMax){ tipMax = document.createElement("span"); tipMax.className = "fee-tip max"; track.appendChild(tipMax); }

    function sync(src){
      var v1 = clamp(parseFloat(rMin.value)||minBound, minBound, maxBound);
      var v2 = clamp(parseFloat(rMax.value)||maxBound, minBound, maxBound);

      if (v1 > v2 - step){
        if (src === rMin) { v2 = clamp(v1 + step, minBound, maxBound); rMax.value = v2; }
        else              { v1 = clamp(v2 - step, minBound, maxBound); rMin.value = v1; }
      }

      var p1 = pct(v1, minBound, maxBound);
      var p2 = pct(v2, minBound, maxBound);
      fill.style.left  = p1 + "%";
      fill.style.right = (100 - p2) + "%";

      // tooltips
      tipMin.textContent = "$" + Math.round(v1);
      tipMax.textContent = "$" + Math.round(v2);
      tipMin.style.left = p1 + "%";
      tipMax.style.left = p2 + "%";
      var close = (p2 - p1) < 6;
      tipMin.classList.toggle("stack", close);
      tipMax.classList.toggle("stack", close);

      // update ONLY the numeric parts in the outputs; keep "Min"/"Max" spans intact
      if (leftVal)  leftVal.textContent  = Math.round(minBound);  // fixed bound
      if (rightVal) rightVal.textContent = Math.round(maxBound);  // fixed bound
    }

    if (!el.__ctdirInited.listeners){
      rMin.addEventListener("input", function(){ sync(rMin); });
      rMax.addEventListener("input", function(){ sync(rMax); });
      el.__ctdirInited.listeners = true;
    }

    el.__ctdirRefresh = function(){
      if (!rMin.value) rMin.value = rMin.getAttribute("min") || minBound;
      if (!rMax.value) rMax.value = rMax.getAttribute("max") || maxBound;
      if (leftVal)  leftVal.textContent  = Math.round(minBound);
      if (rightVal) rightVal.textContent = Math.round(maxBound);
      sync();
    };

    el.__ctdirRefresh();
  }

  function initAll(){
    document.querySelectorAll(".fee-dual").forEach(initDualFee);
  }

  if (document.readyState === "loading"){
    document.addEventListener("DOMContentLoaded", initAll);
  } else {
    initAll();
  }
})();
';

	wp_register_script($handle, '', [], '0.3.2', true);
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
	$post_id = (int) $post_id;
	// 1) rcp_photo from ct-core
	$photo = get_post_meta($post_id, 'rcp_photo', true);
	if ($photo && filter_var($photo, FILTER_VALIDATE_URL)) return $photo;
	// 2) Featured image
	if (function_exists('get_the_post_thumbnail_url')) {
		$img = get_the_post_thumbnail_url($post_id, ct_dir_card_image_size());
		if ($img) return $img;
	}
	// 3) ct-core placeholder (if available)
	if (defined('CT_CORE_PLUGIN_URL')) {
		return CT_CORE_PLUGIN_URL . '/dist/img/profile.png';
	}
	// 4) generic
	return apply_filters('ct_dir/placeholder_image', 'https://www.gravatar.com/avatar/?d=mp&s=420');
}

function ct_dir_taxonomies()
{
	// map used by template and query builder
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
	$q = array(
		's'            => isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '',
		'pg'           => isset($_GET['pg']) ? max(1, intval($_GET['pg'])) : 1,
		'zip'          => isset($_GET['zip']) ? preg_replace('/[^0-9]/', '', (string)$_GET['zip']) : '',
		'radius'       => isset($_GET['radius']) ? intval($_GET['radius']) : 50,
		'profession'   => isset($_GET['profession']) ? absint($_GET['profession']) : 0,
		'service_area' => array_map('absint', isset($_GET['service_area']) ? (array)$_GET['service_area'] : []),
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
		'therapy' => array_map('absint', isset($_GET['therapy']) ? (array) $_GET['therapy'] : []),
	);
	return $q;
}

// random
function ct_dir_is_unfiltered($q)
{
	return empty($q['s'])
		&& empty($q['zip'])
		&& empty($q['service_area'])
		&& empty($q['insurance'])
		&& empty($q['values'])
		&& empty($q['years'])
		&& empty($q['credential'])
		&& empty($q['specialties'])
		&& empty($q['modality'])
		&& empty($q['language'])
		&& empty($q['faith'])
		&& empty($q['therapy'])
		&& empty($q['session'])
		&& (empty($q['fees_min']) && empty($q['fees_max']))
		&& empty($q['profession']); // include this if you consider profession a filter
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
		$tax_query[] = array(
			'taxonomy' => $tax['therapy'],       // 'type-of-therapy'
			'field'    => 'term_id',
			'terms'    => $q['therapy'],         // already an array
		);
	}
	if (ct_dir_is_unfiltered($q)) {
		$args['orderby'] = 'rand';
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

	// Seeded random order when UNFILTERED
	$remove_orderby_filter = null;
	if (ct_dir_is_unfiltered($q)) {
		$seed = isset($_GET['seed']) ? sanitize_text_field($_GET['seed']) : wp_generate_password(6, false);

		// Force RAND(seed) for this query only
		add_filter('posts_orderby', $remove_orderby_filter = function ($orderby) use ($seed) {
			global $wpdb;
			return "RAND('" . esc_sql($seed) . "')";
		});
	}

	$wpq = new WP_Query($args);

	if ($remove_orderby_filter) {
		remove_filter('posts_orderby', $remove_orderby_filter);
	}

	return $wpq;
}


/**
 * ----------------------------------------
 * Shortcode: [ct_directory]
 * ----------------------------------------
 */
// add_shortcode('ct_directory', function ($atts = array(), $content = '') {
// 	$tax  = ct_dir_taxonomies();
// 	$q    = ct_dir_get_query();
// 	$qry  = ct_dir_build_query($q, $tax);
// 	$paged = max(1, (int)$q['pg']);

// 	ob_start();
// 	$tpl = CTDIR_PLUGIN_DIR . 'templates/directory.php';
// 	if (!file_exists($tpl)) {
// 		echo '<p>Template not found: templates/directory.php</p>';
// 	} else {
// 		// Make vars available to template scope: $q, $tax, $qry, $paged
// 		include $tpl;
// 	}
// 	return ob_get_clean();
// });

// random paginations
add_shortcode('ct_directory', function ($atts = array(), $content = '') {
	$tax  = ct_dir_taxonomies();
	$q    = ct_dir_get_query();

	// If unfiltered and no seed yet, create one so pagination keeps a stable random order
	if (ct_dir_is_unfiltered($q) && empty($_GET['seed'])) {
		$_GET['seed'] = wp_generate_password(6, false);
	}

	$qry  = ct_dir_build_query($q, $tax);
	$paged = max(1, (int)$q['pg']);

	ob_start();
	$tpl = CTDIR_PLUGIN_DIR . 'templates/directory.php';
	if (!file_exists($tpl)) {
		echo '<p>Template not found: templates/directory.php</p>';
	} else {
		include $tpl;
	}
	return ob_get_clean();
});



/**
 * ----------------------------------------
 * REST: Single profile JSON (safe)
 * GET /wp-json/ct-dir/v1/profile?id=…|slug=…
 * city/state returned as strings
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
		// helpers
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

		$id   = absint(isset($req['id']) ? $req['id'] : 0);
		$slug = sanitize_title(isset($req['slug']) ? $req['slug'] : '');

		// ---------- ID path (publish-only) ----------
		if ($id) {
			$p = get_post($id);
			if (!$p || is_wp_error($p) || $p->post_type !== 'ct-user-profile' || $p->post_status !== 'publish') {
				return new \WP_Error('not_found', 'Profile not found', array('status' => 404));
			}
		}

		// ---------- Slug path (publish-only) ----------
		if (!$id && $slug) {
			// 1) Try name query (publish only)
			$q = new \WP_Query(array(
				'post_type'      => 'ct-user-profile',
				'name'           => $slug,
				'post_status'    => 'publish',
				'fields'         => 'ids',
				'posts_per_page' => 1,
				'no_found_rows'  => true,
			));
			if ($q->have_posts()) {
				$id = (int)$q->posts[0];
			}

			// 2) Fallback: get_page_by_path limited to publish (some hosts resolve CPTs differently)
			if (!$id && function_exists('get_page_by_path')) {
				$p2 = get_page_by_path($slug, OBJECT, 'ct-user-profile');
				if ($p2 && !is_wp_error($p2) && $p2->post_status === 'publish') {
					$id = (int)$p2->ID;
				}
			}

			// 3) Safety: direct SQL by post_name (publish only)
			if (!$id) {
				global $wpdb;
				$maybe_id = $wpdb->get_var($wpdb->prepare(
					"SELECT ID FROM {$wpdb->posts}
             WHERE post_type=%s AND post_status='publish' AND post_name=%s
             LIMIT 1",
					'ct-user-profile',
					$slug
				));
				if ($maybe_id) $id = (int)$maybe_id;
			}
		}

		if (!$id) {
			return new \WP_Error('not_found', 'Profile not found', array('status' => 404));
		}

		// Normalize fields
		$city  = $meta_single($id, 'rcp_city');
		$state = $meta_single($id, 'rcp_state');
		if ($state === '' && taxonomy_exists('service_area')) {
			$terms = wp_get_post_terms($id, 'service_area');
			if (!is_wp_error($terms) && !empty($terms)) {
				$state = (string)$terms[0]->name;
			}
		}

		// Taxonomies (publish-safe retrieval)
		$tax_map = array();
		$maybe_taxes = array(
			'faith',
			'user_language',
			'modality',
			'specialties-service',
			'type-of-primary-credential',
			'type-of-therapy',
			'service_area',
			'ct_profile_insurance',
			'ct_profile_values',
			'ct_years_of_experience',
			'ct_profession'
		);
		foreach ($maybe_taxes as $tx) {
			if (!taxonomy_exists($tx)) continue;
			$t = wp_get_post_terms($id, $tx);
			if (is_wp_error($t) || empty($t)) continue;
			$tax_map[$tx] = array_map(function ($term) {
				return array(
					'id'   => (int)$term->term_id,
					'slug' => $term->slug,
					'name' => $term->name
				);
			}, $t);
		}

		return array(
			'id'    => (int)$id,
			'title' => get_the_title($id),
			'url'   => get_permalink($id),
			'city'  => $city,
			'state' => $state,
			'lat'   => $meta_single($id, '_ct_latitude'),
			'lng'   => $meta_single($id, '_ct_longitude'),
			'photo' => $profile_image($id),
			'meta'  => get_post_meta($id),  // left raw for inspection
			'taxonomies' => $tax_map,
		);
	}
}


/**
 * ----------------------------------------
 * Distance filter using ct-core ZIP→coords
 *  apply_filters('ct_dir/filter_ids_by_distance', [], $zip, $miles)
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
