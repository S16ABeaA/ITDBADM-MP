USE AnimoBowl;


-- ADD PROCEDURES -- 

-- Procedure 1 Add a Bowling Ball --
DELIMITER $$

CREATE PROCEDURE AddBowlingBall(
    IN p_BranchID INT,
    IN p_BrandID INT,
    IN p_Name VARCHAR(100),
    IN p_Price FLOAT,
    IN p_ImageID VARCHAR(255),
    IN p_Quality ENUM('New','Second Hand'),
    IN p_Type ENUM('Plastic','Urethane','Solid','Pearl','Hybrid'),
    IN p_Weight INT,
    IN p_CoreType ENUM('Asymetric','Symetric'),
    IN p_CoreName VARCHAR(100),
    IN p_Coverstock VARCHAR(100),
    IN p_CoverstockType VARCHAR(100),
    IN p_RG FLOAT,
    IN p_DIFF FLOAT,
    IN p_INTDIFF FLOAT,
    IN p_Quantity INT
)
BEGIN
    DECLARE existingProductID INT;

    -- Check if this bowling ball already exists (same name + weight) across branches
    SELECT bb.ProductID INTO existingProductID
    FROM bowlingball bb
    WHERE bb.Name = p_Name AND bb.weight = p_Weight
    LIMIT 1;

    -- If it doesn't exist yet, create a new product + bowlingball entry
    IF existingProductID IS NULL THEN
        INSERT INTO product (BrandID, Type, Price, ImageID, BranchID, quantity)
        VALUES (p_BrandID, 'ball', p_Price, p_ImageID, p_BranchID, p_Quantity);

        SET existingProductID = LAST_INSERT_ID();

        INSERT INTO bowlingball (
            ProductID, Quality, Name, Type, RG, DIFF, INTDIFF, weight,
            CoreType, CoreName, Coverstock, CoverstockType, BranchID
        )
        VALUES (
            existingProductID, p_Quality, p_Name, p_Type, p_RG, p_DIFF, p_INTDIFF, p_Weight,
            p_CoreType, p_CoreName, p_Coverstock, p_CoverstockType, p_BranchID
        );

   -- If it exists, add this product to the new branch if not already there
    ELSE
        IF NOT EXISTS (
            SELECT 1 FROM product
            WHERE ProductID = existingProductID AND BranchID = p_BranchID
        ) THEN
            INSERT INTO product (ProductID, BrandID, Type, Price, ImageID, BranchID, quantity)
            VALUES (existingProductID, p_BrandID, 'ball', p_Price, p_ImageID, p_BranchID, p_Quantity);
        END IF;

        -- 4Also replicate the bowlingball record if missing in this branch
        IF NOT EXISTS (
            SELECT 1 FROM bowlingball
            WHERE ProductID = existingProductID AND BranchID = p_BranchID
        ) THEN
            INSERT INTO bowlingball (
                ProductID, Quality, Name, Type, RG, DIFF, INTDIFF, weight,
                CoreType, CoreName, Coverstock, CoverstockType, BranchID
            )
            VALUES (
                existingProductID, p_Quality, p_Name, p_Type, p_RG, p_DIFF, p_INTDIFF, p_Weight,
                p_CoreType, p_CoreName, p_Coverstock, p_CoverstockType, p_BranchID
            );
        END IF;
    END IF;
END $$

DELIMITER ;

CALL AddBowlingBall(
    1,                -- BranchID
    1,                -- BrandID
    'Phaze II',      -- Name
    8500,             -- Price
    NULL,             -- ImageID
    'New',            -- Quality
    'Solid',         -- Type
    15,               -- Weight
    'Symetric',       -- CoreType
    'Velocity',       -- CoreName
    'TX-16',     -- Coverstock
    '3000 Abralon',    -- CoverstockType
    2.48,             -- RG
    0.051,            -- DIFF
    0.000,            -- INTDIFF
    1              -- Quantity
);

SELECT *
FROM bowlingball;
-- Procedure 2 Add a Bowling Accessory --
DELIMITER $$

CREATE PROCEDURE AddBowlingAccessories(
    IN p_BranchID INT,
    IN p_BrandID INT,
    IN p_Name VARCHAR(100),
    IN p_Price FLOAT,
    IN p_ImageID VARCHAR(255),
    IN p_Type ENUM('Tape','Grips','Wrister'),
    IN p_Handedness ENUM('Right','Left'),
    IN p_Quantity INT
)
BEGIN
    DECLARE existingProductID INT;

    -- Find existing accessory with same name
    SELECT ba.ProductID INTO existingProductID
    FROM bowlingaccessories ba
    WHERE ba.Name = p_Name
    LIMIT 1;

    IF existingProductID IS NULL THEN
        INSERT INTO product (BrandID, Type, Price, ImageID, BranchID, quantity)
        VALUES (p_BrandID, 'accessories', p_Price, p_ImageID, p_BranchID, p_Quantity);
        SET existingProductID = LAST_INSERT_ID();

        INSERT INTO bowlingaccessories (ProductID, Name, Type, Handedness, BranchID)
        VALUES (existingProductID, p_Name, p_Type, p_Handedness, p_BranchID);
    ELSE
        IF NOT EXISTS (
            SELECT 1 FROM product
            WHERE ProductID = existingProductID AND BranchID = p_BranchID
        ) THEN
            INSERT INTO product (ProductID, BrandID, Type, Price, ImageID, BranchID, quantity)
            VALUES (existingProductID, p_BrandID, 'accessories', p_Price, p_ImageID, p_BranchID, p_Quantity);
        END IF;

        IF NOT EXISTS (
            SELECT 1 FROM bowlingaccessories
            WHERE ProductID = existingProductID AND BranchID = p_BranchID
        ) THEN
            INSERT INTO bowlingaccessories (ProductID, Name, Type, Handedness, BranchID)
            VALUES (existingProductID, p_Name, p_Type, p_Handedness, p_BranchID);
        END IF;
    END IF;
END $$

DELIMITER ;

-- Procedure 3 Add a Bowling Bag --
DELIMITER $$

CREATE PROCEDURE AddBowlingBag(
    IN p_BranchID INT,
    IN p_BrandID INT,
    IN p_Name VARCHAR(100),
    IN p_Price FLOAT,
    IN p_ImageID VARCHAR(255),
    IN p_Type ENUM('Backpack','Roller','Tote'),
    IN p_Size ENUM('1','2','3','4'),
    IN p_Color VARCHAR(20),
    IN p_Quantity INT
)
BEGIN
    DECLARE existingProductID INT;

    -- Find existing bag with same name + color
    SELECT bb.ProductID INTO existingProductID
    FROM bowlingbag bb
    WHERE bb.Name = p_Name AND bb.color = p_Color
    LIMIT 1;

    IF existingProductID IS NULL THEN
        INSERT INTO product (BrandID, Type, Price, ImageID, BranchID, quantity)
        VALUES (p_BrandID, 'bag', p_Price, p_ImageID, p_BranchID, p_Quantity);
        SET existingProductID = LAST_INSERT_ID();

        INSERT INTO bowlingbag (ProductID, Name, Type, Size, BranchID, color)
        VALUES (existingProductID, p_Name, p_Type, p_Size, p_BranchID, p_Color);
    ELSE
        IF NOT EXISTS (
            SELECT 1 FROM product
            WHERE ProductID = existingProductID AND BranchID = p_BranchID
        ) THEN
            INSERT INTO product (ProductID, BrandID, Type, Price, ImageID, BranchID, quantity)
            VALUES (existingProductID, p_BrandID, 'bag', p_Price, p_ImageID, p_BranchID, p_Quantity);
        END IF;

        IF NOT EXISTS (
            SELECT 1 FROM bowlingbag
            WHERE ProductID = existingProductID AND BranchID = p_BranchID
        ) THEN
            INSERT INTO bowlingbag (ProductID, Name, Type, Size, BranchID, color)
            VALUES (existingProductID, p_Name, p_Type, p_Size, p_BranchID, p_Color);
        END IF;
    END IF;
END $$

DELIMITER ;

CALL AddBowlingBag(
    1,                  -- BranchID
    2,                  -- BrandID
    'Storm 3-Ball Roller', -- Name
    9500,               -- Price
    'stormbag3img',     -- ImageID
    'Roller',           -- Type
    '3',                -- Size (3-ball bag)
    'Black/Red',        -- Color
    8                   -- Quantity
);

-- Procedure 4 Add a Bowling Shoe --
DELIMITER $$

CREATE PROCEDURE AddBowlingShoes(
    IN p_BranchID INT,
    IN p_BrandID INT,
    IN p_Name VARCHAR(255),
    IN p_Price FLOAT,
    IN p_ImageID VARCHAR(255),
    IN p_Size INT,
    IN p_Sex ENUM('M','F'),
    IN p_Quantity INT
)
BEGIN
    DECLARE existingProductID INT;

    -- Find existing shoes with same name + size
    SELECT bs.ProductID INTO existingProductID
    FROM bowlingshoes bs
    WHERE bs.name = p_Name AND bs.size = p_Size
    LIMIT 1;

    IF existingProductID IS NULL THEN
        INSERT INTO product (BrandID, Type, Price, ImageID, BranchID, quantity)
        VALUES (p_BrandID, 'shoes', p_Price, p_ImageID, p_BranchID, p_Quantity);
        SET existingProductID = LAST_INSERT_ID();

        INSERT INTO bowlingshoes (ProductID, name, BranchID, size, sex)
        VALUES (existingProductID, p_Name, p_BranchID, p_Size, p_Sex);
    ELSE
        IF NOT EXISTS (
            SELECT 1 FROM product
            WHERE ProductID = existingProductID AND BranchID = p_BranchID
        ) THEN
            INSERT INTO product (ProductID, BrandID, Type, Price, ImageID, BranchID, quantity)
            VALUES (existingProductID, p_BrandID, 'shoes', p_Price, p_ImageID, p_BranchID, p_Quantity);
        END IF;

        IF NOT EXISTS (
            SELECT 1 FROM bowlingshoes
            WHERE ProductID = existingProductID AND BranchID = p_BranchID
        ) THEN
            INSERT INTO bowlingshoes (ProductID, name, BranchID, size, sex)
            VALUES (existingProductID, p_Name, p_BranchID, p_Size, p_Sex);
        END IF;
    END IF;
END $$

DELIMITER ;

-- Procedure 5 Add a Cleaning Supply --
DELIMITER $$

CREATE PROCEDURE AddCleaningSupplies(
    IN p_BranchID INT,
    IN p_BrandID INT,
    IN p_Name VARCHAR(100),
    IN p_Price FLOAT,
    IN p_ImageID VARCHAR(255),
    IN p_Type ENUM('Towel','Cleaner','Puff','Pads'),
    IN p_Quantity INT
)
BEGIN
    DECLARE existingProductID INT;

    -- Find existing cleaning supply with same name
    SELECT cs.ProductID INTO existingProductID
    FROM cleaningsupplies cs
    WHERE cs.Name = p_Name
    LIMIT 1;

    IF existingProductID IS NULL THEN
        INSERT INTO product (BrandID, Type, Price, ImageID, BranchID, quantity)
        VALUES (p_BrandID, 'supplies', p_Price, p_ImageID, p_BranchID, p_Quantity);
        SET existingProductID = LAST_INSERT_ID();

        INSERT INTO cleaningsupplies (ProductID, Name, Type, BranchID)
        VALUES (existingProductID, p_Name, p_Type, p_BranchID);
    ELSE
        IF NOT EXISTS (
            SELECT 1 FROM product
            WHERE ProductID = existingProductID AND BranchID = p_BranchID
        ) THEN
            INSERT INTO product (ProductID, BrandID, Type, Price, ImageID, BranchID, quantity)
            VALUES (existingProductID, p_BrandID, 'supplies', p_Price, p_ImageID, p_BranchID, p_Quantity);
        END IF;

        IF NOT EXISTS (
            SELECT 1 FROM cleaningsupplies
            WHERE ProductID = existingProductID AND BranchID = p_BranchID
        ) THEN
            INSERT INTO cleaningsupplies (ProductID, Name, Type, BranchID)
            VALUES (existingProductID, p_Name, p_Type, p_BranchID);
        END IF;
    END IF;
END $$

DELIMITER ;

DELIMITER $$

-- Procedures 6,7 Add to cart-- 
DELIMITER $$

CREATE PROCEDURE AddOrderDetails(
    IN p_OrderID INT,
    IN p_CustomerID INT,
    IN p_CurrencyID INT,
    IN p_BranchID INT,
    IN p_ProductID INT,
    IN p_Quantity INT
)
BEGIN
    DECLARE checkOrder INT;
    DECLARE converted_price DECIMAL(10,2);

	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;
    START TRANSACTION;


    SELECT COUNT(*) INTO checkOrder
    FROM orders
    WHERE OrderID = p_OrderID;

    IF checkOrder = 0 THEN
        INSERT INTO orders (
            OrderID, CustomerID, CurrencyID, BranchID,
            DatePurchased, Status, Total
        )
        VALUES (
            p_OrderID, p_CustomerID, p_CurrencyID, p_BranchID,
            NULL, 'Pending', 0
        );
    END IF;


    CALL GetPriceInEachCurrency(p_ProductID, p_CurrencyID, converted_price);


    INSERT INTO orderdetails (OrderID, ProductID, Quantity, Price)
    VALUES (p_OrderID, p_ProductID, p_Quantity, converted_price);

    COMMIT;

END $$

DELIMITER ;


DELIMITER $$

CREATE PROCEDURE AddServiceDetails(
    IN p_OrderID INT,
    IN p_CustomerID INT,
    IN p_CurrencyID INT,
    IN p_BranchID INT,
    IN p_ServiceID INT,
    IN p_isFromStore BOOLEAN
)
BEGIN
    DECLARE checkOrder INT;
    DECLARE converted_price DECIMAL(10,2);


	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;
    
    SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;
    START TRANSACTION;



    -- Check if order exists
    SELECT COUNT(*) INTO checkOrder
    FROM orders
    WHERE OrderID = p_OrderID;

    IF checkOrder = 0 THEN
        INSERT INTO orders (
            OrderID, CustomerID, CurrencyID, BranchID,
            DatePurchased, Status, Total, PaymentMode, DeliveryMethod
        )
        VALUES (
            p_OrderID, p_CustomerID, p_CurrencyID, p_BranchID,
            NULL, 'Pending', 0, NULL, NULL
        );
    END IF;

    
    CALL GetServicePriceInEachCurrency(p_ServiceID, p_CurrencyID, converted_price);

    
    IF p_isFromStore THEN 
        SET converted_price = converted_price * 1.05;
    END IF;

    INSERT INTO servicedetails (OrderID, ServiceID, isFromStore, price)
    VALUES (p_OrderID, p_ServiceID, p_isFromStore, converted_price);

    COMMIT;
END $$
DELIMITER ;

DELIMITER $$

CREATE PROCEDURE UpdatedProductQuantity(
    IN p_OrderID INT,
    IN p_ProductID INT,
    IN p_Quantity INT
)
BEGIN

	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;
    
    SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;
    START TRANSACTION;

    
	
    UPDATE orderdetails
    SET quantity = p_Quantity
    WHERE p_OrderID = OrderID AND p_ProductID = ProductID;

    COMMIT;
END $$
DELIMITER ;



-- Edit/Update Details -- 
-- Procedure 20 updates details on order page 
DELIMITER $$

CREATE PROCEDURE CheckOutPage(
    IN p_OrderID INT,
    IN p_PaymentMode ENUM('Cash','Credit Card','Online'),
    IN p_DeliveryMethod ENUM('Pickup','Delivery')
)
BEGIN
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

   
    SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;
    START TRANSACTION;

    
    SELECT OrderID 
    FROM orders
    WHERE OrderID = p_OrderID
    FOR UPDATE;

    
    IF NOT EXISTS (SELECT 1 FROM orders WHERE OrderID = p_OrderID) THEN
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Order ID does not exist.';
    END IF;

    
    UPDATE orders
    SET PaymentMode = p_PaymentMode,
        DeliveryMethod = p_DeliveryMethod,
        Status = 'Processing',
        DatePurchased = NOW()
    WHERE OrderID = p_OrderID;

    COMMIT;
END $$

DELIMITER ;


-- Procedure 21 updates order status -- 
DELIMITER $$

CREATE PROCEDURE UpdateOrderStatus(
    IN p_OrderID INT,
    IN p_Status ENUM('Pending','Processing','Completed','Cancelled')
)
BEGIN
    DECLARE current_status ENUM('Pending','Processing','Completed','Cancelled');

    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;
    START TRANSACTION;

  
    SELECT Status INTO current_status
    FROM orders
    WHERE OrderID = p_OrderID
    FOR UPDATE;

    
    IF current_status IS NULL THEN
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Order does not exist.';
    END IF;

    
    IF current_status = p_Status THEN
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Status is already the same.';
    END IF;

    UPDATE orders
    SET Status = p_Status
    WHERE OrderID = p_OrderID;

    COMMIT;
END $$

DELIMITER ;



-- Procedure 22 updates currency rates -- 
DELIMITER $$
CREATE PROCEDURE UpdateCurrencyRates(
    IN p_currencyID INT,
    IN newRate DOUBLE(8,3)
)
BEGIN
    UPDATE currency
    SET Currency_Rate = newRate
    WHERE p_currencyID = currencyID;
    
END $$ DELIMITER ;

-- Procedure 23 Restock Inventory Per Branch --
DELIMITER $$
CREATE PROCEDURE RestockInventory(
    IN p_ProductID INT,
    IN p_BranchID INT,
    IN addedquantity INT
)
BEGIN
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;
    START TRANSACTION;
    UPDATE product
    SET quantity = quantity + addedquantity
    WHERE p_ProductID = ProductID AND p_BranchID = BranchID;
    
    COMMIT;
END $$ DELIMITER ;

DELIMITER $$
CREATE PROCEDURE ChangeServiceStatus(
    IN p_ServiceID INT,
    IN p_availability BOOLEAN
)
BEGIN
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;
    START TRANSACTION;
    UPDATE services
    SET Availability = p_availability
    WHERE p_ServiceID = ServiceID;
    
    COMMIT;
END $$ DELIMITER ;

-- Product 24 Change Product Price -- 
DELIMITER $$
CREATE PROCEDURE ChangeProductPrice(
    IN p_ProductID INT,
    IN NewPrice FLOAT
)
BEGIN
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;
    START TRANSACTION;
    UPDATE product
    SET price = NewPrice
    WHERE p_ProductID = ProductID;
    COMMIT;
END $$ DELIMITER ;

-- Procedure 25-29 CRUD User Information -- 
DELIMITER $$

CREATE PROCEDURE AddUser(
    IN p_AddressID INT,
    IN p_FirstName VARCHAR(50),
    IN p_LastName VARCHAR(50),
    IN p_Number VARCHAR(20),
    IN p_Email VARCHAR(100),
    IN p_Pass VARCHAR(255)
)
BEGIN
    DECLARE existingUser INT;

    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    -- Highest safety level
    SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;
    START TRANSACTION;

    -- Check if email exists
    SELECT COUNT(*) INTO existingUser
    FROM users
    WHERE Email = p_Email
    FOR UPDATE;

    IF existingUser > 0 THEN
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Email already registered.';
    END IF;

    INSERT INTO users (AddressID, FirstName, LastName, PhoneNumber, Email, Password)
    VALUES (p_AddressID, p_FirstName, p_LastName, p_Number, p_Email, p_Pass);

    COMMIT;
END $$

DELIMITER ;


DELIMITER $$
CREATE PROCEDURE ChangeUserInformation(
    IN p_UserID INT,
    IN p_FirstName VARCHAR(100),
    IN p_LastName VARCHAR(100),
    IN p_MobileNumber VARCHAR(20),
    IN p_Email VARCHAR(255),
    IN p_City VARCHAR(100),
    IN p_Street VARCHAR(255),
    IN p_ZipCode VARCHAR(20)
)
BEGIN
    DECLARE v_AddressID INT;

    SELECT AddressID INTO v_AddressID
    FROM users
    WHERE UserID = p_UserID;

    UPDATE users
    SET 
        FirstName = p_FirstName,
        LastName = p_LastName,
        MobileNumber = p_MobileNumber,
        Email = p_Email
    WHERE UserID = p_UserID;

    UPDATE address
    SET
        City = p_City,
        Street = p_Street,
        zip_code = p_ZipCode
    WHERE AddressID = v_AddressID;
END 
$$ DELIMITER ;


DELIMITER $$
CREATE PROCEDURE DeleteUser(
    IN p_UserID INT
)
BEGIN
    DELETE FROM users
    WHERE UserID = p_UserID;
    ALTER TABLE users AUTO_INCREMENT = 1;
END $$ DELIMITER ;


DELIMITER $$
CREATE PROCEDURE AddAddress(
    IN p_City varchar(100),
    IN p_Street varchar(255),
    IN p_zip_code varchar(10)
)
BEGIN
    INSERT INTO address(City,Street,zip_code)
    VALUES(p_City,p_Street,p_zip_code);
END $$ DELIMITER ;

DELIMITER $$

CREATE PROCEDURE EditAddress(
	IN p_AddressID INT,
    IN p_City varchar(100),
    IN p_Street varchar(255),
    IN p_zip_code varchar(10)
)
BEGIN
    UPDATE address
    SET City = p_city,
		Street = p_Street,
        zip_code = p_zip_code
	WHERE AddressID = p_AddressID;
END $$ DELIMITER ;


-- Procedure 30 Delete a product -- 
DELIMITER $$
CREATE PROCEDURE DeleteProduct(
    IN p_ProductID INT,
    IN p_BranchID INT
)
BEGIN
    DELETE FROM product
    WHERE ProductID = p_ProductID
      AND BranchID = p_BranchID;
    ALTER TABLE product AUTO_INCREMENT = 1;
END $$ DELIMITER ;



-- Procedure 31 delete an order
DELIMITER $$
CREATE PROCEDURE DeleteOrders()
BEGIN
    DELETE FROM orders
    WHERE (Status = 'Completed' OR Status = 'Cancelled') AND DatePurchased <= NOW() - INTERVAL 1 DAY;
    ALTER TABLE orders AUTO_INCREMENT = 1;
    ALTER TABLE orderdetails AUTO_INCREMENT = 1;
    ALTER TABLE orderdetails AUTO_INCREMENT = 1;
END $$

DELIMITER ;

DELIMITER $$
CREATE PROCEDURE RemoveProductFromCart(
IN p_OrderID INT,
IN p_ProductID INT)
BEGIN
    DELETE FROM orderdetails
    WHERE p_OrderID = OrderID AND p_ProductID = ProductID;
    ALTER TABLE orderdetails AUTO_INCREMENT = 1;
END $$

DELIMITER ;

DELIMITER $$
CREATE PROCEDURE RemoveServicefromCart(
IN p_OrderID INT,
IN p_ServiceID INT)
BEGIN
    DELETE FROM servicedetails
    WHERE p_ServiceID = ServiceID AND p_OrderID = OrderID;
    ALTER TABLE orderdetails AUTO_INCREMENT = 1;
END $$

DELIMITER ;

DELIMITER $$
CREATE PROCEDURE GetUserProfile(IN p_userID INT)
BEGIN
    SELECT 
        u.FirstName,
        u.LastName,
        u.Email,
        u.MobileNumber,
        u.AddressID,
        a.City,
        a.Street,
        a.zip_code
    FROM users u
    LEFT JOIN address a ON u.AddressID = a.AddressID
    WHERE u.UserID = p_userID;
END
$$ DELIMITER ;