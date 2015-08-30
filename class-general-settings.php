<?php

function register_clsreg_general_settings() {
	//register our settings
	register_setting( 'clsreg-general-settings-group', 'class_location' );
	register_setting( 'clsreg-general-settings-group', 'fall_from' );
	register_setting( 'clsreg-general-settings-group', 'fall_to' );
    register_setting( 'clsreg-general-settings-group', 'fall_fee' );
	register_setting( 'clsreg-general-settings-group', 'spring_from' );
	register_setting( 'clsreg-general-settings-group', 'spring_to' );
    register_setting( 'clsreg-general-settings-group', 'spring_fee' );
    register_setting( 'clsreg-general-settings-group', 'fullyear_fee' );
	register_setting( 'clsreg-general-settings-group', 'class_start_time' );
    register_setting( 'clsreg-general-settings-group', 'class_end_time' );
}

function clsreg_general_setting_page() {
?>
<div class="wrap">
<h2>Class Registration General Settings</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'clsreg-general-settings-group' ); ?>
    <?php do_settings_sections( 'clsreg-general-settings-group' ); ?>
    <table class="form-table">
    <tr>
        <th style="text-align: right;">
        <label class="tooltip" title="Class Location">Class Location<a><span>?</span></a></label>
        </th>
        <td>
        <input class="title" name="class_location" size="80" value="<?php echo esc_attr( get_option('class_location') ); ?>"/>
        </td></tr>  
    <tr>
        <th style="text-align: right;">
        <label class="tooltip" title="Fall Semester Start Date">Fall Semester From<a><span>?</span></a></label> 
        </th>
        <td>
        <input type="date" name="fall_from" value="<?php echo esc_attr( get_option('fall_from') );?>"/>
        </td>
    </tr>
    <tr>
        <th style="text-align: right;">
        <label class="tooltip" title="Fall Semester End Date">Fall To<a><span>?</span></a></label> 
        </th>
        <td>
        <input type="date" name="fall_to" value="<?php echo esc_attr( get_option('fall_to') );?>"/>
        </td>
    </tr>
    <tr>
        <th style="text-align: right;">
        <label class="tooltip" title="Fall Semester Fee">Fall Fee<a><span>?</span></a></label> 
        </th>
        <td>
        $<input type="number" name="fall_fee" value="<?php echo esc_attr( get_option('fall_fee') );?>"/>
        </td>
    </tr>
    <tr>
        <th style="text-align: right;">
        <label class="tooltip" title="Spring Semester Start Date">Spring Semester From<a><span>?</span></a></label> 
        </th>
        <td>
        <input type="date" name="spring_from" value="<?php echo esc_attr( get_option('spring_from') );?>"/>
        </td>
    </tr>
    <tr>
        <th style="text-align: right;">
        <label class="tooltip" title="Spring Semester End Date">Spring To<a><span>?</span></a></label> 
        </th>
        <td>
        <input type="date" name="spring_to" value="<?php echo esc_attr( get_option('spring_to') );?>"/>
        </td>
    </tr>
    <tr>
        <th style="text-align: right;">
        <label class="tooltip" title="Spring Semester Fee">Spring Fee<a><span>?</span></a></label> 
        </th>
        <td>
        $<input type="number" name="spring_fee" value="<?php echo esc_attr( get_option('spring_fee') );?>"/>
        </td>
    </tr>
    <tr>
        <th style="text-align: right;">
        <label class="tooltip" title="Full Year Total Fee">Full Year Total<a><span>?</span></a></label> 
        </th>
        <td>
        $<input type="number" name="fullyear_fee" value="<?php echo esc_attr( get_option('fullyear_fee') );?>"/>
        </td>
    </tr>
    <br/>
    <tr>
        <th style="text-align: right;">
        <label class="tooltip" title="Class Start Time">Class Start Time<a><span>?</span></a></label> 
        </th>
        <td>
        <input type="time" name="class_start_time" value="<?php echo esc_attr( get_option('class_start_time') );?>"/>
        </td>
    </tr>
    <tr>
        <th style="text-align: right;">
        <label class="tooltip" title="Class End Time">Class End Time<a><span>?</span></a></label> 
        </th>
        <td>
        <input type="time" name="class_end_time" value="<?php echo esc_attr( get_option('class_end_time') );?>"/>
        </td>
    </tr>
    </table>  
  
    <?php submit_button(); ?>

</form>
</div>
<?php } ?>