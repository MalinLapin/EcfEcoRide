<?php

namespace App\Model;

/**
 * Fichier contenant notre classe Preference qui étend la classe BaseModel.
 * Cette classe représente une préférence d'un utilisateur pour une voiture.
 */
class Preference extends BaseModel
{
    private int $idPreference; // Identifiant de la préférence.
    private string $label; // Label de la préférence.
    private bool $isAccepted; // Indique si la préférence est acceptée ou non.    
    private int $idCar; // Identifiant de la voiture associée à cette préférence.
}