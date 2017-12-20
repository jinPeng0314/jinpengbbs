<?php

return array(

    /*
     * 后台的 URI 入口
     * Package URI
     *
     * @type string
     */
    'uri' => 'admin',

    /*
     * 后台专属域名，没有的话可以留空
     *  Domain for routing.
     *
     *  @type string
     */
    'domain' => '',

    /*
     * 应用名称，在页面标题和左上角站点名称处显示
     * Page title
     *
     * @type string
     */
    'title' => env('APP_NAME', 'Laravel'),

    /*
     * 模型配置信息文件存放目录
     * The path to your model config directory
     *
     * @type string
     */
    'model_config_path' => config_path('administrator'),

    /*
     * 配置信息文件存放目录
     * The path to your settings config directory
     *
     * @type string
     */
    'settings_config_path' => config_path('administrator/settings'),

    /*
     * The menu structure of the site. For models, you should either supply the name of a model config file or an array of names of model config
     * files. The same applies to settings config files, except you must prepend 'settings.' to the settings config file name. You can also add
     * custom pages by prepending a view path with 'page.'. By providing an array of names, you can group certain models or settings pages
     * together. Each name needs to either have a config file in your model config path, settings config path with the same name, or a path to a
     * fully-qualified Laravel view. So 'users' would require a 'users.php' file in your model config path, 'settings.site' would require a
     * 'site.php' file in your settings config path, and 'page.foo.test' would require a 'test.php' or 'test.blade.php' file in a 'foo' directory
     * inside your view directory.
     *
     * @type array
     *
     * 	array(
     *		'E-Commerce' => array('collections', 'products', 'product_images', 'orders'),
     *		'homepage_sliders',
     *		'users',
     *		'roles',
     *		'colors',
     *		'Settings' => array('settings.site', 'settings.ecommerce', 'settings.social'),
     * 		'Analytics' => array('E-Commerce' => 'page.ecommerce.analytics'),
     *	)
     */
    'menu' => [
        '用户与权限' => [
            'users',
        ],
    ],

    /*
    * 权限控制的回调函数。
    *
    * 此回调函数需要返回 true 或 false ，用来检测当前用户是否有权限访问后台。
    * `true` 为通过，`false` 会将页面重定向到 `login_path` 选项定义的 URL 中。
    */
    'permission' => function () {
        // 只要是能管理内容的用户，就允许访问后台
        return Auth::check() && Auth::user()->can('manage_contents');
    },

    /*
     * 使用布尔值来设定是否使用后台主页面。
     *
     * 如值为 `true`，将使用 `dashboard_view` 定义的视图文件渲染页面；
     * 如值为 `false`，将使用 `home_page` 定义的菜单条目来作为后台主页。
     */
    'use_dashboard' => false,

    // 设置后台主页视图文件，由 `use_dashboard` 选项决定
    'dashboard_view' => '',

    // 用来作为后台主页的菜单条目，由 `use_dashboard` 选项决定，菜单指的是 `menu` 选项
    'home_page' => 'users',

    // 右上角『返回主站』按钮的链接
    'back_to_site_path' => '/',

    // 当选项 `permission` 权限检测不通过时，会重定向用户到此处设置的路径
    'login_path' => 'login',

    // 允许在登录成功后使用 Session::get('redirect') 将用户重定向到原本想要访问的后台页面
    'login_redirect_key' => 'redirect',

    // 控制模型数据列表页默认的显示条目
    'global_rows_per_page' => 20,

    // 可选的语言，如果不为空，将会在页面顶部显示『选择语言』按钮
    'locales' => [],

//    'custom_routes_file' => app_path('Http/routes/administrator.php'),
);
