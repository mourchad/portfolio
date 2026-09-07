<?php

namespace App\Service;

/**
 * Service pour gérer la conversion des technologies entre string et array.
 */
class ProjectTechnologyService
{
    /**
     * Convertit une chaîne de technologies séparées par des virgules en tableau.
     *
     * @param string $raw Chaîne brute (ex: "Symfony, Twig, Doctrine")
     * @return array<string> Tableau de technologies nettoyées
     */
    public function convertRawToArray(string $raw): array
    {
        return array_values(array_filter(array_map('trim', explode(',', $raw)), static fn ($t) => '' !== $t));
    }

    /**
     * Convertit un tableau de technologies en chaîne séparée par des virgules.
     *
     * @param array<string> $technologies Tableau de technologies
     * @return string Chaîne formatée
     */
    public function convertArrayToRaw(array $technologies): string
    {
        return implode(', ', $technologies);
    }
}
