<?php

/**
 * Template: Directory grid + filters
 * Vars from shortcode: $q, $tax, $qry, $paged
 */

// Ensure we have $paged
if (! isset($paged)) {
  $paged = max(1, intval(get_query_var('paged') ? get_query_var('paged') : 1));
}

// Helpers (same as before)
if (! function_exists('ctdir_as_string')) {
  function ctdir_as_string($v)
  {
    $v = maybe_unserialize($v);
    if (is_array($v)) {
      foreach ($v as $vv) {
        $vv = maybe_unserialize($vv);
        if (is_array($vv)) $vv = reset($vv);
        $vv = trim((string) $vv);
        if ($vv !== '') return $vv;
      }
      return '';
    }
    return trim((string) $v);
  }
}

if (! function_exists('ctdir_meta_single')) {
  function ctdir_meta_single($post_id, $key)
  {
    $v = get_post_meta($post_id, $key, true);
    if ($v === '' || $v === null) {
      $multi = get_post_meta($post_id, $key, false);
      if (! empty($multi)) $v = maybe_unserialize(reset($multi));
    } else {
      $v = maybe_unserialize($v);
    }
    if (is_array($v)) $v = reset($v);
    return trim((string) $v);
  }
}

if (! function_exists('ctdir_city_state')) {
  function ctdir_city_state($post_id, $service_area_tax)
  {
    $city  = ctdir_meta_single($post_id, 'rcp_city');
    $state = ctdir_meta_single($post_id, 'rcp_state');

    if ($state === '' && taxonomy_exists($service_area_tax)) {
      $terms = wp_get_post_terms($post_id, $service_area_tax);
      if (! is_wp_error($terms) && ! empty($terms)) {
        $state = (string) $terms[0]->name;
      }
    }

    $parts = array();
    if ($city !== '')  $parts[] = $city;
    if ($state !== '') $parts[] = $state;
    return implode(', ', $parts);
  }
}
?>
<div class="ctdir">
  <!-- Row 1: Hero -->
  <div class="ctdir-row-hero">
    <form method="get" class="ctdir-hero-form hero-grid">
      <input type="hidden" name="pg" value="1" />
      <!-- Profession (hidden for now) -->
      <label class="hero-field" style="display:none;">
        <span class="hero-label">Profession</span>
        <select name="profession" aria-label="Select a Profession">
          <option value="">Select a Profession</option>
          <?php
          $prof_terms = get_terms(['taxonomy' => $tax['profession'], 'hide_empty' => false]);
          if (!is_wp_error($prof_terms)):
            foreach ($prof_terms as $term): ?>
              <option value="<?php echo esc_attr($term->term_id); ?>"
                <?php selected((int)$q['profession'], (int)$term->term_id); ?>>
                <?php echo esc_html($term->name); ?>
              </option>
          <?php endforeach;
          endif; ?>
        </select>
      </label>

      <!-- Type of Therapy (dropdown) -->
      <label class="hero-field">
        <span class="hero-label">Type of Therapy</span>
        <select name="therapy[]" aria-label="Type of Therapy">
          <option value="">Any</option>
          <?php
          $ther_terms = get_terms(['taxonomy' => $tax['therapy'], 'hide_empty' => false]);
          if (!is_wp_error($ther_terms)):
            foreach ($ther_terms as $term): ?>
              <option value="<?php echo esc_attr($term->term_id); ?>"
                <?php selected((int)($q['therapy'][0] ?? 0), (int)$term->term_id); ?>>
                <?php echo esc_html($term->name); ?>
              </option>
          <?php endforeach;
          endif; ?>
        </select>
      </label>

      <!-- City/State (service_area dropdown) -->
      <label class="hero-field">
        <span class="hero-label">City/State</span>
        <select name="service_area[]" aria-label="City/State">
          <option value="">Any</option>
          <?php
          $sa_terms = get_terms(['taxonomy' => $tax['service_area'], 'hide_empty' => false]);
          if (!is_wp_error($sa_terms)):
            foreach ($sa_terms as $term): ?>
              <option value="<?php echo esc_attr($term->term_id); ?>"
                <?php selected((int)($q['service_area'][0] ?? 0), (int)$term->term_id); ?>>
                <?php echo esc_html($term->name); ?>
              </option>
          <?php endforeach;
          endif; ?>
        </select>
      </label>

      <!-- In Person / Virtual -->
      <fieldset class="hero-field hero-checks" aria-label="In Person / Virtual">
        <span class="hero-label">In Person / Virtual</span>
        <label><input type="checkbox" name="session[]" value="in_person" <?php checked(in_array('in_person', (array)$q['session'], true)); ?>> In person</label>
        <label><input type="checkbox" name="session[]" value="virtual" <?php checked(in_array('virtual',   (array)$q['session'], true)); ?>> Virtual</label>
      </fieldset>
    </form>

    <a style="display:none;" href="#">Suggest a Profession</a>
  </div>

  <!-- Row 2: Filters + Results -->
  <section>
    <div id="ctdir-results">
      <?php include CTDIR_PLUGIN_DIR . 'templates/parts/results.php'; ?>
    </div>
  </section>
</div>
