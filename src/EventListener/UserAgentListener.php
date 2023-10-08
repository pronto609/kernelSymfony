<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class UserAgentListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
//            'kernel.request' => 'onKernelRequest'
            RequestEvent::class => 'onKernelRequest'
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
//        $event->setResponse(new Response('It is my responce!'));
        $request = $event->getRequest();

        $userAgent = $request->headers->get('User-Agent');
        $this->logger->info(
            sprintf('The User-Agent is %s', $userAgent)
        );

    }

}
