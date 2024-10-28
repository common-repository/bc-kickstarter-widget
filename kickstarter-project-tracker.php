<?php
/*
  Plugin Name: BC Kickstarter widget
  Description: Widget and shortcode that allows you to pull project informations from kickstarter.
  Version: 1.0
  Plugin URI: http://www.beocode.com/kickstarter-project-tracker/
  Author: BeoCode.d.o.o.
  Author URI: http://www.beocode.com/
  Developer: Vesna Spasic
  Developer URI: http://www.beocode.com/
  Text Domain: kickstarter_project_tracker
  Requires at least: 4.7.11
  Tested up to: 4.9.8

  Copyright: © 2012-2018 BeoCode.
  License: GNU General Public License, version 2
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */


 
class Kickstarter_Project_Tracker extends WP_Widget {
  // Main constructor
  public function __construct() {
    parent::__construct(
            'kickstarter_project_tracker',
            __( 'BC Kickstarter widget', 'kickstarter_project_tracker' ),
            array(
                'customize_selective_refresh' => true,
            )
        );
  }
  // The widget form (for the backend )
  public function form( $instance ) { 
        $defaults = array(
        'title'    => '',
        'text'     => '',
    );
    
    extract( wp_parse_args( ( array ) $instance, $defaults ) ); ?>
    <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'kickstarter_project_tracker' ); ?></label>
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    </p>
    <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>"><?php _e( 'Project Url:', 'kickstarter_project_tracker' ); ?></label>
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>" type="text" value="<?php echo esc_attr( $text ); ?>" />
    </p>

  <?php }
  // Update widget settings
  public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title']    = isset( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
        $instance['text']     = isset( $new_instance['text'] ) ? wp_strip_all_tags( $new_instance['text'] ) : '';
        return $instance;
  }

  public function widget( $args, $instance ) {
        extract( $args );
        $title    = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
        $text     = isset( $instance['text'] ) ? $instance['text'] : '';
        
        echo $args['before_widget'];
        if ( $text ) {
            $arr = explode("?", $text, 2);
            $new_url = $arr['0'];
            
            // Display the widget
            echo '<div id="kickstarter" class="wp_widget_kickstarter" data-url="'.$new_url.'">';

            // Display widget title if defined
            if ( $title ) {
                echo '<h2>' . $title . '</h2>';
            }

            // Display text field
            $start_url = $arr['0'].'?format=json';
            $url = wp_remote_get($start_url);
            $json_a = json_decode($url['body'], true);
            echo '<p>' . $json_a['card'] . '</p>';
        

            echo '</div>';
        }
        // WordPress core after_widget hook 
        echo $args['after_widget'];
    }

}

// Add shortcode
add_shortcode('kickstarter','kickstarter_project');
function kickstarter_project($args='') {
    
    $value = shortcode_atts( array(
        'url' => 'https://www.kickstarter.com/projects/kolossalgames/mezo-relaunch'
    ), $args );
    $clear_url = explode("?", $value['url'], 2);
    $start_url = $clear_url['0'].'?format=json';
    $url = wp_remote_get($start_url);
    $json = json_decode($url['body'], true);
    $arr = explode("?", $value['url'], 2);
    return '<div id="kickstarter_shortcode" class="wp_widget_kickstarter" data-url="'.$arr['0'].'">'.$json['card'].'</div>';
}


// Register the widget style and scripts
add_action('wp_enqueue_scripts','kickstarter_scripts');
function kickstarter_scripts() {
    wp_enqueue_style( 'kickstarter_css', plugins_url( '/assets/css/kickstarter_css.css', __FILE__ ) );
    wp_enqueue_script( 'kickstarter_js', plugins_url('/assets/js/kickstarter.js', __FILE__ ), array('jquery') );
}

// Register the widget
add_action( 'widgets_init', 'kickstarter_project_tracker_widget' );
function kickstarter_project_tracker_widget() {
    register_widget( 'Kickstarter_Project_Tracker' );
}


