<?php
/**
 * Plugin Name: Reservas
 * Description: Plugin de reservas Embaixador
 * Author: IBloom
 * Version: 0.2
 * Text Domain: reservas
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function register_oembed_widget( $widgets_manager ) {

	require_once( __DIR__ . '/widgets/oembed-widget.php' );

	$widgets_manager->register( new \Elementor_oEmbed_Widget() );

}
add_action( 'elementor/widgets/register', 'register_oembed_widget' );

add_action('admin_menu', function () {
    add_menu_page(
        'Reservas',
        'Reservas',
        'manage_options',
        'cmp-reservas',
        'cmp_reservas_page',
        'dashicons-calendar-alt',
        2
    );
});

function cmp_reservas_page()
{
    $api_url = 'https://app.hostkit.pt/api/getReservations?APIKEY=5aQElqgU34RIgKDsKxIfuqzjVFR7eH8XxUgZ1StjpcD3rTrJRI';
    $response = wp_remote_get($api_url);

    echo '<div class="wrap"><h1>Reservas 3</h1>';

    if (is_wp_error($response)) {
        echo '<div class="notice notice-error"><p>Erro ao aceder à API: ' . esc_html($response->get_error_message()) . '</p></div>';
        return;
    }

    $status_code = wp_remote_retrieve_response_code($response);
    if ($status_code !== 200) {
        echo '<div class="notice notice-error"><p>Status HTTP inesperado: ' . esc_html($status_code) . '</p></div>';
        return;
    }

    $body = wp_remote_retrieve_body($response);
    $body_clean = iconv('UTF-8', 'UTF-8//IGNORE', $body);

    $dados = json_decode($body_clean, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo '<div class="notice notice-error"><p>Erro ao decodificar JSON: ' . esc_html(json_last_error_msg()) . '</p></div>';
        return;
    }

    if (empty($dados)) {
        echo '<p>Nenhuma reserva encontrada.</p>';
        return;
    }

    // Ordenar as reservas por in_date (check-in) crescente
    usort($dados, function($a, $b) {
        $inA = isset($a['in_date']) ? intval($a['in_date']) : 0;
        $inB = isset($b['in_date']) ? intval($b['in_date']) : 0;
        return $inA <=> $inB;
    });

    echo '<table class="widefat fixed" cellspacing="0" style="width: 100%; border-collapse: collapse;">';
    echo '<thead>';
    echo '<tr>';
    echo '<th style="border: 1px solid #ddd; padding: 8px;">Nome</th>';
    echo '<th style="border: 1px solid #ddd; padding: 8px;">Check-In</th>';
    echo '<th style="border: 1px solid #ddd; padding: 8px;">Check-Out</th>';
    echo '<th style="border: 1px solid #ddd; padding: 8px;">Provider</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    foreach ($dados as $reserva) {
        $nome = trim(($reserva['firstname'] ?? '') . ' ' . ($reserva['lastname'] ?? ''));

        $checkinUnix = !empty($reserva['in_date']) ? intval($reserva['in_date']) : 0;
        $checkoutUnix = !empty($reserva['out_date']) ? intval($reserva['out_date']) : 0;

        $checkin = $checkinUnix ? date('Y-m-d', $checkinUnix) : '';
        $checkout = $checkoutUnix ? date('Y-m-d', $checkoutUnix) : '';

        $provider = $reserva['provider'] ?? '';

        echo '<tr>';
        echo '<td style="border: 1px solid #ddd; padding: 8px;">' . esc_html($nome) . '</td>';
        echo '<td style="border: 1px solid #ddd; padding: 8px;">' . esc_html($checkin) . '</td>';
        echo '<td style="border: 1px solid #ddd; padding: 8px;">' . esc_html($checkout) . '</td>';
        echo '<td style="border: 1px solid #ddd; padding: 8px;">' . esc_html($provider) . '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';

    echo '</div>';
}

<?php
// ... teu código atual ...

// Endpoint AJAX para retornar as datas das reservas no formato JSON
add_action('wp_ajax_get_reservas_calendar', 'get_reservas_calendar_callback');
add_action('wp_ajax_nopriv_get_reservas_calendar', 'get_reservas_calendar_callback');

function get_reservas_calendar_callback() {
    // URL da API
    $api_url = 'https://app.hostkit.pt/api/getReservations?APIKEY=5aQElqgU34RIgKDsKxIfuqzjVFR7eH8XxUgZ1StjpcD3rTrJRI';
    $response = wp_remote_get($api_url);

    if (is_wp_error($response)) {
        wp_send_json_error('Erro ao aceder à API: ' . $response->get_error_message());
    }

    $status_code = wp_remote_retrieve_response_code($response);
    if ($status_code !== 200) {
        wp_send_json_error('Status HTTP inesperado: ' . $status_code);
    }

    $body = wp_remote_retrieve_body($response);
    $body_clean = iconv('UTF-8', 'UTF-8//IGNORE', $body);
    $dados = json_decode($body_clean, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        wp_send_json_error('Erro ao decodificar JSON: ' . json_last_error_msg());
    }

    if (empty($dados)) {
        wp_send_json_error('Nenhuma reserva encontrada.');
    }

    // Extrair todas as datas entre check-in e check-out para riscar no calendário
    $datas_riscadas = [];

    foreach ($dados as $reserva) {
        $checkinUnix = !empty($reserva['in_date']) ? intval($reserva['in_date']) : 0;
        $checkoutUnix = !empty($reserva['out_date']) ? intval($reserva['out_date']) : 0;

        if ($checkinUnix && $checkoutUnix && $checkoutUnix >= $checkinUnix) {
            for ($ts = $checkinUnix; $ts <= $checkoutUnix; $ts += 86400) {
                $datas_riscadas[] = date('Y-m-d', $ts);
            }
        }
    }

    // Remover duplicados e ordenar
    $datas_riscadas = array_values(array_unique($datas_riscadas));
    sort($datas_riscadas);

    wp_send_json_success($datas_riscadas);
}
