<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {

  public static function login(Router $router){

    $alertas = [];

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
      $auth = new Usuario($_POST);
      $alertas = $auth->validarLogin();

      if(empty($alertas)) {
        $usuario = Usuario::where('email', $auth->email);

        if($usuario) {
          if($usuario->comprobarPasswordAndVerificado($auth->password)):
            // Autenticar el usuario
            session_start();
            $_SESSION['id'] = $usuario->id;
            $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido; 
            $_SESSION['email'] = $usuario->email;
            $_SESSION['login'] = true;

            // Redireccionamiento
            if($usuario->admin === '1') {
              $_SESSION['admin'] = $usuario->admin ?? NULL;
              header('Location: /admin');
            } else {
              header('Location: /cita');
            }

          endif;

        } else {
          Usuario::setAlerta('error', 'Usuario no encontrado');
        }

      }
    }

    $alertas = Usuario::getAlertas();
    
    $router->render('auth/login', [
      'alertas'=>$alertas
    ]);
  }
  public static function logout(){
    session_start();
    $_SESSION = [];
    header('Location: /');
  }
  public static function olvide(Router $router){

    $alertas = [];

    if($_SERVER['REQUEST_METHOD']==='POST'){

      $auth = new Usuario($_POST);
      $alertas = $auth->validarEmail();

      if(empty($alertas)) {
        $usuario = Usuario::where('email', $auth->email);
        
        if($usuario && $usuario->confirmado === '1'){

          $usuario->crearToken();
          $usuario->guardar();
          
          $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
          $email->enviarInstrucciones();

          // Alerta de éxito
          Usuario::setAlerta('exito', "Revisa tu email");
     
        } else {
          Usuario::setAlerta('error', "El Usuario no existe o no está confirmado");
          
        }
      }
    }
    
    $alertas = Usuario::getAlertas();

    $router->render('auth/olvide-password', [
      'alertas'=>$alertas
    ]);
  }
  public static function recuperar(Router $router){

    $alertas = [];
    $error = false;

    $token = s($_GET['token']);
    // Buscar usuario por su token
    $usuario = Usuario::where('token', $token);

    if(empty($usuario)) {
      Usuario::setAlerta('error', "Token no válido");
      $error = true;
    }


    if($_SERVER['REQUEST_METHOD'] === 'POST') {
      // Leer nuevo password
      $password = new Usuario($_POST);
      $alertas = $password->validarPassword();

      if(empty($alertas)) {
        $usuario->password = NULL;
        $usuario->password = $password->password;
        $usuario->hashPassword();
        $usuario->token = NULL;

        $resultado = $usuario->guardar();

        if($resultado) {
          header('Location: /');
        }
      }

    }

    $alertas = Usuario::getAlertas();

    $router->render('auth/recuperar-password', [
      'alertas' => $alertas,
      'error' => $error
    ]);
  }
  public static function crear(Router $router){

    $usuario = new Usuario();

    // Alertas vacías
    $alertas = [];

    if($_SERVER['REQUEST_METHOD'] === 'POST'){

      $usuario->sincronizar($_POST);
      $alertas = $usuario->validarNuevaCuenta();

      // Revisar que el arreglo de alertas esté vacío
      if(empty($alertas)){
        $resultado = $usuario->existeUsuario();

        if($resultado->num_rows) {
          $alertas = Usuario::getAlertas();
        } else {
          // No está registrado el email suministrado, así que se puede utilizar para crear una nueva cuenta
          // Hashear el password
          $usuario->hashPassword();

          // Generar Token
          $usuario->crearToken();

          // Enviar email
          $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
          $email->enviarConfirmacion();

          // Crear el usuario
          $resultado = $usuario->guardar();
          if($resultado) {
            header('Location: /mensaje');
          }
        }
      }

    }

    $router->render('auth/crear-cuenta', [
      'usuario'=>$usuario,
      'alertas'=>$alertas
    ]);
  }

  public static function confirmar(Router $router) {

    $alertas = [];
    $token = s($_GET['token']);

    $usuario = Usuario::where('token', $token);

    if(empty($usuario)){
      Usuario::setAlerta('error', 'Token no válido');
    } else {
      // Modificar al usuario confirmado
      $usuario->confirmado = '1';
      $usuario->token = '';
      $usuario->guardar();
      Usuario::setAlerta('exito', "Cuenta comprobada correctamente");
    }

    // Obtener alertas
    $alertas = Usuario::getAlertas();

    // Renderizar la vista
    $router->render('auth/confirmar-cuenta', [
      'alertas'=>$alertas,
      'usuario'=>$usuario
    ]);
  }

  public static function mensaje(Router $router) {

    $router->render('auth/mensaje');
  }
}