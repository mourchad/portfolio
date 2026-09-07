<?php

namespace App\Twig;

use App\Entity\Profile;
use App\Entity\SocialLink;
use App\Repository\ProfileRepository;
use App\Repository\SocialLinkRepository;
use Symfony\Contracts\Cache\CacheInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Fonctions globales du site public : liens sociaux et réglages éditoriaux.
 * Les données sont mises en cache pour améliorer les performances.
 */
class AppExtension extends AbstractExtension
{
    public function __construct(
        private readonly SocialLinkRepository $socialLinkRepository,
        private readonly ProfileRepository $profileRepository,
        private readonly CacheInterface $cache,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('social_links', [$this, 'getSocialLinks']),
            new TwigFunction('site_profile', [$this, 'getProfile']),
        ];
    }

    /**
     * @return SocialLink[]
     */
    public function getSocialLinks(): array
    {
        return $this->cache->get('social_links', fn () => $this->socialLinkRepository->findAllOrdered());
    }

    /**
     * Réglages du site — objet vide cohérent si jamais initialisés.
     */
    public function getProfile(): Profile
    {
        return $this->cache->get('site_profile', function () {
            $main = $this->profileRepository->findMain();
            if (null !== $main) {
                return $main;
            }

            $fallback = new Profile();
            $fallback->setEmail(Profile::DEFAULT_EMAIL);

            return $fallback;
        });
    }
}
