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

    if (is_wp_error($response)) {
        echo '<div class="notice notice-error"><p>Erro ao aceder à API: ' . esc_html($response->get_error_message()) . '</p></div>';
        return;
    }

    $body = wp_remote_retrieve_body($response);
    $dados = json_decode($body, true);

    echo '<pre>';
  print_r($dados);
  echo '</pre>';


    echo '<div class="wrap"><h1>Reservas</h1>';

    if (!empty($dados) && is_array($dados)) {
        echo '<table class="widefat fixed striped">';
        echo '<thead><tr><th>Nome</th><th>Check-In</th><th>Check-Out</th><th>Provider</th></tr></thead>';
        echo '<tbody>';

        foreach ($dados as $reserva) {
            $nome = esc_html(($reserva['firstname'] ?? '') . ' ' . ($reserva['lastname'] ?? ''));
            $checkin = !empty($reserva['arrival']) ? date('Y-m-d', $reserva['arrival']) : '-';
            $checkout = !empty($reserva['departure']) ? date('Y-m-d', $reserva['departure']) : '-';
            $provider = esc_html($reserva['provider'] ?? '-');

            echo '<tr>';
            echo '<td>' . $nome . '</td>';
            echo '<td>' . $checkin . '</td>';
            echo '<td>' . $checkout . '</td>';
            echo '<td>' . $provider . '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
    } else {
        echo '<p>Nenhuma reserva encontrada ou erro no formato dos dados.</p>';
    }

    echo '</div>';
}
