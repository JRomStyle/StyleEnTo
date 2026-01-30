<?php
namespace App\Controllers;
use App\Models\User;
class AuthController extends Controller {
    public function login(): void {
        if (auth_user()) {
            redirect('home/index');
        }
        $this->render('auth/login', []);
    }
    public function doLogin(): void {
        if (!is_post() || !check_csrf()) {
            redirect('auth/login');
        }
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $user = User::verify($email, $password);
        if ($user) {
            login_user($user);
            redirect('home/index');
        }
        $this->render('auth/login', ['error' => 'Credenciales inválidas']);
    }
    public function register(): void {
        if (auth_user()) {
            redirect('home/index');
        }
        $this->render('auth/register', []);
    }
    public function doRegister(): void {
        if (!is_post() || !check_csrf()) {
            redirect('auth/register');
        }
        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirm = trim($_POST['confirm'] ?? '');
        if ($nombre === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6 || $password !== $confirm) {
            $this->render('auth/register', ['error' => 'Datos inválidos']);
            return;
        }
        $exists = User::findByEmail($email);
        if ($exists) {
            $this->render('auth/register', ['error' => 'El email ya existe']);
            return;
        }
        User::create($nombre, $email, $password, 'cliente');
        $user = User::findByEmail($email);
        login_user($user);
        redirect('home/index');
    }
    public function logout(): void {
        logout_user();
        redirect('home/index');
    }
}
