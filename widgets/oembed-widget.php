<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor oEmbed Widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Elementor_oEmbed_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve oEmbed widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name(): string {
		return 'Calendar';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve oEmbed widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title(): string {
		return esc_html__( 'Calendar', 'elementor-oembed-widget' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve oEmbed widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon(): string {
		return 'eicon-calendar';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the oEmbed widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget categories.
	 */
	public function get_categories(): array {
		return [ 'general' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the oEmbed widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords(): array {
		return [ 'oembed', 'url', 'link' ];
	}

	/**
	 * Get custom help URL.
	 *
	 * Retrieve a URL where the user can get more information about the widget.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget help URL.
	 */
	public function get_custom_help_url(): string {
		return 'https://developers.elementor.com/docs/widgets/';
	}

	/**
	 * Whether the widget requires inner wrapper.
	 *
	 * Determine whether to optimize the DOM size.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return bool Whether to optimize the DOM size.
	 */
	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	/**
	 * Whether the element returns dynamic content.
	 *
	 * Determine whether to cache the element output or not.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @return bool Whether to cache the element output.
	 */
	protected function is_dynamic_content(): bool {
		return false;
	}

	/**
	 * Register oEmbed widget controls.
	 *
	 * Add input fields to allow the user to customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls(): void {

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'elementor-oembed-widget' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'url',
			[
				'label' => esc_html__( 'URL to embed', 'elementor-oembed-widget' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'input_type' => 'url',
				'placeholder' => esc_html__( 'https://your-link.com', 'elementor-oembed-widget' ),
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render oEmbed widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render(): void {
    $settings = $this->get_settings_for_display();

    if ( empty( $settings['url'] ) ) {
        return;
    }

    // Pega o HTML embebido do URL (se quiser usar)
    $html = wp_oembed_get( $settings['url'] );

    // Definir mês e ano atuais
    $month = date('n'); // 1-12
    $year = date('Y');

    // Primeiro dia do mês
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
    // Quantidade de dias no mês
    $daysInMonth = date('t', $firstDayOfMonth);
    // Dia da semana do primeiro dia do mês (0 = domingo, 6 = sábado)
    $firstWeekDay = date('w', $firstDayOfMonth);

    ?>
    <div class="oembed-elementor-widget">

        <table class="calendar" border="1" cellspacing="0" cellpadding="5" style="border-collapse: collapse; width: 100%; max-width: 400px; text-align: center;">
            <caption><?php echo date('F Y', $firstDayOfMonth); ?></caption>
            <thead>
                <tr>
                    <th>Dom</th>
                    <th>Seg</th>
                    <th>Ter</th>
                    <th>Qua</th>
                    <th>Qui</th>
                    <th>Sex</th>
                    <th>Sáb</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                <?php
                // Espaços em branco antes do primeiro dia do mês
                for ($i = 0; $i < $firstWeekDay; $i++) {
                    echo '<td></td>';
                }

                $currentDay = 1;
                // Preenche os dias do mês
                for ($i = $firstWeekDay; $i < 7; $i++) {
                    echo '<td>' . $currentDay++ . '</td>';
                }
                echo '</tr>';

                // Dias seguintes
                while ($currentDay <= $daysInMonth) {
                    echo '<tr>';
                    for ($i = 0; $i < 7; $i++) {
                        if ($currentDay <= $daysInMonth) {
                            echo '<td>' . $currentDay++ . '</td>';
                        } else {
                            echo '<td></td>';
                        }
                    }
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>

    </div>
    <?php
}


}