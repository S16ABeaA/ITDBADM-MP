USE AnimoBowl;

INSERT INTO brand
VALUES 
(1,'Storm') ,
(2,'Motiv'),
(3,'Hammer'),
(4,'Track'),
(5,'Columbia 300'),
(6,'Brunswick'),
(7,'900 Global'),
(8,'Ebonite'),
(9,'Rotogrip'),
(10,'Turbo'),
(11,'Vise'),
(12,'Radical'),
(13,'Dexter'),
(14,'3G');

-- As of 11/07/25 -- 
INSERT INTO currency
VALUES 
(1,'PHP',1),
(2,'USD',0.017),
(3,'KRW',0.041);

-- branch address -- 
INSERT INTO address
VALUES (1,'Manila','Nagtahan',1015),
(2,'Makati','Poblacion',1210);

INSERT INTO branches
VALUES (1,1), (2,2);



INSERT INTO product 
VALUES
(1,1,'ball',9500.25,NULL,1), 
(2,2,'ball',10000,NULL,1), 
(3,3,'ball',12000,NULL,1),
(4,1,'ball',7000,NULL,1),
(5,7,'ball',9000,NULL,1), 
(6,11,'accessories',750,NULL,1),
(7,15,'accessories',8000,NULL,1),
(8,2,'accessories',750,NULL,1),
(9,3,'supplies',500,NULL,1),
(10,4,'supplies',500,NULL,1),
(11,6,'supplies',250,NULL,1),
(12,11,'supplies',300,NULL,1), 
(13,1,'bag',6000,NULL,1),
(14,2,'bag',3000,NULL,1),
(15,3,'bag',9500,NULL,1),
(16,5,'bag',1500,NULL,1),
(17,6,'bag',8500,NULL,1),
(18,5,'bag',6000,NULL,1),
(19,13,'shoes',12000,NULL,1),
(20,13,'shoes',12000,NULL,1),
(21,13,'shoes',12000,NULL,1),
(22,14,'shoes',10000,NULL,1),
(23,14,'shoes',10000,NULL,1),
(24,14,'shoes',9500,NULL,1),
(25,7,'ball',12000,NULL,1),
(26,9,'ball',10000,NULL,1),
(27,1,'supplies',350,NULL,1);


INSERT INTO bowlingball
(ProductID, ImageID, Quality, Name, Type, CoreType, CoreName, Coverstock, CoverstockType, BranchID)
VALUES
(1, NULL, 'New', 'Phaze II', 'Solid', 'Symetric', 'Velocity', 'TX-16', '3000 Abralon', 1),
(2, NULL, 'New', 'Hyper Venom', 'Pearl', 'Symetric', 'Gear', 'Propulsion MXR', '5500 LSP', 1),
(3, NULL, 'New', 'Purple Solid Urethane', 'Urethane', 'Asymetric', 'FAB', 'Hammer', '500/1000 Siaair Micro Pad', 1),
(4, NULL, 'New', 'Ice Storm', 'Plastic', 'Symetric', 'Storm Traditional 3-piece', 'Polyester', '3500 Polished', 1),
(5, NULL, 'Second Hand', 'Xponent', 'Solid', 'Symetric', 'Shrapnel 2.0', 'Reserve Blend 701', '4000 Abralon', 1),
(25, NULL, 'New', 'Xponent Pearl', 'Pearl', 'Symetric', 'Shrapnel 2.0', 'Reserve Blend 702', '4000 Abralon', 1),
(26, NULL, 'New', 'Magic Gem', 'Hybrid', 'Asymetric', 'Defiant LRG', 'MicroTrax', '2000 Abralon', 1),
(1, NULL, 'New', 'Phaze II', 'Solid', 'Symetric', 'Velocity', 'TX-16', '3000 Abralon', 2),
(2, NULL, 'New', 'Hyper Venom', 'Pearl', 'Symetric', 'Gear', 'Propulsion MXR', '5500 LSP', 2),
(3, NULL, 'New', 'Purple Solid Urethane', 'Urethane', 'Asymetric', 'FAB', 'Hammer', '500/1000 Siaair Micro Pad', 2),
(4, NULL, 'New', 'Ice Storm', 'Plastic', 'Symetric', 'Storm Traditional 3-piece', 'Polyester', '3500 Polished', 2),
(5, NULL, 'Second Hand', 'Xponent', 'Solid', 'Symetric', 'Shrapnel 2.0', 'Reserve Blend 701', '4000 Abralon', 2),
(25, NULL, 'New', 'Xponent Pearl', 'Pearl', 'Symetric', 'Shrapnel 2.0', 'Reserve Blend 702', '4000 Abralon', 2),
(26, NULL, 'New', 'Magic Gem', 'Hybrid', 'Asymetric', 'Defiant LRG', 'MicroTrax', '2000 Abralon', 2);


INSERT INTO weight
VALUES 
(1,1,2.48,0.051,NULL,15,2),
(1,1,2.53,0.050,NULL,14,2),
(2,1,2.48,0.034,NULL,15,2),
(3,1,2.55,0.051,0.014,15,2),
(4,1,2.69,0.006,NULL,15,2),
(4,1,2.74,0.006,NULL,12,1),
(5,1,2.48,0.042,NULL,15,1),
(25,1,2.48,0.042,NULL,15,1),
(26,1,2.470,0.053,0.016,15,1),
(1,2,2.48,0.051,NULL,15,2),
(1,2,2.53,0.050,NULL,14,2),
(2,2,2.48,0.034,NULL,15,2),
(3,2,2.55,0.051,0.014,15,2),
(4,2,2.69,0.006,NULL,15,2),
(4,2,2.74,0.006,NULL,12,1),
(5,2,2.48,0.042,NULL,15,1),
(25,2,2.48,0.042,NULL,15,1),
(26,2,2.470,0.053,0.016,15,1);

-- Bowling balls
ALTER TABLE cleaningsupplies DROP COLUMN ImageID;


INSERT INTO bowlingaccessories
VALUES
(6,'accessories/ViseGrps.jpg','Vise Grip Inserts(Set of 3)','Grips',NULL,1,100),
(6,'accessories/ViseGrps.jpg','Vise Grip Inserts(Set of 3)','Grips',NULL,2,100),
(7,'accessories/Wrister.jpg','Robby Revs 2 Bowling Wrister(KR Strikeforce Edition)','Wrister','Left',1,1),
(7,'accessories/Wrister.jpg','Robby Revs 2 Bowling Wrister(KR Strikeforce Edition)','Wrister','Right',2,1),
(7,'accessories/Wrister.jpg','Robby Revs 2 Bowling Wrister(KR Strikeforce Edition)','Wrister','Left',1,1),
(7,'accessories/Wrister.jpg','Robby Revs 2 Bowling Wrister(KR Strikeforce Edition)','Wrister','Right',2,1),
(8,'accessories/MotivTape.jpg','Motiv Flex Tape','Tape',NULL,1,10),
(8,'accessories/MotivTape.jpg','Motiv Flex Tape','Tape',NULL,2,10)
;

SELECT *
FROM bowlingaccessories;

INSERT INTO cleaningsupplies (ProductID,Name,Type,BranchID,quantity)
VALUES 
(9,'Hammer Premium Towel','Towel',1,2),
(10,'Track Bowling Ball Spray Cleaner','Cleaner',1,5),
(11,'Brunswick Reactive Shammy','Towel',1,2),
(12,'Abralon Pads','Pads',1,100),
(27,'Storm Puff Ball','Puff',1,3),
(9,'Hammer Premium Towel','Towel',2,2),
(10,'Track Bowling Ball Spray Cleaner','Cleaner',2,5),
(11,'Brunswick Reactive Shammy','Towel',2,2),
(12,'Abralon Pads','Pads',2,100),
(27,'Storm Puff Ball','Puff',2,3);
       
INSERT INTO bowlingbag (ProductID,Name,Type,Size,BranchID)
VALUES
(13,'Storm Rolling Thunder 2 Ball Roller Checkered Black/Gold','Roller',2,1),
(14,'Storm Solo 1 Ball Bowling Bag','Tote',1,1),
(15,'Hammer Premium 3 Ball Roller Orange Bowling Bag','Roller',3,1),
(16,'Columbia 300 OGIO Monolithic Bowling Backpack','Backpack',1,1),
(17,'Brunswick Enamel Collar 4 Ball Roller Bag','Roller',4,1),
(18,'Boss Double Tote','Tote',2,1),
(13,'Storm Rolling Thunder 2 Ball Roller Checkered Black/Gold','Roller',2,2),
(14,'Storm Solo 1 Ball Bowling Bag','Tote',1,2),
(15,'Hammer Premium 3 Ball Roller Orange Bowling Bag','Roller',3,2),
(16,'Columbia 300 OGIO Monolithic Bowling Backpack','Backpack',1,2),
(17,'Brunswick Enamel Collar 4 Ball Roller Bag','Roller',4,2),
(18,'Boss Double Tote','Tote',2,2);




INSERT INTO bowlingshoes (ProductID, Name, BranchID)
VALUES
(19,'Dexter C9 Knit Boa (Mens) Black/Gold',1),
(20,'Dexter SST 8 Power Frame Boa (Womens) White/Blue',1),
(21,'Dexter THE 8 Power Frame Boa (Mens)',1),
(19,'Dexter C9 Knit Boa (Mens) Black/Gold',2),
(20,'Dexter SST 8 Power Frame Boa (Womens) White/Blue',2),
(21,'Dexter THE 8 Power Frame Boa (Mens)',2),
(22,'3G Tour Black (Mens)',1),
(23,'3G Tour Ultra / C (Womens) White/Mint ',1),
(24,'3G Belmo Tour S (Mens)',1),
(22,'3G Tour Black (Mens)',2),
(23,'3G Tour Ultra / C (Womens) White/Mint ',2),
(24,'3G Belmo Tour S (Mens)',2);


-- User Addresses -- 
INSERT INTO address
VALUES 
(3,'Quezon City','La Loma',1114), -- Admin
(4,'Manila','Binondo',1006), -- Staff
(5,'Laguna','Calamba',4027), -- User
(6,'Pasay','San Jose',1305), -- User
(7,'Makati','Bel-air',1209); -- Staff

INSERT INTO users
VALUES
(1,3,'Dylan','Akia','09235352953','dylanakia@gmail.com','staff123456','Staff'),
(2,4,'Adrian','Bea',NULL,'adrianbea@gmail.com','@dmin123456','Admin'),
(3,5,'Joaquin','Perez','09923238710','joaquinperez@gmail.com','user123456','Customer'),
(4,6,'Shohei','Ohtani','09127777771','shohei@gmail.com','ilovegambling','Customer'),
(5,7,'Lance','Ong','09553232123','lanceong@gmail.com','staff2123456','Staff');

INSERT INTO staff
VALUES
(1,0),
(2,1),
(5,0);

INSERT INTO services
VALUES
(1,1,'Drilling',750,1),
(2,5,'Polishing',650,1),
(3,5,'Sanding',400,1),
(4,1,'Repair',1500,1);

