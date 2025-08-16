<?php

namespace Arvand\ArvandPanel\Services;

defined('ABSPATH') || exit;

class WPAPRouter
{
    private string $endpoint;
    private array $routes = [];

    public function __construct()
    {
        $this->endpoint = strtolower(get_query_var('wpap-endpoint'));
    }

    public function add($menu_name, $callback): void
    {
        $menus = wpap_get_global_data('menus');

        // If $menu_name is array, index 0: menu name and 1: menu route
        $name = is_array($menu_name) ? $menu_name[0] : $menu_name;

        if (!isset($menus[$name]) || !wpap_is_valid_section($menus[$name]->menu_name)) {
            return;
        }

        $menu = $menus[$name];
        $route = ltrim(is_array($menu_name) ? $menu_name[1] : $menu->route, '/'); // Get route
        $this->routes[$route] = ['menu_name' => $menu->menu_name, 'callback' => $callback];
    }

    public function dispatch()
    {
        foreach ($this->routes as $route => $value) {
            $pattern = preg_replace("#\{\w+\}#", "([^\/]+)", $route);

            if (empty($this->endpoint)) {
                return wp_redirect(wpap_get_page_url_by_name('dash'));
            }

            if (preg_match("#^$pattern$#", $this->endpoint, $matches)) {
                array_shift($matches);
                set_query_var('wpap_current_route', $value['menu_name']);

                if (is_array($value['callback']) && method_exists($value['callback'][0], $value['callback'][1])) {
                    $controller_class = $value['callback'][0];
                    $controller_method = $value['callback'][1];
                    return call_user_func_array([new $controller_class, $controller_method], $matches);
                }

                return call_user_func_array($value['callback'], $matches);
            }
        }

        return wpap_view('notfound');
    }
}
