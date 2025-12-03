<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class AuthService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface $validator,
        private JWTTokenManagerInterface $jwtManager,
        private UserRepository $userRepository
    ) {}

    /**
     * Inscription d'un nouvel utilisateur
     */
    public function register(string $email, string $plainPassword): array
    {
        // Créer un nouvel utilisateur
        $user = new User();
        $user->setEmail($email);

        // Hasher le mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        // Valider l'entité
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            throw new \InvalidArgumentException(implode(', ', $errorMessages));
        }

        // Sauvegarder en base
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return [
            'message' => 'Utilisateur créé avec succès',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail()
            ]
        ];
    }

    /**
     * Connexion d'un utilisateur
     */
    public function login(string $email, string $plainPassword): array
    {
        // Rechercher l'utilisateur par email
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            throw new \InvalidArgumentException('Email ou mot de passe incorrect');
        }

        // Vérifier le mot de passe
        if (!$this->passwordHasher->isPasswordValid($user, $plainPassword)) {
            throw new \InvalidArgumentException('Email ou mot de passe incorrect');
        }

        // Générer le token JWT
        $token = $this->jwtManager->create($user);

        return [
            'token' => $token,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail()
            ]
        ];
    }
}