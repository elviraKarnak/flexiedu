<?php
/**
 * Custom label class file.
 *
 * @package LearnDash\Groups_Plus
 *
 * cspell:ignore WPINC
 */

namespace LearnDash\Groups_Plus\Module;

if ( ! defined( 'WPINC' ) ) {
    exit;
}

use LearnDash\Groups_Plus\lucatume\DI52\App;
use LearnDash\Groups_Plus\Module\Base as Module_Base;
use LearnDash\Groups_Plus\Module\Module_Interface;

/**
 * Custom_Label class.
 * 
 * @since 1.1.0
 */
class Custom_Label extends Module_Base implements Module_Interface {
    /**
     * Constructor method.
     * 
     * @since 1.1.0
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Container method for filter hooks.
     *
     * @since 1.1.0
     * 
     * @return void
     */
    protected function hook_filters(): void {
        add_filter( 'learndash_settings_fields', App::callback( self::class, 'add_settings_fields' ), 10, 2 );
        add_filter( 'learndash_get_label', App::callback( self::class, 'set_default_label' ), 10, 2 );
    }
    
    /**
     * Add settings fields for groups plus addon custom label.
     *
     * @since 1.1.0
     * 
     * @param array  $settings_fields       Existing settings fields.
     * @param string $settings_section_key  Current settings section key.
     * @return array
     */
    public function add_settings_fields( $settings_fields, $settings_section_key ): array {
        if ( $settings_section_key === 'settings_custom_labels' ) {
            $settings_values = get_option( 'learndash_settings_custom_labels' );

            $new_settings_fields = array(
                'organization' => array(
					'name'      => 'organization',
					'type'      => 'text',
					'label'     => esc_html__( 'Organization', 'learndash-groups-plus' ),
					'help_text' => esc_html__( 'Label to replace "organization" (singular).', 'learndash-groups-plus' ),
					'value'     => isset( $settings_values['organization'] ) ? $settings_values['organization'] : '',
					'class'     => 'regular-text',
				),
                'organizations' => array(
					'name'      => 'organizations',
					'type'      => 'text',
					'label'     => esc_html__( 'Organizations', 'learndash-groups-plus' ),
					'help_text' => esc_html__( 'Label to replace "organizations" (plural).', 'learndash-groups-plus' ),
					'value'     => isset( $settings_values['organizations'] ) ? $settings_values['organizations'] : '',
					'class'     => 'regular-text',
				),
                'team' => array(
					'name'      => 'team',
					'type'      => 'text',
					'label'     => esc_html__( 'Team', 'learndash-groups-plus' ),
					'help_text' => esc_html__( 'Label to replace "team" (singular).', 'learndash-groups-plus' ),
					'value'     => isset( $settings_values['team'] ) ? $settings_values['team'] : '',
					'class'     => 'regular-text',
				),
                'teams' => array(
					'name'      => 'teams',
					'type'      => 'text',
					'label'     => esc_html__( 'Teams', 'learndash-groups-plus' ),
					'help_text' => esc_html__( 'Label to replace "teams" (plural).', 'learndash-groups-plus' ),
					'value'     => isset( $settings_values['teams'] ) ? $settings_values['teams'] : '',
					'class'     => 'regular-text',
				),
                'lead_organizer' => array(
					'name'      => 'lead_organizer',
					'type'      => 'text',
					'label'     => esc_html__( 'Lead Organizer', 'learndash-groups-plus' ),
					'help_text' => esc_html__( 'Label to replace "lead organizer" (singular).', 'learndash-groups-plus' ),
					'value'     => isset( $settings_values['lead_organizer'] ) ? $settings_values['lead_organizer'] : '',
					'class'     => 'regular-text',
				),
                'lead_organizers' => array(
					'name'      => 'lead_organizers',
					'type'      => 'text',
					'label'     => esc_html__( 'Lead Organizers', 'learndash-groups-plus' ),
					'help_text' => esc_html__( 'Label to replace "lead organizers" (plural).', 'learndash-groups-plus' ),
					'value'     => isset( $settings_values['lead_organizers'] ) ? $settings_values['lead_organizers'] : '',
					'class'     => 'regular-text',
				),
                'team_leader' => array(
					'name'      => 'team_leader',
					'type'      => 'text',
					'label'     => esc_html__( 'Team Leader', 'learndash-groups-plus' ),
					'help_text' => esc_html__( 'Label to replace "team leader" (singular).', 'learndash-groups-plus' ),
					'value'     => isset( $settings_values['team_leader'] ) ? $settings_values['team_leader'] : '',
					'class'     => 'regular-text',
				),
                'team_leaders' => array(
					'name'      => 'team_leaders',
					'type'      => 'text',
					'label'     => esc_html__( 'Team Leaders', 'learndash-groups-plus' ),
					'help_text' => esc_html__( 'Label to replace "team leaders" (plural).', 'learndash-groups-plus' ),
					'value'     => isset( $settings_values['team_leaders'] ) ? $settings_values['team_leaders'] : '',
					'class'     => 'regular-text',
				),
                'team_member' => array(
					'name'      => 'team_member',
					'type'      => 'text',
					'label'     => esc_html__( 'Team Member', 'learndash-groups-plus' ),
					'help_text' => esc_html__( 'Label to replace "team member" (singular).', 'learndash-groups-plus' ),
					'value'     => isset( $settings_values['team_member'] ) ? $settings_values['team_member'] : '',
					'class'     => 'regular-text',
				),
                'team_members' => array(
					'name'      => 'team_members',
					'type'      => 'text',
					'label'     => esc_html__( 'Team Members', 'learndash-groups-plus' ),
					'help_text' => esc_html__( 'Label to replace "team members" (plural).', 'learndash-groups-plus' ),
					'value'     => isset( $settings_values['team_members'] ) ? $settings_values['team_members'] : '',
					'class'     => 'regular-text',
				),
            );

            $settings_fields = array_merge( $settings_fields, $new_settings_fields );
        }

        return $settings_fields;
    }

    /**
     * Set default label for modifiable labels.
     * 
     * @since 1.1.0
     *
     * @param string $label Existing label.
     * @param string $key   Retrieved label key.
     * @return string       Returned label.
     */    
    public function set_default_label( $label, $key ): string {
        if ( empty( $label ) ) {
            switch ( $key ) {
                case 'organization':
                    $label = __( 'Organization', 'learndash-groups-plus' );
                    break;
                
                case 'organizations':
                    $label = __( 'Organizations', 'learndash-groups-plus' );
                    break;
                
                case 'team':
                    $label = __( 'Team', 'learndash-groups-plus' );
                    break;
                
                case 'teams':
                    $label = __( 'Teams', 'learndash-groups-plus' );
                    break;
                
                case 'lead_organizer':
                    $label = __( 'Lead Organizer', 'learndash-groups-plus' );
                    break;
                
                case 'lead_organizers':
                    $label = __( 'Lead Organizers', 'learndash-groups-plus' );
                    break;
                
                case 'team_leader':
                    $label = __( 'Team Leader', 'learndash-groups-plus' );
                    break;
                
                case 'team_leaders':
                    $label = __( 'Team Leaders', 'learndash-groups-plus' );
                    break;
                
                case 'team_member':
                    $label = __( 'Team Member', 'learndash-groups-plus' );
                    break;
                
                case 'team_members':
                    $label = __( 'Team Members', 'learndash-groups-plus' );
                    break;
            }
        }
        
        return $label;
    }
}
