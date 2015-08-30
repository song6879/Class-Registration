<?php

add_shortcode( 'my-profile', 'my_profile_form' );
add_action('init', 'process_my_form');

function process_my_form()
{
    if (!empty($_POST['nonce_myprofile_form'])) {
        if (!wp_verify_nonce($_POST['nonce_myprofile_form'], 'handle_myprofile_form')) {
            die('You are not authorized to perform this action.');
        } else {
            $error = null;
            if (empty($_POST['stu_last_name'])) {
                wp_redirect( the_permalink() ); //get_permalink(52)
                //$error = new WP_Error('empty_error', __('Please enter name.', 'tahiryasin'));
                //wp_die($error->get_error_message(), __('CustomForm Error', 'tahiryasin'));
            }
            else {
                die('Its safe to do further processing on submitted data.');
                //echo "hello";
                //wp_redirect( the_permalink() );
            }
       }
    }
    if (!empty($_POST['nonce_addclass_form'])) {
        if (!wp_verify_nonce($_POST['nonce_addclass_form'], 'handle_myprofile_form')) {
                die('You are not authorized to perform this action.');
        } else {
            save_my_class_data();
                //die('Save class information.');
        }
    }
    if (!empty($_POST['nonce_addstudent_form'])) {
        if (!wp_verify_nonce($_POST['nonce_addstudent_form'], 'handle_myprofile_form')) {
                die('You are not authorized to perform this action.');
        } else {
            save_my_student_data();
                //die('Save class information.');
        }
    }
}

/*function regclass_callback()
{
    if ( 'POST' !== $_SERVER['REQUEST_METHOD'] 
        or ! isset ( $_POST['classifieds'] )
    )
    {
        return register_class_form();
    }

    // process input show errors or success message
    return "Process Data";

}*/

function my_profile_form()
{
    // return a string with the form HTML

    if ( ! is_user_logged_in() ) {
        return '<pre>Please Log In First</pre>';
//<p>By <a href="bloginfo('url'); wp-register.php">registering</a>, you can save your favorite posts for future reference.</p>
    }
    
    if ( empty( $_GET['page'] ) ) {
         return my_profile_page();
    } elseif (isset( $_GET['page'] ) &&
            ( $_GET['page'] == 'class') ) {
         return my_profile_add_class();
    } elseif (isset( $_GET['page'] ) &&
            ( $_GET['page'] == 'student') ) {
         return my_profile_add_student();
    }
    return "Nothing";
}

function my_profile_page() {
    //$output = '';
    $output = '<div class="wrap">';
    $output .= '<form method="post" action="">'. "\n";
    $output .=  wp_nonce_field('handle_myprofile_form', 'nonce_myprofile_form');
    
    global $current_user;
    get_currentuserinfo();
/*
        //$output .= '<pre>'.print_r(get_object_vars($current_user), true).'</pre>';
        $output .= '<pre>'.'Username: ' . $current_user->user_login . '</pre>';
        $output .= '<pre>'.'User email: ' . $current_user->user_email . '</pre>';
        $output .= '<pre>'.'User first name: ' . $current_user->user_firstname . '</pre>';
        $output .= '<pre>'.'User last name: ' . $current_user->user_lastname . '</pre>';
        $output .= '<pre>'.'User display name: ' . $current_user->display_name . '</pre>';
        $output .= '<pre>'.'User ID: ' . $current_user->ID . '</pre>';
*/
    /*$user_ID = get_current_user_id();
    $userdata = get_userdata( $user_ID );
    //$output .= '<table>';
    //foreach( get_object_vars($userdata) as $key => $value) {
    //   $output .= "<td>(is_object($key)?'object':$key)$key</td><td>(is_object($value)?'object':$value)</td>";
    //}
    //$output .= '<pre>'.print_r(get_object_vars($userdata), true).'</pre>';
    
        $output .= '<pre>'.'Username: ' . $current_user->user_login . '</pre>';
        $output .= '<pre>'.'User email: ' . $current_user->user_email . '</pre>';
        $output .= '<pre>'.'User first name: ' . $current_user->user_firstname . '</pre>';
        $output .= '<pre>'.'User last name: ' . $current_user->user_lastname . '</pre>';
        $output .= '<pre>'.'User display name: ' . $current_user->display_name . '</pre>';
        $output .= '<pre>'.'User ID: ' . $current_user->ID . '</pre>';
*/
    //$output .= '</table>' . "\n";
    
    $output .= 'My Information:<br/>' . "\n";
    $output .= '<table>';
    $output .= '<tr><th>Last Name</th><th>First Name</th><th>Phone</th><th>Email</th></tr>'."\n";
    $output .= '<tr><td><input style="width:98%;" type="text" name="my_last_name" value="'. $current_user->user_lastname.'"</td>';
    $output .=      '<td><input style="width:98%;" type="text" name="my_first_name" value="' . $current_user->user_firstname . '"</td>';
    $output .=      '<td><input style="width:98%;" type="text" name="my_phone" value=""</td>';
    $output .=      '<td><input style="width:98%;" type="email" name="my_email" value="' . $current_user->user_email . '"</td></tr>'."\n";
 
 
    $output .= '<tr><th colspan="2">City</th><th>State</th><th>Zip</th></tr>'."\n";
    $output .= '<tr><td colspan="2"><input type="text" name="my_city" value=""</td>';
    $output .=      '<td><input style="width:98%;" type="text" name="my_state" value=""</td>';
    $output .= '<td><input type="text" name="my_zip" value=""</td></tr></table>' . "\n";


    $output .= '<h2>My Classes:' .  '<a style="padding: 4px 8px; color: #0073aa; background: #e0e0e0; font-weight=600; font-size=13px;" href="' .
        add_query_arg( array( 'page' => 'class', 'id' => $current_user->ID ), NULL ) . '">Add My Class</a></h2><br/>';
    //$output .= my_class_list();

    $output .= '<h2>My Students:' .  '<a style="padding: 4px 8px; color: #0073aa; background: #e0e0e0; font-weight=600; font-size=13px;" href="' .
        add_query_arg( array( 'page' => 'student', 'id' => $current_user->ID ), NULL ) . '">Add Student</a></h2><br/>';
    //$output .= my_student_list();
    
    $output .= '<div >';
    $output .= '<input type="reset" value="Reset" style="float: left; width:30%;" class="button-primary"/>';
    $output .= '<input type="submit" value="Register" style="float: right; width:30%;" class="button-primary"/></div>';
    $output .= '</form></div><span style="display:block;clear:both;height: 0px;padding-top: 100px;"></span>'. "\n";
    return $output;    
}

function my_profile_add_class() {
   //$output = '';
    $output = '<div class="wrap">';
    $output .= '<form method="post" action="">'. "\n";
    //$output .= '<form method="post" action="'.admin_url( 'admin-post.php' ).'">'. "\n";
    //$output .= '<input type="hidden" name="action" value="save_my_class_data" />';
    $output .=  wp_nonce_field('handle_myprofile_form', 'nonce_addclass_form');
    $output .= '<br/>' . active_class_list();    
    $output .= '<div >';
    $output .= '<input type="submit" value="Register" style="float: right; width:30%;" class="button-primary"/></div>';
    $output .= '</form><br/><br/></div>'. "\n";
    return $output;    
}


function my_profile_add_student() {
   $output = '<div class="wrap">';
    $output .= '<form method="post" action="">'. "\n";
    //$output .= '<form method="post" action="'.admin_url( 'admin-post.php' ).'">'. "\n";
    //$output .= '<input type="hidden" name="action" value="save_my_class_data" />';
    $output .=  wp_nonce_field('handle_myprofile_form', 'nonce_addstudent_form');
    
    $output .= '<h2>Add Student:</h2><br/>' . "\n";
    $output .= '<table>';
    /*$output .= '<tr><th><lable for="stu_last_name">Last Name</label></th><th>First Name</th><th>Gender</th><th>Birth Day</th><th>Relation to me</th></tr>'."\n";
    $output .= '<tr><td><input style="width:98%;" type="text" name="stu_last_name" placeholder="Student Last Name" value=""</td>';
    $output .=      '<td><input style="width:98%;" type="text" name="stu_first_name" value=""</td>';
    $output .= '<td><label for="stu_gender"><input type="radio" class="radio" name="stu_gender" value="1"  />Male</label>
                <label for="stu_gender"><input type="radio" class="radio" name="stu_gender" value="0"  />Female</label>
              </td>';
    $output .=      '<td><input style="width:98%;" type="date" name="birth_date" value=""</td>';
    $output .=      '<td><input style="width:98%;" type="tel" name="phone" value=""</td></tr>' . "\n";*/
    $output .= '<tr><th><lable for="stu_last_name">Last Name</label></th>';
    $output .= '<td><input style="width:98%;" type="text" name="stu_last_name" placeholder="Student Last Name" value=""</td></tr>';
    $output .= '<tr><th>First Name</th><td><input style="width:98%;" type="text" name="stu_first_name" value=""</td></tr>';
    $output .= '<tr><th>Gender</th><td><label for="stu_gender"><input type="radio" class="radio" name="stu_gender" value="1"  />Male</label>';
    $output .= '<label for="stu_gender"><input type="radio" class="radio" name="stu_gender" value="0"  />Female</label></td></tr>';
    $output .= '<tr><th>Birth Date</th><td><input style="width:98%;" type="date" name="stu_birth_date" value=""</td></tr>';
    $output .= '<tr><th>Relation to me</th><td><input style="width:98%;" type="tel" name="relation_to_me" value=""</td></tr>'."\n";

    $output .= '</table>' . "\n";
    
    $output .= '<div >';
    $output .= '<input type="submit" value="Add" style="float: right; width:30%;" class="button-primary"/></div>';
    $output .= '</form></div><span style="display:block;clear:both;height: 0px;padding-top: 250px;"></span>'. "\n";
    return $output;    
}

function save_my_student_data() {
    // Check if user has proper security level
    //if ( !current_user_can( 'manage_options' ) )
    //    wp_die( 'Not allowed' );

    // Check if nonce field is present for security
    //check_admin_referer( 'clsreg_add_edit' );
    
    global $wpdb;

    // Place all user submitted values in an array (or empty
    // strings if no value was sent)
    $stu_data = array();
    $stu_data['last_name'] =
        ( isset( $_POST['stu_last_name'] ) ? $_POST['stu_last_name'] : '' );

    $stu_data['fist_name'] =
        ( isset( $_POST['stu_last_name'] ) ? $_POST['stu_last_name'] : '' );

    $stu_data['gender'] =
        ( isset( $_POST['stu_gender'] ) ? $_POST['stu_gender'] : '' );
        
    $stu_data['birth_date'] =
        ( isset( $_POST['stu_birth_date'] ) ? $_POST['stu_birth_date'] : '' );

    $stu_data['phone'] ='';

    $stu_data['address'] = '';

    global $current_user;
    get_currentuserinfo();
    
    // Call the wpdb insert or update method based on value
    // of hidden cls_id field
    if ( isset( $_POST['sid'] ) && $_POST['sid'] == 'new') {
        $wpdb->insert( $wpdb->get_blog_prefix() . 'cls_student_data', $stu_data );
        $wpdb->insert_id;
        $wpdb->insert( $wpdb->get_blog_prefix() . 'cls_student_data', $stu_data );
    } elseif ( isset( $_POST['sid']) &&
        is_numeric( $_POST['sid'] ) ) {
        $wpdb->update( $wpdb->get_blog_prefix() . 'cls_student_data', $cls_data,
            array( 'sid' => $_POST['sid'] ) );
    }

    wp_redirect(remove_query_arg(array('page', 'id'), $_SERVER['REQUEST_URI']));
    exit;
}

function my_class_list() {
    global $wpdb;

    // Prepare query to retrieve bugs from database
    $cls_query = 'select * from ' . $wpdb->get_blog_prefix();
    $cls_query .= 'cls_course_data where cls_active = true ';
    
    $cls_query .= 'ORDER by cls_level and cls_title';
    $cls_items = $wpdb->get_results(  $cls_query, ARRAY_A );


    // Prepare output to be returned to replace shortcode
    $output = '';
    
    $output .= '<table>';

    // Check if any bugs were found
    if ( !empty( $cls_items ) ) {
        $cls_levels = array( 0 => 'Pre', 1 => 'Children', 2 => 'Youth', 3 => 'Adult',  4 => 'Culture');
        $output .= '<tr><th style="width: 50px"></th><th style="width: 120px">Title</th>';
        $output .= '<th style="width: 300px">Description</th>';
        $output .= '<th style="width: 130px">Additionals</th></tr>';
        // Create row in table for each bug
        foreach ( $cls_items as $cls_item ) {
            $title = $cls_levels[$cls_item['cls_level']] ;
            $title .= ' - ' . $cls_item['cls_title'];
            $title .= ' ' . $cls_item['cls_teacher'];
            $title .= ' ' . $cls_item['cls_room'];
            
            $output .= '<tr style="background: #FFF">';
            $output .= '<td><input type="radio" name="reg_cls" value="';
            $output .= esc_attr( $cls_item['cls_id'] ) . '" /></td>';
            $output .= '<td>' . $title . '</td>';
            $output .= '<td>' . $cls_item['cls_description'] . '</td>';
            $output .= '<td>' . $cls_item['cls_additionals'];
            $output .= '</td></tr>';
        }
    } else {
        // Message displayed if no bugs are found
        $output .= '<tr style="background: #FFF">';
        $output .= '<td colspan=4>No class to register</td>';
    }

    $output .= '</table><br />';
    // Return data prepared to replace shortcode on page/post
    return $output;
}

function my_student_list() {
    global $wpdb;

    // Prepare query to retrieve bugs from database
    $cls_query = 'select * from ' . $wpdb->get_blog_prefix();
    $cls_query .= 'cls_course_data where cls_active = true ';
    
    $cls_query .= 'ORDER by cls_level and cls_title';
    $cls_items = $wpdb->get_results(  $cls_query, ARRAY_A );


    // Prepare output to be returned to replace shortcode
    $output = '';
    
    $output .= '<table>';

    // Check if any bugs were found
    if ( !empty( $cls_items ) ) {
        $cls_levels = array( 0 => 'Pre', 1 => 'Children', 2 => 'Youth', 3 => 'Adult',  4 => 'Culture');
        $output .= '<tr><th style="width: 50px"></th><th style="width: 120px">Title</th>';
        $output .= '<th style="width: 300px">Description</th>';
        $output .= '<th style="width: 130px">Additionals</th></tr>';
        // Create row in table for each bug
        foreach ( $cls_items as $cls_item ) {
            $title = $cls_levels[$cls_item['cls_level']] ;
            $title .= ' - ' . $cls_item['cls_title'];
            $title .= ' ' . $cls_item['cls_teacher'];
            $title .= ' ' . $cls_item['cls_room'];
            
            $output .= '<tr style="background: #FFF">';
            $output .= '<td><input type="radio" name="reg_cls" value="';
            $output .= esc_attr( $cls_item['cls_id'] ) . '" /></td>';
            $output .= '<td>' . $title . '</td>';
            $output .= '<td>' . $cls_item['cls_description'] . '</td>';
            $output .= '<td>' . $cls_item['cls_additionals'];
            $output .= '</td></tr>';
        }
    } else {
        // Message displayed if no bugs are found
        $output .= '<tr style="background: #FFF">';
        $output .= '<td colspan=4>No class to register</td>';
    }

    $output .= '</table><br />';
    // Return data prepared to replace shortcode on page/post
    return $output;
}

function save_my_class_data() {
    // Check if user has proper security level
    //if ( !current_user_can( 'manage_options' ) )
    //    wp_die( 'Not allowed' );

    // Check if nonce field is present for security
    //check_admin_referer( 'clsreg_add_edit' );
/*
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
    }*/

    // Redirect the page to the user submission form
    //wp_redirect( $_SERVER['REQUEST_URI']);//add_query_arg( NULL, NULL, NULL));//'my-profile' ) );
    wp_redirect(remove_query_arg(array('page', 'id'), $_SERVER['REQUEST_URI']));
    exit;
}

