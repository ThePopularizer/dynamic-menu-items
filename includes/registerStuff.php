<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/***************** Admin Scripts *****************/
function BBWDDYNOMENUITM_dm_enqueue_scripts(){
	$bbwdFlexStylesHandle = 'bbwd-dyna';
	if (!wp_script_is( $bbwdFlexStylesHandle, 'registered' )) {
		wp_register_style('bbwd-dyna', BBWDDYNOMNUITM_URL.'/assets/dymenu-css.css', array(), BBWDDYNOMNUITM_VERSION);
	} 
	if (!wp_script_is( $bbwdFlexStylesHandle, 'enqueued' )) {
		wp_enqueue_style('bbwd-dyna');
	}
	
	wp_register_script( 'bbwddymenuitm-js', BBWDDYNOMNUITM_URL.'assets/dymenu-js.js', array('jquery'), BBWDDYNOMNUITM_VERSION, array('in_footer'=>true) );
	wp_localize_script( 'bbwddymenuitm-js', 'bbwdDymenuJSObj',
		array( 
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce(BBWDDYNOMNUITM_NONCE),
		)
	);
	
	wp_enqueue_script('bbwddymenuitm-js');
	
}
add_action('admin_enqueue_scripts', 'BBWDDYNOMENUITM_dm_enqueue_scripts');

/***************** Add Menu Item Dropdown (when DM item is in menu) *****************/
function BBWDDYNOMENUITM_add_custom_menu_item_option($item_id, $item, $depth, $args) {
	$bbwd_post_meta = get_post_meta($item_id);
	if(!isset($bbwd_post_meta['_bbwd_dymenu_post_type']) || $bbwd_post_meta['_bbwd_dymenu_post_type'] == ''){
		return;
	}
	$bbwd_dy_nonce = wp_create_nonce( BBWDDYNOMNUITM_NONCE );
	
	if(isset($bbwd_post_meta['_bbwd_dymenu_post_tax'][0]) && $bbwd_post_meta['_bbwd_dymenu_post_tax'][0] !== '' && $bbwd_post_meta['_bbwd_dymenu_post_tax'][0] !== 'all' ){
		$bbwd_d_tax_name = str_replace('_', ' ', $bbwd_post_meta['_bbwd_dymenu_post_tax'][0]);
		$bbwd_d_tax_name_clean = ucwords($bbwd_d_tax_name);
		$bbwd_d_terms_arg = array (
				'taxonomy' => $bbwd_post_meta['_bbwd_dymenu_post_tax'][0], 
				'orderby' => 'name',
		);

		$bbwd_d_reg_terms = get_terms($bbwd_d_terms_arg);
		
		?>
		<div class="field-taxonomy">
			<label for="bbwd_taxonomy_terms_<?php echo esc_attr($item_id); ?>[]">Include By Terms:</label>
			<select name="bbwd_taxonomy_terms_<?php echo esc_attr($item_id); ?>[]" id="taxonomy_terms_<?php echo esc_attr($item_id); ?>" class="widefat bbwd_d_m_select" multiple="multiple" data-hide-target='bbwd_dy_real_terms_<?php echo esc_attr($item_id); ?>'>

				<?php foreach($bbwd_d_reg_terms as $bbwd_term){
			$bbwd_d_meta_key = isset($bbwd_post_meta['_bbwd_dymenu_post_meta_key']) && isset($bbwd_post_meta['_bbwd_dymenu_post_meta_key'][0]) ? $bbwd_post_meta['_bbwd_dymenu_post_meta_key'][0] : '';
			$bbwd_d_meta_val = isset($bbwd_post_meta['_bbwd_dymenu_post_meta_value'][0]) ? $bbwd_post_meta['_bbwd_dymenu_post_meta_value'][0] : '';
			if(!isset($bbwd_term)){continue;}
			$bbwd_d_term_in_array = isset($bbwd_post_meta['_bbwd_dymenu_post_tax_items'][0]) && str_contains($bbwd_post_meta['_bbwd_dymenu_post_tax_items'][0], $bbwd_term->term_id); 
			$bbwd_d_term_name = str_replace('_', ' ', $bbwd_term->name);
			
					?><option value="<?php echo esc_attr($bbwd_term->term_id) ?>" <?php echo $bbwd_d_term_in_array ? esc_attr('selected') : esc_attr(''); ?> ><?php echo esc_html($bbwd_d_term_name) ?></option><?php
					} ?>
			</select>
			<p class='description'>
				Hold cntrl ("Command" for Mac) to select multiple. If left empty "<?php echo esc_html($bbwd_post_meta['_bbwd_dymenu_post_type'][0]); ?>" with any "<?php echo esc_html($bbwd_post_meta['_bbwd_dymenu_post_tax'][0]); ?>" will be displayed.
			</p>
			<p class='description description-wide bbwdWidth50'>
				<?php $bbwd_operate_define = isset($bbwd_post_meta['_bbwd_dymenu_post_tax_operate'][0]) ? $bbwd_post_meta['_bbwd_dymenu_post_tax_operate'][0] : ''; ?>
				<label for="bbwd_d_post_operate_<?php echo esc_attr($item_id); ?>"><?php echo esc_html($bbwd_d_tax_name_clean); ?> Operator:</label>
				<select name="bbwd_d_post_operate_<?php echo esc_attr($item_id); ?>" class="widefat bbwdWidth50">
					<option value='OR' <?php echo $bbwd_operate_define == 'OR' ? esc_attr('selected') : esc_attr(''); ?> >Or</option>
					<option value='AND' <?php echo $bbwd_operate_define == 'AND' ? esc_attr('selected') : esc_attr(''); ?> >And</option>
				</select>
			</p>
		<?php } ?>
				
			<br>
			<div class='bbwdWidth100 bbwdCol'>
				<p class='bbwdMarg0'>
					<strong>Include By Meta Field</strong>
				</p>
				<div class='bbwdFlex bbwdRowNW bbwdGap10'>
					<p class='description description-wide'>
						<label for='bbwd_d_post_meta_key_<?php echo esc_attr($item_id); ?>'>Meta Key</label>
						<input type="text" name='bbwd_d_post_meta_key_<?php echo esc_attr($item_id); ?>' value='<?php echo isset($bbwd_d_meta_key) ?  esc_attr($bbwd_d_meta_key) : ''; ?>' class="widefat" >
					</p>
					<p class='description description-wide'>
						<label for='bbwd_d_post_meta_value_<?php echo esc_attr($item_id); ?>'>Meta Value</label>
						<input type="text" name='bbwd_d_post_meta_value_<?php echo esc_attr($item_id); ?>' value='<?php echo isset($bbwd_d_meta_key) ?  esc_attr($bbwd_d_meta_val) : ''; ?>' class="widefat" >
					</p>
				</div>
			</div>
			<br>
			<?php 
				$bbwd_d_p_count = isset($bbwd_post_meta['_bbwd_dymenu_post_count'][0]) ? $bbwd_post_meta['_bbwd_dymenu_post_count'][0] : 5;
			?>
			<p class='description description-wide'>
						<label for='bbwd_dymenu_post_count_<?php echo esc_attr($item_id); ?>'>Quantity To Display</label>
						<input type="number" min="1" max="50" id="bbwd_dymenu_post_count_<?php echo esc_attr($item_id); ?>" name='bbwd_dymenu_post_count_<?php echo esc_attr($item_id); ?>' value='<?php echo esc_attr($bbwd_d_p_count) ?>' class="widefat" >
					</p>
			<div class='bbwdFlex bbwdRowNW bbwdGap10'>
					<p class='description description-wide bbwdWidth100'>
						<?php $bbwd_order_define = isset($bbwd_post_meta['_bbwd_dymenu_post_tax_order'][0]) ? $bbwd_post_meta['_bbwd_dymenu_post_tax_order'][0] : ''; ?>
						<label for="bbwd_d_post_order_<?php echo esc_attr($item_id); ?>">Order By:</label>
						<select name="bbwd_d_post_order_<?php echo esc_attr($item_id); ?>" class="widefat" >
							<option value='name' <?php echo $bbwd_order_define == 'name' ? esc_attr('selected') : esc_attr(''); ?> >Name</option>
							<option value='id' <?php echo $bbwd_order_define == 'id' ? esc_attr('selected') : esc_attr(''); ?>>ID</option>
							<option value='date' <?php echo $bbwd_order_define == 'date' ? esc_attr('selected') : ''; ?> >Date</option>
							<option value='menu_order' <?php echo $bbwd_order_define == 'menu_order' ? esc_attr('selected') : ''; ?> >Menu Order</option>
						</select>
					</p>
					<p class='description description-wide bbwdWidth100'>
						<?php $bbwd_sort_define = isset($bbwd_post_meta['_bbwd_dymenu_post_tax_sort_direct'][0]) ? $bbwd_post_meta['_bbwd_dymenu_post_tax_sort_direct'][0] : ''; ?>
						<label for="bbwd_d_post_sort_direct_<?php echo esc_attr($item_id); ?>">Sort Direction:</label>
						<select name="bbwd_d_post_sort_direct_<?php echo esc_attr($item_id); ?>" class="widefat" >
							<option value='ASC' <?php echo $bbwd_sort_define == 'ASC' ? esc_html('selected') : esc_html(''); ?> >Ascending</option>
							<option value='DESC' <?php echo $bbwd_sort_define == 'DESC' ? esc_html('selected') : esc_html(''); ?> >Descending</option>
						</select>
					</p>
			</div>
			<?php $bbwd_d_terms = isset($bbwd_post_meta['_bbwd_dymenu_post_tax_items'][0]) ? $bbwd_post_meta['_bbwd_dymenu_post_tax_items'][0] : 'all';?>
			<input type='hidden' id='bbwd_dy_real_terms_<?php echo esc_attr($item_id); ?>' name='bbwd_dy_real_terms_<?php echo esc_attr($item_id); ?>' value='<?php echo esc_attr($bbwd_d_terms) ?>'>
			<input type="hidden" name="bbwd_gib_checker" value="<?php echo esc_attr($bbwd_dy_nonce); ?>">
    <?php
}
add_action('wp_nav_menu_item_custom_fields', 'BBWDDYNOMENUITM_add_custom_menu_item_option', 10, 4);

/***************** Save Dropdown Value (when DM item is in menu) *****************/
function BBWDDYNOMENUITM_save_custom_menu_item_option($menu_id, $menu_item_db_id) {
	if( isset($_POST['bbwd_gib_checker']) && !wp_verify_nonce( $_POST['bbwd_gib_checker'], BBWDDYNOMNUITM_NONCE )){
		die( esc_html('Security check failure: nonce incorrect.') );
	}
	if ( !current_user_can( 'manage_options' ) ) {
        wp_die( esc_html( 'You do not have the proper permissions to edit menus.' ) );
    }
	
    $BBWDDYMENITM_existing_meta = get_post_meta($menu_item_db_id, '_bbwd_dymenu_post_type', true);
    $BBWDDYMENITM_is_custom_menu_item =  !empty($BBWDDYMENITM_existing_meta) ;

    if (!$BBWDDYMENITM_is_custom_menu_item) {
        return;
    }
	
	$BBWDDYMENUITM_custom_post_real_terms = isset( $_POST['bbwd_dy_real_terms_'.$menu_item_db_id]) ? sanitize_text_field( wp_unslash( $_POST['bbwd_dy_real_terms_'.$menu_item_db_id]) ) : '' ;
	$BBWDDYMENUITM_custom_post_opertate = isset( $_POST['bbwd_d_post_operate_'.$menu_item_db_id]) ? sanitize_text_field( wp_unslash( $_POST['bbwd_d_post_operate_'.$menu_item_db_id]) ) : '||';
	$BBWDDYMENUITM_custom_post_sort = isset( $_POST['bbwd_d_post_order_'.$menu_item_db_id]) ? sanitize_text_field( wp_unslash( $_POST['bbwd_d_post_order_'.$menu_item_db_id]) ) : 'date';
	$BBWDDYMENUITM_custom_post_count = isset( $_POST['bbwd_dymenu_post_count_'.$menu_item_db_id]) ? sanitize_text_field( wp_unslash( $_POST['bbwd_dymenu_post_count_'.$menu_item_db_id]) ) : 1;
	$BBWDDYMENUITM_custom_post_sort_direct = isset( $_POST['bbwd_d_post_sort_direct_'.$menu_item_db_id]) ? sanitize_text_field( wp_unslash( $_POST['bbwd_d_post_sort_direct_'.$menu_item_db_id]) ) : 'ASC';
	$BBWDDYMENUITM_custom_post_meta_key = isset( $_POST['bbwd_d_post_meta_key_'.$menu_item_db_id]) ? sanitize_text_field( wp_unslash( $_POST['bbwd_d_post_meta_key_'.$menu_item_db_id]) ) : '';
	$BBWDDYMENUITM_custom_post_meta_value = isset( $_POST['bbwd_d_post_meta_value_'.$menu_item_db_id]) ? sanitize_text_field( wp_unslash( $_POST['bbwd_d_post_meta_value_'.$menu_item_db_id]) ) : '';

	update_post_meta($menu_item_db_id, '_bbwd_dymenu_post_tax_items', $BBWDDYMENUITM_custom_post_real_terms);
	update_post_meta($menu_item_db_id, '_bbwd_dymenu_post_tax_operate', $BBWDDYMENUITM_custom_post_opertate);
	update_post_meta($menu_item_db_id, '_bbwd_dymenu_post_tax_order', $BBWDDYMENUITM_custom_post_sort);
	update_post_meta($menu_item_db_id, '_bbwd_dymenu_post_count', $BBWDDYMENUITM_custom_post_count);
	update_post_meta($menu_item_db_id, '_bbwd_dymenu_post_tax_sort_direct', $BBWDDYMENUITM_custom_post_sort_direct);
	update_post_meta($menu_item_db_id, '_bbwd_dymenu_post_meta_key', $BBWDDYMENUITM_custom_post_meta_key);
	update_post_meta($menu_item_db_id, '_bbwd_dymenu_post_meta_value', $BBWDDYMENUITM_custom_post_meta_value);
        
}
add_action('wp_update_nav_menu_item', 'BBWDDYNOMENUITM_save_custom_menu_item_option', 10, 2);

/***************** Frontend View *****************/
function BBWDDYNOMENUITM_modify_custom_menu_item($sorted_menu_items, $args) {
    $offset = 0;
    foreach ($sorted_menu_items as $key => $item) {
        $bbwd_dmi_parent_id = $item->ID;
        $adjusted_key = $key + $offset;
        $bbwd_dmi_p_type = get_post_meta($bbwd_dmi_parent_id, '_bbwd_dymenu_post_type', true);
        if (!empty($bbwd_dmi_p_type)) {
            $bbwd_dmi_p_tax = get_post_meta($bbwd_dmi_parent_id, '_bbwd_dymenu_post_tax', true);
            $bbwd_dmi_p_count = get_post_meta($bbwd_dmi_parent_id, '_bbwd_dymenu_post_count', true);
            $bbwd_dmi_p_order = get_post_meta($bbwd_dmi_parent_id, '_bbwd_dymenu_post_tax_order', true);
            $bbwd_dmi_p_sort = get_post_meta($bbwd_dmi_parent_id, '_bbwd_dymenu_post_tax_sort_direct', true);
            $bbwd_dmi_p_operate = get_post_meta($bbwd_dmi_parent_id, '_bbwd_dymenu_post_tax_operate', true);
            $bbwd_dmi_p_item_string = get_post_meta($bbwd_dmi_parent_id, '_bbwd_dymenu_post_tax_items', true);
            $bbwd_dmi_p_meta_key = get_post_meta($bbwd_dmi_parent_id, '_bbwd_dymenu_post_meta_key', true);
            $bbwd_dmi_p_meta_value = get_post_meta($bbwd_dmi_parent_id, '_bbwd_dymenu_post_meta_value', true);

            if (!$bbwd_dmi_p_count) {
                $bbwd_dmi_p_count = 5;
            }

            $bbwd_dmi_p_items = 'all';
            if ($bbwd_dmi_p_item_string && $bbwd_dmi_p_item_string !== '') {
                $bbwd_dmi_p_items = str_contains($bbwd_dmi_p_item_string, ',') ? explode(',', $bbwd_dmi_p_item_string) : $bbwd_dmi_p_item_string;
            }

            $bbwd_dmi_args = array(
                'post_type' => $bbwd_dmi_p_type,
                'posts_per_page' => $bbwd_dmi_p_count,
                'orderby' => $bbwd_dmi_p_order,
                'order' => $bbwd_dmi_p_sort,
            );

            if ($bbwd_dmi_p_items !== 'all') {
                $bbwd_dmi_t_query = array('relation' => $bbwd_dmi_p_operate);
                if (is_array($bbwd_dmi_p_items)) {
                    foreach ($bbwd_dmi_p_items as $bbwd_dmi_t) {
                        $bbwd_dmi_arr = array(
                            'taxonomy' => $bbwd_dmi_p_tax,
                            'field' => 'id',
                            'terms' => $bbwd_dmi_t,
                        );
                        array_push($bbwd_dmi_t_query, $bbwd_dmi_arr);
                    }
                } else {
                    $bbwd_dmi_arr = array(
                        'taxonomy' => $bbwd_dmi_p_tax,
                        'field' => 'id',
                        'terms' => $bbwd_dmi_p_items,
                    );
                    array_push($bbwd_dmi_t_query, $bbwd_dmi_arr);
                }

                $bbwd_dmi_args['tax_query'] = $bbwd_dmi_t_query;
            }
            if (isset($bbwd_dmi_p_meta_key) && $bbwd_dmi_p_meta_key !== '' && isset($bbwd_dmi_p_meta_value) && $bbwd_dmi_p_meta_value !== '') {
                $bbwd_dmi_args['meta_query'] = array(
                    array(
                        'key' => $bbwd_dmi_p_meta_key,
                        'value' => $bbwd_dmi_p_meta_value,
                    )
                );
            }
            $bbwd_dmi_postslist = get_posts($bbwd_dmi_args);

            $submenu_items = array();
            foreach ($bbwd_dmi_postslist as $bbwd_dmi_p) {
                $bbwd_dmi_permaL = get_permalink($bbwd_dmi_p->ID);
                $submenu_items[] = (object) [
                    'ID' => $bbwd_dmi_p->ID,
                    'db_id' => $bbwd_dmi_p->ID,
                    'menu_item_parent' => $bbwd_dmi_parent_id,
                    'title' => $bbwd_dmi_p->post_title,
                    'url' => $bbwd_dmi_permaL,
                    'classes' => ['menu-item', 'menu-item-type-custom'],
                    'type' => 'custom',
                    'object' => '',
                    'object_id' => 0,
                    'target' => '',
                    'attr_title' => '',
                    'xfn' => '',
                    'current' => false,
                    'current_item_parent' => false,
                    'current_item_ancestor' => false,
                ];
            }
            array_splice($sorted_menu_items, $adjusted_key + 1, 0, $submenu_items);
            $offset += count($submenu_items);
            if (!empty($submenu_items)) {
                $item->classes[] = 'menu-item-has-children';
            }
        }
    }

    return $sorted_menu_items;
}
add_filter('wp_nav_menu_objects', 'BBWDDYNOMENUITM_modify_custom_menu_item', 10, 2);



function BBWDDYNOMENUITM_add_custom_menu_items_to_menu() {
    add_meta_box(
        'dynamic-menu-item', 
        'Dynamic Menu Item', 
        'BBWDDYNOMENUITM_dynamic_menu_item_meta_box', 
        'nav-menus', 
        'side', 
        'default'
    );
}
add_action('admin_menu', 'BBWDDYNOMENUITM_add_custom_menu_items_to_menu');

function BBWDDYNOMENUITM_dynamic_menu_item_meta_box() {
	$bbwd_dm_cur_menu = wp_get_nav_menus();
	$menu_id = 0;
	if (!empty($bbwd_dm_cur_menu)) {
        $menu_id = absint(get_user_option('nav_menu_recently_edited')); 
        if (!$menu_id) {
            $menu_id = isset($bbwd_dm_cur_menu[0]->term_id) ? absint($bbwd_dm_cur_menu[0]->term_id) : 0;
        }
    }
	$bbwd_dm_reg_post_types = get_post_types(array('public'=>true), 'objects');
	$bbwd_d_reg_tax = get_taxonomies();
    ?>
    <div class="custom-menu-item-selector">
        <label for="custom_menu_item_type">Post Type:</label>
        <select name="custom_menu_item_type" id="custom_menu_item_type" class="widefat">
            <option value="none">Select</option>
			<?php foreach($bbwd_dm_reg_post_types as $b_key=>$b_post){?>
            <option value="<?php echo esc_attr($b_post->name); ?>"><?php echo esc_html($b_post->label); ?></option>
			<?php }; ?>
        </select>
		<br>
		<label for="custom_menu_item_tax">Taxonomy:</label>
        <select name="custom_menu_item_tax" id="custom_menu_item_tax" class="widefat">
            <option value="all">All</option>
	<?php foreach($bbwd_d_reg_tax as $b_tax){
					$bbwd_dy_t_name = str_replace('_', ' ', $b_tax);
					$bbwd_dy_tax_name = ucwords($bbwd_dy_t_name);
	?>
            <option value="<?php echo esc_attr($b_tax); ?>"><?php echo esc_html($bbwd_dy_tax_name); ?></option>
			<?php }; ?>
        </select>
		<p class="button-controls wp-clearfix">
			<span class='add-to-menu'>
				<?php submit_button('Add to Menu', 'secondary', 'add-custom_menu_item_type', false, array( 'id'=>"add-custom-menu-item", 'data-menu-id'=>$menu_id, 'data-bbwd-gib'=>wp_create_nonce(BBWDDYNOMNUITM_NONCE), 'class'=>'submit-add-to-menu right') ); ?>
			</span>
		</p>
    </div>
	
    <?php 
}

function BBWDDYNOMENUITM_dymenu_update_type_label( $item ) {
	$bbwd_dym_label = get_post_meta( $item->ID, '_bbwd_dymenu_post_type', true );
	$bbwd_dym_tax = get_post_meta( $item->ID, '_bbwd_dymenu_post_tax', true );
	
	$bbwd_d_spaced_label = str_replace('_', ' ', $bbwd_dym_label);
	$bbwd_d_spaced_tax = str_replace('_', ' ', $bbwd_dym_tax);
	if ( ! empty( $bbwd_dym_label ) ) {
		$bbwd_tax_title = $bbwd_dym_tax == 'none' ? 'All': 'By '.ucwords($bbwd_d_spaced_tax);
		$item->type_label = ucwords($bbwd_d_spaced_label). 's '.$bbwd_tax_title ;
		$item->classes = array('bbwd_dy_menu_item');
	}

    return $item;
}
add_filter( 'wp_setup_nav_menu_item', 'BBWDDYNOMENUITM_dymenu_update_type_label');

/***************** Ajax Handle *****************/
function BBWDDYNOMENUITM_add_dm_menu_item_ajax() {
    check_ajax_referer(BBWDDYNOMNUITM_NONCE, 'nonce');
    if (!isset($_POST['menu_id'], $_POST['post_type'])) {
		$bbwd_dm_m_id = isset($_POST['menu_id']) ? '' : 'Menu Id';
		$bbwd_dm_p_t = isset($_POST['post_type']) ? '' : 'Custom Post Type';
		$bbwd_dm_sep = !isset($_POST['post_type']) && !isset($_POST['menu_id']) ? ',' : '';
		
        wp_send_json_error('Missing parameters ('.$bbwd_dm_m_id.$bbwd_dm_sep.$bbwd_dm_p_t.')');
    }
	
    $menu_id = absint($_POST['menu_id']);
    $bbwd_dym_post_type = isset($_POST['post_type']) ? sanitize_text_field( wp_unslash($_POST['post_type']) ) : null;
    $bbwd_dym_post_tax = isset($_POST['post_tax']) ? sanitize_text_field( wp_unslash($_POST['post_tax']) ) : null;

    $item_data = array(
        'menu-item-title'   => ucfirst(str_replace('_', ' ', $bbwd_dym_post_type)),
        'menu-item-url'     => '#',
        'menu-item-status'  => 'publish',
    );
    $menu_item_id = wp_update_nav_menu_item($menu_id, 0, $item_data);
    if ( is_wp_error($menu_item_id) ) {
		$bbwd_dm_er_messes = $menu_item_id->get_error_messages();
		$bbwd_dm_er_mess = '';
		foreach($bbwd_dm_er_messes as $bbwd_e_m){
			$bbwd_dm_er_mess .= $bbwd_e_m.' ';
		}
        wp_send_json_error( array('Failed to add menu item: '.$bbwd_dm_er_mess, $menu_item_id) );
    }
	if( is_wp_error($bbwd_dm_item_return) ){
		$bbwd_dm_item_errors = $bbwd_dm_item_return->get_error_messages();
		$bbwd_dm_item_return = '';
		foreach($bbwd_dm_item_errors as $bbwd_it_er_mes){
			$bbwd_dm_item_return .= $bbwd_it_er_mes.' ';
		}
	}
	update_post_meta($menu_item_id, '_bbwd_dymenu_post_type', $bbwd_dym_post_type);
	update_post_meta($menu_item_id, '_bbwd_dymenu_post_tax', $bbwd_dym_post_tax);
	update_post_meta($menu_item_id, '_bbwd_dymenu_post_count', 5);
    $NewHTML = BBWDDYNOMENUITM_get_custom_menu_item_edit_html($menu_item_id);
    wp_send_json_success(array('menu_html' => $NewHTML));
	wp_die();
}
add_action('wp_ajax_bbwd_add_dm_menu_item', 'BBWDDYNOMENUITM_add_dm_menu_item_ajax');

function BBWDDYNOMENUITM_get_custom_menu_item_edit_html($item_id) {
	$menu_item = wp_setup_nav_menu_item(get_post($item_id));
    if (!$menu_item) {
        return '<p>Invalid Menu Item</p>';
    }
    require_once ABSPATH . 'wp-admin/includes/nav-menu.php';
    $walker = new Walker_Nav_Menu_Edit();
    $output = '';
    $args = (object) array();
    $walker->start_el($output, $menu_item, 0, $args);
    return $output;
}



