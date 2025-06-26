<?php
/**
 * Plugin Name: Reservas
 * Description: Plugin de reservas embaixador
 * Author: IBloom
 * Author URI: https://www.ibloom.digital/pt/agencia-digital-lisboa/
 * Version: 0.1
 * Text Domain: Reservas
 * 
 */

 // Function to add the "Reservas" menu item
function cmp_add_reservas_menu() {
    add_menu_page(
        'Reservas',          // Page title
        'Reservas',          // Menu title
        'manage_options',   // Capability
        'cmp-reservas',      // Menu slug
        'cmp_reservas_page', // Function to display the page content
        'dashicons-awards', // Icon URL (Dashicons: https://developer.wordpress.org/resource/dashicons/)
        2                   // Position
    );
}

// Hook the function to 'admin_menu' action
add_action('admin_menu', 'cmp_add_reservas_menu');

// Function to display the "Winners" page content
function cmp_reservas_page() {
    ?>
    <h1>Reservas</h1>
    <table>
  <tr>
    <th>Apartamento</th>
    <th>Nome</th>
    <th>Numero de pessoas</th>
    <th>Check-In</th>
    <th>Check-Out</th>
  </tr>
  <tr>
    <td>Alfreds Futterkiste</td>
    <td>Maria Anders</td>
    <td>Germany</td>
  </tr>
  <tr>
    <td>Centro comercial Moctezuma</td>
    <td>Francisco Chang</td>
    <td>Mexico</td>
  </tr>
  <tr>
    <td>Ernst Handel</td>
    <td>Roland Mendel</td>
    <td>Austria</td>
  </tr>
  <tr>
    <td>Island Trading</td>
    <td>Helen Bennett</td>
    <td>UK</td>
  </tr>
  <tr>
    <td>Laughing Bacchus Winecellars</td>
    <td>Yoshi Tannamuri</td>
    <td>Canada</td>
  </tr>
  <tr>
    <td>Magazzini Alimentari Riuniti</td>
    <td>Giovanni Rovelli</td>
    <td>Italy</td>
  </tr>
</table>

<button onclick="callAPI()">Teste</button>
    <?php
}

function callAPI() {
  // Enfileirar o JS
  wp_enqueue_script('widget-contacto-js');

  // Chamada à API
  $api_url = 'https://app.hostkit.pt/api/getReservations?APIKEY=5aQElqgU34RIgKDsKxIfuqzjVFR7eH8XxUgZ1StjpcD3rTrJRI';
  $response = wp_remote_get($api_url);

  if (!is_wp_error($response)) {
      $body = wp_remote_retrieve_body($response);
      $dados = json_decode($body, true);

      // Passar os dados para o JS
      wp_add_inline_script('widget-contacto-js', 'window.reservasHostkit = ' . json_encode($dados) . ';', 'before');
  }
}

function widget_contacto_enqueue_script() {
    wp_register_script(
        'widget-contacto-js',
        plugins_url('/widget-contacto.js', __FILE__),
        [],
        null,
        true
    );
}
add_action('wp_enqueue_scripts', 'widget_contacto_enqueue_script');
