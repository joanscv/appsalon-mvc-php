<?php

namespace Controllers;

use Model\Servicio;
use MVC\Router;

class ServicioController {

  private static function getAdminName() {
    session_start();
    isAdmin();
    return $_SESSION['nombre'];
  }

  public static function index(Router $router) {

    $nombre = self::getAdminName();
    $servicios = Servicio::all();
    
    $router->render('admin/servicios/index', [
      'servicios' => $servicios,
      'nombre' => $nombre
    ]);
  }

  public static function crear(Router $router) {

    $nombre = self::getAdminName();

    $servicio = new Servicio;
    $alertas = [];

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
      $servicio->sincronizar($_POST);
      $alertas = $servicio->validar();

      if(empty($alertas)) {
        $servicio->guardar();
        header('Location: /servicios');
      }
    }

    $router->render('admin/servicios/crear', [
      'nombre' => $nombre,
      'servicio' => $servicio,
      'alertas' => $alertas
    ]);
  }

  public static function actualizar(Router $router) {

    $nombre = self::getAdminName();
    $alertas=[];

    $id=validarORedireccionar('/servicios');
    $servicio = Servicio::find($id);

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
      $servicio->sincronizar($_POST);
      $alertas = $servicio->validar();

      if(empty($alertas)){
        $servicio->guardar();
        header('Location: /servicios');
      }
    }

    $router->render('admin/servicios/actualizar', [
      'nombre' => $nombre,
      'alertas' => $alertas,
      'servicio' => $servicio
    ]);
  }

  public static function eliminar() {
    session_start();
    isAdmin();

    if($_SERVER['REQUEST_METHOD']==='POST') {

      $id=$_POST['id'];
      $id = filter_var($id, FILTER_VALIDATE_INT);
  
      if(!$id) {
        header("Location: /servicios");
      }

      $servicio = Servicio::find($id);
      $servicio->eliminar();
      header("Location: /servicios");
    }
  }
}