<?php

return [
    // Use Argon as the default password hasher. Argon2id is used by PHP when available.
    'driver' => env('HASH_DRIVER', 'argon'),

    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 12),
    ],

    // Strong Argon2id defaults. Tune based on server capacity.
    'argon' => [
        'memory' => env('ARGON2_MEMORY', 65536), // KiB (64MB)
        'threads' => env('ARGON2_THREADS', 1),
        'time' => env('ARGON2_TIME', 4),
        // If the framework supports specifying type, prefer Argon2id
        // 'type' => PASSWORD_ARGON2ID,
    ],
];
