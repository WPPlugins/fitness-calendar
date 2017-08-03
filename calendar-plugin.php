<?php
/*	Plugin Name: calendar plugin

    Plugin URI: http://worldwidewebsolution.com

    Description: This is fitness calendar plugin . you can also add up to 7 extra video with a main video of specific date with 	instruction of videos , title ,subtitle and some other features too.

    Author: worldwidewebsolution.com

	Version: 1.0

    Author URI: http://worldwidewebsolution.com */
	
/* Admin page */


// front end
function ft_calendar_front_scripts() {

	wp_enqueue_style( 'front-css', plugin_dir_url(__FILE__).'calendar.css', array(), null, 'all' );
	wp_enqueue_script( 'fitness-calendar', plugin_dir_url(__FILE__) .'calendar.js' , array('jquery'));
}
add_action('wp_enqueue_scripts', 'ft_calendar_front_scripts' );


// back end
function ft_calendar_admin_scripts() {
	wp_enqueue_style( 'admin-css', plugin_dir_url(__FILE__).'calendar.css', array(), null, 'all' );
wp_enqueue_script( 'fitness-calendar', plugin_dir_url(__FILE__) .'calendar.js' , array('jquery'));

	
}
add_action('admin_enqueue_scripts',	'ft_calendar_admin_scripts' );

/*
 * In this part you are going to define custom table list class,



 * that will display your database records in nice looking table

*/


   
if (!class_exists('WP_List_Table')) {



    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');



}


/**



 * Custom_Table_Example_List_Table class that will display our custom table



 * records in nice table



 */



class ft_calendar_Example_List_Table extends WP_List_Table {


    /**



     * [REQUIRED] You must declare constructor and give some basic params



     */

    function __construct() {



        global $status, $page;



        parent::__construct(array(



            'singular' => 'calendar',



            'plural' => 'calendars',



        ));



    }
	

function column_default($item, $column_name){
        switch($column_name){
            case 'day':
			case 'week_title' :
			case 'video_title' :
			case 'subtitle' :
			case 'equipment' :
			case 'rest_time' : 
            return $item[$column_name];
           default:
            //    return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }
	    /**



     * [OPTIONAL] this is example, how to render column with actions,



     * when you hover row "Edit" links showed

*/

  function column_day($item) {


          $actions = array(



            'edit' => sprintf('<a href="?page=add-new&id=%s">%s</a>', $item['id'], __('Edit', 'calendar')),

        );


       return sprintf('%s %s', $item['day'], $this->row_actions($actions)


      );
	  

    }


    /**



     * [REQUIRED] this is how checkbox column renders


*/	
	    function column_cb($item) {



        return sprintf(



                '<input type="checkbox" video_title="id[]" value="%s" />', $item['id']



        );



    }

    /**



     * [REQUIRED] This method return columns to display in table



     * you can skip columns that you do not want to show



     * like content, or description

*/


    function get_columns($columns = '') {


        $columns = array(


            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text

            'day' => __('Day', 'ft_calendar_table_example'),
			
					
			'week_title' => __('Week Title', 'ft_calendar_table_example'),
			
						

            'video_title' => __('Video Title', 'ft_calendar_table_example'),



            'subtitle' => __('Subtitle', 'ft_calendar_table_example'),



            'equipment' => __('Equipment', 'ft_calendar_table_example'),



            'rest_time' => __('Rest Time', 'ft_calendar_table_example')
        );

        return $columns;
    }

    /**



     * [OPTIONAL] This method return columns that may be used to sort table



     * all strings in array - is column names



     * notice that true on name column means that its default sort

*/

    function get_sortable_columns() {



        $sortable_columns = array(

			
			'day' => array('day', true),

			
			'week_title' => array('week_title', false),
			
			
            'video_title' => array('video_title', false),

            'subtitle' => array('subtitle', false),



            'equipment' => array('equipment', false),

            'rest_time' => array('rest_time', false)


        );





        return $sortable_columns;



    }


    /**



     * [REQUIRED] This is the most important method


     * It will get rows from database and prepare them to be showed in table



     */


    function prepare_items() {



        global $wpdb;



        $table_name = $wpdb->prefix . 'calendar'; // do not forget about tables prefix



//print_r($table_name);



        $per_page = 5; // constant, how much records will be shown per page



        $columns = $this->get_columns();

//print_r($columns);

        $hidden = array();



        $sortable = $this->get_sortable_columns();




        // here we configure table headers, defined in our methods



        $this->_column_headers = array($columns, $hidden, $sortable);



        // [OPTIONAL] process bulk action if any



        $this->process_bulk_action();




        // will be used in pagination settings



        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");


        // prepare query params, as usual current page, order by and order direction



        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged'] -1) * 5) : 0;

//$paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged'] -1) * 50) : 0;



        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'id';



        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'ASC';


        // [REQUIRED] define $items array



        // notice that last argument is ARRAY_A, so we will retrieve array



        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);
		//$this->items = $records;

        // [REQUIRED] configure pagination



        $this->set_pagination_args(array(



            'total_items' => $total_items, // total items defined above



            'per_page' => $per_page, // per page constant defined at top of method



            'total_pages' => ceil($total_items / $per_page) // calculate pages count



        ));


    }

}


/* show menu to dashboard*/
function ft_calendar_admin_actions() {

 add_menu_page("Calendar plugin", "Calendar plugin", 1, "calendar-plugin", "ft_calendar_admin");


    add_submenu_page('calendar-plugin', __('Listing', 'calendar-plugin'), __('Listing', 'calendar-plugin'), 'activate_plugins', 'calendar-plugin', 'ft_calendar_admin');
	
    add_submenu_page('calendar-plugin', __('Add New', 'calendar-plugin'), __('Add New', 'calendar-plugin'), 'activate_plugins', 'add-new', 'ft_calendar_add_new_form_handler');
	
}
add_action('admin_menu', 'ft_calendar_admin_actions');



/**



 * List page handler



 *



 * This function renders our custom table



 * Notice how we display message about successfull deletion



 * Actualy this is very easy, and you can add as many features



 * as you want.



 *



 * Look into /wp-admin/includes/class-wp-*-list-table.php for examples



 */


function ft_calendar_admin() {

	

    global $wpdb;




    $table = new ft_calendar_Example_List_Table();



    $table->prepare_items();



//print_r($table);

    $message = '';




    if ('delete' === $table->current_action()) {



        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted : %d', 'ft_calendar_table_example'), count($_REQUEST['id'])) . '</p></div>';



    }

    ?>
<div class="wrap">

  <div class="icon32 icon32-posts-post" id="icon-edit"><br>

  </div>

  <h2>
<?php _e('Video Listing', 'ft_calendar_table_example') ?>

    <a class="add-new-h2"  href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=add-new'); ?>">

<?php _e('Add New Video', 'ft_calendar_table_example') ?>

    </a> </h2>

  <?php echo $message;  ?>

  <form id="video-table" method="GET">

    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>

    <?php $table->display() ?>

  </form>
</div>
<?php


}	
/**



 * PART 4. Form for adding andor editing row



 * ============================================================================



 *



 * In this part you are going to add admin page for adding andor editing items



 * You cant put all form into this function, but in this example form will



 * be placed into meta box, and if you want you can split your form into



 * as many meta boxes as you want



 */

function ft_calendar_add_new_form_handler() { 


    global $wpdb;



    $table_name = $wpdb->prefix . 'calendar'; // do not forget about tables prefix
	
	    $message = '';



    $notice = '';

    // this is default $item which will be used for new records





  $default = array(



        'id' => '',

		
		'week_title' => '',


        'day' => '',



        'video' => '',


        'video_title' => '',



        'subtitle' => '',



        'equipment' => '',



        'rest_time' => '',


    );







    // here we are verifying does this request is post back and have correct nonce



    if (wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {



        // combine our default item with request params



        $item = shortcode_atts($default, $_REQUEST);


//echo"<pre>";
//print_r($item);

        // validate data, and if all ok save item to database



        // if id is zero insert otherwise update



        $item_valid = ft_calendar_table_example_validate_video($item);



        if ($item_valid === true) {
		

             //   $result = $wpdb->update($table_name, $item);

        } 



    } else {



        // if this is not post back we load item to edit or give new one to create



        $item = $default;

        if (isset($_REQUEST['id'])) {



            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);

        }

}


    add_meta_box('add_new_form_meta_box', 'Add New Video', 'ft_ft_calendar_table_example_add_new_form_meta_box_handler', 'video', 'normal', 'default');


    ?>
<div class="wrap">

  <div class="icon32 icon32-posts-post" id="icon-edit"><br>

  </div>

  <h2>

    <?php _e('Calendar', 'ft_calendar_table_example') ?>

    <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=calendar-plugin'); ?>">

    <?php _e('back to list', 'ft_calendar_table_example') ?>

    </a> </h2>

  <?php if (!empty($notice)): ?>

  <div id="notice" class="error">

    <p><?php echo $notice ?></p>

  </div>
  <?php endif; ?>

  <?php if (!empty($message)): ?>

  <div id="message" class="updated">

    <p><?php echo $message ?></p>

  </div>
  <?php endif; ?>

  <form id="form" method="POST" enctype="multipart/form-data">

    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__)) ?>"/>

     <!--NOTICE: here we storing id to determine will be item added or updated--> 

    <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

    <div class="metabox-holder" id="poststuff">

      <div id="post-body">

        <div id="post-body-content">

          <!-- /* And here we call our custom meta box */ -->
          <?php do_meta_boxes('video', 'normal', $item); ?>
          <input type="submit" value="<?php _e('Save', 'ft_calendar_table_example') ?>" id="submit" class="button-primary" name="submit">

        </div>

      </div>

    </div>

  </form>

</div>
<?php
}

/**



 * This function renders our custom meta box



 * $item is row

*/


function ft_ft_calendar_table_example_add_new_form_meta_box_handler($item) {


    ?>
<div class="check">
 <h1>Welcome to 3 Month Calendar plugin</h1>
  <form class='basic-grey' id="calendar"  method="post" action="" >
    <label>
        <span>Week Title :</span>
        <input id="week_title" type="text" name="week_title" placeholder="<?php _e('Week Title', 'ft_calendar_table_example') ?>"  value="<?php echo esc_attr($item['week_title']) ?>"/>
    </label>
	<br />
    <label>
        <span>Day :</span>
        <input id="day" type="number" name="day" maxlength="2"  placeholder="<?php _e('Day', 'ft_calendar_table_example') ?>"  value="<?php echo esc_attr($item['day']) ?>"/>
    </label>
	<br />
     <label>
        <span> Add Video URL</span>
        <input id="video" type="text" name="video" placeholder="<?php _e('https://www.youtube.com/embed/N2CJrhHEydA', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['video']) ?>"/>
    </label>
	<br />
     <label>
        <span> Video Title</span>
        <input id="video_title" type="text" name="video_title" placeholder="<?php _e('Video Title', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['video_title']) ?>" />
    </label>
	<br />
     <label>
        <span> Subtitle </span>
        <input id="subtitle" type="text" name="subtitle" placeholder="<?php _e('Video Subtitle', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['subtitle']) ?>" />
    </label>
	<br />
     <label>
        <span> Equipements </span>
        <input id="equipement" type="text" name="equipment" placeholder="<?php _e('Exercise Equipment', 'ft_calendar_table_example') ?>"  value="<?php echo esc_attr($item['equipment']) ?>"/>
    </label>
	<br />
	    <label>
        <span>Description :</span>
        <textarea id="description" name="description" placeholder="<?php _e('Workout Description', 'ft_calendar_table_example') ?>"  value="<?php echo esc_attr($item['description']) ?>"></textarea>
    </label>
	<br />
	     <label>
        <span> Rest Time </span>
        <input id="rest_time" type="text" name="rest_time" placeholder="<?php _e('Rest Time', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['rest_time']) ?>" />
    </label>
	<br />
	<h1>EXTRA Videos</h1>
	<hr />
			<br />
			
			<h1>Video 1</h1>
     <label>
        <span> Add Video TITLE-1</span>
        <input id="video_title1" type="text" name="video_title1" placeholder="<?php _e('Add Video TITLE-1', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['video_title1']) ?>"/>
    </label>

		<br />
     <label>
        <span> Add Video URL-1</span>
        <input id="video1" type="text" name="video1" placeholder="<?php _e('Your Video URL-1', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['video1']) ?>"/>
    </label>
		<br />
     <label>
        <span> Add Video-1 Sets</span>
        <input id="set1" type="text" name="set1" placeholder="<?php _e('Video-1 Sets', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['set1']) ?>"/>
    </label>
		<br />
     <label>
        <span> Add Video-1 REPS</span>
        <input id="rep1" type="text" name="rep1" placeholder="<?php _e('Video-1 REPS', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['rep1']) ?>"/>
    </label>
		<br />
     <label>
        <span> Video-1 Description</span>
        <input id="description1" type="text" name="description1" placeholder="<?php _e('Video-1 Description', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['description1']) ?>"/>
    </label>
				<br />
				<h1>Video 2</h1>
     <label>
        <span> Add Video TITLE-2</span>
        <input id="video_title2" type="text" name="video_title2" placeholder="<?php _e('Add Video TITLE-2', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['video_title2']) ?>"/>
    </label>

		<br />
     <label>
        <span> Add Video URL-2</span>
        <input id="video2" type="text" name="video2" placeholder="<?php _e('Your Video URL-2', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['video2']) ?>"/>
    </label>
		<br />
     <label>
        <span> Add Video-2 Sets</span>
        <input id="set2" type="text" name="set2" placeholder="<?php _e('Video-2 Sets', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['set2']) ?>"/>
    </label>
		<br />
     <label>
        <span> Add Video-2 REPS</span>
        <input id="rep2" type="text" name="rep2" placeholder="<?php _e('Video-2 REPS', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['rep2']) ?>"/>
    </label>
		<br />
     <label>
        <span> Video-2 Description</span>
        <input id="description2" type="text" name="description2" placeholder="<?php _e('Video-2 Description', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['description2']) ?>"/>
    </label>
	
				<br />
				<h1>Video 3</h1>
     <label>
        <span> Add Video TITLE-3</span>
        <input id="video_title3" type="text" name="video_title3" placeholder="<?php _e('Add Video TITLE-3', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['video_title3']) ?>"/>
    </label>

		<br />
     <label>
        <span> Add Video URL-3</span>
        <input id="video3" type="text" name="video3" placeholder="<?php _e('Your Video URL-3', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['video3']) ?>"/>
    </label>
		<br />
     <label>
        <span> Add Video-3 Sets</span>
        <input id="set3" type="text" name="set3" placeholder="<?php _e('Video-3 Sets', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['set3']) ?>"/>
    </label>
		<br />
     <label>
        <span> Add Video-3 REPS</span>
        <input id="rep3" type="text" name="rep3" placeholder="<?php _e('Video-3 REPS', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['rep3']) ?>"/>
    </label>
		<br />
     <label>
        <span> Video-3 Description</span>
        <input id="description3" type="text" name="description3" placeholder="<?php _e('Video-3 Description', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['description3']) ?>"/>
    </label>
				<br />
				<h1>Video 4</h1>
     <label>
        <span> Add Video TITLE-4</span>
        <input id="video_title4" type="text" name="video_title4" placeholder="<?php _e('Add Video TITLE-4', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['video_title4']) ?>"/>
    </label>

		<br />
     <label>
        <span> Add Video URL-4</span>
        <input id="video4" type="text" name="video4" placeholder="<?php _e('Your Video URL-4', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['video4']) ?>"/>
    </label>
		<br />
     <label>
        <span> Add Video-4 Sets</span>
        <input id="set4" type="text" name="set4" placeholder="<?php _e('Video-4 Sets', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['rep4']) ?>"/>
    </label>
		<br />
     <label>
        <span> Add Video-4 REPS</span>
        <input id="rep4" type="text" name="rep4" placeholder="<?php _e('Video-4 REPS', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['set1']) ?>"/>
    </label>
		<br />
     <label>
        <span> Video-4 Description</span>
        <input id="description4" type="text" name="description4" placeholder="<?php _e('Video-4 Description', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['description4']) ?>"/>
    </label>
				<br />
				<h1>Video 5</h1>
     <label>
        <span> Add Video TITLE-5</span>
        <input id="video_title5" type="text" name="video_title5" placeholder="<?php _e('Add Video TITLE-5', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['video_title5']) ?>"/>
    </label>

		<br />
     <label>
        <span> Add Video URL-5</span>
        <input id="video5" type="text" name="video5" placeholder="<?php _e('Your Video URL-5', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['video5']) ?>"/>
    </label>
		<br />
     <label>
        <span> Add Video-5 Sets</span>
        <input id="set5" type="text" name="set5" placeholder="<?php _e('Video-5 Sets', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['set5']) ?>"/>
    </label>
		<br />
     <label>
        <span> Add Video-5 REPS</span>
        <input id="rep5" type="text" name="rep5" placeholder="<?php _e('Video-5 REPS', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['rep5']) ?>"/>
    </label>
		<br />
     <label>
        <span> Video-5 Description</span>
        <input id="description5" type="text" name="description5" placeholder="<?php _e('Video-5 Description', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['description5']) ?>"/>
    </label>
				<br />
				<h1>Video 6</h1>
     <label>
        <span> Add Video TITLE-6</span>
        <input id="video_title6" type="text" name="video_title6" placeholder="<?php _e('Add Video TITLE-6', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['video_title6']) ?>"/>
    </label>

		<br />
     <label>
        <span> Add Video URL-6</span>
        <input id="video6" type="text" name="video6" placeholder="<?php _e('Your Video URL-6', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['video6']) ?>"/>
    </label>
		<br />
     <label>
        <span> Add Video-6 Sets</span>
        <input id="set6" type="text" name="set6" placeholder="<?php _e('Video-6 Sets', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['set6']) ?>"/>
    </label>
		<br />
     <label>
        <span> Add Video-6 REPS</span>
        <input id="rep6" type="text" name="rep6" placeholder="<?php _e('Video-6 REPS', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['rep6']) ?>"/>
    </label>
		<br />
     <label>
        <span> Video-6 Description</span>
        <input id="description6" type="text" name="description6" placeholder="<?php _e('Video-6 Description', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['description6']) ?>"/>
    </label>

				<br />
				<h1>Video 7</h1>
     <label>
        <span> Add Video TITLE-7</span>
        <input id="video_title7" type="text" name="video_title7" placeholder="<?php _e('Add Video TITLE-7', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['video_title7']) ?>"/>
    </label>

		<br />
     <label>
        <span> Add Video URL-7</span>
        <input id="video7" type="text" name="video7" placeholder="<?php _e('Your Video URL-7', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['video7']) ?>"/>
    </label>
		<br />
     <label>
        <span> Add Video-7 Sets</span>
        <input id="set7" type="text" name="set7" placeholder="<?php _e('Video-7 Sets', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['set7']) ?>"/>
    </label>
		<br />
     <label>
        <span> Add Video-7 REPS</span>
        <input id="rep7" type="text" name="rep7" placeholder="<?php _e('Video-7 REPS', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['rep7']) ?>"/>
    </label>
		<br />
     <label>
        <span> Video-7 Description</span>
        <input id="description7" type="text" name="description7" placeholder="<?php _e('Video-7 Description', 'ft_calendar_table_example') ?>" value="<?php echo esc_attr($item['description7']) ?>"/>
    </label>

       

  </form>
  <p>Short code to show calendar in page is [calendar]</p>
 

  </div>
<?php

}


/**

 * Simple function that validates data and retrieve bool on success



 * and error message(s) on error


*/

function ft_calendar_table_example_validate_video($item) {



    $messages = array();



    if (empty($item['day']))



        $messages[] = __('Day required', 'ft_calendar_table_example');


    if (empty($item['video']))



        $messages[] = __('Video URL Required', 'ft_calendar_table_example');



    if (empty($item['video_title']))


        $messages[] = __('Video Title Required', 'custom_table_example');


    if (empty($messages))



        return true;



    return implode('<br />', $messages);



}

/**



 * Do not forget about translating your plugin, use __('english string', 'your_uniq_plugin_name') to retrieve translated string



 * and _e('english string', 'your_uniq_plugin_name') to echo it

*/

function ft_calendar_table_example_languages() {



    load_plugin_textdomain('ft_calendar_table_example', false, dirname(plugin_basename(__FILE__)));



}

add_action('init', 'ft_calendar_table_example_languages');



function ft_calendar_database() {
global $jal_db_version;

$jal_db_version = '1.0';


	global $wpdb;

	global $calendar;



	$table_name = $wpdb->prefix . 'calendar';

	

	//$charset_collate = $wpdb->get_charset_collate();



	$sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (

		`id` mediumint(9) NOT NULL AUTO_INCREMENT,
		
		`month` tinytext NOT NULL,
		
		`week` tinytext NOT NULL,
		
		`week_title` varchar(1000) NOT NULL,

		`day` tinytext NOT NULL,

		`video` varchar(1000) NOT NULL,
		
		`video_title` varchar(1000) NOT NULL,
		
		`subtitle` varchar(1000) NOT NULL,
		
		`description` varchar(1000) NOT NULL,
		
		`video1` varchar(1000) NOT NULL,
		
		`video_title1` varchar(1000) NOT NULL,
		
		`set1` varchar(1000) NOT NULL,
		
		`rep1` varchar(1000) NOT NULL,
		
		`description1` varchar(1000) NOT NULL,
		
		`video2` varchar(1000) NOT NULL,
		
		`video_title2` varchar(1000) NOT NULL,
		
		`set2` varchar(1000) NOT NULL,
		
		`rep2` varchar(1000) NOT NULL,
		
		`description2` varchar(1000) NOT NULL,
		
		`video3` varchar(1000) NOT NULL,
		
		`video_title3` varchar(1000) NOT NULL,
		
		`set3` varchar(1000) NOT NULL,
		
		`rep3` varchar(1000) NOT NULL,
		
		`description3` varchar(1000) NOT NULL,
		
		`video4` varchar(1000) NOT NULL,
		
		`video_title4` varchar(1000) NOT NULL,
		
		`set4` varchar(1000) NOT NULL,
		
		`rep4` varchar(1000) NOT NULL,
		
		`description4` varchar(1000) NOT NULL,
		
		`video5` varchar(1000) NOT NULL,
		
		`video_title5` varchar(1000) NOT NULL,
		
		`set5` varchar(1000) NOT NULL,
		
		`rep5` varchar(1000) NOT NULL,
		
		`description5` varchar(1000) NOT NULL,
		
		`video6` varchar(1000) NOT NULL,
		
		`video_title6` varchar(1000) NOT NULL,
		
		`set6` varchar(1000) NOT NULL,
		
		`rep6` varchar(1000) NOT NULL,
		
		`description6` varchar(1000) NOT NULL,
		
		`video7` varchar(1000) NOT NULL,
		
		`video_title7` varchar(1000) NOT NULL,
		
		`set7` varchar(1000) NOT NULL,
		
		`rep7` varchar(1000) NOT NULL,
		
		`description7` varchar(1000) NOT NULL,

		`equipment` varchar(1000) NOT NULL,

		`rest_time` varchar(1000) NOT NULL,

		PRIMARY KEY (`id`)

	) ";



	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	dbDelta( $sql );



	add_option( 'jal_db_version', $jal_db_version );

}



function ft_calendar_data_insert() {

	global $wpdb;

	

for($i=1;$i<=84;$i++)


	{

	$table_name = $wpdb->prefix . 'calendar';

	

	$wpdb->insert( 

		$table_name, 

		array( 

			'day' => $i, 

		) 

	);

	}

}



register_activation_hook( __FILE__, 'ft_calendar_database' );

register_activation_hook( __FILE__, 'ft_calendar_data_insert' );


function jal_db_update_check() {


    global $jal_db_version;




    if (get_site_option('jal_db_version') != $jal_db_version) {



        ft_calendar_database();

    }


}

add_action('plugins_loaded', 'jal_db_update_check');

register_uninstall_hook(__FILE__, 'ft_calendar_uninstall');

function ft_calendar_uninstall(){
	
	//drop a custom db table
	global $wpdb;
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}calendar" );
	
	
	}
	


function ft_calendar_front($attr){
    global $wpdb;
       
$table_name = $wpdb->prefix.'calendar';

        $querystr = "SELECT * FROM $table_name";
	
        $result = $wpdb->get_results($querystr, OBJECT);

if($_GET[month] == 2) { $day = 29; $week = 5;}
	
	else if ($_GET[month] == 3) { $day = 57; $week = 9;}
	
	else if($_GET[month] == 1) { $day = 1; $week = 1;}
	
	else { $day =1; $week = 1;}

 
        if ($result) {
 ?>
	<form id="frmcalendar" name="frmcalendar" method="get" action="<?php echo get_permalink(); ?>"> 
      <table class="calendar" border="1" cellpadding="0" cellspacing="0">
	 
        <tbody>
		
          <tr>
		  
            <td colspan="7" class="calendar_td"><a name="week1"></a>
              <p><span class="weekLbl">Week #<?php echo $week;?> - </span><?php  if ( $result[$week-1]->week == $week ) { echo $result[$week-1]->week_title; }?></p></td>
			 
          </tr>
		
          <tr class="calendar-row">
<?php 		$j = $week+1; 
			 for($i=$day;$i<28+$day;$i++){  
			   
			 if ($_GET[calendar_day] == $i) 
			 { 
			 echo "<style>";
			 echo ".calendar{ display:none;}";
			 echo "</style>";
			 
			 echo "<div class='calendar_main_div'>";
			 echo "<div class='calendar_main_div_1'>";
			echo "<h3 style='color:white; background-color:red;'>";
			echo  $result[$i-1]->video_title;
			echo "</h3>";
			
			echo "<h3 style='color:white; background-color:grey;'>";
			echo  $result[$i-1]->week_title;
			echo "</h3>";
			
			echo "<center>";
			echo "<h3>";
			echo  $result[$i-1]->subtitle;
			echo "</h3>";
			echo "</center>";
			
			echo "<div class='calendar_video'>";
			
?>

		<center>	<iframe id="video_iframe_main" width="600" height="300" src="<?php echo $result[$i-1]->video; ?>" frameborder="0" allowfullscreen ></iframe> </center>
<?php

			echo "</div>";
			
			echo "<div class='section_below_video'>";
			
			echo "<div class ='calendar_equipment'>";
			echo  "<h3>Eqipment</h3>";
			echo  $result[$i-1]->equipment;
			echo "</div>";
			
			echo "<div class ='calendar_rest_time'>";
			echo  "<h3>Rest Time</h3>";
			echo  $result[$i-1]->rest_time;
			echo "</div>";
			
			echo "</div>";
			echo "</div>";
			
			echo "<div class='calendar_main_div_2'>";
			echo "<center>";
			echo "<div class='calendar_button'>";
			echo "<input type='button' id='calendar_extra_videos' class='button' value='workout'></button>";
			echo "<input type='button' id='calendar_description' class='button' value='Description'></button>";
			echo "</div>";
			echo "</center>";
			echo "</div>";
			
			echo "<div class = 'video_description'>";
			echo  "<h3>Description</h3>";
			echo $result[$i-1]->description;
			echo "</div>";
			
			echo "<div class='calendar_extra_videos'>";
			echo "<div class='extra_vid'>";
			echo  "<h3>Workout</h3>";
			if ($result[$i-1]->video1 != NULL) {
			echo "<div class='calendar_extra_video1'>";
?>
			<div class="iframe">
			<iframe class="video_iframe" width="200" height="120" src="<?php echo $result[$i-1]->video1; ?>" frameborder="0" allowfullscreen ></iframe>
			</div>
<?php
		//	echo $result[$i-1]->video1;
			echo "</div>";
			echo "<div class='video_data'>";
			echo "<b>1: </b> ";
			echo "<b>";
			echo $result[$i-1]->video_title1;
			echo "</b>";
			echo "<br>";
			echo "<b>SETS:</b> ";
			echo $result[$i-1]->set1;
			echo "<br>";
			echo "<b>REPS:</b> ";
			echo $result[$i-1]->rep1;
			echo "<br>";
			
			echo $result[$i-1]->description1;
			echo "</div>";
			
			echo "</div>";
			}
			if ($result[$i-1]->video2 != NULL) {
			echo "<div class='extra_vid'>";
			
			echo "<div class='calendar_extra_video2'>";
?>
			<div class="iframe">
			<iframe class="video_iframe" width="200" height="120" src="<?php echo $result[$i-1]->video2; ?>" frameborder="0" allowfullscreen ></iframe>
			</div>
<?php
			//echo $result[$i-1]->video2;
			echo "</div>";
			
			echo "<div class='video_data'>";
			echo "<b>2: </b> ";
			echo "<b>";
			echo $result[$i-1]->video_title2;
			echo "</b>";
			echo "<br>";
			echo "<b>SETS:</b> ";
			echo $result[$i-1]->set2;
			echo "<br>";
			echo "<b>REPS:</b> ";
			echo $result[$i-1]->rep2;
			echo "<br>";
			echo $result[$i-1]->description2;
			echo "</div>";
			
			echo "</div>";
			}
			if ($result[$i-1]->video3 != NULL) {
			echo "<div class='extra_vid'>";
			echo "<div class='calendar_extra_video3'>";
			?>

						<div class="iframe">
			<iframe class="video_iframe" width="200" height="120" src="<?php echo $result[$i-1]->video3; ?>" frameborder="0" allowfullscreen ></iframe>
			</div>
<?php

			//echo $result[$i-1]->video3;
			echo "</div>";
			echo "<div class='video_data'>";
			echo "<b>3: </b> ";
			echo "<b>";
			echo $result[$i-1]->video_title3;
			echo "</b>";
			echo "<br>";
			echo "<b>SETS:</b> ";
			echo $result[$i-1]->set3;
			echo "<br>";
			echo "<b>REPS:</b> ";
			echo $result[$i-1]->rep3;
			echo "<br>";
			echo $result[$i-1]->description3;
			echo "</div>";
			echo "</div>";
			}
			if ($result[$i-1]->video4 != NULL) {
			echo "<div class='extra_vid'>";
			echo "<div class='calendar_extra_video4'>";

?>
			<div class="iframe">
			<iframe class="video_iframe" width="200" height="120" src="<?php echo $result[$i-1]->video4; ?>" frameborder="0" allowfullscreen ></iframe>
			</div>
<?php

			//echo $result[$i-1]->video4;
			echo "</div>";
			echo "<div class='video_data'>";
			echo "<b>4: </b> ";
			echo "<b>";
			echo $result[$i-1]->video_title4;
			echo "</b>";
			echo "<br>";
			echo "<b>SETS:</b> ";
			echo $result[$i-1]->set4;
			echo "<br>";
			echo "<b>REPS:</b> ";
			echo $result[$i-1]->rep4;
			echo "<br>";
			echo $result[$i-1]->description4;
			echo "</div>";
			echo "</div>";
			}
			if ($result[$i-1]->video5 != NULL) {
			echo "<div class='extra_vid'>";
			echo "<div class='calendar_extra_video5'>";
						?>
			<div class="iframe">
			<iframe class="video_iframe" width="200" height="120" src="<?php echo $result[$i-1]->video5; ?>" frameborder="0" allowfullscreen ></iframe>
			</div>
<?php			
			//echo $result[$i-1]->video5;
			echo "</div>";
			echo "<div class='video_data'>";
			echo "<b>5: </b> ";
			echo "<b>";
			echo $result[$i-1]->video_title5;
			echo "</b>";
			echo "<br>";
			echo "<b>SETS:</b> ";
			echo $result[$i-1]->set5;
			echo "<br>";
			echo "<b>REPS:</b> ";
			echo $result[$i-1]->rep5;
			echo "<br>";
			echo $result[$i-1]->description5;
			echo "</div>";
			echo "</div>";
			}
			if ($result[$i-1]->video6 != NULL) {
			
			echo "<div class='extra_vid'>";
			echo "<div class='calendar_extra_video6'>";
						?>
			<div class="iframe">
			<iframe class="video_iframe" id="video_frame1" width="200" height="120" src="<?php echo $result[$i-1]->video6; ?>" frameborder="0" allowfullscreen ></iframe>
			</div>
<?php
			//echo $result[$i-1]->video6;
			echo "</div>";
			echo "<div class='video_data'>";
			echo "<b>6: </b> ";
			echo "<b>";
			echo $result[$i-1]->video_title6;
			echo "</b>";
			echo "<br>";
			echo "<b>SETS:</b> ";
			echo $result[$i-1]->set6;
			echo "<br>";
			echo "<b>REPS:</b> ";
			echo $result[$i-1]->rep6;
			echo "<br>";
			echo $result[$i-1]->description6;
			echo "</div>";
			echo "</div>";
			}
			if ($result[$i-1]->video7 != NULL) {
			echo "<div class='extra_vid'>";
			echo "<div class='calendar_extra_video7'>";
?>
			<div class="iframe">
			<iframe class="video_iframe" width="200" height="120" src="<?php echo $result[$i-1]->video7; ?>" frameborder="0" allowfullscreen ></iframe>
			</div>
			<?php
		//	echo $result[$i-1]->video7;
			echo "</div>";
			echo "<div class='video_data'>";
			echo "<b>7: </b> ";
			echo "<b>";
			echo $result[$i-1]->video_title7;
			echo "</b>";
			echo "<br>";
			echo "<b>SETS:</b> ";
			echo $result[$i-1]->set7;
			echo "<br>";
			echo "<b>REPS:</b> ";
			echo $result[$i-1]->rep7;
			echo "<br>";
			echo $result[$i-1]->description7;
			echo "</div>";
			echo "</div>";
			}
			echo "</div>"; 
			
		//	echo "<center>";
	//		echo "<input type='button' id='mark_completed' value='Mark Completed'></button>";
		//	echo "</center>";
			echo "</div>";
			exit;}
			  ?>
            <td><div class="dayCell"> 
                <div class="cellDayNumber"><?php echo $i;?> </div>
                <span class="workout"> <a href="<?php echo get_permalink();?>?calendar_day=<?php echo $i; ?>" class="calendar-day-active"> <?php echo $result[$i-1]->video_title;?></a> </span> </div> 
              <a class="dayAnchor" name="day1"></a>
              <!-- Anchor Tag -->
              <?php   echo " </td>"; if($i%7==0 && $i!=1 && $i < (28+$day-1) ){echo "</tr> <tr'>
            <td colspan='7' ><a name='week'></a><p><span class='weekLbl'>Week #".$j."- </span> ";?> <?php if ( $result[$i]->week == $j ) { echo $result[$i]->week_title; } ?></p></td></tr><tr class="calendar-row"><?php $j= $j+1;} }  ?> 
          </tr>
		  <tr><ul class="pager">
  <li><a href="<?php echo get_permalink(); ?>?month=1 ">Month 1</a></li>
  <li><a href="<?php echo get_permalink(); ?>?month=2 ">Month 2</a></li>
  <li><a href="<?php echo get_permalink(); ?>?month=3 ">Month 3</a></li>
</ul></tr>
        </tbody>
      </table>
</form>
<?php
        } 

   }
   
/*.......... Updating database by admin form...........  */
   
global $wpdb;

if (isset($_REQUEST['submit'])) {
//require('../wp-load.php');
//echo "<pre>";

$table_name = $wpdb->prefix . 'calendar';

$week_title = $_REQUEST['week_title'];

$day = $_REQUEST['day'];

if($day<=28){ $month = 1;}

elseif(($day>=29) && ($day<=56)) { $month = 2;}

else { $month = 3;}

$month ;

if($day<=7){ $week = 1;}

elseif (($day>=7) && ($day<=14)){ $week = 2;}

elseif (($day>=14) && ($day<=21)) { $week = 3;}

elseif (($day>=21) && ($day<=28)) { $week = 4;}

elseif (($day>=28) && ($day<=35)) { $week = 5;}

elseif (($day>=35) && ($day<=42)) { $week = 6;}

elseif (($day>=42) && ($day<=49)) { $week = 7;}

elseif (($day>=49) && ($day<=56)) { $week = 8;}

elseif (($day>=56) && ($day<=63)) { $week = 9;}

elseif (($day>=63) && ($day<=70)) { $week = 10;}

elseif (($day>=70) && ($day<=77)) { $week = 11;}

elseif (($day>=77) && ($day<=84)) { $week = 12;}

$week;

$video_title = sanitize_text_field($_REQUEST['video_title']);


$video = sanitize_text_field($_REQUEST['video']);

$subtitle = sanitize_text_field($_REQUEST['subtitle']);

$description = sanitize_text_field($_REQUEST['description']);

$video_title1 = sanitize_text_field($_REQUEST['video_title1']);

$video1 = sanitize_text_field($_REQUEST['video1']);

$set1 = sanitize_text_field($_REQUEST['set1']);

$rep1 = sanitize_text_field($_REQUEST['rep1']);

$description1 = sanitize_text_field($_REQUEST['description1']);

$video_title2 = sanitize_text_field($_REQUEST['video_title2']);

$video2 = sanitize_text_field($_REQUEST['video2']);

$set2 = sanitize_text_field($_REQUEST['set2']);

$rep2 = sanitize_text_field($_REQUEST['rep2']);

$description2 = sanitize_text_field($_REQUEST['description2']);

$video_title3 = sanitize_text_field($_REQUEST['video_title3']);

$video3 = sanitize_text_field($_REQUEST['video3']);

$set3 = sanitize_text_field($_REQUEST['set3']);

$rep3 = sanitize_text_field($_REQUEST['rep3']);

$description3 = sanitize_text_field($_REQUEST['description3']);

$video_title4 = sanitize_text_field($_REQUEST['video_title4']);

$video4 = sanitize_text_field($_REQUEST['video4']);

$set4 = sanitize_text_field($_REQUEST['set4']);

$rep4 = sanitize_text_field($_REQUEST['rep4']);

$description4 = sanitize_text_field($_REQUEST['description4']);

$video_title5 = sanitize_text_field($_REQUEST['video_title5']);

$video5 = sanitize_text_field($_REQUEST['video5']);

$set5 = sanitize_text_field($_REQUEST['set5']);

$rep5 = sanitize_text_field($_REQUEST['rep5']);

$description5 = sanitize_text_field($_REQUEST['description5']);

$video_title6 = sanitize_text_field($_REQUEST['video_title6']);

$video6 = sanitize_text_field($_REQUEST['video6']);

$set6 = sanitize_text_field($_REQUEST['set6']);

$rep6 = sanitize_text_field($_REQUEST['rep6']);

$description6 = sanitize_text_field($_REQUEST['description6']);

$video_title7 = sanitize_text_field($_REQUEST['video_title7']);

$video7 = sanitize_text_field($_REQUEST['video7']);

$set7 = sanitize_text_field($_REQUEST['set7']);

$rep7 = sanitize_text_field($_REQUEST['rep7']);

$description7 = sanitize_text_field($_REQUEST['description7']);


$equipment = sanitize_text_field($_REQUEST['equipment']);

$rest_time = sanitize_text_field($_REQUEST['rest_time']);

//$video = "111";
   
 // print_r($table_name);
 
$result_data = $wpdb->update( $table_name, array(
    'month' => $month,
	'week' => $week,
	'week_title' => $week_title,
	'video_title' => $video_title,
    'video' => $video,
	'subtitle' => $subtitle,
	'description' => $description,
	'video_title1' => $video_title1,
    'video1' => $video1,
	'set1' => $set1,
	'rep1' => $rep1,
	'description1' => $description1,
	'video_title2' => $video_title2,
    'video2' => $video2,
	'set2' => $set2,
	'rep2' => $rep2,
	'description2' => $description2,
	'video_title3' => $video_title3,
    'video3' => $video3,
	'set3' => $set3,
	'rep3' => $rep3,
	'description3' => $description3,
	'video_title4' => $video_title4,
    'video4' => $video4,
	'set4' => $set4,
	'rep4' => $rep4,
	'description4' => $description4,
	'video_title5' => $video_title5,
    'video5' => $video5,
	'set5' => $set5,
	'rep5' => $rep5,
	'description5' => $description5,
	'video_title6' => $video_title6,
    'video6' => $video6,
	'set6' => $set6,
	'rep6' => $rep6,
	'description6' => $description6,
	'video_title7' => $video_title7,
    'video7' => $video7,
	'set7' => $set7,
	'rep7' => $rep7,
	'description7' => $description7,
	'equipment' => $equipment,
	'rest_time' => $rest_time
	
  ), 
array('id' => $day),
  array('%d','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s'),
  array('%d') );
                if ($result_data === FALSE) {

	  $notice = __('There was an error while saving item', 'ft_calendar_table_example');
                   

                } else {

                  
 $message = __('Item was successfully saved', 'ft_calendar_table_example');

                }

//echo $wpdb->last_query; exit;
}


add_shortcode('calendar', 'ft_calendar_front'); 

?>