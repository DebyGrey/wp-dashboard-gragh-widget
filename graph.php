<?php

/**
 * Plugin Name:       Graph
 * Description:       Graph widget plugin
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Deborah Fashoro
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       graph
 */

add_action('admin_enqueue_scripts', 'graph_enqueue_scripts');

/**
 * Enqueue scripts and styles.
 *
 * @return void
 */
function graph_enqueue_scripts()
{
    wp_enqueue_style('graph-style', plugin_dir_url(__FILE__) . 'build/index.css');
    wp_enqueue_script('graph-script', plugin_dir_url(__FILE__) . 'build/index.js', array('wp-element'), '1.0.0', true);
}

// require_once(plugin_dir_path(__FILE__) . '/includes/widgets/graph-widget.php');

// function register_graph()
// {
//     register_widget('Graph_Widget');
// }

// add_action('widgets_init', 'register_graph');


add_action('wp_dashboard_setup', 'graph_dashboard_widgets');

function graph_dashboard_widgets()
{
    wp_add_dashboard_widget('graph_widget', 'Graph Widget', 'dashboard_graph');
}

function dashboard_graph()
{
    echo "<div id='graph'></div>";
}

function my_graph_create_db()
{

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'graph';

    $sql = "CREATE TABLE $table_name (
            graph_id bigint(20) NOT NULL AUTO_INCREMENT,
            duration varchar(255) NOT NULL,
            data longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`data`)),
            PRIMARY KEY (`graph_id`)
        ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    $sevenDays = [
        [
            "name" => "Page A",
            "uv" => 4000,
            "pv" => 2700,
            "amt" => 2400
        ],
        [
            "name" => "Page B",
            "uv" => 3000,
            "pv" => 1998,
            "amt" => 2210
        ],
        [
            "name" => "Page C",
            "uv" => 1000,
            "pv" => 9800,
            "amt" => 2290
        ],
        [
            "name" => "Page D",
            "uv" => 7780,
            "pv" => 3908,
            "amt" => 2000
        ],
        [
            "name" => "Page E",
            "uv" => 9890,
            "pv" => 4800,
            "amt" => 2181
        ],
        [
            "name" => "Page F",
            "uv" => 2390,
            "pv" => 2800,
            "amt" => 2500
        ],
        [
            "name" => "Page G",
            "uv" => 3490,
            "pv" => 1300,
            "amt" => 2100
        ]
    ];
    $fifteenDays = [
        [
            "name" => "Page A",
            "uv" => 590,
            "pv" => 800,
            "amt" => 1400,
        ],
        [
            "name" => "Page B",
            "uv" => 868,
            "pv" => 967,
            "amt" => 1506,
        ],
        [
            "name" => "Page C",
            "uv" => 1397,
            "pv" => 1098,
            "amt" => 989,
        ],
        [
            "name" => "Page D",
            "uv" => 1480,
            "pv" => 1200,
            "amt" => 1228,
        ],
        [
            "name" => "Page E",
            "uv" => 1520,
            "pv" => 1108,
            "amt" => 1100,
        ],
        [
            "name" => "Page F",
            "uv" => 1400,
            "pv" => 680,
            "amt" => 1700,
        ],
        [
            "name" => "Page G",
            "uv" => 590,
            "pv" => 800,
            "amt" => 1400,
        ],
    ];

    $lastMonth = [
  [
    "name" => "Page A",
    "uv" => 4000,
    "pv" => 2400,
    "amt" => 2400,
  ],
  [
    "name" => "Page B",
    "uv" => 3000,
    "pv" => 1398,
    "amt" => 2210,
  ],
  [
    "name" => "Page C",
    "uv" => 2000,
    "pv" => 9800,
    "amt" => 2280,
  ],
  [
    "name" => "Page D",
    "uv" => 2780,
    "pv" => 3908,
    "amt" => 2000,
  ],
  [
    "name" => "Page E",
    "uv" => 1890,
    "pv" => 4800,
    "amt" => 2181,
  ],
  [
    "name" => "Page F",
    "uv" => 2390,
    "pv" => 3800,
    "amt" => 2500,
  ],
  [
    "name" => "Page G",
    "uv" => 3490,
    "pv" => 4300,
    "amt" => 2100,
  ],
];

    $wpdb->insert($table_name, [
        'duration' => 'Last 7 days',
        'data' => json_encode($sevenDays),
    ]);

    $wpdb->insert($table_name, [
        'duration' => 'Last 15 days',
        'data' => json_encode($fifteenDays),
    ]);

    $wpdb->insert($table_name, [
        'duration' => 'Last Month',
        'data' => json_encode($lastMonth),
    ]);
}

register_activation_hook(__FILE__, 'my_graph_create_db');


add_action('rest_api_init', 'register_api_route');
function register_api_route()
{

    register_rest_route(
        'wp/v2',
        'graph',
        array(
            'methods' => 'GET',
            'callback' => 'get_graph',
        )
    );
}

function get_graph(WP_REST_Request $request)
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'graph';
    $sql = "SELECT * FROM $table_name";
    $posts = $wpdb->get_results($sql);

    return $posts;
}
