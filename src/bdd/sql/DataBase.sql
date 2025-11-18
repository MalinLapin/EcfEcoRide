-- Active: 1741707596826@@127.0.0.1@3306
CREATE TABLE brand (
    id_brand INT PRIMARY KEY AUTO_INCREMENT,
    label VARCHAR(30) NOT NULL UNIQUE
);

CREATE TABLE user (
    id_user INT PRIMARY KEY AUTO_INCREMENT,
    pseudo VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL,
    credit_balance INT,
    photo VARCHAR(255),
    grade DECIMAL(2, 1),
    role ENUM('user', 'employee', 'admin') NOT NULL DEFAULT 'user',
    is_active BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE car (
    id_car INT PRIMARY KEY AUTO_INCREMENT,
    model VARCHAR(20) NOT NUll,
    registration_number VARCHAR(20) NOT NULL UNIQUE,
    first_registration DATE NOT NULL,
    energy_type ENUM(
        'electric',
        'hybrid',
        'escence',
        'diesel',
        'gpl'
    ) NOT NULL,
    color VARCHAR(20) NOT NULL,
    id_brand INT NOT NULL,
    id_user INT NOT NULL,
    FOREIGN KEY (id_brand) REFERENCES brand (id_brand),
    FOREIGN KEY (id_user) REFERENCES user (id_user) ON DELETE CASCADE
);

CREATE TABLE ridesharing (
    id_ridesharing INT PRIMARY KEY AUTO_INCREMENT,
    departure_date DATETIME NOT NULL,
    departure_city VARCHAR(50) NOT NULL,
    departure_address VARCHAR(255),
    arrival_city VARCHAR(50) NOT NULL,
    arrival_address VARCHAR(255),
    arrival_date DATETIME,
    available_seats INT NOT NULL CHECK (
        available_seats BETWEEN 0 AND 6
    ),
    price_per_seat INT NOT NULL,
    status ENUM(
        'pending',
        'ongoing',
        'completed',
        'canceled'
    ) DEFAULT 'pending',
    created_at DATETIME NOT NULL,
    id_driver INT NOT NULL,
    id_car INT NOT NULL,
    FOREIGN KEY (id_driver) REFERENCES user (id_user),
    FOREIGN KEY (id_car) REFERENCES car (id_car)
);

CREATE TABLE participate (
    id_participate INT PRIMARY KEY AUTO_INCREMENT,
    id_participant INT NOT NULL,
    id_ridesharing INT NOT NULL,
    confirmed BOOLEAN NOT NULL DEFAULT FALSE,
    nb_seats INT NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    completed_at DATETIME DEFAULT NULL,
    UNIQUE KEY user_ride (
        id_participant,
        id_ridesharing
    ),
    FOREIGN KEY (id_participant) REFERENCES user (id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_ridesharing) REFERENCES ridesharing (id_ridesharing)
);

/* valeur par défaut pour tester les utilisateurs */
INSERT INTO
    user (
        pseudo,
        email,
        password,
        created_at,
        credit_balance,
        photo,
        grade,
        role
    )
VALUES (
        'marctest',
        'marc.uny@test.com',
        '$2y$10$A1vWrB6MeA/4y06As54BR.rvFOgrxUX/YjOveiUyP2FWVw6l9MSya',
        NOW(),
        100,
        'marc.jpg',
        5.0,
        'employee'
    ),
    (
        'elinauny',
        'elina.uny@test.com',
        '$2y$10$A1vWrB6MeA/4y06As54BR.rvFOgrxUX/YjOveiUyP2FWVw6l9MSya',
        NOW(),
        50,
        'elina.jpg',
        4.0,
        'user'
    ),
    (
        'axeluny',
        'axel.uny@test.com',
        '$2y$10$A1vWrB6MeA/4y06As54BR.rvFOgrxUX/YjOveiUyP2FWVw6l9MSya',
        NOW(),
        75,
        'axel.jpg',
        4.2,
        'user'
    ),
    (
        'mystereuny',
        'mystere.uny@test.com',
        '$2y$10$A1vWrB6MeA/4y06As54BR.rvFOgrxUX/YjOveiUyP2FWVw6l9MSya',
        NOW(),
        75,
        'mystere.jpg',
        4.2,
        'user'
    ),
    (
        'milkauny',
        'milka.uny@test.com',
        '$2y$10$A1vWrB6MeA/4y06As54BR.rvFOgrxUX/YjOveiUyP2FWVw6l9MSya',
        NOW(),
        30,
        'milka.jpg',
        NULL,
        'employee'
    ),
    (
        'José',
        'jose.ecoride@test.com',
        '$2y$10$A1vWrB6MeA/4y06As54BR.rvFOgrxUX/YjOveiUyP2FWVw6l9MSya',
        NOW(),
        100,
        NULL,
        NULL,
        'admin'
    );

INSERT INTO
    brand (label)
VALUES ('Toyota'),
    ('Ford'),
    ('BMW'),
    ('Mercedes'),
    ('Peugeot'),
    ('Renault'),
    ('Citroën'),
    ('Volkswagen'),
    ('Audi'),
    ('Opel'),
    ('Nissan'),
    ('Fiat'),
    ('Dacia'),
    ('Seat'),
    ('Skoda'),
    ('Hyundai'),
    ('Kia'),
    ('Mazda'),
    ('Honda'),
    ('Volvo'),
    ('Mini'),
    ('Alfa Romeo'),
    ('Jeep'),
    ('Land Rover'),
    ('Porsche'),
    ('Tesla'),
    ('Suzuki'),
    ('Mitsubishi'),
    ('Lexus'),
    ('Jaguar'),
    ('DS Automobiles'),
    ('Smart'),
    ('Subaru'),
    ('Chevrolet');

/* fixture générer par IA pour compléter les données de départ et effectuer plusieur test.*/

/* Insertion des voitures */

-- Voiture d'elinaUny (électrique)
INSERT INTO
    car (
        model,
        registration_number,
        first_registration,
        energy_type,
        color,
        id_brand,
        id_user
    )
VALUES (
        'Zoe',
        'AB-123-CD',
        '2021-03-15',
        'electric',
        'Blanc',
        7,
        2
    );
-- Renault Zoe pour elina

-- Voiture de mystereuny (hybride)
INSERT INTO
    car (
        model,
        registration_number,
        first_registration,
        energy_type,
        color,
        id_brand,
        id_user
    )
VALUES (
        'Prius',
        'EF-456-GH',
        '2020-06-20',
        'hybrid',
        'Gris',
        1,
        4
    );
-- Toyota Prius pour mystere

-- Deuxième voiture de mystereuny (diesel)
INSERT INTO
    car (
        model,
        registration_number,
        first_registration,
        energy_type,
        color,
        id_brand,
        id_user
    )
VALUES (
        '308',
        'IJ-789-KL',
        '2019-11-10',
        'diesel',
        'Noir',
        5,
        4
    );
-- Peugeot 308 pour mystere

-- Voiture de marctest (essence)
INSERT INTO
    car (
        model,
        registration_number,
        first_registration,
        energy_type,
        color,
        id_brand,
        id_user
    )
VALUES (
        'Golf',
        'MN-012-OP',
        '2022-01-05',
        'escence',
        'Bleu',
        8,
        1
    );
-- VW Golf pour marc

-- Voiture de milkauny (électrique)
INSERT INTO
    car (
        model,
        registration_number,
        first_registration,
        energy_type,
        color,
        id_brand,
        id_user
    )
VALUES (
        'Model 3',
        'QR-345-ST',
        '2023-05-12',
        'electric',
        'Rouge',
        26,
        5
    );
-- Tesla Model 3 pour milka

-- Voiture de José (GPL)
INSERT INTO
    car (
        model,
        registration_number,
        first_registration,
        energy_type,
        color,
        id_brand,
        id_user
    )
VALUES (
        'Duster',
        'UV-678-WX',
        '2020-09-18',
        'gpl',
        'Vert',
        13,
        6
    );
-- Dacia Duster pour José

/* Trajets d'elinaUny - Saint-Étienne <-> Lyon (réguliers) */

-- Trajet 1 : Saint-Étienne -> Lyon (complété)
INSERT INTO
    ridesharing (
        departure_date,
        departure_city,
        departure_address,
        arrival_city,
        arrival_address,
        arrival_date,
        available_seats,
        price_per_seat,
        status,
        created_at,
        id_driver,
        id_car
    )
VALUES (
        '2024-01-15 08:00:00',
        'Saint-Étienne',
        '10 Place Jean Jaurès',
        'Lyon',
        '25 Rue de la République',
        '2024-01-15 09:00:00',
        0,
        8,
        'completed',
        '2025-11-18 17:12:00',
        2,
        1
    );

-- Trajet 2 : Saint-Étienne -> Lyon (complété)
INSERT INTO
    ridesharing (
        departure_date,
        departure_city,
        departure_address,
        arrival_city,
        arrival_address,
        arrival_date,
        available_seats,
        price_per_seat,
        status,
        created_at,
        id_driver,
        id_car
    )
VALUES (
        '2024-01-18 07:30:00',
        'Saint-Étienne',
        '10 Place Jean Jaurès',
        'Lyon',
        '25 Rue de la République',
        '2024-01-18 08:30:00',
        1,
        8,
        'completed',
        '2025-11-18 17:12:00',
        2,
        1
    );

-- Trajet 3 : Lyon -> Saint-Étienne (complété)
INSERT INTO
    ridesharing (
        departure_date,
        departure_city,
        departure_address,
        arrival_city,
        arrival_address,
        arrival_date,
        available_seats,
        price_per_seat,
        status,
        created_at,
        id_driver,
        id_car
    )
VALUES (
        '2024-01-18 18:00:00',
        'Lyon',
        '25 Rue de la République',
        'Saint-Étienne',
        '10 Place Jean Jaurès',
        '2024-01-18 19:00:00',
        2,
        8,
        'completed',
        '2025-11-18 17:12:00',
        2,
        1
    );

-- Trajet 4 : Saint-Étienne -> Lyon (complété)
INSERT INTO
    ridesharing (
        departure_date,
        departure_city,
        departure_address,
        arrival_city,
        arrival_address,
        arrival_date,
        available_seats,
        price_per_seat,
        status,
        created_at,
        id_driver,
        id_car
    )
VALUES (
        '2025-11-10 08:00:00',
        'Saint-Étienne',
        '10 Place Jean Jaurès',
        'Lyon',
        '25 Rue de la République',
        '2025-11-10 09:00:00',
        0,
        8,
        'completed',
        '2025-11-18 17:12:00',
        2,
        1
    );

-- Trajet 5 : Saint-Étienne -> Lyon (en attente)
INSERT INTO
    ridesharing (
        departure_date,
        departure_city,
        departure_address,
        arrival_city,
        arrival_address,
        arrival_date,
        available_seats,
        price_per_seat,
        status,
        created_at,
        id_driver,
        id_car
    )
VALUES (
        '2025-12-05',
        'Saint-Étienne',
        '10 Place Jean Jaurès',
        'Lyon',
        '25 Rue de la République',
        '2025-12-05 09:00:00',
        2,
        8,
        'pending',
        '2025-11-18 17:12:00',
        2,
        1
    );

-- Trajet 6 : Lyon -> Saint-Étienne (en attente)
INSERT INTO
    ridesharing (
        departure_date,
        departure_city,
        departure_address,
        arrival_city,
        arrival_address,
        arrival_date,
        available_seats,
        price_per_seat,
        status,
        created_at,
        id_driver,
        id_car
    )
VALUES (
        '2025-12-10 18:30:00',
        'Lyon',
        '25 Rue de la République',
        'Saint-Étienne',
        '10 Place Jean Jaurès',
        '2025-12-10 19:30:00',
        3,
        8,
        'pending',
        '2025-11-18 17:12:00',
        2,
        1
    );

/* Trajets de mystereuny - Trajets variés */

-- Trajet 7 : Paris -> Marseille (complété)
INSERT INTO
    ridesharing (
        departure_date,
        departure_city,
        departure_address,
        arrival_city,
        arrival_address,
        arrival_date,
        available_seats,
        price_per_seat,
        status,
        created_at,
        id_driver,
        id_car
    )
VALUES (
        '2024-01-10 06:00:00',
        'Paris',
        'Gare de Lyon',
        'Marseille',
        'Vieux Port',
        '2025-12-20 14:00:00',
        1,
        45,
        'pending',
        '2025-11-18 17:12:00',
        4,
        2
    );

-- Trajet 8 : Bordeaux -> Toulouse (complété)
INSERT INTO
    ridesharing (
        departure_date,
        departure_city,
        departure_address,
        arrival_city,
        arrival_address,
        arrival_date,
        available_seats,
        price_per_seat,
        status,
        created_at,
        id_driver,
        id_car
    )
VALUES (
        '2024-01-14 10:00:00',
        'Bordeaux',
        'Place de la Bourse',
        'Toulouse',
        'Capitole',
        '2024-01-14 12:30:00',
        2,
        18,
        'completed',
        '2025-11-18 17:12:00',
        4,
        3
    );

-- Trajet 9 : Lyon -> Grenoble (complété)
INSERT INTO
    ridesharing (
        departure_date,
        departure_city,
        departure_address,
        arrival_city,
        arrival_address,
        arrival_date,
        available_seats,
        price_per_seat,
        status,
        created_at,
        id_driver,
        id_car
    )
VALUES (
        '2024-01-20 14:00:00',
        'Lyon',
        'Part-Dieu',
        'Grenoble',
        'Gare SNCF',
        '2024-01-20 15:30:00',
        1,
        12,
        'completed',
        '22025-11-18 17:12:00',
        4,
        2
    );

-- Trajet 10 : Lille -> Strasbourg (en cours)
INSERT INTO
    ridesharing (
        departure_date,
        departure_city,
        departure_address,
        arrival_city,
        arrival_address,
        arrival_date,
        available_seats,
        price_per_seat,
        status,
        created_at,
        id_driver,
        id_car
    )
VALUES (
        '2024-02-08 07:00:00',
        'Lille',
        'Grand Place',
        'Strasbourg',
        'Place Kléber',
        '2024-02-08 12:00:00',
        3,
        35,
        'pending',
        '2025-11-18 17:12:00',
        4,
        3
    );

-- Trajet 11 : Nice -> Montpellier (en attente)
INSERT INTO
    ridesharing (
        departure_date,
        departure_city,
        departure_address,
        arrival_city,
        arrival_address,
        arrival_date,
        available_seats,
        price_per_seat,
        status,
        created_at,
        id_driver,
        id_car
    )
VALUES (
        '2024-02-12 09:00:00',
        'Nice',
        'Promenade des Anglais',
        'Montpellier',
        'Place de la Comédie',
        '2024-02-12 12:30:00',
        4,
        28,
        'pending',
        '2025-11-18 17:12:00',
        4,
        2
    );

-- Trajet 12 : Nantes -> Rennes (en attente)
INSERT INTO
    ridesharing (
        departure_date,
        departure_city,
        departure_address,
        arrival_city,
        arrival_address,
        arrival_date,
        available_seats,
        price_per_seat,
        status,
        created_at,
        id_driver,
        id_car
    )
VALUES (
        '2024-02-15 16:00:00',
        'Nantes',
        'Place Royale',
        'Rennes',
        'Place de la Mairie',
        '2024-02-15 17:30:00',
        3,
        10,
        'pending',
        '2025-11-18 17:12:00',
        4,
        3
    );

/* Participations de mystereuny aux trajets d'elina */

-- mystereuny participe au trajet 1 d'elina (Saint-Étienne -> Lyon) - complété
INSERT INTO
    participate (
        id_participant,
        id_ridesharing,
        confirmed,
        nb_seats,
        created_at,
        completed_at
    )
VALUES (
        4,
        1,
        TRUE,
        2,
        '2025-11-18 17:12:00',
        '2024-01-15 09:00:00'
    );

-- mystereuny participe au trajet 4 d'elina (Saint-Étienne -> Lyon) - complété
INSERT INTO
    participate (
        id_participant,
        id_ridesharing,
        confirmed,
        nb_seats,
        created_at,
        completed_at
    )
VALUES (
        4,
        4,
        TRUE,
        1,
        '2025-11-18 17:12:00',
        '2024-01-22 09:00:00'
    );

/* Participations d'axeluny aux trajets d'elina */

-- axeluny participe au trajet 2 d'elina (Saint-Étienne -> Lyon) - complété
INSERT INTO
    participate (
        id_participant,
        id_ridesharing,
        confirmed,
        nb_seats,
        created_at,
        completed_at
    )
VALUES (
        3,
        2,
        TRUE,
        1,
        '2025-11-18 17:12:00',
        '2024-01-18 08:30:00'
    );

-- axeluny participe au trajet 3 d'elina (Lyon -> Saint-Étienne) - complété
INSERT INTO
    participate (
        id_participant,
        id_ridesharing,
        confirmed,
        nb_seats,
        created_at,
        completed_at
    )
VALUES (
        3,
        3,
        TRUE,
        1,
        '2025-11-18 17:12:00',
        '2024-01-18 19:00:00'
    );

-- axeluny participe au trajet 5 d'elina (Saint-Étienne -> Lyon) - en attente
INSERT INTO
    participate (
        id_participant,
        id_ridesharing,
        confirmed,
        nb_seats,
        created_at,
        completed_at
    )
VALUES (
        3,
        5,
        TRUE,
        1,
        '2025-11-18 17:12:00',
        NULL
    );

/* Participations d'axeluny aux trajets de mystereuny */

-- axeluny participe au trajet 7 de mystere (Paris -> Marseille) - complété
INSERT INTO
    participate (
        id_participant,
        id_ridesharing,
        confirmed,
        nb_seats,
        created_at,
        completed_at
    )
VALUES (
        3,
        7,
        TRUE,
        2,
        '2025-11-18 17:12:00',
        NULL
    );

-- axeluny participe au trajet 8 de mystere (Bordeaux -> Toulouse) - complété
INSERT INTO
    participate (
        id_participant,
        id_ridesharing,
        confirmed,
        nb_seats,
        created_at,
        completed_at
    )
VALUES (
        3,
        8,
        TRUE,
        1,
        '2025-11-18 17:12:00',
        '2024-01-14 12:30:00'
    );

-- axeluny participe au trajet 9 de mystere (Lyon -> Grenoble) - complété
INSERT INTO
    participate (
        id_participant,
        id_ridesharing,
        confirmed,
        nb_seats,
        created_at,
        completed_at
    )
VALUES (
        3,
        9,
        TRUE,
        2,
        '2025-11-18 17:12:00',
        '2024-01-20 15:30:00'
    );

-- axeluny participe au trajet 10 de mystere (Lille -> Strasbourg) - en attente
INSERT INTO
    participate (
        id_participant,
        id_ridesharing,
        confirmed,
        nb_seats,
        created_at,
        completed_at
    )
VALUES (
        3,
        10,
        TRUE,
        1,
        '2025-11-18 17:12:00',
        NULL
    );