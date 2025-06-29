<?php

return [

    /**
     * Enable/disable the package
     */
    'enabled' => env('CSS_CLEANER_ENABLED', true),

    /**
     * 🎨 CSS Source Directory
     */
    'css_path' => public_path(),

    /**
     * 📁 Output Directory
     */
    'output_path' => public_path('css/optimized'),

    /**
     * 🔍 Content Files to Scan
     */
    'content_paths' => [
        'resources/views/**/*.blade.php',
        'resources/js/**/*.{vue,js}',
    ],

    /**
     * 🛡️ Safelist Configuration
     */
    'safelist' => [
        // Core
        'active',
        'show',
        'hidden',
        'collapse',
        // Animations
        'fade',
        'slide',
        'animate-*',
        // Framework specific
        'modal-*',
        '/^tooltip/',
        '/^carousel/',
    ],

    /**
     * ⚙️ PurgeCSS Options
     */
    'purge_options' => [
        'keyframes' => true,
        'fontFace' => true,
        'variables' => false,
        'rejected' => false
    ],

    /**
     * ✨ Minification Options
     */
    'minify_options' => [
        'remove_comments' => true,
        'preserve_license' => false,
        'advanced' => true
    ],

    /**
     * 🚫 Excluded Files
     */
    'exclude_files' => [
        // '*.min.css',
        // 'vendor/*.css'
    ]
];
