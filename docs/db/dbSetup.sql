CREATE DATABASE IF NOT EXISTS inventorymanagement;
USE inventorymanagement;

-- Tables for items
CREATE TABLE IF NOT EXISTS bundle (
    bundleID int auto_increment,
    bundleInfo varchar(255) NOT NULL,
    
    PRIMARY KEY (bundleID)
);

CREATE TABLE IF NOT EXISTS inventory (
    inventoryID int AUTO_INCREMENT,
    name varchar(45) NOT NULL,
    description varchar(255) NOT NULL,
    quantity int NOT NULL,
    bundleID int NOT NULL,
    
    PRIMARY KEY (inventoryID),
    CONSTRAINT fk_bundleID FOREIGN KEY (bundleID) REFERENCES bundle(bundleID)
);

CREATE TABLE IF NOT EXISTS bundleItems (
    ID int auto_increment,
    bundleID int NOT NULL,
    inventoryID int NOT NULL,
    quantity int NOT NULL,
    
    PRIMARY KEY (ID),
    CONSTRAINT fk_bundleID FOREIGN KEY (bundleID) REFERENCES bundle(bundleID),
    CONSTRAINT fk_inventoryID FOREIGN KEY (inventoryID) REFERENCES inventory(inventoryID)
);

-- Tables for users
CREATE TABLE IF NOT EXISTS users (
    userID int AUTO_INCREMENT,
	username varchar(45) UNIQUE NOT NULL,
    password varchar(45) NOT NULL,
    credentialLevel varchar(45),
    
    PRIMARY KEY (userID)
);

CREATE TABLE IF NOT EXISTS orders (
    orderID int AUTO_INCREMENT,
    orderStatus varchar(45) NOT NULL,
    notes varchar(255),
    date date NOT NULL,
    
    PRIMARY KEY (orderID)
);

CREATE TABLE IF NOT EXISTS orderItems (
    ID int AUTO_INCREMENT,
    orderID int NOT NULL,
    inventoryID int NOT NULL,
    name varchar(45) NOT NULL,
    quantity int NOT NULL,
    price double NOT NULL,
    
    PRIMARY KEY (ID),
    CONSTRAINT fk_orderID FOREIGN KEY (orderID) REFERENCES orders(orderID),
    CONSTRAINT fk_inventoryID FOREIGN KEY (inventoryID) REFERENCES inventory(inventoryID)
);