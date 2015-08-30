<?php

/*
add_action( 'admin_menu', 'clsreg_settings_menu' );

function clsreg_settings_menu() {
    add_options_page( 'Class Registration Data Management',
        'Class Registration', 'manage_options',
        'class-registration',
        'clsreg_class_setup_page' );
}*/

function clsreg_class_setup_page() {
    global $wpdb;
    $cls_levels = array( 0 => 'Pre', 1 => 'Children', 2 => 'Youth', 3 => 'Adult',  4 => 'Culture');
?>

<!-- Top-level menu -->
<div id="clsreg-general" class="wrap">
    <h2>Class Registration <a class="add-new-h2" href="<?php echo
            add_query_arg( array( 'page' => 'class-registration', 'id' => 'new' ),
              admin_url('admin.php') ); ?>">Add New Class</a></h2>

    <!-- Display course list if no parameter sent in URL -->
    <?php if ( empty( $_GET['id'] ) ) {
        $cls_query = 'select * from ';
        $cls_query .= $wpdb->get_blog_prefix() . 'cls_course_data ';
        $cls_query .= 'ORDER by cls_level, cls_create_date DESC';

        $cls_items = $wpdb->get_results( $cls_query, ARRAY_A );
    ?>
    <h3>Manage Class Entries</h3>
    <form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
      <input type="hidden" name="action" value="delete_clsreg_data" />
        
      <!-- Adding security through hidden referrer field -->
      <?php wp_nonce_field( 'clsreg_deletion' ); ?>
        
      <table class="wp-list-table widefat fixed" >
        <thead><tr><th style="width: 50px"></th>
            <!-- <th style="width: 50px">ID</th> -->
            <th style="width: 200px">Title</th>
            <th>Teacher</th></tr></thead>
        <?php
            // Display courses if query returned results
            if ( $cls_items ) {
                foreach ( $cls_items as $cls_item ) {
                    echo '<tr style="background: #FFF">';
                    echo '<td><input type="checkbox" name="clses[]" value="';
                    echo esc_attr( $cls_item['cls_id'] ) . '" /></td>';
                    // echo '<td>' . $cls_item['cls_id'] . '</td>';
                    echo '<td><a href="';
                    echo add_query_arg( array(
                            'page' => 'class-registration',
                            'id' => $cls_item['cls_id'] ),
                        admin_url( 'admin.php' ) );
                    echo '">' . $cls_levels[$cls_item['cls_level']] . ' - ' . $cls_item['cls_title'] . '</a></td>';
                    echo '<td>' . $cls_item['cls_teacher'] . '</td></tr>' . "\n";
                }
            } else {
                echo '<tr style="background: #FFF">';
                echo '<td colspan=4>No Class Added Yet</td></tr>';
            }
        ?>
      </table><br />
    
      <input type="submit" value="Delete Selected" class="button-primary"/>
    </form>
    
    
    <?php } elseif ( isset( $_GET['id'] ) &&
            ( $_GET['id'] == 'new' ||
                is_numeric( $_GET['id'] ) ) ) {
        $cls_id = $_GET['id'];
        $cls_data = array();
        $mode = 'new';
        // Query database if numeric id is present
        if ( is_numeric( $cls_id ) ) {
            $cls_query = 'select * from ' . $wpdb->get_blog_prefix();
            $cls_query .= 'cls_course_data where cls_id = %d';
            $cls_data =
                $wpdb->get_row( $wpdb->prepare( $cls_query, $cls_id ), ARRAY_A );

            // Set variable to indicate page mode
            if ( $cls_data ) $mode = 'edit';
        } else {
            $cls_data['cls_title'] = '';
            $cls_data['cls_teacher'] = '';
            $cls_data['cls_room'] = '';
            $cls_data['cls_level'] = '';
            $cls_data['cls_language'] = '';
            $cls_data['cls_active'] = '';
            $cls_data['cls_description'] = '';
            $cls_data['cls_additionals'] = '';
        }

        // Display title based on current mode
        if ( $mode == 'new' ) {
            echo '<h3>Add New Class</h3>';
        } elseif ( $mode == 'edit' ) {
            echo '<h3>Edit Class #' . $cls_data['cls_id'] . ' - ';
            echo $cls_data['cls_title'] . '</h3>';
        }
    ?>
    <form method="post"
        action="<?php echo admin_url( 'admin-post.php' ); ?>">
        <input type="hidden" name="action" value="save_clsreg_data" />
        <input type="hidden" name="cls_id"
               value="<?php echo esc_attr( $cls_id ); ?>" />

        <!-- Adding security through hidden referrer field -->
        <?php wp_nonce_field( 'clsreg_add_edit' ); ?>
        <!-- Display class editing form -->
        <table>
            <tr>
                <td style="width: 150px">Title</td>
                <td><input type="text" name="cls_title" size="60" 
                           value="<?php echo esc_attr(
                            $cls_data['cls_title'] ); ?>"/></td>
            </tr>
            <tr>
                <td style="width: 150px">Teacher</td>
                <td><input type="text" name="cls_teacher"  
                           value="<?php echo esc_attr(
                            $cls_data['cls_teacher'] ); ?>"/></td>
            </tr>
            <tr>
                <td style="width: 150px">Room</td>
                <td><input type="text" name="cls_room" 
                           value="<?php echo esc_attr(
                            $cls_data['cls_room'] ); ?>"/></td>
            </tr>
           <tr>
                <td style="width: 150px">Level</td>
                <td>
                  <select name="cls_level">
                   <?php
                   
                   foreach( $cls_levels as $level_id => $level ) {
                      // Add selected tag when entry matches existing level
                      echo '<option value="' . $level_id . '" ';
                      selected( $cls_data['cls_level'], $level_id );
                      echo '>' . $level . '</option>';
                   }
                   ?>
                  </select>
                </td>
            </tr>
            <tr>
              <td>
                <label>Instruction language<a><span>?</span></a></label></td>
              <td><label for="cls_language"><input type="radio" class="radio" name="cls_language" value="1" <?php if ($cls_data['cls_language'] == "1"){echo "checked";}?> />English</label>
                <label for="cls_language"><input type="radio" class="radio" name="cls_language" value="2" <?php if ($cls_data['cls_language'] == "2"){echo "checked";}?> />Mandarin</label>
              </td>
            </tr>
            <tr>
              <td>
                <label>Active<a><span>?</span></a></label></td>
              <td><label for="cls_active"><input type="radio" class="radio" name="cls_active" value="1" <?php if ($cls_data['cls_active'] == "1"){echo "checked";}?> />Yes</label>
                <label for="cls_active"><input type="radio" class="radio" name="cls_active" value="0" <?php if ($cls_data['cls_active'] == "0"){echo "checked";}?> />No</label>
              </td>
            </tr>
            <tr>
                <td>Description</td>
                <td><textarea name="cls_description" rows="4" cols="60"><?php echo esc_textarea(
                    $cls_data['cls_description'] ); ?></textarea></td>
            </tr>
            <tr>
                <td>Additionals</td>
                 <td><textarea name="cls_additionals" cols="60"><?php echo esc_textarea(
                    $cls_data['cls_additionals'] ); ?></textarea></td>
            </tr>
        </table>
        <input type="submit" value="Submit" class="button-primary"/>
        </form>
<?php } ?>
</div>
<?php }

add_action( 'admin_init', 'clsreg_admin_init' );

function clsreg_admin_init() {
    add_action( 'admin_post_save_clsreg_data',
        'process_clsreg_data' );
    add_action( 'admin_post_delete_clsreg_data',
        'delete_clsreg_data' );
}

function process_clsreg_data() {
    // Check if user has proper security level
    if ( !current_user_can( 'manage_options' ) )
        wp_die( 'Not allowed' );

    // Check if nonce field is present for security
    check_admin_referer( 'clsreg_add_edit' );

    global $wpdb;

    error_log('=='. $_POST['cls_level'] . '==');
    error_log('++'. $_POST['cls_active'] . '++');
    // Place all user submitted values in an array (or empty
    // strings if no value was sent)
    $cls_data = array();
    $cls_data['cls_title'] =
        ( isset( $_POST['cls_title'] ) ? $_POST['cls_title'] : '' );

    $cls_data['cls_teacher'] =
        ( isset( $_POST['cls_teacher'] ) ? $_POST['cls_teacher'] : '' );

    $cls_data['cls_room'] =
        ( isset( $_POST['cls_room'] ) ? $_POST['cls_room'] : '' );
        
    $cls_data['cls_language'] =
        ( isset( $_POST['cls_language'] ) ? $_POST['cls_language'] : '' );

    $cls_data['cls_level'] =
        ( isset( $_POST['cls_level'] ) ? $_POST['cls_level'] : 0 );

    $cls_data['cls_active'] = $_POST['cls_active'];

    $cls_data['cls_description'] =
        ( isset( $_POST['cls_description'] ) ? $_POST['cls_description'] : '' );

    $cls_data['cls_additionals'] =
        ( isset( $_POST['cls_additionals'] ) ? $_POST['cls_additionals'] : '' );

    // Set class create date as current date
    $cls_data['cls_create_date'] = date( 'Y-m-d' );


    // Call the wpdb insert or update method based on value
    // of hidden cls_id field
    if ( isset( $_POST['cls_id'] ) && $_POST['cls_id'] == 'new') {
        $wpdb->insert( $wpdb->get_blog_prefix() . 'cls_course_data', $cls_data );
    } elseif ( isset( $_POST['cls_id']) &&
        is_numeric( $_POST['cls_id'] ) ) {
        $wpdb->update( $wpdb->get_blog_prefix() . 'cls_course_data', $cls_data,
            array( 'cls_id' => $_POST['cls_id'] ) );
    }

    // Redirect the page to the user submission form
    wp_redirect( add_query_arg( 'page', 'class-registration', admin_url( 'admin.php' ) ) );
    exit;
}

function delete_clsreg_data() {
    // Check that user has proper security level
    if ( !current_user_can( 'manage_options' ) )
        wp_die( 'Not allowed' );

    // Check if nonce field is present
    check_admin_referer( 'clsreg_deletion' );

    // If classes are present, cycle through array and call SQL
    // command to delete entries one by one
    if ( !empty( $_POST['clses'] ) ) {
        // Retrieve array of classes IDs to be deleted
        $clses_to_delete = $_POST['clses'];
        
        global $wpdb;
        foreach ( $clses_to_delete as $cls_to_delete ) {
            $query = 'DELETE from ' . $wpdb->get_blog_prefix();
            $query .= 'cls_course_data ';
            $query .= 'WHERE cls_id = %d';
            $wpdb->query( $wpdb->prepare( $query, $cls_to_delete) );
        }
    }
    // Redirect the page to the user submission form
    wp_redirect( add_query_arg( 'page', 'class-registration',
                admin_url( 'admin.php' ) ) );
    exit;
}

add_shortcode( 'active-class-list', 'clsreg_shortcode_list' );

function clsreg_shortcode_list() {
    global $wpdb;

    // Prepare query to retrieve classes from database
    $cls_query = 'select * from ' . $wpdb->get_blog_prefix();
    $cls_query .= 'cls_course_data ';
    
    $cls_query .= 'ORDER by cls_id DESC';
    $cls_items = $wpdb->get_results(  $cls_query, ARRAY_A );


    // Prepare output to be returned to replace shortcode
    $output = '';
    
    $output .= '<table>';

    // Check if any classes were found
    if ( !empty( $cls_items ) ) {
        $output .= '<tr><th style="width: 80px">ID</th>';
        $output .= '<th style="width: 300px">Title / Desc</th>';
        $output .= '<th>Teacher</th></tr>';
        // Create row in table for each class
        foreach ( $cls_items as $cls_item ) {
            $output .= '<tr style="background: #FFF">';
            $output .= '<td>' . $cls_item['cls_id'] . '</td>';
            $output .= '<td>' . $cls_item['cls_title'] . '</td>';
            $output .= '<td>' . $cls_item['cls_teacher'];
            $output .= '</td></tr>';
            $output .= '<tr><td></td><td colspan="2">';
            $output .= $cls_item['cls_description'];
            $output .= '</td></tr>';
        }
    } else {
        // Message displayed if no courses are found
        $output .= '<tr style="background: #FFF">';
        $output .= '<td colspan=3>No Classes to Display</td>';
    }

    $output .= '</table><br />';
    // Return data prepared to replace shortcode on page/post
    return $output;
}