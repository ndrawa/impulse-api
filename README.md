# Lumen PHP Framework

[![Build Status](https://travis-ci.org/laravel/lumen-framework.svg)](https://travis-ci.org/laravel/lumen-framework)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel/framework)](https://packagist.org/packages/laravel/lumen-framework)
[![Latest Stable Version](https://img.shields.io/packagist/v/laravel/framework)](https://packagist.org/packages/laravel/lumen-framework)
[![License](https://img.shields.io/packagist/l/laravel/framework)](https://packagist.org/packages/laravel/lumen-framework)

Laravel Lumen is a stunningly fast PHP micro-framework for building web applications with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Lumen attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as routing, database abstraction, queueing, and caching.

## Configuration
- PHP >= 7.3

## Installation
1. terminal run "cp .env.example .env"
2. set your .env settings
    DB_CONNECTION = pgsql
    DB_PORT = 5432
3. still in .env, change APP_TIMEZONE to Asia/Jakarta
4. composer install
5. php artisan jwt:secret
6. php artisan migrate:fresh --seed
7. php -S localhost:8000 -t public

## Commit Rules
1. create new brach
2. commit message to master: vYY.MM.DD.XX [XX = date's version start from 00]

Time GMT+7
