<?php
/**
 * Twenty Twenty-Four Child Theme functions and definitions
 */

// Register Custom Post Type
function register_wp_projects_post_type() {
    $labels = array(
        'name'                  => _x( 'WordPress Projects', 'Post Type General Name', 'twentytwentyfour-child' ),
        'singular_name'         => _x( 'WordPress Project', 'Post Type Singular Name', 'twentytwentyfour-child' ),
        'menu_name'            => __( 'WordPress Projects', 'twentytwentyfour-child' ),
        'name_admin_bar'       => __( 'WordPress Project', 'twentytwentyfour-child' ),
        'archives'             => __( 'Project Archives', 'twentytwentyfour-child' ),
        'attributes'           => __( 'Project Attributes', 'twentytwentyfour-child' ),
        'parent_item_colon'    => __( 'Parent Project:', 'twentytwentyfour-child' ),
        'all_items'            => __( 'All Projects', 'twentytwentyfour-child' ),
        'add_new_item'         => __( 'Add New Project', 'twentytwentyfour-child' ),
        'add_new'              => __( 'Add New', 'twentytwentyfour-child' ),
        'new_item'             => __( 'New Project', 'twentytwentyfour-child' ),
        'edit_item'            => __( 'Edit Project', 'twentytwentyfour-child' ),
        'update_item'          => __( 'Update Project', 'twentytwentyfour-child' ),
        'view_item'            => __( 'View Project', 'twentytwentyfour-child' ),
        'view_items'           => __( 'View Projects', 'twentytwentyfour-child' ),
        'search_items'         => __( 'Search Project', 'twentytwentyfour-child' ),
    );
    $args = array(
        'label'                 => __( 'WordPress Project', 'twentytwentyfour-child' ),
        'description'           => __( 'WordPress Projects', 'twentytwentyfour-child' ),
        'labels'                => $labels,
        'supports'              => array( 
            'title',           // Enables the title field
            'editor',          // Enables the content editor
            'thumbnail',       // Enables featured image
            'custom-fields',   // Enables custom fields
            'excerpt',         // Enables excerpt
            'revisions'        // Enables revisions
        ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-portfolio',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
        'register_meta_box_cb'  => 'add_project_metaboxes'
    );
    register_post_type( 'wp_project', $args );
}
add_action( 'init', 'register_wp_projects_post_type', 0 );

// Add theme support for featured images if not already added
function twentytwentyfour_child_setup() {
    add_theme_support( 'post-thumbnails' );
}
add_action( 'after_setup_theme', 'twentytwentyfour_child_setup' );

// Add custom meta boxes for project fields
function add_project_metaboxes() {
    add_meta_box(
        'project_details',
        __('Project Details', 'twentytwentyfour-child'),
        'project_details_callback',
        'wp_project',
        'normal',
        'high'
    );
}

// Callback function to display the meta box content
function project_details_callback($post) {
    // Add nonce for security
    wp_nonce_field('project_details_nonce', 'project_details_nonce');
    
    // Get existing values
    $deadline = get_post_meta($post->ID, '_project_deadline', true);
    $client_name = get_post_meta($post->ID, '_client_name', true);
    $project_cost = get_post_meta($post->ID, '_project_cost', true);
    
    ?>
    <div class="project-meta-box">
        <p>
            <label for="project_deadline"><?php _e('Project Deadline:', 'twentytwentyfour-child'); ?></label><br>
            <input type="date" id="project_deadline" name="project_deadline" value="<?php echo esc_attr($deadline); ?>">
        </p>
        <p>
            <label for="client_name"><?php _e('Client Name:', 'twentytwentyfour-child'); ?></label><br>
            <input type="text" id="client_name" name="client_name" value="<?php echo esc_attr($client_name); ?>" size="25">
        </p>
        <p>
            <label for="project_cost"><?php _e('Project Cost:', 'twentytwentyfour-child'); ?></label><br>
            <input type="number" id="project_cost" name="project_cost" value="<?php echo esc_attr($project_cost); ?>" step="0.01">
        </p>
    </div>
    <?php
}

// Save the meta box data
function save_project_details($post_id) {
    // Check if nonce is set
    if (!isset($_POST['project_details_nonce'])) {
        return;
    }
    
    // Verify nonce
    if (!wp_verify_nonce($_POST['project_details_nonce'], 'project_details_nonce')) {
        return;
    }
    
    // Save the fields
    if (isset($_POST['project_deadline'])) {
        update_post_meta($post_id, '_project_deadline', sanitize_text_field($_POST['project_deadline']));
    }
    if (isset($_POST['client_name'])) {
        update_post_meta($post_id, '_client_name', sanitize_text_field($_POST['client_name']));
    }
    if (isset($_POST['project_cost'])) {
        update_post_meta($post_id, '_project_cost', sanitize_text_field($_POST['project_cost']));
    }
}
add_action('save_post_wp_project', 'save_project_details');

// Shortcode function
function wp_projects_shortcode() {
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    
    $args = array(
        'post_type' => 'wp_project',
        'posts_per_page' => 6,
        'paged' => $paged,
        'orderby' => 'date',
        'order' => 'DESC'
    );

    $query = new WP_Query($args);
    
    ob_start();
    ?>
    <!-- Test message to verify shortcode is working -->
    <!-- <div style="background: #f0f0f0; padding: 10px; margin-bottom: 20px;">
        Shortcode is working! <?php echo $query->found_posts; ?> projects found.
    </div> -->
    
    <div class="wp-projects-container">
        <!-- Filters Section -->
        <div class="wp-projects-filters">
            <div class="filter-group">
                <label for="client-filter">Search by client name:</label>
                <input type="text" id="client-filter" placeholder="Enter client name">
            </div>
            <div class="filter-group">
                <label for="deadline-filter">Deadline:</label>
                <input type="date" id="deadline-filter">
            </div>
            <div class="filter-group">
                <label for="cost-filter">Project Cost:</label>
                <select id="cost-filter">
                    <option value="">All</option>
                    <option value="0-1000">$0 - $1,000</option>
                    <option value="1001-5000">$1,001 - $5,000</option>
                    <option value="5001-10000">$5,001 - $10,000</option>
                    <option value="10001+">$10,001+</option>
                </select>
            </div>
            <button class="filter-button" id="apply-filters">Apply Filters</button>
        </div>

        <!-- Projects List -->
        <div class="wp-projects-list" id="projects-grid">
            <?php
            if ($query->have_posts()) :
                while ($query->have_posts()) : $query->the_post();
                    $client_name = get_post_meta(get_the_ID(), '_client_name', true);
                    $project_deadline = get_post_meta(get_the_ID(), '_project_deadline', true);
                    $project_cost = get_post_meta(get_the_ID(), '_project_cost', true);
                    ?>
                    <div class="project-card">
                        <div class="project-image">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('large'); ?>
                            <?php else : ?>
                                <img src="<?php echo get_template_directory_uri(); ?>/images/placeholder.jpg" alt="Placeholder">
                            <?php endif; ?>
                        </div>
                        <div class="project-details">
                            <h3><?php the_title(); ?></h3>
                            <div class="project-meta">
                                <p><strong>Client:</strong> <?php echo esc_html($client_name); ?></p>
                                <p><strong>Deadline:</strong> <?php echo esc_html($project_deadline); ?></p>
                                <p><strong>Cost:</strong> $<?php echo number_format((float)$project_cost, 2); ?></p>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="read-more">View Project</a>
                        </div>
                    </div>
                <?php
                endwhile;
            endif;
            wp_reset_postdata();
            ?>
        </div>

        <!-- Pagination -->
        <div class="wp-projects-pagination">
            <?php
            echo paginate_links(array(
                'total' => $query->max_num_pages,
                'current' => $paged,
                'prev_text' => '&laquo; Previous',
                'next_text' => 'Next &raquo;',
                'type' => 'list'
            ));
            ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// Register shortcode
add_shortcode('wp_projects', 'wp_projects_shortcode');

// Enqueue scripts and styles
function wp_projects_enqueue_scripts() {
    wp_enqueue_style('wp-projects-style', get_stylesheet_directory_uri() . '/css/projects.css');
    wp_enqueue_script('jquery');
    wp_enqueue_script('wp-projects-script', get_stylesheet_directory_uri() . '/js/projects.js', array('jquery'), '1.0', true);
    wp_localize_script('wp-projects-script', 'wpProjects', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'wp_projects_enqueue_scripts');

// Register AJAX handlers
add_action('wp_ajax_filter_projects', 'filter_projects');
add_action('wp_ajax_nopriv_filter_projects', 'filter_projects');

// AJAX filter function
function filter_projects() {
    $paged = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $client = isset($_POST['client']) ? sanitize_text_field($_POST['client']) : '';
    $deadline = isset($_POST['deadline']) ? sanitize_text_field($_POST['deadline']) : '';
    $cost_range = isset($_POST['cost']) ? sanitize_text_field($_POST['cost']) : '';

    $args = array(
        'post_type' => 'wp_project',
        'posts_per_page' => 6, // Changed from 10 to 6 posts per page
        'paged' => $paged,
        'meta_query' => array('relation' => 'AND')
    );

    // Add filter conditions
    if (!empty($client)) {
        $args['meta_query'][] = array(
            'key' => '_client_name',
            'value' => $client,
            'compare' => 'LIKE'
        );
    }

    if (!empty($deadline)) {
        $args['meta_query'][] = array(
            'key' => '_project_deadline',
            'value' => $deadline,
            'compare' => '='
        );
    }

    if (!empty($cost_range)) {
        $range = explode('-', $cost_range);
        if (count($range) == 2) {
            $args['meta_query'][] = array(
                'key' => '_project_cost',
                'value' => array($range[0], $range[1]),
                'type' => 'NUMERIC',
                'compare' => 'BETWEEN'
            );
        } elseif (strpos($cost_range, '+') !== false) {
            $min_cost = intval($cost_range);
            $args['meta_query'][] = array(
                'key' => '_project_cost',
                'value' => $min_cost,
                'type' => 'NUMERIC',
                'compare' => '>='
            );
        }
    }

    $query = new WP_Query($args);
    $response = array();
    $response['html'] = '';

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $client_name = get_post_meta(get_the_ID(), '_client_name', true);
            $project_deadline = get_post_meta(get_the_ID(), '_project_deadline', true);
            $project_cost = get_post_meta(get_the_ID(), '_project_cost', true);

            ob_start();
            ?>
            <div class="project-card">
                <div class="project-image">
                    <?php if (has_post_thumbnail()) : ?>
                        <?php the_post_thumbnail('large'); ?>
                    <?php else : ?>
                        <img src="<?php echo get_template_directory_uri(); ?>/images/placeholder.jpg" alt="Placeholder">
                    <?php endif; ?>
                </div>
                <div class="project-details">
                    <h3><?php the_title(); ?></h3>
                    <div class="project-meta">
                        <p><strong>Client:</strong> <?php echo esc_html($client_name); ?></p>
                        <p><strong>Deadline:</strong> <?php echo esc_html($project_deadline); ?></p>
                        <p><strong>Cost:</strong> $<?php echo number_format((float)$project_cost, 2); ?></p>
                    </div>
                    <a href="<?php the_permalink(); ?>" class="read-more">View Project</a>
                </div>
            </div>
            <?php
            $response['html'] .= ob_get_clean();
        }

        // Add pagination to response
        $response['pagination'] = paginate_links(array(
            'total' => $query->max_num_pages,
            'current' => $paged,
            'prev_text' => '&laquo; Previous',
            'next_text' => 'Next &raquo;',
            'type' => 'list'
        ));

        wp_reset_postdata();
    } else {
        $response['html'] = '<p>No projects found matching your criteria.</p>';
        $response['pagination'] = '';
    }

    wp_send_json($response);
    wp_die();
}

// Register REST API endpoint for top 3 projects
add_action('rest_api_init', function () {
    register_rest_route('wp-projects/v1', '/top-projects', array(
        'methods' => 'GET',
        'callback' => 'get_top_three_projects',
        'permission_callback' => '__return_true'
    ));
});

// Callback function to get top 3 projects by cost
function get_top_three_projects() {
    $args = array(
        'post_type' => 'wp_project',
        'posts_per_page' => 3,
        'meta_key' => '_project_cost',
        'orderby' => 'meta_value_num',
        'order' => 'DESC'
    );

    $query = new WP_Query($args);
    $projects = array();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            
            // Get project meta data with correct meta keys (added underscore prefix)
            $project_cost = get_post_meta(get_the_ID(), '_project_cost', true);
            $client_name = get_post_meta(get_the_ID(), '_client_name', true);
            $project_deadline = get_post_meta(get_the_ID(), '_project_deadline', true);
            
            // Get featured image
            $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
            
            // Format the project data
            $projects[] = array(
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'client_name' => $client_name,
                'project_cost' => floatval($project_cost),
                'project_deadline' => $project_deadline,
                'thumbnail_url' => $thumbnail_url ? $thumbnail_url : '',
                'permalink' => get_permalink()
            );
        }
        wp_reset_postdata();
    }

    if (empty($projects)) {
        return new WP_REST_Response(array(
            'status' => 'error',
            'message' => 'No projects found',
            'data' => array()
        ), 404);
    }

    return new WP_REST_Response(array(
        'status' => 'success',
        'message' => 'Top 3 projects retrieved successfully',
        'data' => $projects
    ), 200);
} 