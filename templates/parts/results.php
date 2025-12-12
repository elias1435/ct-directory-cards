<?php
// Expects $q, $tax, $qry, $paged in scope

// TEMP DEBUG: view the actual query args when you add &debug=1 to the URL
if (current_user_can('manage_options') && isset($_GET['debug'])) {
    // rebuild exactly how the partial does
    $___tax   = isset($tax) ? $tax : ct_dir_taxonomies();
    $___q     = isset($q)   ? $q   : ct_dir_get_query();
    $___qry   = isset($qry) ? $qry : ct_dir_build_query($___q, $___tax);

    echo '<pre style="background:#111;color:#0f0;padding:10px;white-space:pre-wrap;"><strong>CTDIR DEBUG</strong>' . PHP_EOL;
    echo 'TAX MAP: ' . esc_html(print_r($___tax, true)) . PHP_EOL . PHP_EOL;
    echo 'Q: ' . esc_html(print_r($___q, true)) . PHP_EOL . PHP_EOL;
    echo 'FOUND_POSTS: ' . (int) $___qry->found_posts . PHP_EOL . PHP_EOL;

    // Reflect back the actual WP_Query args via filter
    add_filter('posts_request', function ($sql) {
        echo 'SQL: ' . esc_html($sql) . PHP_EOL . '</pre>';
        return $sql;
    });
}
?>


<!-- Row 2: Filters + Results -->
<div class="ctdir-wrap">
    <div class="ctdir-cols">
        <aside class="ctdir-filter">
            <form method="get" class="ctdir-filter-form">
                <input type="hidden" name="pg" value="<?php echo esc_attr($paged); ?>" />
                <input type="hidden" name="profession" value="<?php echo esc_attr($q['profession']); ?>" />

                <div class="acc search-fields">
                    <button type="button">Search</button>
                    <div class="panel">
                        <input type="text" name="q" value="<?php echo esc_attr($q['s']); ?>" placeholder="Search by keyword" />
                    </div>
                </div>

                <div class="acc">
                    <button type="button">Type of Primary Credential</button>
                    <div class="panel checklist">
                        <?php
                        $cred_terms = get_terms(array('taxonomy' => $tax['credential'], 'hide_empty' => false));
                        if (!is_wp_error($cred_terms)):
                            foreach ($cred_terms as $term): ?>
                                <label>
                                    <input type="checkbox" name="credential[]" value="<?php echo esc_attr($term->term_id); ?>" <?php checked(in_array($term->term_id, $q['credential'], true)); ?> />
                                    <?php echo esc_html($term->name); ?>
                                </label>
                        <?php endforeach;
                        endif; ?>
                    </div>
                </div>

                <div class="acc">
                    <button type="button">Specialties / Services</button>
                    <div class="panel">
                        <select name="specialties">
                            <option value="">Any</option>
                            <?php
                            $spec_terms = get_terms(array('taxonomy' => $tax['specialties'], 'hide_empty' => false));
                            if (!is_wp_error($spec_terms)):
                                foreach ($spec_terms as $term): ?>
                                    <option value="<?php echo esc_attr($term->term_id); ?>" <?php selected((int)$q['specialties'], (int)$term->term_id); ?>>
                                        <?php echo esc_html($term->name); ?>
                                    </option>
                            <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                </div>

                <div class="acc">
                    <button type="button">Modality</button>
                    <div class="panel">
                        <select name="modality">
                            <option value="">Any</option>
                            <?php
                            $mod_terms = get_terms(array('taxonomy' => $tax['modality'], 'hide_empty' => false));
                            if (!is_wp_error($mod_terms)):
                                foreach ($mod_terms as $term): ?>
                                    <option value="<?php echo esc_attr($term->term_id); ?>" <?php selected((int)$q['modality'], (int)$term->term_id); ?>>
                                        <?php echo esc_html($term->name); ?>
                                    </option>
                            <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                </div>

                <!-- Type of Therapy (checkbox list) -->
                <div class="acc hide-item">
                    <button type="button">Type of Therapy</button>
                    <div class="panel checklist">
                        <?php
                        $ther_terms = get_terms(array('taxonomy' => $tax['therapy'], 'hide_empty' => false));
                        if (!is_wp_error($ther_terms) && !empty($ther_terms)):
                            foreach ($ther_terms as $term): ?>
                                <label>
                                    <input type="checkbox" name="therapy[]" value="<?php echo esc_attr($term->term_id); ?>" <?php checked(in_array($term->term_id, (array)$q['therapy'], true)); ?> />
                                    <?php echo esc_html($term->name); ?>
                                </label>
                            <?php endforeach;
                        else: ?>
                            <small>No therapy terms found.</small>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="acc hide-item">
                    <button type="button">In Person / Virtual</button>
                    <div class="panel checklist">
                        <label><input type="checkbox" name="session[]" value="in_person" <?php checked(in_array('in_person', $q['session'], true)); ?> /> In person</label>
                        <label><input type="checkbox" name="session[]" value="virtual" <?php checked(in_array('virtual',   $q['session'], true)); ?> /> Virtual</label>
                    </div>
                </div>

                <div class="acc">
                    <button type="button">Distance / Zip Code / City</button>
                    <div class="panel">
                        <input type="text" name="zip" value="<?php echo esc_attr($q['zip']); ?>" placeholder="Zip code" />
                        <div class="chips">
                            <label><input type="radio" name="radius" value="5" <?php checked($q['radius'], 5);  ?> /> 5 miles</label>
                            <label><input type="radio" name="radius" value="15" <?php checked($q['radius'], 15); ?> /> 15 miles</label>
                            <label><input type="radio" name="radius" value="25" <?php checked($q['radius'], 25); ?> /> 25 miles</label>
                        </div>
                    </div>
                </div>

                <div class="acc hide-item">
                    <button type="button">State</button>
                    <div class="panel checklist">
                        <?php
                        $sa_terms = get_terms(array('taxonomy' => $tax['service_area'], 'hide_empty' => false));
                        if (!is_wp_error($sa_terms)):
                            foreach ($sa_terms as $term): ?>
                                <label>
                                    <input type="checkbox" name="service_area[]" value="<?php echo esc_attr($term->term_id); ?>" <?php checked(in_array($term->term_id, $q['service_area'], true)); ?> />
                                    <?php echo esc_html($term->name); ?>
                                </label>
                        <?php endforeach;
                        endif; ?>
                    </div>
                </div>

                <div class="acc">
                    <button type="button">Fees</button>
                    <div class="panel pt-big">
                        <?php
                        $min_default = 0;
                        $max_default = 500;
                        $step = 5;
                        $fees_min = isset($q['fees_min']) ? (float)$q['fees_min'] : $min_default;
                        $fees_max = isset($q['fees_max']) ? (float)$q['fees_max'] : $max_default;
                        $fees_min = max($min_default, min($fees_min, $max_default));
                        $fees_max = max($min_default, min($fees_max, $max_default));
                        if ($fees_min > $fees_max) $fees_min = max($min_default, $fees_max - $step);
                        ?>
                        <div class="fee-dual" data-min="<?php echo esc_attr($min_default); ?>" data-max="<?php echo esc_attr($max_default); ?>" data-step="<?php echo esc_attr($step); ?>">
                            <output class="fee-left">$<span class="fee-val"><?php echo esc_html($fees_min ?: 0); ?></span><span class="fee-label">Min</span></output>
                            <div class="fee-track">
                                <input class="fee-min" type="range" name="fees_min" min="0" max="500" step="5" value="<?php echo esc_attr($fees_min ?: 0); ?>">
                                <input class="fee-max" type="range" name="fees_max" min="0" max="500" step="5" value="<?php echo esc_attr($fees_max ?: 500); ?>">
                                <div class="fill" aria-hidden="true"></div>
                            </div>
                            <output class="fee-right">$<span class="fee-val"><?php echo esc_html($fees_max ?: 500); ?></span><span class="fee-label">Max</span></output>
                        </div>
                    </div>
                </div>

                <div class="acc">
                    <button type="button">Insurance</button>
                    <div class="panel checklist">
                        <?php
                        $ins_terms = get_terms(array('taxonomy' => $tax['insurance'], 'hide_empty' => false));
                        if (!is_wp_error($ins_terms)):
                            foreach ($ins_terms as $term): ?>
                                <label>
                                    <input type="checkbox" name="insurance[]" value="<?php echo esc_attr($term->term_id); ?>" <?php checked(in_array($term->term_id, $q['insurance'], true)); ?> />
                                    <?php echo esc_html($term->name); ?>
                                </label>
                        <?php endforeach;
                        endif; ?>
                    </div>
                </div>

                <div class="acc">
                    <button type="button">Language</button>
                    <div class="panel">
                        <select name="language">
                            <option value="">Any</option>
                            <?php
                            $lang_terms = get_terms(array('taxonomy' => $tax['language'], 'hide_empty' => false));
                            if (!is_wp_error($lang_terms)):
                                foreach ($lang_terms as $term): ?>
                                    <option value="<?php echo esc_attr($term->term_id); ?>" <?php selected((int)$q['language'], (int)$term->term_id); ?>>
                                        <?php echo esc_html($term->name); ?>
                                    </option>
                            <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                </div>

                <div class="acc">
                    <button type="button">Faith</button>
                    <div class="panel">
                        <select name="faith">
                            <option value="">Any</option>
                            <?php
                            $faith_terms = get_terms(array('taxonomy' => $tax['faith'], 'hide_empty' => false));
                            if (!is_wp_error($faith_terms)):
                                foreach ($faith_terms as $term): ?>
                                    <option value="<?php echo esc_attr($term->term_id); ?>" <?php selected((int)$q['faith'], (int)$term->term_id); ?>>
                                        <?php echo esc_html($term->name); ?>
                                    </option>
                            <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                </div>

                <p><button type="button" class="btn ctdir-reset et_pb_button">Reset Filters</button></p>
            </form>
        </aside>

        <section>
            <div class="ctdir-grid">
                <?php if ($qry->have_posts()) : while ($qry->have_posts()) : $qry->the_post(); ?>
                        <?php
                        if (!function_exists('ctdir_city_state')) {
                            function ctdir_city_state($post_id, $service_area_tax)
                            {
                                $city  = get_post_meta($post_id, 'rcp_city', true);
                                $state = get_post_meta($post_id, 'rcp_state', true);
                                if ($state === '' && taxonomy_exists($service_area_tax)) {
                                    $terms = wp_get_post_terms($post_id, $service_area_tax);
                                    if (!is_wp_error($terms) && !empty($terms)) $state = (string)$terms[0]->name;
                                }
                                $parts = array();
                                if ($city)  $parts[] = trim((string)$city);
                                if ($state) $parts[] = trim((string)$state);
                                return implode(', ', $parts);
                            }
                        }
                        $city_state = ctdir_city_state(get_the_ID(), $tax['service_area']);
                        $role_terms = wp_get_post_terms(get_the_ID(), $tax['profession']);
                        $role_name  = (!is_wp_error($role_terms) && !empty($role_terms)) ? $role_terms[0]->name : '';
                        $img = function_exists('ct_dir_profile_image_url') ? ct_dir_profile_image_url(get_the_ID()) : '';
                        ?>
                        <div class="ctdir-card">
                            <div class="hdr"><?php echo esc_html($city_state); ?></div>
                            <div class="inner">
                                <div class="photo"><?php if ($img) : ?><img src="<?php echo esc_url($img); ?>" alt="<?php the_title_attribute(); ?>"><?php endif; ?></div>
                                <h4 class="name"><?php the_title(); ?></h4>
                                <div class="role"><?php echo esc_html($role_name); ?></div>
                                <a class="btn" href="<?php the_permalink(); ?>">View Details</a>
                            </div>
                        </div>
                    <?php endwhile;
                else: ?>
                    <p>No matches found.</p>
                <?php endif;
                wp_reset_postdata(); ?>
            </div>

            <?php
            // Pagination, preserve current args (+ partial when we are in an AJAX request)
            $current_args = $_GET;
            unset($current_args['pg']);
            $base = esc_url(add_query_arg(array_merge($current_args, array('pg' => '%#%'))));
            $links = paginate_links(array(
                'base'      => $base,
                'format'    => '',
                'current'   => $paged,
                'total'     => max(1, (int) $qry->max_num_pages),
                'type'      => 'array',
                'prev_text' => '&laquo;',
                'next_text' => '&raquo;',
            ));
            if ($links) {
                echo '<nav class="ctdir-pagination">';
                foreach ($links as $l) echo '<span>' . $l . '</span>';
                echo '</nav>';
            }
            ?>
        </section>
    </div>
</div>
