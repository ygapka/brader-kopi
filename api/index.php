<?php

// Ensure /tmp/views exists for compiled Blade templates in serverless environment
if (! is_dir('/tmp/views')) {
    @mkdir('/tmp/views', 0755, true);
}

require __DIR__.'/../public/index.php';
