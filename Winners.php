<?php
/**
 * Plugin Name: Reservas
 * Description: Plugin de reservas Embaixador
 * Author: IBloom
 * Version: 0.1
 * Text Domain: reservas
 */

// Cria o menu no admin
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

// Página do admin
function cmp_reservas_page()
{
    ?>
    <div class="wrap">
        <h1>Reservas</h1>

        <table class="widefat fixed">
            <thead>
                <tr>
                    <th>Apartamento</th>
                    <th>Nome</th>
                    <th>Número de pessoas</th>
                    <th>Check-In</th>
                    <th>Check-Out</th>
                </tr>
            </thead>
            <tbody>
                <tr><td colspan="5">Exemplo estático (podes substituir depois pelos dados da API)</td></tr>
            </tbody>
        </table>

        <br>
        <button class="button button-primary" onclick="callAPI()">Teste</button>
    </div>
    <?php
}

// Carrega JS no admin
add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'toplevel_page_cmp-reservas') return;

    wp_enqueue_script(
        'reservas-api-script',
        plugin_dir_url(__FILE__) . 'reservas.js',
        ['jquery'],
        null,
        true
    );

    wp_localize_script('reservas-api-script', 'reservasAjax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('reservas_nonce')
    ]);
});

// AJAX handler
add_action('wp_ajax_reservas_call_api', function () {
    check_ajax_referer('reservas_nonce', 'nonce');

    $api_url = 'https://app.hostkit.pt/api/getReservations?APIKEY=5aQElqgU34RIgKDsKxIfuqzjVFR7eH8XxUgZ1StjpcD3rTrJRI';
    $response = wp_remote_get($api_url);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => $response->get_error_message()]);
    }

    $body = wp_remote_retrieve_body($response);
    $dados = json_decode($body, true);
    wp_send_json_success($dados);
});
