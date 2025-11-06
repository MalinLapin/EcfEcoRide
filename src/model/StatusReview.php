<?php

namespace App\model;
/**
 * Enumération pour représenter les différents statuts d'une revue.
 * Cette énumération permet de définir clairement les états possibles d'une revue,
 * ce qui aide à éviter les erreurs de typage et de logique dans le code.
 */
enum StatusReview: string
{
    case pending = 'pending'; // La revue est en attente de traitement.
    case approved = 'approved'; // La revue a été approuvée.
    case rejected = 'rejected'; // La revue a été rejetée.
}