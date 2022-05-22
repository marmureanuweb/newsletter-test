<?php

declare(strict_types=1);

namespace App\Service\Newsletter;

use App\Entity\Newsletter;
use App\Repository\NewsletterRepository;
use Psr\Log\LoggerInterface;

use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\Routing\Router;

class NewsletterManager
{
    public const UNSUBSCRIBE_ROUTE_NAME = 'app_newsletter_unsubscribe';

    public function __construct(
        private ?NewsletterRepository $newsletterRepository = null,
        private ?CustomerRepositoryInterface $customerRepository = null,
        private ?Router $router = null,
        private ?LoggerInterface $logger = null,
    )
    {
    }

    /**
     * Load newsletter entity by [id] or [code]
     *
     * @param string $identifier
     * @return Newsletter|null
     */
    public function loadNewsletterByIdentifier(string $identifier):?Newsletter
    {
        $identifier = trim($identifier);
        // First try to load newsletter by [id]
        $newsletter = $this->newsletterRepository->find((int)$identifier);
        if ($newsletter === null) {
            $newsletter = $this->newsletterRepository->findOneBy(
                ['code' => $identifier]
            );
        }

        return $newsletter ?? null;
    }

    /**
     * Get newsletter content to be delivered
     *
     * @param Newsletter|null $newsletter
     * @return string|null
     */
    public function getNewsletterForDelivery(?Newsletter $newsletter):?string
    {
        if (null === $newsletter) {
            return null;
        }
        $newsletterContent = '';
        if (!empty($newsletter->getContent())) {
            $newsletterContent .= $newsletter->getContent();
        }

        return $newsletterContent;
    }

    /**
     * Used in unsubscribe link action
     * @param string $customerEmail
     * @param int $newsletterId
     * @return bool
     */
    public function unsubscribeCustomerByLink(string $customerEmail, int $newsletterId):bool
    {
        /** @var CustomerInterface $customer */
        $customer = $this->customerRepository->findOneBy(
            ['email'=>trim($customerEmail)]
        );
        if ($customer === null) {
            return false;
        }

        /** @var Newsletter $newsletter */
        $newsletter = $this->newsletterRepository->find($newsletterId);
        if ($newsletter === null) {
            return false;
        }

        return $this->unsubscribeCustomerFromNewsletter($customer, $newsletter);
    }

    /**
     * Generate unsubscribe link to be send in email
     *
     * @param string $customerEmail
     * @param int $newsletterId
     * @return string|null
     */
    public function generateUnsubscribeLink(string $customerEmail, int $newsletterId):?string
    {
        $newsletterId = (string) $newsletterId;
        $url = $this->router->generate(
            self::UNSUBSCRIBE_ROUTE_NAME,
             [
                 'customer_email' => base64_encode($customerEmail),
                 'newsletter_id' => base64_encode($newsletterId),
             ]
        );

        return $url;
    }

    /**
     * @param CustomerInterface $customer
     * @param Newsletter $newsletter
     * @return bool
     */
    public function unsubscribeCustomerFromNewsletter(CustomerInterface $customer, Newsletter $newsletter):bool
    {
        $unsubscribed = false;
        try {
            $newsletter->removeSubscriber($customer);
            $unsubscribed = true;
        } catch (\Exception $exception) {
            $this->logger->error(
                'Error while removing from subscribers list in [' . __METHOD__ . ']! Message:'.$exception->getMessage()
        );
        }

        return $unsubscribed;
    }
}
