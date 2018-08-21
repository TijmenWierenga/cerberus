<?php

namespace Cerberus\OAuth;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserEntityInterface, UserInterface
{
    /**
     * @var UuidInterface
     */
    private $id;
    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $password;

    /**
     * @var Collection|ScopeEntityInterface[]
     */
    private $scopes;

    private function __construct(UuidInterface $id, string $username, string $password, array $scopes)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->scopes = new ArrayCollection($scopes);
    }

    public static function new(UuidInterface $id, string $username, string $password, array $scopes = []): self
    {
        return new self($id, $username, $password, $scopes);
    }

    /**
     * Return the user's identifier.
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return (string) $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return Collection|ScopeEntityInterface[]
     */
    public function getScopes(): Collection
    {
        return $this->scopes;
    }

    public function addScope(ScopeEntityInterface $scope, ScopeEntityInterface ...$scopes): void
    {
        $scopes[] = $scope;

        foreach ($scopes as $scope) {
            if (! $this->hasScope($scope)) {
                $this->scopes->add($scope);
            }
        }
    }

    public function removeScope(ScopeEntityInterface $scope, ScopeEntityInterface ...$scopes): void
    {
        $scopes[] = $scope;

        foreach ($scopes as $scope) {
            if ($this->hasScope($scope)) {
                $this->scopes->removeElement($scope);
            }
        }
    }

    public function hasScope(ScopeEntityInterface $scope): bool
    {
        return $this->scopes->contains($scope);
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        $roles = [];

        foreach ($this->scopes as $scope) {
            $role = new Role("ROLE_" . strtoupper($scope->getIdentifier()));
            $roles[] = $role;
        }

        return $roles;
    }

    public function hasRole(Role $role): bool
    {
        return count(array_filter($this->getRoles(), function (Role $userRole) use ($role) {
            return $userRole->getRole() === $role->getRole();
        })) > 0;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        $this->password = "";
    }
}
