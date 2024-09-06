<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function () use ($router) {
    //Metodos Genericos    
    $router->get('getEtapas/{iIdProyecto}', 'EtapaController@getEtapas');

    //Rutas Usuario
    $router->group(['prefix' => 'usuario'], function () use ($router) {
        $router->post('nuevoUsuario', 'UsuarioController@create');
        $router->post('login', 'UsuarioController@login');
    });

    //Rutas Admin
    $router->group(['prefix' => 'admin'], function () use ($router) {
        $router->post('getEtapasAdmin', 'EtapaController@getEtapasAdmin');
        $router->post('obtenerLotesEtapaAdmin', 'LoteController@obtenerLotesEtapaAdmin');
        $router->post('cambiarStatusLote', 'LoteController@cambiarStatusLote');
        $router->post('obtenerCotizacionesLote', 'CotizacionController@obtenerCotizacionesLote');  
    });

    //Rutas Cotizador
    $router->group(['prefix' => 'cotizador'], function () use ($router) {
        $router->get('obtenerLotesEtapa/{iIdEtapa}', 'LoteController@obtenerLotesEtapa');
        $router->get('obtenerPlazosPorEtapa/{iIdEtapa}', 'EtapaController@obtenerPlazosPorEtapa');
        $router->post('guardarCotizacion', 'CotizacionController@guardarCotizacion');      
    });
});
