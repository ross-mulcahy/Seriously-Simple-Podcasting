<?php

namespace SeriouslySimplePodcasting\Controllers\Integrations\Elementor\Widgets;

use SeriouslySimplePodcasting\Controllers\Players_Controller;

class Elementor_Media_Player_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'Media Player';
	}

	public function get_title() {
		return __( 'Media Player', 'seriously-simple-podcasting' );
	}

	public function get_icon() {
		return 'fa fa-play';
	}

	public function get_categories() {
		return [ 'basic' ];
	}

	public function get_episodes() {
		$args = array(
			'fields'         => array( 'post_title, id' ),
			'posts_per_page' => - 1,
			'post_type'      => ssp_post_types( true ),
			'post_status'    => array( 'publish', 'draft', 'future' ),
		);

		$episodes       = get_posts( $args );
		$episodeOptions = [];
		foreach ( $episodes as $episode ) {
			$episodeOptions[ $episode->ID ] = $episode->post_title;
		}

		return $episodeOptions;
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'seriously-simple-podcasting' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$episodeOptions = $this->get_episodes();
		$episodeOptionsValues = array_values( $episodeOptions );
		$this->add_control(
			'show_elements',
			[
				'label' => __( 'Select Episode', 'seriously-simple-podcasting' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'options' => $episodeOptions,
				'default' => array_shift( $episodeOptionsValues )
			]
		);

		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$episodes = $this->get_episodes();

		$episode_id = $settings['show_elements'];

		$media_player = new Players_Controller( __FILE__, SSP_VERSION );
		echo '<div>' . $episodes[ $episode_id ] . '</div>';
		echo '<div>' . $media_player->render_media_player( $episode_id ) . '</div>';
	}

	protected function _content_template() {
		?>
		<# _.each( settings.show_elements, function( element ) { #>
		<div>{{{ element }}}</div>
		<# } ) #>
		<?php
	}
}
