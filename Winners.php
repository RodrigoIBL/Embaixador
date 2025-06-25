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
     // Add subpages
     add_submenu_page(
        'cmp-winners',          // Parent slug
        'Ver recompensas',           // Page title
        'Ver recompensas',           // Menu title
        'manage_options',       // Capability
        'cmp-ver_recompensas',       // Menu slug
        'cmp_ver_recompensas'   // Function to display the subpage content
    );
    add_submenu_page(
        'cmp-winners',          // Parent slug
        'Adicionar Progresso',           // Page title
        'Adicionar Progresso',           // Menu title
        'manage_options',       // Capability
        'cmp-adicionar_progresso',       // Menu slug
        'cmp_adicionar_progresso'   // Function to display the subpage content
    );
}

// Hook the function to 'admin_menu' action
add_action('admin_menu', 'cmp_add_reservas_menu');

function buscar_dados() {
    
}

// Function to display the "Winners" page content
function cmp_winners_page() {
    ?>
    
    <?php
}
function cmp_ver_recompensas() {
    ?>
    
    <?php
}

function cmp_adicionar_progresso() {
    ?>
    
    <?php
}
