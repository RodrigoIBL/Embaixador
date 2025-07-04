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

    $datasReservadas = []; // Vamos guardar todas as datas reservadas aqui

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

        // Vamos preencher todas as datas entre checkin e checkout no array $datasReservadas
        if ($checkinUnix && $checkoutUnix && $checkoutUnix >= $checkinUnix) {
            for ($dia = $checkinUnix; $dia <= $checkoutUnix; $dia += 86400) { // 86400s = 1 dia
                $datasReservadas[] = date('Y-m-d', $dia);
            }
        }
    }

    echo '</tbody>';
    echo '</table>';

    // Remover duplicados
    $datasReservadas = array_unique($datasReservadas);

    // Passar as datas reservadas para o JavaScript
    ?>
    <div id="calendario-reservas"></div>

    <style>
        #calendario-reservas {
            max-width: 600px;
            margin-top: 30px;
            font-family: Arial, sans-serif;
        }
        #calendario-reservas table {
            width: 100%;
            border-collapse: collapse;
        }
        #calendario-reservas th, #calendario-reservas td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
        #calendario-reservas th {
            background-color: #f2f2f2;
        }
        .data-reservada {
            text-decoration: line-through;
            color: red;
            font-weight: bold;
        }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const datasReservadas = <?php echo json_encode($datasReservadas); ?>;
        
        // Função para criar o calendário do mês atual
        function criarCalendario() {
            const container = document.getElementById('calendario-reservas');
            const hoje = new Date();
            const ano = hoje.getFullYear();
            const mes = hoje.getMonth();

            // Primeiro dia do mês e quantos dias tem o mês
            const primeiroDiaSemana = new Date(ano, mes, 1).getDay(); // 0=Domingo..6=Sabado
            const totalDias = new Date(ano, mes + 1, 0).getDate();

            // Dias da semana em português
            const diasSemana = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

            let html = `<h2>Calendário - ${ano}-${(mes + 1).toString().padStart(2, '0')}</h2>`;
            html += '<table><thead><tr>';
            for (let d of diasSemana) {
                html += `<th>${d}</th>`;
            }
            html += '</tr></thead><tbody><tr>';

            // Espaços antes do primeiro dia do mês
            let diaSemanaIndex = primeiroDiaSemana;
            if (diaSemanaIndex === 0) diaSemanaIndex = 7; // Domingo como 7 para colocar no final da semana

            for (let i = 1; i < diaSemanaIndex; i++) {
                html += '<td></td>';
            }

            for (let dia = 1; dia <= totalDias; dia++) {
                const dataFormatada = `${ano}-${(mes + 1).toString().padStart(2, '0')}-${dia.toString().padStart(2, '0')}`;
                const reservado = datasReservadas.includes(dataFormatada);

                if (reservado) {
                    html += `<td class="data-reservada">${dia}</td>`;
                } else {
                    html += `<td>${dia}</td>`;
                }

                if ((dia + diaSemanaIndex - 1) % 7 === 0) {
                    html += '</tr><tr>';
                }
            }

            // Completar a última linha com células vazias
            const resto = (totalDias + diaSemanaIndex - 1) % 7;
            if (resto !== 0) {
                for (let i = resto; i < 7; i++) {
                    html += '<td></td>';
                }
            }

            html += '</tr></tbody></table>';
            container.innerHTML = html;
        }

        criarCalendario();
    });
    </script>

    <?php
    echo '</div>';
}
