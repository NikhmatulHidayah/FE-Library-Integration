<?php

/*
 * This file is part of jwt-auth.
 *
 * (c) Sean Tymon <tymon148@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    'secret' => env('JWT_SECRET'), // JWT secret key
    'keys' => [
        'public' => env('JWT_PUBLIC_KEY'),  // Path ke public key untuk RSA atau EC
        'private' => env('JWT_PRIVATE_KEY'), // Path ke private key untuk RSA atau EC
    ],
    'ttl' => env('JWT_TTL', 60),
    'algo' => env('JWT_ALGO', Tymon\JWTAuth\Providers\JWT\Provider::ALGO_HS256), // Default HS256
    'providers' => [
        'jwt' => Tymon\JWTAuth\Providers\JWT\Lcobucci::class,
        'auth' => Tymon\JWTAuth\Providers\Auth\Illuminate::class,
        'storage' => Tymon\JWTAuth\Providers\Storage\Illuminate::class,
    ],
];

