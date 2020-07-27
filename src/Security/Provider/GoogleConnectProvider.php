<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Security\Provider;

use App\Entity\User;
use App\Repository\UserRepository;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class GoogleConnectProvider implements UserProviderInterface
{
    private $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    /**
     * Loads the user for the given googleId.
     *
     * @throws UsernameNotFoundException if the user is not found
     *
     * @return UserInterface
     */
    public function loadUserById($id)
    {
        $user = $this->userRepository->findOneBy(['googleId' => $id]);
        if ($user instanceof User) {
            return $user;
        }
    }

    /**
     * Loads the user for the given email, which is stored in username, too.
     *
     * @param string $username
     *
     * @throws UsernameNotFoundException if the user is not found
     *
     * @return UserInterface
     */
    public function loadUserByUsername($username)
    {
        $user = $this->loadUserByEmail($username);
        if ($user instanceof User) {
            return $user;
        }
        throw new UsernameNotFoundException();
    }

    /**
     * Loads the user for the given email.
     *
     * @param string $email
     *
     * @return UserInterface
     */
    public function loadUserByEmail($email)
    {
        // email can be empty, prevent searching for users with email IS NULL.
        if (empty($email)) {
            return;
        }

        $user = $this->userRepository->findOneBy(['email' => $email]);
        if ($user instanceof User) {
            return $user;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(GoogleUser $response)
    {
        $existing = $this->loadUserById($response->getId());

        if ($existing instanceof User) {
            return $existing;
        }

        // check if we already have this user
        $existing = $this->loadUserByEmail($response->getEmail());
        if ($existing instanceof User) {
            // update the google_id
            $existing->setGoogleId($response->getId());

            $this->userRepository->save($existing);

            return $existing;
        }

        // we don't know the user, create it
        $user = new User();

        $user->setEmail($response->getEmail());
        $user->setGoogleId($response->getId());
        $this->userRepository->save($user);

        return $user;
    }

    /**
     * Refreshes the user.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @throws UnsupportedUserException  if the user is not supported
     * @throws UsernameNotFoundException if the user is not found
     */
    public function refreshUser(UserInterface $user)
    {
        /** @var User $user  */
        return $this->userRepository->find($user->getId());
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     */
    public function supportsClass($class)
    {
        return User::class === $class;
    }
}
