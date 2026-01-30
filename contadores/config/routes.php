<?php
$router = new Router();
$router->get('/', 'AuthController@login');
$router->get('/login', 'AuthController@login');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout', ['auth']);

$router->get('/dashboard', 'DashboardController@index', ['auth']);

$router->get('/clientes', 'ClienteController@index', ['auth']);
$router->get('/clientes/crear', 'ClienteController@create', ['auth']);
$router->post('/clientes/crear', 'ClienteController@create', ['auth']);
$router->get('/clientes/editar', 'ClienteController@edit', ['auth']);
$router->post('/clientes/editar', 'ClienteController@edit', ['auth']);
$router->get('/clientes/eliminar', 'ClienteController@delete', ['auth', 'admin']);

$router->get('/documentos', 'DocumentoController@index', ['auth']);
$router->post('/documentos/subir', 'DocumentoController@upload', ['auth']);

$router->get('/vencimientos', 'VencimientoController@index', ['auth']);
$router->post('/vencimientos/crear', 'VencimientoController@create', ['auth']);
$router->get('/vencimientos/pagar', 'VencimientoController@pagar', ['auth']);

$router->get('/usuarios', 'UsuarioController@index', ['auth', 'admin']);
$router->post('/usuarios/crear', 'UsuarioController@crear', ['auth', 'admin']);

$router->get('/configuracion', 'ConfigController@index', ['auth', 'admin']);
return $router;

