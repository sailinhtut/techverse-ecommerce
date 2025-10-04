<?php

use Illuminate\Foundation\Inspiring;
// use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
// use Illuminate\Routing\Router;
// use Illuminate\Foundation\Configuration\Middleware as MiddlewareConfig;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 
// Artisan::command('routes:passed-middleware-group', function () {
//     $routes = collect(Route::getRoutes())->sortBy(fn($route) => $route->uri());
// 
//     $this->info(str_pad('Method', 10) . str_pad('URI', 60) . 'Middleware');
//     $this->info(str_repeat('-', 80));
// 
//     foreach ($routes as $route) {
//         $method = implode('|', $route->methods());
//         $uri = $route->uri();
//         $middlewares = implode(', ', $route->gatherMiddleware());
// 
//         $this->line(str_pad($method, 10) . str_pad($uri, 60) . $middlewares);
//     }
// })->purpose('List All Middleware');
// 
// 
// Artisan::command('routes:passed-middleware {uri : The URI of the route} {--method=get : HTTP method (get, post, put, delete)}', function (Router $router) {
//     $uri = trim($this->argument('uri'), '/');
//     $method = strtolower($this->option('method') ?? 'get');
// 
//     $routes = Route::getRoutes();
//     $route = collect($routes)->first(function ($r) use ($uri, $method) {
//         $routeUri = trim($r->uri(), '/');
//         $methods = array_map('strtolower', $r->methods());
//         return $routeUri === $uri && in_array($method, $methods);
//     });
// 
//     if (!$route) {
//         $this->error("Route '{$uri}' with method '{$method}' not found.");
//         return 1;
//     }
// 
//     $middlewareConfig = app(MiddlewareConfig::class);
//     $globalMiddleware = $middlewareConfig->getGlobalMiddleware();
//     $middlewareAlias = $middlewareConfig->getMiddlewareAliases();
//     $middlewareGroups = $middlewareConfig->getMiddlewareGroups();
// 
//     $this->info("");
//     $this->warn("Middleware Information");
//     $this->info("");
// 
//     // Global Middleware
//     $global_rows = [];
//     foreach ($globalMiddleware as $class) {
//         $global_rows[] = [$class];
//     }
//     $this->table(['Global Middleware(s)'], $global_rows);
// 
//     // Middleware Aliases
//     $middleware_alias_rows = [];
//     foreach ($middlewareAlias as $class => $value) {
//         $middleware_alias_rows[] = [$class, $value];
//     }
//     $this->table(['Middleware Alias', 'Corresponding Classes'], $middleware_alias_rows);
// 
//     // Middleware Groups
//     $middleware_group_rows = [];
//     foreach ($middlewareGroups as $class => $value) {
//         $middleware_group_rows[] = [$class, json_encode($value, JSON_PRETTY_PRINT)];
//     }
//     $this->table(['Middleware Groups', 'Member Middlewares'], $middleware_group_rows);
// 
//     // Route-specific middleware
//     $rows = [];
//     foreach ($globalMiddleware as $class) {
//         $rows[] = ['(global)', $class];
//     }
// 
//     $routeMiddleware = $route->gatherMiddleware();
// 
//     $expandMiddleware = function ($name) use (&$expandMiddleware, $middlewareGroups, $middlewareAlias) {
//         $expanded = [];
// 
//         if (isset($middlewareGroups[$name])) {
//             foreach ($middlewareGroups[$name] as $member) {
//                 $expanded = array_merge($expanded, $expandMiddleware($member));
//             }
//         } elseif (str_starts_with($name, 'auth:')) {
//             $guard = explode(':', $name, 2)[1] ?? null;
//             $class = $middlewareAlias['auth'] ?? \Illuminate\Auth\Middleware\Authenticate::class;
//             $expanded[] = $class . ($guard ? " (guard: {$guard})" : '');
//         } else {
//             [$alias, $params] = explode(':', $name, 2) + [null, null];
//             $class = $middlewareAlias[$alias] ?? $alias;
//             if ($params) $class .= " (params: {$params})";
//             $expanded[] = $class;
//         }
// 
//         return $expanded;
//     };
// 
//     foreach ($routeMiddleware as $name) {
//         foreach ($expandMiddleware($name) as $class) {
//             $rows[] = [$name, $class];
//         }
//     }
// 
//     $this->info("");
//     $this->warn("Middleware(s) for route /{$uri} [HTTP:{$method}]");
//     $this->info("");
//     $this->table(['Passed Middleware(s)', 'Middleware Classes'], $rows);
// 
//     return 0;
// })->purpose('List all middleware (with classes) for a given route, including global middleware and expanded groups');
// 
// 
// Artisan::command('middlewares:group-list {group=all : web, api or all}', function (Router $router) {
//     $group = $this->argument('group');
//     $groups = $router->getMiddlewareGroups();
// 
//     if ($group === 'all') {
//         foreach (['web', 'api'] as $g) {
//             $this->line("\n<info>Group: {$g}</info>");
//             displayGroup($this, $groups[$g] ?? []);
//         }
//     } else {
//         if (!isset($groups[$group])) {
//             $this->error("Middleware group [{$group}] not found.");
//             return;
//         }
//         $this->line("\n<info>Group: {$group}</info>");
//         displayGroup($this, $groups[$group]);
//     }
// })->purpose('List all middleware pipelines for web and api routes');
// 
// // Helper function to display group
// function displayGroup($command, array $middlewares)
// {
//     $rows = [];
//     foreach ($middlewares as $m) {
//         $rows[] = [$m];
//     }
//     $command->table(['Middleware'], $rows);
// }
