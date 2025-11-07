<?php

return [

    'pdf' => [
        'enabled' => true,
        'binary'  => env(
            'WKHTMLTOPDF_BINARY',
            '"C:\\Program Files\\wkhtmltopdf\\bin\\wkhtmltopdf.exe"'
        ),
        'timeout' => false,
        'options' => [
            'no-outline'               => true,
            'enable-javascript'        => false,  // tidak perlu JS
            'enable-local-file-access' => true,   // meski tidak ada file lokal
        ],
        'env' => [],
    ],

    'image' => [
        'enabled' => true,
        'binary'  => env('WKHTML_IMG_BINARY', '/usr/local/bin/wkhtmltoimage'),
        'timeout' => false,
        'options' => [],
        'env'     => [],
    ],

];
