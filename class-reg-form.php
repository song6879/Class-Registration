<?php

add_shortcode( 'register-class', 'register_class_form' );
add_shortcode( 'register-a-course', 'register_a_course_form' );
add_action('init', 'regclass_process_form');
/*
function has_my_shortcode($posts) {
    error_log("XXXXXXXXXXXXXXXXXXXXXX");
    if ( empty($posts) )
        return $posts;

    $found = false;

    foreach ($posts as $post) {
        error_log($post->post_content);
        $pos = stripos($post->post_content, '[register-class]');
        error_log($pos);
        if ( $pos !== false ) {
            $found = true;
            break;
        }
    }
    
    if ($found){
        error_log("YYYYYYYYYYYYYYYYYYY");
        //$file = plugin_dir_url(__FILE__).'js/form_validate.js';
        //error_log($file);
        //wp_register_script('my_script',  $file );
        //wp_print_scripts('my_script');
        //add_action( 'wp_footer', 'print_script' );
        add_action( 'wp_header', 'print_style' );
    }
    return $posts;
}
function print_style() {
    $file = plugin_dir_url(__FILE__).'css/mycustom.css';
    //error_log($file);
    wp_register_style('my_style',  $file );
    wp_print_styles('my_style');
}

function print_myscript() {
    $file = plugin_dir_url(__FILE__).'js/form_validate.js';
    //error_log($file);
    wp_register_script('my_script',  $file );
    wp_print_scripts('my_script');
}

add_action('the_posts', 'has_my_shortcode');*/
function theme_name_scripts() {
	wp_enqueue_style( 'style-name', plugins_url('css/mycustom.css', __FILE__) );
	//wp_enqueue_script( 'script-name', get_template_directory_uri() . '/js/example.js', array(), '1.0.0', true );
}

add_action( 'wp_enqueue_scripts', 'theme_name_scripts' );

function tomdy($date) {
    return date("M d,Y", strtotime($date));
}

function setValue( $fieldName ) {
  return isset( $_POST[$fieldName] )? $_POST[$fieldName] : '';
}

function setChecked( $fieldName, $fieldValue ) {
  return  (isset( $_POST[$fieldName] ) and $_POST[$fieldName] == $fieldValue) ? ' checked="checked"' : '';
}
function validateField( $fieldName, $missingFields ) {
  return in_array( $fieldName, $missingFields ) ? ' class="error"' : '';
}

function regclass_process_form()
{
    // error_log("+++++++++++++++++++++++++++++++++++++++++");
    // error_log(print_r($_POST, true));
    if (!empty($_POST['nonce_regclass_form'])) {
        if (!wp_verify_nonce($_POST['nonce_regclass_form'], 'handle_regclass_form')) {
                die('You are not authorized to perform this action.');
            } else {
                //die('Its safe to do further processing on submitted data.');
                $error = null;
                if ( isset($_POST['signup']) ) {
                  wp_redirect(wp_registration_url( add_query_arg('id', $_GET['id'], get_permalink())));
                  exit();
                }else if ( isset($_POST['signin'])) {
                    wp_redirect(wp_login_url( add_query_arg('id', $_GET['id'], get_permalink())));
                    exit();
                }
                else {//if( isset($_POST['register'])) {
                    //die('Its safe to register.');
                    //echo "hello";
                    //error_log("register");
                    wp_redirect( the_permalink() );
                }
            }
    }
    // process input show errors or success message
    return "Process Data";

}

function register_class_form()
{
    $checkingFields = false;
    if(!isset($_GET['id'])) {
       return active_class_list();
    }
    elseif( !isset($_POST['register']) && !isset($_POST['submit']) ) {
         //error_log("!register");
         return do_shortcode('[register-a-course '. 'id="'. $_GET['id'].'"]');
    } elseif (isset($_POST['submit'])) {
        $requiredFields = array( "stu_last_name", "stu_first_name", "stu_gender", "stu_phone" );
        $checkingFields = true;
         // return print_r($_POST, true);//'Processing submit register'; 
    }
    // return a string with the form HTML
    // Prepare query to retrieve bugs from database
    global $wpdb;
    $cls_query = 'select * from ' . $wpdb->get_blog_prefix() . 'cls_course_data where cls_id = %d';
    $cls_data = $wpdb->get_row(  $wpdb->prepare($cls_query, $_GET['id']), ARRAY_A );
      
    if(!$cls_data) {
         return 'There is no course with that id';
    }

    $adultStudent = $cls_data['cls_level'] == 4 ? true: false;
    $missingFields = array();
    if($checkingFields) {
       if($adultStudent) {
           $requiredFields[] = 'stu_email';
       } else {
           $requiredFields[] = 'parent1_last_name';
           $requiredFields[] = 'parent1_first_name';
           $requiredFields[] = 'parent1_phone';
           $requiredFields[] = 'parent1_email';
       }

        foreach ( $requiredFields as $requiredField ) {
            if ( !isset( $_POST[$requiredField] ) or !$_POST[$requiredField] ) {
                $missingFields[] = $requiredField;
            }
        }
        
        if(!$missingFields) {
            return processing_submit();
        }
    }
    
    $output = '<br/><div class="wrap">';
    
    $level_id = $cls_data['cls_level'];
    //error_log("===============".get_levels($level_id));
    $title = $level_id>'0' && $level_id<'4'? get_levels($level_id):'';
    //error_log("***************".$title);
    $output .=  '<p>You are going to register <strong>' . $title . ' ' . $cls_data['cls_title']. "</strong></p>\n";
    
    if($missingFields) {
        $output .=  '<p class="error">Please fill the fields highlighted below of student information '. ($adultStudent?'':'and guardian/parent information') ."</p>\n";
    }
    else {
        $output .=  "<p>Please fill the ". ($missingFields?' fields highlighted below of':'')."student information ". ($adultStudent?'':'and guardian/parent information') ."</p>\n";
    }
    $output .= '<br/><form id="registerclass" method="post" action="">'. "\n";
    $output .=  wp_nonce_field('handle_regclass_form', 'nonce_regclass_form');
 
    $output .= student_information($missingFields, $adultStudent);
    $output .= '<div >';
    $output .= '<input type="reset" value="Reset" style="float: left; width:30%;" class="button-primary"/>';
    $output .= '<input type="submit" name="submit" value="Send details" style="float: right; width:30%;" class="button-primary"/></div>';
    $output .= '</form><br/><br/></div>'. "\n";
    return $output;
}

function student_information($missingFields, $adultStudent) {
    $output = '学生(Student):<br/>' . "\n";
    $output .= '<table>';
    $output .= '<tr><th' . validateField( "stu_last_name", $missingFields) . '><label for="stu_last_name">Last Name</label></th>'.
               '<th' . validateField( "stu_first_name", $missingFields) . '><label for="stu_first_name">First Name</label></th>'.
               '<th' . validateField( "stu_gender", $missingFields) . '><label for="stu_gender">Gender</label></th>' . 
             ($adultStudent? '': '<th>Birth Date</th>'). 
             '<th' . validateField( "stu_phone", $missingFields) . '><lable for="stu_phone">Phone</label></th>' .
             ($adultStudent? '<th' . validateField( "stu_email", $missingFields) . '><lable for="stu_email">Email</label></th>':''). '</tr>'."\n";
    $output .= '<tr><td><input style="width:98%;" type="text" name="stu_last_name" placeholder="Student Last Name" value="' . 
                      setValue( "stu_last_name" ) . '" requiredxx </td>';
    $output .=      '<td><input style="width:98%;" type="text" name="stu_first_name" placeholder="Student First Name" value="' .
                      setValue( "stu_first_name" ) . '" requiredxx </td>';
    $output .= '<td><label for="stu_gender"><input type="radio" class="radio" name="stu_gender" value="M"  ' .
                           setChecked( "stu_gender", "M" ) . '/>Male</label>
                <label for="stu_gender"><input type="radio" class="radio" name="stu_gender" value="F"  ' . 
                           setChecked( "stu_gender", "F" ) . '/>Female</label>
              </td>';
    if(!$adultStudent) {
        $output .=      '<td><input style="width:98%;" type="date" name="stu_birth_date" value=""</td>';
    }
    $output .=      '<td><input style="width:98%;" type="tel" name="stu_phone" placeholder="Student Phone Num" value="' . 
        setValue( "stu_phone" ) . '" requiredxx </td>';
    
    if($adultStudent) {
        $output .=      '<td><input style="width:98%;" type="email" name="stu_email" placeholder="Student Email" value="' . 
        setValue( "stu_email" ) . '" requiredxx </td></tr>' . "\n";
    }
    else
        $output .= '</tr>' . "\n";
    
    $output .= '<tr><th colspan="2">City</th><th>State</th><th colspan="2">Zip</th></tr>'."\n";
    $output .= '<tr><td colspan="2"><input type="text" name="stu_city" value="' . setValue( "stu_city" ) . '"</td>';
    $output .=      '<td><input style="width:98%;" type="text" name="stu_state" value="' . setValue( "stu_state" ) . '"</td>';
    $output .= '<td colspan=2><input type="text" name="stu_zip" value="' . setValue( "stu_zip" ) . '"</td></tr></table>' . "\n";

    if(!$adultStudent) {
        $output .= '父母/监护人(Parent/Guardien):<br/>' . "\n";
        $output .= '<table>';
        $output .= '<tr><th' . validateField( "parent1_last_name", $missingFields) . '>Last Name</th>'.
           '<th' . validateField( "parent1_first_name", $missingFields) . '>First Name</th><th>Relation</th>'.
           '<th' . validateField( "parent1_phone", $missingFields) . '>Phone</th>'.
           '<th' . validateField( "parent1_email", $missingFields) . '>Email</th></tr>'."\n";
        $output .= '<tr><td><input style="width:98%;" type="text" name="parent1_last_name" placeholder="First Guardien Last Name" value="' .
                      setValue( "parent1_last_name" ) . '" requiredxx </td>';
        $output .=      '<td><input style="width:98%;" type="text" name="parent1_first_name" placeholder="First Guardien First Name" value="' .
                      setValue( "parent1_first_name" ) . '" requiredxx "</td>';
        $output .=      '<td><input style="width:98%;" type="text" name="parent1_relation" value="' . setValue( "parent1_relation" ) . '"</td>';
        $output .=      '<td><input style="width:98%;" type="text" name="parent1_phone" placeholder="First Guardien Phone Num" value="' . 
                      setValue( "parent1_phone" ) . '" requiredxx </td>';
        $output .=      '<td><input style="width:98%;" type="email" name="parent1_email" placeholder="First Guardien Email" value="' . 
                      setValue( "parent1_email" ) . '" requiredxx </td></tr>';
        $output .= '<tr><td><input style="width:98%;" type="text" name="parent2_last_name" value="' . setValue( "parent2_last_name" ) . '"</td>';
        $output .=      '<td><input style="width:98%;" type="text" name="parent2_first_name" value="' . setValue( "parent2_first_name" ) . '"</td>';
        $output .=      '<td><input style="width:98%;" type="text" name="parent2_relation" value="' . setValue( "parent2_relation" ) . '"</td>';
        $output .=      '<td><input style="width:98%;" type="text" name="parent2_phone" value="' . setValue( "parent2_phone" ) . '"</td>';
        $output .=      '<td><input style="width:98%;" type="email" name="parent2_email" value="' . setValue( "parent2_email" ) . '"</td></tr></table>';
    }
    return $output;
}

function student_information_for_mail($adultStudent) {
    $output = '学生(Student):<br/>' . "\n";
    $output .= '<table>';
    $output .= '<tr><td>Last Name</td><td>'. setValue( "stu_last_name" ) . '</td></tr>'.
               '<tr><td>First Name</td><td>' .setValue( "stu_first_name" ) . '</td></tr>'."\n";
    $output .= '<tr><td>Gender</td><td>' . ($_POST['stu_gender'] == 'M' ? 'Male':'Female') . '</td></tr>'; 
    if($adultStudent) {
         $output .= '<tr><td>Phone</td><td>' . setValue( "stu_phone" ) . '</td></tr>' .
                    '<tr><td>Email</td><td>' . setValue( "stu_email" ) . '</td></tr>' . "\n";
    } else {
         $output .= '<tr><td>Birth Date</td><td>'. $_POST['stu_birth_date'].'</td></tr>' .
                    '<tr><td>Phone</td><td>' . setValue( "stu_phone" ) . '</td></tr>' . "\n";
    }
  
    $output .= '<tr><td>City</td><td>'. setValue( "stu_city" ) . '</td></tr>';
    $output .= '<tr><td>State</td><td>'. setValue( "stu_state" ) . '</td></tr>';
    $output .= '<tr><td>Zip</td><td>'. setValue( "stu_zip" ) . '</td></tr>';

    if(!$adultStudent) {
        $output .= '父母/监护人(Parent/Guardien):<br/>' . "\n";
        $output .= '<table>';
        $output .= '<tr><td>Last Name</td><td>' . setValue( "parent1_last_name" ) . '</td></tr>';
        $output .= '<tr><td>First Name</td><td>' . setValue( "parent1_first_name" ) . '</td></tr>';
        $output .= '<tr><td>Relation</td><td>' . setValue( "parent1_relation" ) . '</td></tr>';
        $output .= '<tr><td>Phone</td><td>' . setValue( "parent1_phone" ) . '</td></tr>';
        $output .= '<tr><td>Email</td><td>' . setValue( "parent1_email" ) . '</td></tr>';

        $output .= '<tr><td>Last Name</td><td>' . setValue( "parent2_last_name" ) . '</td></tr>';
        $output .= '<tr><td>First Name</td><td>' . setValue( "parent2_first_name" ) . '</td></tr>';
        $output .= '<tr><td>Relation</td><td>' . setValue( "parent2_relation" ) . '</td></tr>';
        $output .= '<tr><td>Phone</td><td>' . setValue( "parent2_phone" ) . '</td></tr>';
        $output .= '<tr><td>Email</td><td>' . setValue( "parent2_email" ) . '</td></tr>';
        
        $output .= '</table>';
    }
    return $output;
}


function get_levels($level_id) {
    $cls_levels = array( 0 => 'Pre', 1 => 'Children', 2 => 'Youth', 3 => 'Adult',  4 => 'Culture');
    //error_log($level_id . '-----'. $cls_levels[$level_id]);
    return $cls_levels[$level_id];
}

function active_class_list() {
    global $wpdb;

    // Prepare query to retrieve bugs from database
    $cls_query = 'select * from ' . $wpdb->get_blog_prefix();
    $cls_query .= 'cls_course_data where cls_active = true ';
    
    $cls_query .= 'ORDER by cls_level, cls_title';
    $cls_items = $wpdb->get_results(  $cls_query, ARRAY_A );


    // Prepare output to be returned to replace shortcode
   
    $output = '<h2><p style="text-align: center;">We provide the following courses this year</p></h2>'."\n";

    // Check if any bugs were found
    if ( !empty( $cls_items ) ) {
         
         $output .= "<ul>\n";
        // Create row in table for each bug
        foreach ( $cls_items as $cls_item ) {
            $title = get_levels($cls_item['cls_level']) ;
            $title .= ' - ' . $cls_item['cls_title'];
            $title .= ' | ' . $cls_item['cls_teacher'];
            $title .= ' | Room-' . $cls_item['cls_room'];
            
             $output .= '<li><h3><a href="' . add_query_arg( array( 'id' => $cls_item['cls_id'] ), NULL ) .'"'. ">$title</a></h3></li>";
            //$output .= "<p>" . $cls_item['cls_description'] . "</p>";
            //$output .= isset($cls_item['cls_additionals'])?"<p>" . $cls_item['cls_additionals'] . "</p>":'';

            $output .= "\n";
            /*$output .= '<tr style="background: #FFF">';
            $output .= '<td><input type="radio" name="reg_cls" value="';
            $output .= esc_attr( $cls_item['cls_id'] ) . '" /></td>';
            $output .= '<td>' . $title . '</td>';
            $output .= '<td>' . $cls_item['cls_description'] . '</td>';
            $output .= '<td>' . $cls_item['cls_additionals'];
            $output .= '</td></tr>';*/
        }
        $output .= "</ul>\n";
    } else {
        // Message displayed if no bugs are found
        $output .= '<h3>No courses available now</h3';
    }
    
    // Return data prepared to replace shortcode on page/post
    return $output;
}

function register_a_course_form($atts) {
    $atts = shortcode_atts( array( 'id' => NULL), $atts );
    
    if( !isset($atts['id'] ) ) return '';
    
    global $wpdb;


    // Prepare query to retrieve bugs from database
    $cls_query = 'select * from ' . $wpdb->get_blog_prefix() . 'cls_course_data where cls_id = %d';
    
    $cls_data = $wpdb->get_row(  $wpdb->prepare($cls_query, $atts['id']), ARRAY_A );
    
    if(!$cls_data) {
         return 'There is no course with that id';
    }
    $adultStudent = $cls_data['cls_level'] == 4 ? true: false;
    
    $output = '<br/><div class="wrap">';
    
    $output .= '<form method="post" action="'. add_query_arg('id', $atts['id'], get_permalink()) . '">' . "\n";
    $output .=  wp_nonce_field('handle_regclass_form', 'nonce_regclass_form');
    
    $output .= course_table($cls_data);
    
    $output .=  "<p>Class Location: " . esc_attr(get_option('class_location')) . "</p>";
    if(!$adultStudent) {
        $output .=  "<ul>";
        $output .=  "<li>" . tomdy( get_option('fall_from')) . " - ". tomdy( get_option('fall_to')) . "(Fall Semester)<br/>";
        $output .=  "Fee: $". get_option('fall_fee') . "(due at first class)</li>";
        $output .= "<li>" . tomdy( get_option('spring_from')) . " - ". tomdy( get_option('spring_to')) . "(Spring Semester)<br/>";;
        $output .=  "Fee: $". get_option('spring_fee') . "(due at first class)</li>";
        $output .=  "<li>Full year total $". get_option('fullyear_fee') . "(due at first class)</li>";
        $output .=  "</ul>";
    }
    $output .= '<div>';
    if ( ! is_user_logged_in() ) {
      //$output .= '<a href="' .wp_login_url( add_query_arg('id', $atts['id'], get_permalink())) .'" class="button primary-button">Sign In</a>';
      $output .= '<input type="submit" name="signup" value="Create an account" style="float: left; width:30%;" class="button-primary"/>';
      $output .= '<input type="submit" name="signin" value="Sign In" style="float: right; width:30%;" class="button-primary"/>';
    } else {
    $output .= '<input type="submit" name="register" value="Register" style="float: right; width:30%;" class="button-primary"/></div>';
    }
    $output .= '</form><br/><br/><br/><br/><br/><br/>'. "\n";
    return $output;
}

function course_table($cls_data) {
    $output =  '<table>' . "\n";
    $output .=  '<tr><td style="width: 150px">Class Title</td><td>' . $cls_data['cls_title']. "</td>\n";
    $output .=  '<td style="width: 150px">Teacher</td><td>' . $cls_data['cls_teacher']."</td></tr>\n";
    $output .=  '<tr><td style="width: 150px">Room:</td><td>' . $cls_data['cls_room']."</td>\n";
    $output .=  '<td style="width: 150px">Class Level</td><td>' . get_levels($cls_data['cls_level'])."</td></tr>\n";

    $output .=  '<tr><td style="width: 150px">Class Time</td><td>';
    $output .=  "(Fri)" ."(".get_option('class_start_time'). "-". get_option('class_end_time').")</td>";
    $output .=  '<td colspan="2"><label>Class instructed in '. ($cls_data['cls_language'] == "1"?'English': 'Mandarin')."</td></tr>\n";
    
    $output .=  '<tr><td>Description</td><td colspan="3">'. $cls_data['cls_description']. "</td></tr>\n";
    if($cls_data['cls_additionals'] ) {
        $output .=  '<tr><td colspan="4">' . $cls_data['cls_additionals'] . "</td></tr>\n";
    }
    $output .=  "</table>\n";
    
    return $output;
}
add_filter( 'wp_mail_content_type', 'set_content_type' );
function set_content_type( $content_type ) {
	return 'text/html';
}
function processing_submit() {
    global $wpdb;
    $cls_query = 'select * from ' . $wpdb->get_blog_prefix() . 'cls_course_data where cls_id = %d';
    $cls_data = $wpdb->get_row(  $wpdb->prepare($cls_query, $_GET['id']), ARRAY_A );
      
    if(!$cls_data) {
         return 'There is no course with that id';
    }

    $output = "<p>This is your class information</p>\n";
    $output .= course_table($cls_data);
    $output .= "<p>This is your student informaiton</p>\n";
    $adultStudent = $cls_data['cls_level'] == 4 ? true: false;
    $output .= student_information_for_mail($adultStudent);
    
    if ($adultStudent) {
      $to = $_POST['stu_email'];
    }else {
      if (isset($_POST['parent2_email'])) {
        $to = array($_POST['parent1_email'], $_POST['parent2_email']);
      }
      else {
        $to = $_POST['parent1_email'];
      }
    }
    $subject = "Vermont Chinese School 2015-2016 Class Registration";
    $content = "<html><header>". $subject . "</header>\n<body>".$output . "</body></html>";
    //$status = wp_mail($to, $subject, $content);
    return $output;
}

/*
function class_reg_page() {
    global $wpdb;
    $cls_levels = array( 0 => 'Children', 1 => 'Youth', 2 => 'Adult' );
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
        $cls_query .= 'ORDER by cls_create_date DESC';

        $cls_items = $wpdb->get_results( $cls_query, ARRAY_A );
    ?>
    <h3>Manage Class Entries</h3>
    <form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
      <input type="hidden" name="action" value="delete_clsreg_data" />
        
      <!-- Adding security through hidden referrer field -->
      <?php wp_nonce_field( 'clsreg_deletion' ); ?>
        
      <table class="wp-list-table widefat fixed" >
        <thead><tr><th style="width: 50px"></th>
            <th style="width: 50px">ID</th>
            <th style="width: 200px">Title</th>
            <th>Teacher</th></tr></thead>
        <?php
            // Display courses if query returned results
            if ( $cls_items ) {
                foreach ( $cls_items as $cls_item ) {
                    echo '<tr style="background: #FFF">';
                    echo '<td><input type="checkbox" name="clses[]" value="';
                    echo esc_attr( $cls_item['cls_id'] ) . '" /></td>';
                    echo '<td>' . $cls_item['cls_id'] . '</td>';
                    echo '<td><a href="';
                    echo add_query_arg( array(
                            'page' => 'class-registration',
                            'id' => $cls_item['cls_id'] ),
                        admin_url( 'options-general.php' ) );
                    echo '">' . $cls_levels[$cls_item['cls_level']] . ' - ' . $cls_item['cls_title'] . '</a></td>';
                    echo '<td>' . $cls_item['cls_teacher'] . '</td></tr>' . "\n";
                }
            } else {
                echo '<tr style="background: #FFF">';
                echo '<td colspan=4>No Clases Added Yet</td></tr>';
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
*/