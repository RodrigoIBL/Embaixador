<?php
/**
 * Plugin Name: Reservas
 * Description: Plugin de reservas Embaixador
 * Author: IBloom
 * Version: 0.2
 * Text Domain: reservas
 */

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

    echo '<div class="wrap"><h1>Reservas</h1>';

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
