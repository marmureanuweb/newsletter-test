<?php

declare(strict_types=1);

namespace App\Entity\Customer;

use App\Entity\Newsletter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Customer as BaseCustomer;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_customer")
 */
class Customer extends BaseCustomer
{
    /**
     * @ORM\ManyToMany(targetEntity=Newsletter::class, mappedBy="subscribers")
     */
    private $newsletters;

    public function __construct()
    {
        parent::__construct();
        $this->newsletters = new ArrayCollection();
    }

    /**
     * @return Collection<int, Newsletter>
     */
    public function getNewsletters(): Collection
    {
        return $this->newsletters;
    }

    public function addNewsletter(Newsletter $newsletter): self
    {
        if (!$this->newsletters->contains($newsletter)) {
            $this->newsletters[] = $newsletter;
            $newsletter->addSubscriber($this);
        }

        return $this;
    }

    public function removeNewsletter(Newsletter $newsletter): self
    {
        if ($this->newsletters->removeElement($newsletter)) {
            $newsletter->removeSubscriber($this);
        }

        return $this;
    }
}
