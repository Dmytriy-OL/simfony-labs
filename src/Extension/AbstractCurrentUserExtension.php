<?php

declare(strict_types=1);

namespace App\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

abstract class AbstractCurrentUserExtension implements
    QueryCollectionExtensionInterface,
    QueryItemExtensionInterface
{
    public const FIRST_ELEMENT_ARRAY = 0;
    public const ADMIN_ROLES = [User::ADMIN];

    protected Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ): void {
        if ($this->isFiltering($operationName, $resourceClass)) {
            return;
        }

        $this->buildQuery($queryBuilder);
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        string $operationName = null,
        array $context = []
    ): void {
        if ($this->isFiltering($operationName, $resourceClass)) {
            return;
        }

        $this->buildQuery($queryBuilder);
    }

    protected function isFiltering(?string $operationName, string $resourceClass): bool
    {
        return !$this->apply($operationName)
            || $resourceClass !== $this->getResourceClass()
            || (
                $this->security->getUser()
                && count(array_intersect(self::ADMIN_ROLES, $this->security->getUser()->getRoles()))
            );
    }

    protected function apply(?string $operationName): bool
    {
        return $operationName && str_contains($operationName, 'get');
    }

    abstract public function getResourceClass(): string;

    abstract public function buildQuery(QueryBuilder $queryBuilder): void;
}
