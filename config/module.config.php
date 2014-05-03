<?php

return [
    'console' => [
        'router' => [
            'routes' => [
                'spiffy-assetic.dump' => [
                    'options' => [
                        'route' => 'assetic dump [--verbose|-v]',
                        'defaults' => [
                            'controller' => 'Spiffy\Assetic\Controller\ConsoleController',
                            'action' => 'dump'
                        ]
                    ]
                ],
                'spiffy-assetic.watch' => [
                    'options' => [
                        'route' => 'assetic watch [--force|-f] [--verbose|-v] [--period=]',
                        'defaults' => [
                            'controller' => 'Spiffy\Assetic\Controller\ConsoleController',
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
            'Spiffy\Assetic\Controller\AssetController' => 'Spiffy\Assetic\Controller\AssetController',
        ],
        'factories' => [
            'Spiffy\Assetic\Controller\ConsoleController' => 'Spiffy\Assetic\Controller\ConsoleControllerFactory',
        ]
    ],
    'listeners' => [
        // todo: implement
        //'Spiffy\Assetic\Mvc\RouteLoader',
        'Spiffy\Assetic\Mvc\RenderListener',
    ],
    'service_manager' => [
        'invokables' => [
            // mvc listener
            'Spiffy\Assetic\Mvc\RenderListener' => 'Spiffy\Assetic\Mvc\RenderListener',
        ],
        'factories' => [
            // assetic
            'Assetic\AssetWriter' => 'Spiffy\Assetic\Assetic\AssetWriterFactory',
            'Assetic\Extension\Twig\AsseticExtension' => 'Spiffy\Assetic\Twig\AsseticExtensionFactory',
            'Assetic\Factory\AssetFactory' => 'Spiffy\Assetic\Assetic\AssetFactoryFactory',

            // assetic filters
            'Assetic\Filter\LessFilter' => 'Spiffy\Assetic\Assetic\Filter\LessFilterFactory',
            'Spiffy\Assetic\Assetic\Filter\CssModulePathFilter' => 'Spiffy\Assetic\Assetic\Filter\CssModulePathFilterFactory',

            // mvc listeners
            'Spiffy\Assetic\Mvc\RouteLoader' => 'Spiffy\Assetic\Mvc\RouteLoaderFactory',

            // plugins
            'Spiffy\Assetic\Plugin\AssetLoaderPlugin' => 'Spiffy\Assetic\Plugin\AssetLoaderPluginFactory',
            'Spiffy\Assetic\Plugin\DirectoryLoaderPlugin' => 'Spiffy\Assetic\Plugin\DirectoryLoaderPluginFactory',
            'Spiffy\Assetic\Plugin\FilterLoaderPlugin' => 'Spiffy\Assetic\Plugin\FilterLoaderPluginFactory',
            'Spiffy\Assetic\Plugin\TwigLoaderPlugin' => 'Spiffy\Assetic\Plugin\TwigLoaderPluginFactory',

            'Spiffy\Assetic\AsseticService' => 'Spiffy\Assetic\AsseticServiceFactory',
            'Spiffy\Assetic\ModuleOptions' => 'Spiffy\Assetic\ModuleOptionsFactory',
        ]
    ],
    'spiffy-assetic' => [
        'debug' => false,
        'autoload' => false,
        'root_dir' => './',
        'output_dir' => 'public',
        'assets' => [],
        'filters' => [
            'cssembed' => 'Assetic\Filter\PhpCssEmbedFilter',
            'cssmin' => 'Assetic\Filter\CssMinFilter',
            'cssmodulepath' => 'Spiffy\Assetic\Assetic\Filter\CssModulePathFilter',
            'cssrewrite' => 'Assetic\Filter\CssRewriteFilter',
            'jsmin' => 'Assetic\Filter\JSMinFilter',
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
            'Spiffy\Assetic\Plugin\DirectoryLoaderPlugin',
        ],
        'parsers' => [
            'javascripts' => ['tag' => 'javascripts', 'output' => 'js/*.js'],
            'stylesheets' => ['tag' => 'stylesheets', 'output' => 'css/*.css'],
            'image' => ['tag' => 'image', 'output' => 'image/*', 'single' => true],
        ],
        'plugins' => [
            'asset_loader' => 'Spiffy\Assetic\Plugin\AssetLoaderPlugin',
            'filter_loader' => 'Spiffy\Assetic\Plugin\FilterLoaderPlugin',
        ],
    ],
    'zfctwig' => [
        'extensions' => [
            'assetic' => 'Assetic\Extension\Twig\AsseticExtension'
        ],
    ]
];
