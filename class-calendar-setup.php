<?php
function get_current_year_events() {
    global $wpdb;
    $fall_from = get_option('fall_from');
    $spring_to = date('Y-m-d', strtotime(get_option('spring_to') . '10 days'));
    // error_log("=================");
    // error_log($spring_to);
    
    $event_query = 'select * from ';
    $event_query .= $wpdb->get_blog_prefix() . 'cls_event_data ';
    $event_query .= "WHERE event_date BETWEEN CAST('$fall_from' AS DATE) AND CAST('$spring_to' AS DATE) ";
    $event_query .= 'ORDER by event_date ASC';

    $evt_items = $wpdb->get_results( $event_query, ARRAY_A );


    return $evt_items;
}
function add_normal_classes() {
    global $wpdb;
    $day0 = get_option('fall_from');
    $day1 = get_option('spring_to');
    if(!$day0 || !$day1) return;
    
    $t0 = strtotime($day0);
    $t1 = strtotime($day1);

    $event_data = array();
    for($t=$t0; $t<=$t1; ) {
        $day = date('Y-m-d', $t);

        // error_log("=== $day ===");
        $event_data['event_date'] = $day;
        $event_data['event_type'] = 'Class';
        $event_data['event_start_time'] = get_option('class_start_time');
        $event_data['event_end_time'] = get_option('class_end_time');
        $event_data['event_additionals'] = '';

        $wpdb->insert( $wpdb->get_blog_prefix() . 'cls_event_data', $event_data );
        $t = strtotime($day . ' 7 days');
    }
}

function clsreg_calendar_setup_page() {
    global $wpdb;
?>

<!-- Top-level menu -->
<div id="calendar-general" class="wrap">
    <h2>Event Calendar Setup <a class="add-new-h2" href="<?php echo
            add_query_arg( array( 'page' => 'calendar-setup', 'id' => 'new' ),
              admin_url('admin.php') ); ?>">Add New Event</a></h2>

    <!-- Display course list if no parameter sent in URL -->
    <?php if ( empty( $_GET['id'] ) ) {
        $evt_items = get_current_year_events();
        if(!$evt_items) {
            add_normal_classes();
            $evt_items = get_current_year_events();
        }
    ?>
    
    <h3>Manage Calendar Event Entries</h3>
    <form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
      <input type="hidden" name="action" value="delete_event_entry" />
        
      <!-- Adding security through hidden referrer field -->
      <?php wp_nonce_field( 'calendar_event_deletion' ); ?>
        
      <table class="wp-list-table widefat fixed" >
        <thead><tr><th style="width: 50px"></th>
            <th style="width: 200px">Date</th>
            <th>Event</th>
            <th>Begin At</th>
            <th>End At</th></tr></thead>
        <?php
            // Display courses if query returned results
            if ( $evt_items ) {
                foreach ( $evt_items as $evt_item ) {
                    $time = strtotime($evt_item['event_date']);
                    echo '<tr style="background: #FFF">';
                    echo '<td><input type="checkbox" name="evts[]" value="';
                    echo esc_attr( $evt_item['event_id'] ) . '" /></td>';
                    echo '<td><a href="';
                    echo add_query_arg( array(
                            'page' => 'calendar-setup',
                            'id' => $evt_item['event_id'] ),
                        admin_url( 'admin.php' ) );
                    echo '">' .  date('m/d/Y', $time) . '(' . date('D', $time) . ')' . '</a></td>';
                    echo '<td>' . $evt_item['event_type'] . ($evt_item['event_additionals']? '('. $evt_item['event_additionals']. ')':'') .'</td>';
                    echo '<td>' . $evt_item['event_start_time'] . '</td>';
                    echo '<td>' . $evt_item['event_end_time'] . '</td></tr>' . "\n";
                }
            } else {
                echo '<tr style="background: #FFF">';
                echo '<td colspan=4>No Event Added Yet</td></tr>';
            }
        ?>
      </table><br />
    
      <input type="submit" value="Delete Selected" class="button-primary"/>
    </form>
    
    <?php } elseif ( isset( $_GET['id'] ) &&
            ( $_GET['id'] == 'new' ||
                is_numeric( $_GET['id'] ) ) ) {
        $event_id = $_GET['id'];
        $event_data = array();
        $mode = 'new';
        error_log('==111==');
        // Query database if numeric id is present
        if ( is_numeric( $event_id ) ) {
            $event_query = 'select * from ' . $wpdb->get_blog_prefix();
            $event_query .= 'cls_event_data where event_id = %d';
            $event_data =
                $wpdb->get_row( $wpdb->prepare( $event_query, $event_id ), ARRAY_A );

            // Set variable to indicate page mode
            if ( $event_data ) $mode = 'edit';
        } else {
            $event_data['event_date'] = '';
            $event_data['event_type'] = 'No Class';
            $event_data['event_start_time'] = get_option('class_start_time');
            $event_data['event_end_time'] = get_option('class_end_time');
            $event_data['event_additionals'] = '';
        }

        // Display title based on current mode
        if ( $mode == 'new' ) {
            echo '<h3>Add New Calendar Event</h3>';
        } elseif ( $mode == 'edit' ) {
            echo '<h3>Edit Calendar Event #' . $event_data['event_id'] . ' - ';
            echo $event_data['event_date'] . '</h3>';
        }
        error_log('==222==');
    ?>
    <form method="post"
        action="<?php echo admin_url( 'admin-post.php' ); ?>">
        <input type="hidden" name="action" value="save_event_entry" />
        <input type="hidden" name="event_id"
               value="<?php echo esc_attr( $event_id ); ?>" />

        <!-- Adding security through hidden referrer field -->
        <?php wp_nonce_field( 'event_add_edit' ); ?>
        <!-- Display class editing form -->
        <table>
            <tr>
                <td style="width: 150px">Date</td>
                <td><input type="date" name="event_date" size="60" 
                           value="<?php echo esc_attr(
                            $event_data['event_date'] ); ?>"/></td>
            </tr>
            <tr>
                <td style="width: 150px">Type</td>
                <td><input type="text" name="event_type"  
                           value="<?php echo esc_attr(
                            $event_data['event_type'] ); ?>"/></td>
            </tr>
            <tr>
                <td style="width: 150px">Start Time</td>
                <td><input type="time" name="event_start_time" 
                           value="<?php echo esc_attr(
                            $event_data['event_start_time'] ); ?>"/></td>
            </tr>
           <tr>
                <td style="width: 150px">End Time</td>
                <td>
                  <input type="time" name="event_end_time" 
                           value="<?php echo esc_attr(
                            $event_data['event_end_time'] ); ?>"/>
                </td>
            </tr>
            <tr>
                <td>Additionals</td>
                 <td><textarea name="event_additionals" cols="60"><?php echo esc_textarea(
                    $event_data['event_additionals'] ); ?></textarea></td>
            </tr>
        </table>
        <input type="submit" value="Submit" class="button-primary"/>
        </form>
<?php } ?>
</div>
<?php }


add_action( 'admin_init', 'event_admin_init' );

function event_admin_init() {
    add_action( 'admin_post_save_event_entry',
        'process_event_entry' );
    add_action( 'admin_post_delete_event_entry',
        'delete_event_entry' );
}

function process_event_entry() {
    // Check if user has proper security level
    if ( !current_user_can( 'manage_options' ) )
        wp_die( 'Not allowed' );

    // Check if nonce field is present for security
    check_admin_referer( 'event_add_edit' );

    global $wpdb;

    // Place all user submitted values in an array (or empty
    // strings if no value was sent)
    $event_data = array();
    $event_data['event_date'] =
        ( isset( $_POST['event_date'] ) ? $_POST['event_date'] : '' );

    $event_data['event_type'] =
        ( isset( $_POST['event_type'] ) ? $_POST['event_type'] : '' );

    $event_data['event_start_time'] =
        ( isset( $_POST['event_start_time'] ) ? $_POST['event_start_time'] : '' );
        
    $event_data['event_end_time'] =
        ( isset( $_POST['event_end_time'] ) ? $_POST['event_end_time'] : '' );

    $event_data['event_additionals'] =
        ( isset( $_POST['event_additionals'] ) ? $_POST['event_additionals'] : '' );


    // Call the wpdb insert or update method based on value
    // of hidden event_id field
    if ( isset( $_POST['event_id'] ) && $_POST['event_id'] == 'new') {
        $wpdb->insert( $wpdb->get_blog_prefix() . 'cls_event_data', $event_data );
    } elseif ( isset( $_POST['event_id']) &&
        is_numeric( $_POST['event_id'] ) ) {
        $wpdb->update( $wpdb->get_blog_prefix() . 'cls_event_data', $event_data,
            array( 'event_id' => $_POST['event_id'] ) );
    }

    // Redirect the page to the user submission form
    wp_redirect( add_query_arg( 'page', 'calendar-setup', admin_url( 'admin.php' ) ) );
    exit;
}

function delete_event_entry() {
    // Check that user has proper security level
    if ( !current_user_can( 'manage_options' ) )
        wp_die( 'Not allowed' );

    // Check if nonce field is present
    check_admin_referer( 'calendar_event_deletion' );

    // If classes are present, cycle through array and call SQL
    // command to delete entries one by one
    if ( !empty( $_POST['evts'] ) ) {
        // Retrieve array of events IDs to be deleted
        $evts_to_delete = $_POST['evts'];
        
        global $wpdb;
        foreach ( $evts_to_delete as $evt_to_delete ) {
            $query = 'DELETE from ' . $wpdb->get_blog_prefix();
            $query .= 'cls_event_data ';
            $query .= 'WHERE event_id = %d';
            $wpdb->query( $wpdb->prepare( $query, $evt_to_delete) );
        }
    }
    // Redirect the page to the user submission form
    wp_redirect( add_query_arg( 'page', 'calendar-setup',
                admin_url( 'admin.php' ) ) );
    exit;
}
