/*!
 * CT Directory – front-end interactions
 * - Partial fetch to avoid double header/footer
 * - Clean query building (no default noise)
 * - Robust AJAX (no .finally() dependency)
 * - One-open accordion + auto-open active
 * - Rebind pagination after updates
 * - Works with hero + sidebar (array normalization)
 */

(function () {
  "use strict";

  // ---------------------------------------------------------------------------
  // Small utilities (ES5-safe)
  // ---------------------------------------------------------------------------
  function closest(el, sel) {
    if (!el) return null;
    if (el.closest) return el.closest(sel);
    while (el && el.nodeType === 1) {
      if (matches(el, sel)) return el;
      el = el.parentNode;
    }
    return null;
  }
  function matches(el, sel) {
    var p = Element.prototype;
    var f = p.matches || p.msMatchesSelector || p.webkitMatchesSelector;
    return f.call(el, sel);
  }
  function toArray(nodeList) {
    return Array.prototype.slice.call(nodeList || [], 0);
  }

  // Single shared state
  var _ctDir =
    window._ctDir || (window._ctDir = { ajax: { inProgress: false } });

  // ---------------------------------------------------------------------------
  // Spinner helpers (scoped to results section)
  // ---------------------------------------------------------------------------
  function markLoading(form) {
    var ctdir = closest(form, ".ctdir");
    var section = ctdir ? ctdir.querySelector(".ctdir-cols section") : null;
    if (section) section.classList.add("is-loading");
  }
  function unmarkLoading(form) {
    var ctdir = closest(form, ".ctdir");
    var section = ctdir ? ctdir.querySelector(".ctdir-cols section") : null;
    if (section) section.classList.remove("is-loading");
  }

  // ---------------------------------------------------------------------------
  // Build clean querystring:
  //  - Skip empty/default values
  //  - Normalize hero scalar names → array names
  //  - Keep seed for stable unfiltered random
  //  - Add partial=1 so server returns only results markup
  // ---------------------------------------------------------------------------
  function buildQuery(form) {
    var DEFAULTS = {
      pg: "1",
      profession: "",
      fees_min: 0,
      fees_max: 500,
    };

    var params = new URLSearchParams();
    var fd = new FormData(form);

    fd.forEach(function (v, k) {
      if (v == null) return;
      var val = ("" + v).trim();

      // Skip empties
      if (val === "") return;

      // Skip common defaults to keep URL clean
      if (k === "profession" && (val === "" || val === "0")) return;
      if (k === "fees_min" && +val === DEFAULTS.fees_min) return;
      if (k === "fees_max" && +val === DEFAULTS.fees_max) return;
      if (k === "pg" && val === DEFAULTS.pg) return;

      // Normalize hero single names → arrays (safety net)
      if (k === "therapy") k = "therapy[]";
      if (k === "service_area") k = "service_area[]";

      params.append(k, val);
    });

    // Keep pg if explicitly set
    var pgEl = form.querySelector('input[name="pg"]');
    if (pgEl && pgEl.value && pgEl.value !== DEFAULTS.pg) {
      params.set("pg", pgEl.value);
    }

    // Keep the seed if present in the current URL
    try {
      var cur = new URL(window.location.href);
      var seed = cur.searchParams.get("seed");
      if (seed && !params.has("seed")) params.set("seed", seed);
    } catch (e) {}

    // Ask server for the results partial only
    params.set("partial", "1");

    return params;
  }

  // ---------------------------------------------------------------------------
  // Fetch partial HTML and swap into #ctdir-results (no full page)
  // Also update the URL in-place (without partial=1)
  // ---------------------------------------------------------------------------
  function submitNow(form, resetPage) {
    if (!form || _ctDir.ajax.inProgress) return;

    if (resetPage) {
      var pg = form.querySelector('input[name="pg"]');
      if (!pg) {
        pg = document.createElement("input");
        pg.type = "hidden";
        pg.name = "pg";
        form.appendChild(pg);
      }
      pg.value = "1";
    }

    var qs = buildQuery(form);
    var url = window.location.pathname + "?" + qs.toString();

    _ctDir.ajax.inProgress = true;
    markLoading(form);

    var doFetch = window.fetch
      ? fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } }).then(
          function (r) {
            return r.text();
          }
        )
      : new Promise(function (resolve, reject) {
          try {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", url, true);
            xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
            xhr.onreadystatechange = function () {
              if (xhr.readyState === 4) {
                if (xhr.status >= 200 && xhr.status < 300)
                  resolve(xhr.responseText);
                else reject(new Error("XHR " + xhr.status));
              }
            };
            xhr.send(null);
          } catch (e) {
            reject(e);
          }
        });

    doFetch
      .then(function (html) {
        var box = document.querySelector("#ctdir-results");
        if (box) box.innerHTML = html;

        // Update address bar w/o partial=1
        try {
          var clean = new URL(window.location.href);
          clean.search =
            "?" +
            (function () {
              var p = new URLSearchParams(qs.toString());
              p.delete("partial");
              return p.toString();
            })();
          history.replaceState({}, "", clean);
        } catch (e) {}

        bindPagination(); // rebind newly inserted links
      })
      .catch(function (err) {
        console.error("[ctdir] AJAX failed – fallback to full submit:", err);
        if (form.requestSubmit) form.requestSubmit();
        else form.submit();
      })
      .then(function () {
        // safe "finally"
        _ctDir.ajax.inProgress = false;
        unmarkLoading(form);
      });
  }

  // ---------------------------------------------------------------------------
  // Pagination (rebounds after each partial render)
  // ---------------------------------------------------------------------------
  function bindPagination() {
    toArray(
      document.querySelectorAll("#ctdir-results .ctdir-pagination a")
    ).forEach(function (a) {
      a.addEventListener("click", function (e) {
        e.preventDefault();
        var c = document.querySelector(".ctdir");
        var f = c ? c.querySelector(".ctdir-filter-form") : null;
        if (!f) return;

        var m = a.href.match(/(?:[?&])pg=(\d+)/);
        var pg = f.querySelector('input[name="pg"]');
        if (!pg) {
          pg = document.createElement("input");
          pg.type = "hidden";
          pg.name = "pg";
          f.appendChild(pg);
        }
        pg.value = m ? m[1] : "1";

        submitNow(f, false);
      });
    });
  }
  document.addEventListener("DOMContentLoaded", bindPagination);

  // ---------------------------------------------------------------------------
  // Event delegation: change + debounced text search
  // ---------------------------------------------------------------------------
  document.addEventListener("change", function (e) {
    var f = closest(e.target, ".ctdir-filter-form, .ctdir-hero-form");
    if (f) submitNow(f, true);
  });

  var keyTimer;
  document.addEventListener("keyup", function (e) {
    if (!matches(e.target, 'input[type="text"]')) return;
    var f = closest(e.target, ".ctdir-filter-form");
    if (!f) return;
    clearTimeout(keyTimer);
    keyTimer = setTimeout(function () {
      submitNow(f, true);
    }, 400);
  });

  // ---------------------------------------------------------------------------
  // One-open accordion + auto-open first active on load
  // ---------------------------------------------------------------------------
  document.addEventListener("click", function (e) {
    if (!matches(e.target, ".ctdir-filter .acc > button")) return;
    e.preventDefault();

    var clickedAcc = closest(e.target, ".acc");
    var wrap = closest(clickedAcc, ".ctdir-filter");
    if (!wrap) return;

    // close others
    toArray(wrap.querySelectorAll(".acc")).forEach(function (acc) {
      if (acc === clickedAcc) return;
      acc.classList.remove("open");
      var p = acc.querySelector(".panel");
      if (p) p.style.display = "none";
    });

    // toggle clicked
    var panel = clickedAcc.querySelector(".panel");
    var open = !clickedAcc.classList.contains("open");
    clickedAcc.classList.toggle("open", open);
    if (panel) panel.style.display = open ? "block" : "none";
  });

  document.addEventListener("DOMContentLoaded", function () {
    var wrap = document.querySelector(".ctdir-filter");
    if (!wrap) return;

    var accs = toArray(wrap.querySelectorAll(".acc"));
    var toOpen = null;

    for (var i = 0; i < accs.length; i++) {
      var acc = accs[i];
      var panel = acc.querySelector(".panel");
      if (!panel) continue;

      var hasChecked = panel.querySelector(
        'input[type="checkbox"]:checked, input[type="radio"]:checked'
      );

      var textish = toArray(
        panel.querySelectorAll(
          'input[type="text"], input[type="number"], select'
        )
      );
      var hasValue = false;
      for (var j = 0; j < textish.length; j++) {
        var el = textish[j];
        if (el.tagName === "SELECT") {
          if (el.value && el.value !== "") {
            hasValue = true;
            break;
          }
        } else {
          var v = (el.value || "").replace(/^\s+|\s+$/g, "");
          if (v !== "") {
            hasValue = true;
            break;
          }
        }
      }

      if (hasChecked || hasValue) {
        toOpen = acc;
        break;
      }
    }

    for (var k = 0; k < accs.length; k++) {
      var a = accs[k];
      var p = a.querySelector(".panel");
      var open = a === toOpen;
      if (open) a.classList.add("open");
      else a.classList.remove("open");
      if (p) p.style.display = open ? "block" : "none";
    }
  });

  // ---------------------------------------------------------------------------
  // Reset button (resets fields, refreshes fee slider if present, AJAX submit)
  // ---------------------------------------------------------------------------
  document.addEventListener("click", function (e) {
    var btn = closest(e.target, ".ctdir-reset");
    if (!btn) return;
    e.preventDefault();

    var f = closest(btn, "form");
    if (!f) return;

    if (f.reset) f.reset();
    toArray(f.querySelectorAll("select")).forEach(function (s) {
      s.value = "";
    });
    toArray(
      f.querySelectorAll('input[type="text"], input[type="number"]')
    ).forEach(function (i) {
      i.value = "";
    });
    toArray(
      f.querySelectorAll('input[type="radio"], input[type="checkbox"]')
    ).forEach(function (i) {
      i.checked = false;
    });

    // pg back to 1
    var pg = f.querySelector('input[name="pg"]');
    if (!pg) {
      pg = document.createElement("input");
      pg.type = "hidden";
      pg.name = "pg";
      f.appendChild(pg);
    }
    pg.value = "1";

    // profession cleared if present as hidden
    var prof = f.querySelector('input[name="profession"]');
    if (prof) prof.value = "";

    // fee slider visual refresh if the component exposes a hook
    toArray(f.querySelectorAll(".fee-dual")).forEach(function (el) {
      if (el.__ctdirRefresh) el.__ctdirRefresh();
    });

    submitNow(f, true);
  });

  // ---------------------------------------------------------------------------
  // Optional: If a fee slider initializer runs elsewhere, we leave it alone.
  // If it doesn't, you can attach your initializer here by exposing a hook.
  // ---------------------------------------------------------------------------
})();
