<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Newsletter;
use App\Repository\NewsletterRepository;
use App\Service\Newsletter\NewsletterManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewsletterController extends AbstractController
{
    public function unsubscribe(
        Request $request,
        NewsletterManager $newsletterManager,
        NewsletterRepository $newsletterRepository
    ): Response
    {
        $newsletterId = $request->get('newsletter_id');
        $customerEmail = $request->get('customer_email');
        $newsletterId = (int)base64_decode($newsletterId);
        $customerEmail = (string)base64_decode($customerEmail);

        /** @var Newsletter $newsletter */
        $newsletter = $newsletterRepository->find($newsletterId);
        $newsletterName = $newsletter?->getName();

        $unsubscribed = $newsletterManager->unsubscribeCustomerByLink($customerEmail, $newsletterId);
        return $this->render('newsletter/unsubscribe_response.twig', [
            'newsletter_name' => $newsletterName ?: '',
            'unsubscribed_flag' => $unsubscribed,
            'unsubscribed_message' => ($unsubscribed ? 'Unsubscribed' : 'unknown'),
        ]);
    }
}
