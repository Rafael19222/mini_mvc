<?php

declare(strict_types=1);

namespace Mini\Controllers;

use Mini\Core\Controller;
use Mini\Models\User;

class AuthController extends Controller
{
    public function login(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $error = null;

            $user = User::findByEmail($email);
            if ($user && $user->verifyPassword($password)) {
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['user_name'] = $user->getNom();
                header('Location: /');
                exit;
            } else {
                $error = 'Email ou mot de passe incorrect';
            }
        }

        $this->render('auth/login', [
            'title' => 'Connexion',
            'error' => $error ?? null
        ]);
    }

    public function register(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            if (empty($nom) || empty($email) || empty($password)) {
                $error = 'Tous les champs sont obligatoires';
            } elseif ($password !== $password_confirm) {
                $error = 'Les mots de passe ne correspondent pas';
            } elseif (strlen($password) < 6) {
                $error = 'Le mot de passe doit faire au minimum 6 caractères';
            } else {
                $user = User::create([
                    'nom' => $nom,
                    'email' => $email,
                    'password' => $password
                ]);

                if ($user) {
                    $_SESSION['user_id'] = $user->getId();
                    $_SESSION['user_name'] = $user->getNom();
                    header('Location: /');
                    exit;
                } else {
                    $error = 'Cet email existe déjà';
                }
            }
        }

        $this->render('auth/register', [
            'title' => 'Inscription',
            'error' => $error
        ]);
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        session_destroy();
        header('Location: /');
        exit;
    }
}