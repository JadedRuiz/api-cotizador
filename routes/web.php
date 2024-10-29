<?php

use App\Http\Controllers\LoteController;

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
        //Rutas lote
        $router->group(['prefix' => 'lote'], function () use ($router) {
            $router->post('obtenerLotesEtapa', 'LoteController@obtenerLotesEtapa');
            $router->post('cambiarStatusLote', 'LoteController@cambiarStatusLote');
            $router->post('guardarLote', 'LoteController@guardarLote');
        });
        $router->post('obtenerCotizacionesLote', 'CotizacionController@obtenerCotizacionesLote');
        $router->post('getEtapasAdmin', 'EtapaController@getEtapasAdmin');
        $router->post('generarCotizacionAdmin', 'CotizacionController@generarCotizacionAdmin');    
    });

    //Rutas Cotizador
    $router->group(['prefix' => 'cotizador'], function () use ($router) {
        $router->get('getLotesPorEtapaId/{iIdEtapa}', 'LoteController@getLotesPorEtapaId');
        $router->get('obtenerPlazosPorEtapa/{iIdEtapa}', 'EtapaController@obtenerPlazosPorEtapa');
        $router->post('guardarCotizacion', 'CotizacionController@guardarCotizacion');      
    });
});
