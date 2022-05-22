<?php

declare(strict_types=1);

namespace App\Message;

use App\Dto\SubscriberDto;

final class SendNewsletterMessage
{
     public function __construct(
         private int $newsletterId,
         private string $newsletterName,
         private string $content,
         private SubscriberDto $subscriberDto
     )
     {
     }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return int
     */
    public function getNewsletterId(): int
    {
        return $this->newsletterId;
    }

    /**
     * @return string
     */
    public function getNewsletterName(): string
    {
        return $this->newsletterName;
    }

    /**
     * @return SubscriberDto
     */
    public function getSubscriber(): SubscriberDto
    {
        return $this->subscriberDto;
    }
}
