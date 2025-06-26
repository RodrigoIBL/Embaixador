# Embaixador


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
        
        // As datas na API estão em timestamp Unix em string, converter para data legível
        $checkin = !empty($reserva['in_date']) ? date('Y-m-d', intval($reserva['in_date'])) : '';
        $checkout = !empty($reserva['out_date']) ? date('Y-m-d', intval($reserva['out_date'])) : '';

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