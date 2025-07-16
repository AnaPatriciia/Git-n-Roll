
<?php

class Login {

    public static function init() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return session_start();
        }
    }

    public static function loginCLiente($object) {
        self::init();
        $_SESSION['usuarios'] = [
            'id_usuario' => $object->id_usuario,
            'telefone' => $object->telefone
        ];

         header('location: ../../senac/Views/buyathome.php');
        exit;
    }

    public static function loginAdm($object) {
        self::init();
        $_SESSION['administrador'] = [
            'id_administrador' => $object->id_administrador,
            'email' => $object->id_usuario,
        ];
        header('location: ../../Pages/adm/perfil_adm.php');
        exit;
        
    }

    public static function IsLogedCliente() {
        self::init();
        return isset($_SESSION['usuarios']['usuario']);
    }

    public static function IsLogedAdm() {
        self::init();
        return isset($_SESSION['administrador']['id_administrador']);
    }

    public static function RequireLogin() {
        self::init();
        if (!self::IsLogedCliente() && !self::IsLogedAdm()) {
            header('location: ../../senac/Views/cadastro.php');
            exit;
        }
    }

    public static function RequireLogout() {
        self::init();
        if (self::IsLogedCliente()) {
            header('location: ../../Pages/user/Home.php');
            exit;
        }

        if (self::IsLogedAdm()) {
            header('location: ../../Pages/adm/perfil_adm.php');
            exit;
        }
    }

    public static function Logout() {
        self::init();
        session_unset();
        session_destroy();
        header('location: ../../Pages/user/Home.php');
        exit;
    }
}
