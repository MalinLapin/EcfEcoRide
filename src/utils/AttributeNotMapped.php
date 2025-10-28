<?php

namespace App\Attribute;

/**
 * Attribut pour marquer les propriétés qui ne doivent PAS
 * être enregistrées en base de données
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class NotMapped 
{
    // Classe vide, elle sert juste de "marqueur"
}