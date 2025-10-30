SHOW DATABASES;
DROP DATABASE inventorymanagement;

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
    CONSTRAINT fk_bundleID1 FOREIGN KEY (bundleID) REFERENCES bundle(bundleID)
);

CREATE TABLE IF NOT EXISTS bundleItems (
    ID int auto_increment,
    bundleID int NOT NULL,
    inventoryID int NOT NULL,
    quantity int NOT NULL,
    
    PRIMARY KEY (ID),
    CONSTRAINT fk_bundleID2 FOREIGN KEY (bundleID) REFERENCES bundle(bundleID),
    CONSTRAINT fk_inventoryID1 FOREIGN KEY (inventoryID) REFERENCES inventory(inventoryID)
);

-- Tables for users
CREATE TABLE IF NOT EXISTS users (
    userID int AUTO_INCREMENT,
	username varchar(45) UNIQUE NOT NULL,
    password varchar(45) NOT NULL,
    credentialLevel int NOT NULL,
    
    PRIMARY KEY (userID)
);

CREATE TABLE IF NOT EXISTS orders (
    orderID int AUTO_INCREMENT,
    orderStatus varchar(45) NOT NULL,
    notes varchar(255),
    date date NOT NULL,
    userID int NOT NULL,
    timestamp timestamp,
    
    PRIMARY KEY (orderID),
    CONSTRAINT fk_userID1 FOREIGN KEY (userID) REFERENCES users(userID)
);

CREATE TABLE IF NOT EXISTS orderItems (
    ID int AUTO_INCREMENT,
    orderID int NOT NULL,
    inventoryID int NOT NULL,
    name varchar(45) NOT NULL,
    quantity int NOT NULL,
    price double NOT NULL,
    
    PRIMARY KEY (ID),
    CONSTRAINT fk_orderID1 FOREIGN KEY (orderID) REFERENCES orders(orderID),
    CONSTRAINT fk_inventoryID2 FOREIGN KEY (inventoryID) REFERENCES inventory(inventoryID)
);

CREATE TABLE IF NOT EXISTS logs (
	logID int AUTO_INCREMENT,
    userID int NOT NULL,
    inventoryID int NOT NULL,
    action text,
    timestamp timestamp,
    
    PRIMARY KEY (logID),
    CONSTRAINT fk_userID2 FOREIGN KEY (userID) REFERENCES users(userID),
    CONSTRAINT fk_inventoryID3 FOREIGN KEY (inventoryID) REFERENCES inventory(inventoryID)
);
