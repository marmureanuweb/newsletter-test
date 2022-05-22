<?php

declare(strict_types=1);

namespace App\Dto;

use Sylius\Component\Core\Model\CustomerInterface;

class SubscriberDto
{
    public function __construct(
        private ?string $firstName = null,
        private ?string $lastName = null,
        private ?string $email = null,
    )
    {
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     */
    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     */
    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function populate(CustomerInterface $customer)
    {
        $this->setEmail($customer->getEmail());
        $this->setFirstName($customer->getFirstName());
        $this->setLastName($customer->getLastName());
    }
}
