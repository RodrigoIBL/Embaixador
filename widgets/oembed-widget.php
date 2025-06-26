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
    <div id="reservas-calendar-container" style="max-width: 400px; margin: 20px auto; font-family: Arial, sans-serif;">
        <div style="text-align: center; margin-bottom: 10px;">
            <button id="prev-month" style="margin-right: 10px;">&lt; Mês Anterior</button>
            <span id="month-year" style="font-weight: bold;"></span>
            <button id="next-month" style="margin-left: 10px;">Mês Seguinte &gt;</button>
        </div>
        <table id="reservas-calendar" border="1" cellspacing="0" cellpadding="5" style="width: 100%; border-collapse: collapse; text-align: center;">
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
            <tbody id="calendar-body"></tbody>
        </table>
        <p id="calendar-loading" style="text-align:center; display:none;">A carregar reservas...</p>
        <p id="calendar-error" style="color:red; text-align:center; display:none;"></p>
    </div>

    <script>
    (function(){
        const calendarBody = document.getElementById('calendar-body');
        const monthYearLabel = document.getElementById('month-year');
        const prevMonthBtn = document.getElementById('prev-month');
        const nextMonthBtn = document.getElementById('next-month');
        const loadingText = document.getElementById('calendar-loading');
        const errorText = document.getElementById('calendar-error');

        // Estado do mês/ano atual exibido
        let currentYear, currentMonth;

        // Datas riscadas vindas da API (YYYY-MM-DD)
        let blockedDates = [];

        // Formatar mês por extenso (pt-PT)
        const monthNames = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];

        // Inicializa o calendário no mês atual
        function initCalendar() {
            const today = new Date();
            currentYear = today.getFullYear();
            currentMonth = today.getMonth();
            fetchBlockedDatesAndRender();
        }

        // Buscar as datas riscadas via AJAX
        function fetchBlockedDatesAndRender() {
            loadingText.style.display = 'block';
            errorText.style.display = 'none';
            calendarBody.innerHTML = '';

            const ajaxUrl = '<?php echo admin_url("admin-ajax.php"); ?>';

            fetch(`${ajaxUrl}?action=get_reservas_calendar`)
            .then(response => response.json())
            .then(data => {
                loadingText.style.display = 'none';

                if (data.success) {
                    blockedDates = data.data;
                    renderCalendar(currentYear, currentMonth);
                } else {
                    errorText.textContent = data.data || 'Erro ao carregar reservas.';
                    errorText.style.display = 'block';
                }
            })
            .catch(() => {
                loadingText.style.display = 'none';
                errorText.textContent = 'Erro na requisição AJAX.';
                errorText.style.display = 'block';
            });
        }

        // Renderiza o calendário para o mês e ano dados
        function renderCalendar(year, month) {
            monthYearLabel.textContent = `${monthNames[month]} ${year}`;
            calendarBody.innerHTML = '';

            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const startDayOfWeek = firstDay.getDay(); // 0=Dom, 1=Seg ...

            let row = document.createElement('tr');
            // Espaços vazios antes do primeiro dia
            for(let i = 0; i < startDayOfWeek; i++) {
                let emptyCell = document.createElement('td');
                emptyCell.innerHTML = '&nbsp;';
                row.appendChild(emptyCell);
            }

            for (let day = 1; day <= lastDay.getDate(); day++) {
                if ((startDayOfWeek + day - 1) % 7 === 0 && day !== 1) {
                    calendarBody.appendChild(row);
                    row = document.createElement('tr');
                }

                const cell = document.createElement('td');
                cell.textContent = day;

                const dateStr = `${year}-${String(month + 1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
                if (blockedDates.includes(dateStr)) {
                    cell.style.textDecoration = 'line-through';
                    cell.style.color = '#d00';
                    cell.title = 'Data reservada';
                }

                row.appendChild(cell);
            }

            // Completar a última linha com células vazias para o resto da semana
            while (row.children.length < 7) {
                let emptyCell = document.createElement('td');
                emptyCell.innerHTML = '&nbsp;';
                row.appendChild(emptyCell);
            }
            calendarBody.appendChild(row);
        }

        prevMonthBtn.addEventListener('click', () => {
            if (currentMonth === 0) {
                currentMonth = 11;
                currentYear--;
            } else {
                currentMonth--;
            }
            renderCalendar(currentYear, currentMonth);
        });

        nextMonthBtn.addEventListener('click', () => {
            if (currentMonth === 11) {
                currentMonth = 0;
                currentYear++;
            } else {
                currentMonth++;
            }
            renderCalendar(currentYear, currentMonth);
        });

        initCalendar();
    })();
    </script>
    <?php
}


}