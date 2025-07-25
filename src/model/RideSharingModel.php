<?php

namespace App\Model;

use DateTimeImmutable;

/**
 * Enumération pour représenter les différents statuts d'un covoiturage.
 * Cette énumération permet de définir clairement les états possibles d'un covoiturage,
 * ce qui aide à éviter les erreurs de typage et de logique dans le code.
 */
enum Status: string
{
        case Pending = 'pending'; // le covoiturage est crée mais pas lancé
        case Ongoing = "ongoing"; // le covoiturage est en cours
        case Completed = "completed"; // le covoiturage est fini
        case Cancelled = "cancelled"; // le covoiturage à été annuler par le chauffeur ou un employer/admin
}

// Fichier contenant notre classe RideSharingModel qui étend la classe BaseModel.
class RideSharingModel extends BaseModel
{
    private int $idRideSharing; // Identifiant du covoiturage.
    private DateTimeImmutable $departureDate; //Date de départ
    private string $departureCity; //Ville de départ
    private string $departureAdress; //Adresse de départ
    private string $arrivalCity; //Ville d'arriver
    private string $arrivalAdress; //Adresse d'arriver
    private DateTimeImmutable $arrivalDate; //Date d'arriver
    private int $availableSeats; //Place disponnible
    private int $priceParSeat; //Prix par place
    private Status $status; //Est défini par notre énum pour éviter des erreur de tipo ou de type.
    private DateTimeImmutable $createdAt; //Date de création du covoiturage
    private int $idDriver; // Identifiant du conducteur
    private int $idCar; // Identifiant de la voiture utilisée pour le covoiturage
}