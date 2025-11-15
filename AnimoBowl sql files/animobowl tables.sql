CREATE SCHEMA AnimoBowl;

USE AnimoBowl;

CREATE TABLE brand
(
	BrandID INT PRIMARY KEY NOT NULL UNIQUE AUTO_INCREMENT,
    Name VARCHAR(30) NOT NULL
);


CREATE TABLE product (
    ProductID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    BrandID INT NOT NULL,
    Type ENUM('supplies', 'accessories', 'ball', 'bag', 'shoes'),
    Price FLOAT,
	ImageID VARCHAR(255),
    BranchID INT PRIMARY KEY NOT NULL,
    quantity INT,
    FOREIGN KEY (BrandID) REFERENCES brand (BrandID),
    FOREIGN KEY (BranchID) REFERENCES branches (BranchID)
);

CREATE TABLE bowlingbag
(
	ProductID INT NOT NULL,
    Name VARCHAR(100) NOT NULL,
    Type ENUM('Backpack', 'Roller', 'Tote') NOT NULL,
    Size ENUM('1', '2', '3', '4') NOT NULL,
    BranchID INT NOT NULL,
    Color VARCHAR(20) NOT NULL,
    FOREIGN KEY (ProductID,BranchID) REFERENCES product(ProductID,BranchID) ON DELETE CASCADE
);

CREATE TABLE cleaningsupplies
(
	ProductID INT NOT NULL,
    Name VARCHAR(100) NOT NULL,
    Type ENUM('Towel', 'Cleaner', 'Puff', 'Pads') NOT NULL,
    BranchID INT NOT NULL,
    FOREIGN KEY (ProductID,BranchID) REFERENCES product(ProductID,BranchID) ON DELETE CASCADE
);


CREATE TABLE bowlingaccessories
(
	ProductID INT NOT NULL,
    Name VARCHAR(100) NOT NULL,
    Type ENUM('Tape', 'Grips', 'Wrister') NOT NULL,
    Handedness ENUM('Right', 'Left'),
	BranchID INT NOT NULL,
    FOREIGN KEY (ProductID,BranchID) REFERENCES product(ProductID,BranchID) ON DELETE CASCADE
);


-- Bowling Ball -- 
CREATE TABLE bowlingball
(
	ProductID INT NOT NULL,
    Quality ENUM('New', 'Second Hand') NOT NULL,
    Name VARCHAR(100) NOT NULL,
    Type ENUM('Plastic', 'Urethane', 'Solid', 'Pearl', 'Hybrid') NOT NULL,
    RG FLOAT NOT NULL,
    DIFF FLOAT NOT NULL,
    INTDIFF FLOAT,
    weight INT NOT NULL,
    CoreType ENUM('Asymetric', 'Symetric') NOT NULL,
    CoreName VARCHAR(100) NOT NULL,
    Coverstock VARCHAR(100) NOT NULL,
    CoverstockType VARCHAR(100) NOT NULL,
    BranchID INT NOT NULL,
    FOREIGN KEY (ProductID,BranchID) REFERENCES product(ProductID,BranchID) ON DELETE CASCADE
);

-- shoes -- 
CREATE TABLE bowlingshoes
(
ProductID INT NOT NULL,
Name VARCHAR(255) NOT NULL,
BranchID INT NOT NULL,
size INT,
sex enum('M','F') NOT NULL,
FOREIGN KEY (ProductID,BranchID) REFERENCES product(ProductID,BranchID)
);


CREATE TABLE address
(
AddressID INT PRIMARY KEY NOT NULL UNIQUE AUTO_INCREMENT,
City VARCHAR(100) NOT NULL,
Street VARCHAR(255),
Zip_Code VARCHAR(10)
);

CREATE TABLE branches 
(
BranchID INT PRIMARY KEY NOT NULL UNIQUE AUTO_INCREMENT,
AddressID INT,
Name VARCHAR(50) NOT NULL,
FOREIGN KEY (AddressID) REFERENCES address(AddressID)
);

CREATE TABLE currency
(
CurrencyID INT PRIMARY KEY NOT NULL UNIQUE AUTO_INCREMENT,
Currency_Name VARCHAR(3) NOT NULL,
Currency_Rate DOUBLE(6,2) NOT NULL
);

CREATE TABLE users
(
UserID INT PRIMARY KEY NOT NULL UNIQUE AUTO_INCREMENT,
AddressID INT,
FirstName VARCHAR(50) NOT NULL,
LastName VARCHAR(50) NOT NULL,
MobileNumber VARCHAR(20),
Email VARCHAR(100) NOT NULL,
Password VARCHAR(255) NOT NULL,
Role ENUM('Staff', 'Admin', 'Customer') DEFAULT 'Customer',
FOREIGN KEY (AddressID) REFERENCES address(AddressID) ON DELETE CASCADE
);

CREATE TABLE staff
(
StaffID INT,
AdminPrivs BOOLEAN,
FOREIGN KEY (StaffID) REFERENCES users(UserID)
);

CREATE TABLE services
(
ServiceID INT PRIMARY KEY NOT NULL UNIQUE AUTO_INCREMENT,
StaffID INT,
Type ENUM('Drilling', 'Polishing', 'Sanding', 'Repair'),
Price FLOAT NOT NULL,
Availability BOOLEAN,
FOREIGN KEY (StaffID) REFERENCES staff(StaffID)
);
CREATE TABLE orders
(
OrderID INT PRIMARY KEY NOT NULL UNIQUE AUTO_INCREMENT,
CustomerID INT,
CurrencyID INT,
BranchID INT,
DatePurchased DATETIME,
DateCompleted DATETIME DEFAULT NULL,
Status ENUM('Pending', 'Processing', 'Completed', 'Cancelled') DEFAULT 'Pending',
Total FLOAT NOT NULL,
PaymentMode ENUM('Cash', 'Credit Card', 'Online') DEFAULT NULL,
DeliveryMethod ENUM('Pickup', 'Delivery') DEFAULT NULL,
FOREIGN KEY (CustomerID) REFERENCES users(UserID) ON DELETE CASCADE ,
FOREIGN KEY (CurrencyID) REFERENCES currency(CurrencyID),
FOREIGN KEY (BranchID) REFERENCES branches(BranchID)
);


CREATE TABLE orderdetails
(
OrderDetailsID INT PRIMARY KEY NOT NULL UNIQUE AUTO_INCREMENT,
OrderID INT,
ProductID INT,
Quantity INT NOT NULL DEFAULT 1,
price double(10,2),
FOREIGN KEY (OrderID) REFERENCES orders(OrderID) ON DELETE CASCADE
);

DROP TABLE orderdetails;
CREATE TABLE servicedetails
(
ServiceOrderID INT PRIMARY KEY NOT NULL UNIQUE AUTO_INCREMENT,
OrderID INT,
ServiceID INT,
isFromStore BOOLEAN,
price double(10,2),
FOREIGN KEY (ServiceID) REFERENCES services(ServiceID) ON DELETE CASCADE
);
