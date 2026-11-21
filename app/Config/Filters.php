<?php
namespace Config;
class Filters extends \CodeIgniter\Config\Filters {
 public array $aliases = ['csrf'=>\CodeIgniter\Filters\CSRF::class,'auth'=>\App\Filters\AuthFilter::class];
 public array $required = ['before'=>[], 'after'=>[]];
 public array $globals = ['before'=>['csrf'],'after'=>[]];
 public array $methods = [];
 public array $filters = [];
}
