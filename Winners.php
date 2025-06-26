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
    echo '<p><strong>Status HTTP da resposta:</strong> ' . esc_html($status_code) . '</p>';

    $body = wp_remote_retrieve_body($response);

    // Forçar a codificação UTF-8
    $body_utf8 = mb_convert_encoding($body, 'UTF-8', 'UTF-8');

    echo '<p><strong>Conteúdo bruto da resposta da API (forçado UTF-8):</strong></p>';
    echo '<pre>' . esc_html($body_utf8) . '</pre>';

    $dados = json_decode($body_utf8, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo '<p><strong>Erro ao decodificar JSON:</strong> ' . esc_html(json_last_error_msg()) . '</p>';
        return;
    }

    echo '<p><strong>Array decodificado do JSON:</strong></p>';
    echo '<pre>' . print_r($dados, true) . '</pre>';

    // Aqui podes continuar a mostrar a tabela como antes
}
