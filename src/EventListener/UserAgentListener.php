<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
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

    private function isMac(Request $request): bool
    {
        if ($request->query->has('mac')) {
            return $request->query->getBoolean('mac');
        }
        $userAgent = $request->headers->get('User-Agent');

        return str_contains($userAgent, 'Mac');
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
//        $event->setResponse(new Response('It is my responce!'));
        $request = $event->getRequest();
/*
        $request->attributes->set('_controller', function ($slug = null) {
//            dd($slug);

            return new Response('I just took over the controller!');
        });*/

        $userAgent = $request->headers->get('User-Agent');
        $this->logger->info(
            sprintf('The User-Agent is %s', $userAgent)
        );

        $request->attributes->set('_isMac', $this->isMac($request));

//        $isMac = strpos($userAgent, 'Mac') !== false;
//        $request->attributes->set('isMac', $isMac);
    }

}
