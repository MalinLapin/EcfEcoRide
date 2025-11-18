<?php

namespace App\model;

/**
 * Enumération pour représenter les différents rôles d'un utilisateur.
 * Cette énumération permet de définir clairement les rôles possibles d'un utilisateur,
 * ce qui aide à éviter les erreurs de typage et de logique dans le code.
 */
enum Role: string
{
        case user = 'user'; // Rôle par défaut pour les utilisateurs normaux.
        case admin = 'admin'; // Rôle pour les administrateurs du système.
        case employee = 'employee';// Rôle pour les employés du service client ou de la gestion des covoiturages.
}