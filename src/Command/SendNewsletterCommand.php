<?php

declare(strict_types=1);

namespace App\Command;

use App\Dto\SubscriberDto;
use App\Entity\Newsletter;
use App\Message\SendNewsletterMessage;
use App\Service\Newsletter\NewsletterManager;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\SemaphoreStore;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:send-newsletter',
    description: 'Send newsletter to subscribers by passing newsletter [ID] or [Code]',
)]
class SendNewsletterCommand extends Command
{
    public const NEWSLETTER_IDENTIFIER = 'newsletter_identifier';
    public const NEWSLETTER_LOCK_IDENTIFIER = 'newsletter_command_lock';
    private LockFactory $lockFactory;

    public function __construct(
        string $name = null,
        private ?NewsletterManager $newsletterManager = null,
        private ?MessageBusInterface $messageBus = null
    )
    {
        parent::__construct($name);
        $store = new SemaphoreStore();
        $this->lockFactory = new LockFactory($store);
    }

    protected function configure(): void
    {
        $this
            ->addArgument(self::NEWSLETTER_IDENTIFIER, InputArgument::REQUIRED, 'Newsletter ID or CODE')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $lock = $this->lockFactory->createLock(self::NEWSLETTER_LOCK_IDENTIFIER);
        if (!$lock->acquire()) {
            $this->io->warning('Command already running!');
            return Command::FAILURE;
        }

        // Newsletter identifier can be [id] or [code]
        $newsletterIdentifier = $input->getArgument(self::NEWSLETTER_IDENTIFIER);
        if (!$newsletterIdentifier) {
            $this->io->error('Please provide a newsletter [id] or [code]!');
            return Command::FAILURE;
        }

        /** @var Newsletter $newsletter */
        $newsletter = $this->newsletterManager->loadNewsletterByIdentifier($newsletterIdentifier);
        if (null === $newsletter) {
            $this->io->error("Newsletter not found by identifier [$newsletterIdentifier]!");
            return Command::FAILURE;
        }

        if ($newsletter->getStatus() === false) {
            $this->io->warning("Newsletter [". $newsletter->getName() ."] is DISABLED!");
            return Command::INVALID;
        }

        $subscribersCount = count($newsletter->getSubscribers());
        $subscribersCountMessage = "Newsletter [". $newsletter->getName() ."] has [$subscribersCount] subscribers!";
        if ($subscribersCount === 0) {
            $this->io->warning($subscribersCountMessage . ' Cannot send newsletter!');
            return Command::INVALID;
        }
        $this->io->info($subscribersCountMessage);

        // Send messages to queue
        try {
            $this->sendMessagesToQueue($newsletter);
        } finally {
            $lock->release();
        }

        $this->io->success('Newsletter sent!');
        return Command::SUCCESS;
    }

    /**
     * @param Newsletter $newsletter
     * @return void
     */
    public function sendMessagesToQueue(Newsletter $newsletter):void
    {
        $newsletterContent = $this->newsletterManager->getNewsletterForDelivery($newsletter);
        /** @var CustomerInterface $subscriber */
        foreach ($newsletter->getSubscribers() as $subscriber) {
            /** @var SubscriberDto $subscriberDto */
            $subscriberDto = new SubscriberDto();
            $subscriberDto->populate($subscriber);

            $this->io->info("Sending newsletter to ". $subscriber->getEmail());

            // Format newsletter message
            $newsletterMessage = new SendNewsletterMessage(
                $newsletter->getId(),
                $newsletter->getName(),
                $newsletterContent,
                $subscriberDto
            );
            $published = $this->messageBus->dispatch($newsletterMessage);
        }
    }
}
