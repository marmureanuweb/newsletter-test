<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Dto\SubscriberDto;
use App\Message\SendNewsletterMessage;
use App\Service\Newsletter\NewsletterManager;
use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class SendNewsletterMessageHandler implements MessageHandlerInterface
{
    public const NEWSLETTER_TYPE = 'newsletter_type';

    public function __construct(
        private ?SenderInterface $emailSender = null,
        private ?NewsletterManager $newsletterManager = null
    )
    {
    }

    /**
     * Send email to subscriber
     *
     * @param SendNewsletterMessage $message
     * @return void
     */
    public function __invoke(SendNewsletterMessage $message):void
    {
        Assert::notNull($message);
        /** @var SubscriberDto $subscriber */
        $subscriber = $message->getSubscriber();

        $unsubscriberUrl = $this->newsletterManager->generateUnsubscribeLink(
            $subscriber->getEmail(),
            $message->getNewsletterId()
        );
        echo $unsubscriberUrl;

        $this->emailSender->send(
            self::NEWSLETTER_TYPE,
            [$subscriber->getEmail()],
            [
                'firstname' => $subscriber->getFirstName(),
                'subject' => $message->getNewsletterName(),
                'newsletter_name' => $message->getNewsletterName(),
                'newsletter_content' => $message->getContent(),
                'unsubscribe_link' => $unsubscriberUrl,
            ]
        );
    }
}
