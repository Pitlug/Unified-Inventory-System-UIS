DROP DATABASE IF EXISTS inventorymanagement;
CREATE DATABASE IF NOT EXISTS inventorymanagement;
USE inventorymanagement;

-- Tables for items
DROP TABLE IF EXISTS bundleItems;
DROP TABLE IF EXISTS orderItems;
DROP TABLE IF EXISTS logs;
DROP TABLE IF EXISTS logActions;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS bundle;
DROP TABLE IF EXISTS inventory;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS categories;

CREATE TABLE IF NOT EXISTS bundle (
    bundleID int auto_increment,
    bundleInfo varchar(255) NOT NULL,
    
    PRIMARY KEY (bundleID)
);


CREATE TABLE IF NOT EXISTS categories (
	categoryID int auto_increment,
    categoryName varchar(45) NOT NULL,
    categoryDesc varchar(255) NOT NULL,
    
    PRIMARY KEY (categoryID)
);


CREATE TABLE IF NOT EXISTS inventory (
    inventoryID int AUTO_INCREMENT,
    name varchar(45) NOT NULL,
    description varchar(255) NOT NULL,
    quantity int NOT NULL,
    categoryID int NOT NULL,
    
    PRIMARY KEY (inventoryID),
    CONSTRAINT fk_categoryID1 FOREIGN KEY (categoryID) REFERENCES categories(categoryID)
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
    credentialLevel int,
    
    PRIMARY KEY (userID)
);


CREATE TABLE IF NOT EXISTS orders (
    orderID int AUTO_INCREMENT,
    orderStatus varchar(45) NOT NULL,
    orderName varchar(100) NOT NULL,
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
    inventoryID int NULL,
    name varchar(45) NOT NULL,
    quantity int NOT NULL,
    price double NOT NULL,
    
    PRIMARY KEY (ID),
    CONSTRAINT fk_orderID1 FOREIGN KEY (orderID) REFERENCES orders(orderID),
    CONSTRAINT fk_inventoryID2 FOREIGN KEY (inventoryID) REFERENCES inventory(inventoryID) ON DELETE SET NULL
);


CREATE TABLE IF NOT EXISTS logActions (
	logActionID int auto_increment,
    orderID int,
    inventoryID int,
    bundleID int,
    userID int,
    
    PRIMARY KEY (logActionID),
    CONSTRAINT fk_orderID2 FOREIGN KEY (orderID) REFERENCES orders(orderID),
    CONSTRAINT fk_inventoryID4 FOREIGN KEY (inventoryID) REFERENCES inventory(inventoryID),
    CONSTRAINT fk_bundleID3 FOREIGN KEY (bundleID) REFERENCES bundle(bundleID),
    CONSTRAINT fk_userID3 FOREIGN KEY (userID) REFERENCES users(userID)
);

CREATE TABLE IF NOT EXISTS logs (
	logID int AUTO_INCREMENT,
    userID int NOT NULL,
    inventoryID int NOT NULL,
    action text,
    timestamp timestamp,
    logActionID int NOT NULL,
    
    PRIMARY KEY (logID),
    CONSTRAINT fk_userID2 FOREIGN KEY (userID) REFERENCES users(userID),
    CONSTRAINT fk_inventoryID3 FOREIGN KEY (inventoryID) REFERENCES inventory(inventoryID),
    CONSTRAINT fk_logActionID1 FOREIGN KEY (logActionID) REFERENCES logActions(logActionID)
);

INSERT INTO categories (categoryName, categoryDesc) VALUES ('Uncategorized', 'Default category for items added from completed orders');

INSERT INTO users (userID, 
username, password,
credentialLevel) VALUES
(1, "admin", "password", 0);