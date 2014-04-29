<?php

return [
    'console' => [
        'router' => [
            'routes' => [
                'spiffy-assetic.dump' => [
                    'options' => [
                        'route' => 'assetic dump [--verbose|-v]',
                        'defaults' => [
                            'controller' => 'SpiffyAssetic\Controller\ConsoleController',
                            'action' => 'dump'
                        ]
                    ]
                ],
                'spiffy-assetic.watch' => [
                    'options' => [
                        'route' => 'assetic watch [--force|-f] [--verbose|-v] [--period=]',
                        'defaults' => [
                            'controller' => 'SpiffyAssetic\Controller\ConsoleController',
                            'action' => 'watch',
                            'period' => 1
                        ]
                    ]
                ]
            ]
        ]
    ],
    'controllers' => [
        'invokables' => [
            'SpiffyAssetic\Controller\AssetController' => 'SpiffyAssetic\Controller\AssetController',
        ],
        'factories' => [
            'SpiffyAssetic\Controller\ConsoleController' => 'SpiffyAssetic\Controller\ConsoleControllerFactory',
        ]
    ],
    'listeners' => [
        // todo: implement
        //'SpiffyAssetic\Mvc\RouteLoader',
        'SpiffyAssetic\Mvc\RenderListener',
    ],
    'service_manager' => [
        'invokables' => [
            // mvc listener
            'SpiffyAssetic\Mvc\RenderListener' => 'SpiffyAssetic\Mvc\RenderListener',
        ],
        'factories' => [
            // assetic
            'Assetic\AssetWriter' => 'SpiffyAssetic\Assetic\AssetWriterFactory',
            'Assetic\Extension\Twig\AsseticExtension' => 'SpiffyAssetic\Twig\AsseticExtensionFactory',
            'Assetic\Factory\AssetFactory' => 'SpiffyAssetic\Assetic\AssetFactoryFactory',

            // assetic filters
            'Assetic\Filter\LessFilter' => 'SpiffyAssetic\Assetic\LessFilterFactory',

            // mvc listeners
            'SpiffyAssetic\Mvc\RouteLoader' => 'SpiffyAssetic\Mvc\RouteLoaderFactory',

            // plugins
            'SpiffyAssetic\Plugin\AssetLoaderPlugin' => 'SpiffyAssetic\Plugin\AssetLoaderPluginFactory',
            'SpiffyAssetic\Plugin\DirectoryLoaderPlugin' => 'SpiffyAssetic\Plugin\DirectoryLoaderPluginFactory',
            'SpiffyAssetic\Plugin\FilterLoaderPlugin' => 'SpiffyAssetic\Plugin\FilterLoaderPluginFactory',
            'SpiffyAssetic\Plugin\TwigLoaderPlugin' => 'SpiffyAssetic\Plugin\TwigLoaderPluginFactory',

            'SpiffyAssetic\AsseticService' => 'SpiffyAssetic\AsseticServiceFactory',
            'SpiffyAssetic\ModuleOptions' => 'SpiffyAssetic\ModuleOptionsFactory',
        ]
    ],
    'spiffy-assetic' => [
        'debug' => false,
        'autoload' => false,
        'root_dir' => './',
        'output_dir' => 'public',
        'assets' => [],
        'filters' => [
            'cssmin' => 'Assetic\Filter\CssMinFilter',
            'jsmin' => 'Assetic\Filter\JsMinFilter',
            'less' => 'Assetic\Filter\LessFilter',
        ],
        'filter_options' => [
            'less' => [
                'node_bin' => '/usr/bin/node',
                'node_paths' => ['/usr/lib/node_modules'],
                'load_paths' => [],
            ]
        ],
        'variables' => [],
        'console_plugins' => [
            'SpiffyAssetic\Plugin\DirectoryLoaderPlugin',
        ],
        'plugins' => [
            'SpiffyAssetic\Plugin\AssetLoaderPlugin',
            'SpiffyAssetic\Plugin\FilterLoaderPlugin',
        ]
    ],
    'zfctwig' => [
        'extensions' => [
            'assetic' => 'Assetic\Extension\Twig\AsseticExtension'
        ],
    ]
];
