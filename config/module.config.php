<?php
return array(
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'oauth2provider' => include_once __DIR__ . '/OAuth2provider.config.php',
    'router'             => include_once __DIR__ . '/routes.config.php',
);