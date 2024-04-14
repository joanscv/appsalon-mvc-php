<?php

function debuguear($variable) : string {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

// Escapa / Sanitizar el HTML
function s($html) : string {
    $s = htmlspecialchars($html);
    return $s;
}

// Chequea si el elemento actual de un arreglo es igual al siguiente
function esUltimo(string $actual, string $proximo): bool {
    if($actual !== $proximo){
        return true;
    }
    return false;
}

// Función que revisa que el usuario esté autenticado 

function isAuth() : void {

    if(!isset($_SESSION['login'])) {
        header('Location: /');
    }
}

// Revisa que el usuario sea un administrador

function isAdmin() : void {
    if(!isset($_SESSION['admin'])){

        if(!isset($_SESSION['login'])) {
            header('Location: /');
        }
        header('Location: /cita');
    }
}

function validarORedireccionar(string $url): int {
  // Validar la URL por ID válido
  $id = $_GET['id'];
  $id = filter_var($id, FILTER_VALIDATE_INT);
  
  if(!$id) {
    header("Location: {$url}");
  }

  return $id;
}