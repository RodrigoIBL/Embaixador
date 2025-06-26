<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined(constant_name: 'ABSPATH')) exit;

public function get_name() {
    return 'hello_widget';
}
public function get_title() {
    return 'Hello Widget';
}
public function get_icon() {
    return 'eicon-text';
}

public function get_categories() {
    return ['general'];
}

public function render(): void {
    echo '<h2>This is a custom widget!</h2>';
}