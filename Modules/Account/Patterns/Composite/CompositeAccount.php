<?php

namespace Modules\Account\Patterns\Composite;

class CompositeAccount implements AccountComponent
{
    /** @var AccountComponent[] */
    private array $children = [];

    public function add(AccountComponent $account): void
    {
        $this->children[] = $account;
    }

    public function getBalance(): float
    {
        return array_sum(
            array_map(fn ($child) => $child->getBalance(), $this->children)
        );
    }

    public function deposit(float $amount): void
    {
        $count = count($this->children);
        if ($count === 0) {
            throw new \DomainException("Composite account has no children");
        }

        $portion = $amount / $count;

        foreach ($this->children as $child) {
            $child->deposit($portion);
        }
    }

    public function withdraw(float $amount): void
    {
        $count = count($this->children);
        if ($count === 0) {
            throw new \DomainException("Composite account has no children");
        }

        $portion = $amount / $count;

        foreach ($this->children as $child) {
            $child->withdraw($portion);
        }
    }
}
