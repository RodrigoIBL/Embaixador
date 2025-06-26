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

    $dados = [];

    if (!is_wp_error($response)) {
        $body = wp_remote_retrieve_body($response);
        $dados = json_decode($body, true);
    }

    ?>
    <div class="wrap">
        <h1>Reservas</h1>

        <table class="widefat fixed striped">
            <thead>
                <tr>
                    <th>Apartamento</th>
                    <th>Nome</th>
                    <th>Nº Pessoas</th>
                    <th>Check-In</th>
                    <th>Check-Out</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($dados) && is_array($dados)) {
                    foreach ($dados as $reserva) {
                        echo '<tr>';
                        echo '<td>' . esc_html($reserva['apartment_name'] ?? '-') . '</td>';
                        echo '<td>' . esc_html($reserva['client_name'] ?? '-') . '</td>';
                        echo '<td>' . esc_html($reserva['guests'] ?? '-') . '</td>';
                        echo '<td>' . esc_html($reserva['checkin'] ?? '-') . '</td>';
                        echo '<td>' . esc_html($reserva['checkout'] ?? '-') . '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="5">Nenhuma reserva encontrada ou erro na API.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}
