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
    ?>
    <div class="oembed-elementor-widget">

        <style>
            .calendar {
                border-collapse: collapse;
                width: 100%;
                max-width: 400px;
                text-align: center;
                margin: 10px 0;
            }
            .calendar caption {
                font-weight: bold;
                font-size: 1.2em;
                margin-bottom: 10px;
            }
            .calendar th, .calendar td {
                border: 1px solid #ccc;
                padding: 5px;
                width: 14.28%; /* 100%/7 dias */
            }
            .calendar td.empty {
                background-color: #f5f5f5;
            }
            .calendar-nav {
                margin-bottom: 5px;
            }
            .calendar-nav button {
                padding: 5px 10px;
                margin: 0 5px;
                cursor: pointer;
            }
        </style>

        <div class="calendar-nav">
            <button id="prevMonthBtn">Anterior</button>
            <button id="nextMonthBtn">Próximo</button>
        </div>

        <table id="calendarTable" class="calendar" aria-label="Calendário mensal">
            <caption id="calendarCaption"></caption>
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
            <tbody id="calendarBody">
                <!-- Dias serão inseridos aqui via JS -->
            </tbody>
        </table>

        <script>
            (function() {
                const caption = document.getElementById('calendarCaption');
                const tbody = document.getElementById('calendarBody');
                const prevBtn = document.getElementById('prevMonthBtn');
                const nextBtn = document.getElementById('nextMonthBtn');

                let currentDate = new Date();

                function renderCalendar(date) {
                    const year = date.getFullYear();
                    const month = date.getMonth();

                    // Primeiro dia do mês
                    const firstDay = new Date(year, month, 1);
                    const firstWeekDay = firstDay.getDay(); // 0=dom, 6=sab
                    // Quantidade de dias no mês
                    const daysInMonth = new Date(year, month + 1, 0).getDate();

                    caption.textContent = date.toLocaleString('pt-PT', { month: 'long', year: 'numeric' });

                    tbody.innerHTML = '';

                    let row = document.createElement('tr');

                    // Espaços em branco antes do primeiro dia
                    for (let i = 0; i < firstWeekDay; i++) {
                        const cell = document.createElement('td');
                        cell.classList.add('empty');
                        row.appendChild(cell);
                    }

                    // Dias do mês
                    for (let day = 1; day <= daysInMonth; day++) {
                        if ((row.children.length) === 7) {
                            tbody.appendChild(row);
                            row = document.createElement('tr');
                        }

                        const cell = document.createElement('td');
                        cell.textContent = day;
                        row.appendChild(cell);
                    }

                    // Preencher a última linha com espaços se necessário
                    while (row.children.length < 7) {
                        const cell = document.createElement('td');
                        cell.classList.add('empty');
                        row.appendChild(cell);
                    }
                    tbody.appendChild(row);
                }

                prevBtn.addEventListener('click', () => {
                    currentDate.setMonth(currentDate.getMonth() - 1);
                    renderCalendar(currentDate);
                });

                nextBtn.addEventListener('click', () => {
                    currentDate.setMonth(currentDate.getMonth() + 1);
                    renderCalendar(currentDate);
                });

                renderCalendar(currentDate);
            })();
        </script>

    </div>
    <?php
}


}