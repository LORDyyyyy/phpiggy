<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;
use Framework\Exceptions\ValidationException;

class UserService
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Checks if the given email is already taken by another user.
     * Throws a ValidationException if the email is already taken
     * and ValidationExceptionMiddleware handles that error.
     *
     * @param string $email The email to check.
     *
     * @throws ValidationException If the email is already taken.
     */
    public function isEmailTaken(string $email): void
    {
        $emailCount = $this->db->query(
            'SELECT COUNT(*) FROM users WHERE email = :email',
            ['email' => $email]
        )->count();

        if ($emailCount > 0) {
            throw new ValidationException(['email' => ['Email is already taken']]);
        }
    }

    public function create(array $data): void
    {
        $password = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);

        $this->db->query(
            'INSERT INTO users (email, password, age, country, social_media_url)
            VALUES (:email, :password, :age, :country, :social_media_url)',
            [
                'email' => $data['email'],
                'password' => $password,
                'age' => $data['age'],
                'country' => $data['country'],
                'social_media_url' => $data['socialMediaURL']
            ]
        );

        session_regenerate_id();

        $_SESSION['user'] = $this->db->id();
    }

    public function login(array $data): void
    {
        $user = $this->db->query(
            'SELECT * FROM users WHERE email = :email',
            [
                'email' => $data['email'],
            ]
        )->find();

        $passwordMatch = password_verify(
            $data['password'],
            $user['password'] ?? ''
        );

        if (!$user || !$passwordMatch) {
            throw new ValidationException(['password' => ['Invalid Credentials']]);
        }

        session_regenerate_id();

        $_SESSION['user'] = $user['id'];
    }

    public function logout(): void
    {
        session_destroy();

        $params = session_get_cookie_params();

        setcookie(
            'PHPSESSID',
            '',
            time() - 3600,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
}
