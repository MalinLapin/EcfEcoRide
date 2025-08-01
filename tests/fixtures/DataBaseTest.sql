-- Active: 1741707596826@@127.0.0.1@3306
DROP DATABASE IF EXISTS Ecoride_test;

CREATE DATABASE Ecoride_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE Ecoride_test;

CREATE TABLE brand (
    id_brand INT PRIMARY KEY AUTO_INCREMENT,
    label VARCHAR(30) NOT NULL UNIQUE
);

CREATE TABLE user (
    id_user INT PRIMARY KEY AUTO_INCREMENT,
    last_name VARCHAR(50) NULL,
    first_name VARCHAR(50) NULL,
    pseudo VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL,
    credit_balance INT,
    photo VARCHAR(255),
    grade DECIMAL(2, 1),
    role ENUM(
        'user',
        'employe',
        'driver',
        'admin'
    ) NOT NULL DEFAULT 'user',
    is_active BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE car (
    id_car INT PRIMARY KEY AUTO_INCREMENT,
    model VARCHAR(20),
    registration_number VARCHAR(20) NOT NULL,
    first_registration DATE NOT NULL,
    energy_type VARCHAR(20) NOT NULL,
    color VARCHAR(20) NOT NULL,
    id_brand INT NOT NULL,
    id_user INT NOT NULL,
    FOREIGN KEY (id_brand) REFERENCES brand (id_brand),
    FOREIGN KEY (id_user) REFERENCES user (id_user)
);

CREATE TABLE ridesharing (
    id_ridesharing INT PRIMARY KEY AUTO_INCREMENT,
    departure_date DATETIME NOT NULL,
    departure_city VARCHAR(50) NOT NULL,
    departure_adress VARCHAR(255),
    arrival_city VARCHAR(50) NOT NULL,
    arrival_adress VARCHAR(255),
    arrival_date DATETIME,
    available_seats INT NOT NULL,
    price_par_seat INT NOT NULL,
    status ENUM(
        'pending',
        'ongoing',
        'completed',
        'cancelled'
    ) NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL,
    id_driver INT NOT NULL,
    id_car INT NOT NULL,
    FOREIGN KEY (id_driver) REFERENCES user (id_user),
    FOREIGN KEY (id_car) REFERENCES car (id_car)
);

CREATE TABLE participate (
    id_participant INT NOT NULL,
    id_ridesharing INT NOT NULL,
    PRIMARY KEY (
        id_participant,
        id_ridesharing
    ),
    FOREIGN KEY (id_participant) REFERENCES user (id_user),
    FOREIGN KEY (id_ridesharing) REFERENCES ridesharing (id_ridesharing)
);

INSERT INTO
    user (
        last_name,
        first_name,
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
        'Uny',
        'Marc',
        'marcuny',
        'marc.uny@test.com',
        '$2y$10$eW8z5Z1b7f3a5d9F4Q6uUO0k1j5Y1Z5h1Z5h1Z5h1Z5h1Z5h1Z5h1',
        NOW(),
        100,
        'marc.jpg',
        5.0,
        'admin'
    ),
    (
        'Uny',
        'Elina',
        'elinauny',
        'elina.uny@test.com',
        '$2y$10$eW8z5Z1b7f3a5d9F4Q6uUO0k1j5Y1Z5h1Z5h1Z5h1Z5h1Z5h1Z5h1',
        NOW(),
        50,
        'elina.jpg',
        4.0,
        'user'
    ),
    (
        'Uny',
        'Axel',
        'axeluny',
        'axel.uny@test.com',
        '$2y$10$eW8z5Z1b7f3a5d9F4Q6uUO0k1j5Y1Z5h1Z5h1Z5h1Z5h1Z5h1Z5h1',
        NOW(),
        75,
        'axel.jpg',
        4.2,
        'driver'
    ),
    (
        'Uny',
        'Mystère',
        'mystereuny',
        'mystere.uny@test.com',
        '$2y$10$eW8z5Z1b7f3a5d9F4Q6uUO0k1j5Y1Z5h1Z5h1Z5h1Z5h1Z5h1Z5h1',
        NOW(),
        75,
        'mystere.jpg',
        4.2,
        'driver'
    ),
    (
        'Uny',
        'Milka',
        'milkauny',
        'milka@uny@test.com',
        '$2y$10$eW8z5Z1b7f3a5d9F4Q6uUO0k1j5Y1Z5h1Z5h1Z5h1Z5h1Z5h1Z5h1',
        NOW(),
        30,
        'milka.jpg',
        NULL,
        'employe'
    );

INSERT INTO
    brand (label)
VALUES ('Toyota'),
    ('Ford'),
    ('BMW'),
    ('Mercedes');

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
        'Corolla',
        'ABC123',
        '2020-01-15',
        'Petrol',
        'Red',
        1,
        2
    ),
    (
        'Focus',
        'XYZ456',
        '2019-05-20',
        'Diesel',
        'Blue',
        2,
        3
    ),
    (
        'X5',
        'LMN789',
        '2021-03-10',
        'Electric',
        'Black',
        3,
        3
    ),
    (
        'A-Class',
        'OPQ012',
        '2018-07-25',
        'Hybrid',
        'White',
        4,
        4
    ),
    (
        'Civic',
        'RST345',
        '2022-11-30',
        'Petrol',
        'Green',
        1,
        4
    );

INSERT INTO
    ridesharing (
        departure_date,
        departure_city,
        departure_adress,
        arrival_city,
        arrival_adress,
        arrival_date,
        available_seats,
        price_par_seat,
        status,
        created_at,
        id_driver,
        id_car
    )
VALUES (
        '2023-10-01 08:00:00',
        'Paris',
        '123 Rue de Paris',
        'Lyon',
        '456 Avenue de Lyon',
        '2023-10-01 12:00:00',
        3,
        20,
        'pending',
        NOW(),
        3,
        1
    ),
    (
        '2023-10-02 09:00:00',
        'Marseille',
        '789 Boulevard de Marseille',
        'Nice',
        '321 Rue de Nice',
        '2023-10-02 11:00:00',
        2,
        25,
        'ongoing',
        NOW(),
        4,
        2
    ),
    (
        '2023-10-03 07:30:00',
        'Bordeaux',
        '654 Avenue de Bordeaux',
        'Toulouse',
        '987 Rue de Toulouse',
        NULL,
        4,
        30,
        'pending',
        NOW(),
        2,
        3
    ),
    (
        '2023-10-04 10:15:00',
        'Nantes',
        '159 Boulevard de Nantes',
        'Rennes',
        '753 Rue de Rennes',
        NULL,
        1,
        15,
        'completed',
        NOW(),
        2,
        4
    ),
    (
        '2023-10-05 06:45:00',
        'Strasbourg',
        '852 Avenue de Strasbourg',
        'Mulhouse',
        '951 Rue de Mulhouse',
        NULL,
        5,
        40,
        'cancelled',
        NOW(),
        4,
        5
    ),
    (
        '2023-10-06 08:30:00',
        'Lille',
        '258 Boulevard de Lille',
        'Roubaix',
        NULL,
        NULL,
        2,
        18,
        'pending',
        NOW(),
        1,
        1
    );

INSERT INTO
    participate (
        id_participant,
        id_ridesharing
    )
VALUES (2, 1),
    (3, 2),
    (2, 3),
    (3, 4),
    (4, 5),
    (2, 6),
    (4, 6);