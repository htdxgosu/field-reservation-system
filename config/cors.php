<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'], // Đảm bảo rằng send-otp là một trong các đường dẫn được phép
    'allowed_methods' => ['*'],  // Cho phép tất cả các phương thức HTTP
    'allowed_origins' => ['*'],  // Cho phép tất cả các nguồn (có thể thay bằng địa chỉ frontend cụ thể)
    'allowed_headers' => ['*'],  // Cho phép tất cả các header
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];