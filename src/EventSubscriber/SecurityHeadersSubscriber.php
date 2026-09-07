<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Ajoute les en-têtes HTTP de sécurité à chaque réponse :
 * CSP stricte, anti-clickjacking, anti-sniffing, politique de référent et de permissions.
 */
class SecurityHeadersSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly string $environment,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', 0],
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $path = $request->getPathInfo();

        // Ne pas polluer la barre d'outils de debug.
        if (str_starts_with($path, '/_wdt') || str_starts_with($path, '/_profiler')) {
            return;
        }

        $response = $event->getResponse();
        $headers = $response->headers;

        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('X-Frame-Options', 'DENY');
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(), usb=()');

        // HSTS uniquement en production sur HTTPS.
        if ('prod' === $this->environment && $request->isSecure()) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // CSP complète en production ; en dev on l'omet pour laisser vivre la debug bar.
        if ('prod' === $this->environment) {
            $csp = implode('; ', [
                "default-src 'self'",
                "script-src 'self' https://cdn.jsdelivr.net",
                "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com",
                "font-src 'self' data: https://fonts.gstatic.com https://cdn.jsdelivr.net",
                "img-src 'self' data: blob:",
                "connect-src 'self'",
                "object-src 'none'",
                "base-uri 'self'",
                "form-action 'self'",
                "frame-ancestors 'none'",
            ]);
            $headers->set('Content-Security-Policy', $csp);
        }
    }
}
