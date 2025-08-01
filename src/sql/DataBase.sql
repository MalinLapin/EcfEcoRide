-- Active: 1741707596826@@127.0.0.1@3306
CREATE DATABASE Ecoride;

DROP DATABASE Ecoride;

USE Ecoride;

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
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
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