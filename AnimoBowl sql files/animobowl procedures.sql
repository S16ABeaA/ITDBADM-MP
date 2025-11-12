USE AnimoBowl;

-- Procedure 1: Get the Price for each currency(Products)-- 

DELIMITER $$
CREATE PROCEDURE GetPriceInEachCurrency(
    IN p_product_id INT,
    IN p_currency_id INT,
    OUT convertedPrice DECIMAL(10,2)
)
BEGIN
    DECLARE base_price DECIMAL(10,2);
    DECLARE rate DECIMAL(10,4);
    SELECT Price INTO base_price
    FROM product
    WHERE ProductID = p_product_id
    LIMIT 1;
    SELECT Currency_Rate INTO rate
    FROM currency
    WHERE CurrencyID = p_currency_id;
    SET convertedPrice = base_price * rate;
END
$$ DELIMITER ;

CALL GetPriceInEachCurrency(1,1,@result);
SELECT @result;


-- Procedure 2:Get the Price for each currency(Services)-- 
DELIMITER $$

CREATE PROCEDURE GetServicePriceInEachCurrency(
    IN s_service_id INT,
    IN p_currency_id INT,
    OUT convertedPrice DECIMAL(10,2)
)
BEGIN
    DECLARE base_price DECIMAL(10,2);
    DECLARE rate DECIMAL(10,4);
    SELECT Price INTO base_price
    FROM services
    WHERE ServiceID = s_service_id;
    SELECT Currency_Rate INTO rate
    FROM currency
    WHERE CurrencyID = p_currency_id;
    SET convertedPrice = base_price * rate;
END
$$ DELIMITER ;


CALL GetServicePriceInEachCurrency(1,2,@result);
SELECT @result;

-- Procedure 3:Get the inventory stock per item -- 
DELIMITER $$

CREATE PROCEDURE GetProductInventory(
    IN p_ProductID INT,
    IN p_BranchID INT
)
BEGIN
    DECLARE v_Type ENUM('supplies','accessories','ball','bag','shoes');

    -- Get product type
    SELECT Type INTO v_Type
    FROM product
    WHERE ProductID = p_ProductID AND BranchID = p_BranchID;

    -- Balls
    IF v_Type = 'ball' THEN
        SELECT b.ProductID, b.Name, b.RG, b.DIFF, b.INTDIFF, b.Weight, b.Quality, b.CoreType, 
               b.CoreName, b.Coverstock, b.CoverstockType, b.BranchID,p.quantity
        FROM bowlingball b
        JOIN product p ON b.productID AND p.productID
        WHERE b.ProductID = p_ProductID AND b.BranchID = p_BranchID
		LIMIT 1;
    -- Shoes
    ELSEIF v_Type = 'shoes' THEN
        SELECT sh.ProductID, sh.Name, sh.Size, sh.Sex, sh.BranchID,p.quantity
        FROM bowlingshoes sh
        JOIN product p ON sh.productID AND p.productID
        WHERE sh.ProductID = p_ProductID AND sh.BranchID = p_BranchI
		LIMIT 1;
    -- Bags
    ELSEIF v_Type = 'bag' THEN
        SELECT bg.ProductID, bg.Name, bg.Color, bg.Size, bg.BranchID,p.quantity
        FROM bowlingbag bg
        JOIN product p ON bg.productID AND p.productID
        WHERE bg.ProductID = p_ProductID AND bg.BranchID = p_BranchID
		LIMIT 1;
    -- Accessories
    ELSEIF v_Type = 'accessories' THEN
        SELECT a.ProductID, a.Name, a.Type, a.Handedness, a.BranchID,p.quantity
        FROM bowlingaccessories a
		JOIN product p ON a.productID AND p.productID
        WHERE a.ProductID = p_ProductID AND a.BranchID = p_BranchID
		LIMIT 1;
    -- Supplies
    ELSEIF v_Type = 'supplies' THEN
        SELECT cs.ProductID, cs.Name, cs.Type, cs.Quantity, cs.BranchID,p.quantity
        FROM cleaningsupplies cs
        JOIN product p ON cs.productID AND p.productID
        WHERE cs.ProductID = p_ProductID AND cs.BranchID = p_BranchID
        LIMIT 1;
    END IF;

END $$


DELIMITER ;


CALL GetProductInventory(13,2);

-- Procedure 4 Service Availability -- 
DELIMITER $$
CREATE PROCEDURE GetServiceAvailability(
    INOUT p_serviceid INT
)
BEGIN
    DECLARE avail TINYINT(1);

    SELECT Availability INTO avail
    FROM services
    WHERE ServiceID = p_serviceid
    LIMIT 1;

    SET p_serviceid = avail;
END $$

DELIMITER ;

-- Procedure 5 Get All Products per category -- 
DELIMITER $$

CREATE PROCEDURE GetProductsByCategory(IN p_category VARCHAR(50),IN branch_id INT)
BEGIN
    IF p_category = 'bowlingball' THEN
        SELECT Name FROM bowlingball WHERE branch_id = branchid;
    ELSEIF p_category = 'bowlingshoes' THEN
        SELECT Name FROM bowlingshoes WHERE branch_id = branchid;
    ELSEIF p_category = 'bowlingbag' THEN
        SELECT Name FROM bowlingbag WHERE branch_id = branchid;
    ELSEIF p_category = 'accessories' THEN
        SELECT Name FROM bowlingaccessories WHERE branch_id = branchid;
    ELSEIF p_category = 'supplies' THEN
        SELECT Name FROM cleaningsupplies WHERE branch_id = branchid;
    ELSE
        SELECT 'Invalid category name. Choose from: bowlingball, bowlingshoes, bowlingbag, accessories, supplies.' AS Error;
    END IF;
END $$


DELIMITER ;
CALL GetProductsByCategory('bowlingball',1);
CALL GetProductsByCategory('supplies',2);

-- Procedure 6 Get product by name(search function)-- 
DELIMITER $$

CREATE PROCEDURE GetProductByName(
    IN p_search VARCHAR(100),
    IN p_branchID INT
)
BEGIN
    -- Search across all categories (ball, shoes, bag, accessories, supplies)
    SELECT 'bowlingball' AS Category, Name
    FROM bowlingball
    WHERE Name LIKE CONCAT('%', p_search, '%') AND BranchID = p_branchID

    UNION ALL

    SELECT 'bowlingshoes' AS Category, Name
    FROM bowlingshoes
    WHERE Name LIKE CONCAT('%', p_search, '%') AND BranchID = p_branchID

    UNION ALL

    SELECT 'bowlingbag' AS Category, Name
    FROM bowlingbag
    WHERE Name LIKE CONCAT('%', p_search, '%') AND BranchID = p_branchID

    UNION ALL

    SELECT 'accessories' AS Category, Name
    FROM bowlingaccessories
    WHERE Name LIKE CONCAT('%', p_search, '%') AND BranchID = p_branchID

    UNION ALL

    SELECT 'supplies' AS Category, Name
    FROM cleaningsupplies
    WHERE Name LIKE CONCAT('%', p_search, '%') AND BranchID = p_branchID;
END $$

DELIMITER ;

-- Procedure 7 Get all orders made by a CUSTOMER-- 
DELIMITER $$

CREATE PROCEDURE GetCustomerOrderHistory(IN customerid INT)
BEGIN
    SELECT *
    FROM orders o 
    WHERE CustomerID = customerid;
END $$
DELIMITER ;
CALL GetCustomerOrderHistory(1);

-- Procedure 8 Get Shoes per Gender and Size -- 
DELIMITER $$

CREATE PROCEDURE GetShoesPerParameter(
    IN p_sex VARCHAR(1),  
    IN p_size INT,
    IN branch INT
)
BEGIN
    SELECT bs.Name, bs.size, bs.sex, p.Price
    FROM product p
    JOIN bowlingshoes bs ON p.ProductID = bs.ProductID
    WHERE (p_sex IS NULL OR bs.sex = p_sex)
      AND (p_size IS NULL OR bs.size = p_size)
      AND p.BranchID = branch;
END $$

DELIMITER ;


CALL GetShoesPerParameter('F',7,1);

-- Procedure 9 Get Cleaning Supplies per Type -- 
DELIMITER $$

CREATE PROCEDURE GetSuppliesperType(IN typeof VARCHAR(10))
BEGIN
    SELECT cs.Name,p.price
    FROM product p
    JOIN cleaningsupplies cs ON cs.ProductID = p.ProductID
    WHERE typeof = cs.type;
END 
$$ DELIMITER ;


CALL GetSuppliesperType('pads');

-- Procedure 10 Get Accessories per Type -- 
DELIMITER $$

CREATE PROCEDURE GetAccessoriesperType(IN typeof VARCHAR(20))
BEGIN
    SELECT ba.Name,p.price,ba.Handedness
    FROM product p
    JOIN bowlingaccessories ba ON ba.ProductID = p.ProductID
    WHERE typeof = ba.type;
END 
$$ DELIMITER ;

-- Procedure 11 Bowling Bag search per type and sizes -- 
DELIMITER $$

CREATE PROCEDURE GetBagPerParameter(
    IN Typeof VARCHAR(10),
    IN sizes INT
)
BEGIN
    SELECT bb.Name, bb.type,bb.color,bb.size,p.price
    FROM product p
    JOIN bowlingbag bb ON p.ProductID = bb.ProductID
    WHERE (Typeof IS NULL OR Typeof = bb.type)
      AND (sizes IS NULL OR sizes = bb.size);
END $$

DELIMITER ;


CALL GetBagPerParameter('Tote',NULL);

-- Procedure 12 Bowling Ball Search -- 
DELIMITER $$

CREATE PROCEDURE GetBallPerParameter(
    IN weights INT,
    IN qual VARCHAR(10),
    IN types VARCHAR(20),
    IN core VARCHAR(20)
)
BEGIN
    SELECT b.Name AS 'Brand',bb.Name, bb.Quality,bb.weight,p.price
    FROM brand b
    JOIN product p ON p.BrandID = b.BrandID
    JOIN bowlingball bb ON p.ProductID = bb.ProductID
    WHERE (weight IS NULL OR weights = bb.weight)
      AND (qual IS NULL OR qual = bb.quality)
      AND (types IS NULL OR types = bb.Type)
      AND (core IS NULL OR bb.CoreType = core);
END $$

DELIMITER ;


CALL GetBallPerParameter('15',NULL,'Plastic',NULL);



-- ADD PROCEDURES -- 

-- Procedure 13 Add a Bowling Ball --
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
-- Procedure 14 Add a Bowling Accessory --
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

-- Procedure 15 Add a Bowling Bag --
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

-- Procedure 16 Add a Bowling Shoe --
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

-- Procedure 17 Add a Cleaning Supply --
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

-- Procedures 18,19 Add to cart-- 
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



    -- Check if order already exists
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

    CALL GetPriceInEachCurrency(p_ProductID, p_CurrencyID, converted_price);

    INSERT INTO orderdetails (OrderID, ProductID, Quantity, Price)
    VALUES (p_OrderID, p_ProductID, p_Quantity, converted_price);
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



    -- Check if order already exists
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
	SELECT @temp_price INTO converted_price;

    IF p_isFromStore = TRUE THEN 
        SET converted_price = converted_price * 1.05;
    END IF;
    INSERT INTO servicedetails (OrderID, ServiceID, isFromStore, Price)
    VALUES (p_OrderID, p_ServiceID, p_isFromStore, converted_price);
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
    -- Make sure the order exists
    IF EXISTS (SELECT 1 FROM orders WHERE OrderID = p_OrderID) THEN

        UPDATE orders
        SET PaymentMode = p_PaymentMode,
            DeliveryMethod = p_DeliveryMethod,
            Status = 'Processing',
            DatePurchased = NOW()
        WHERE OrderID = p_OrderID;

    ELSE
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Order ID does not exist.';
    END IF;
END $$

DELIMITER ;

-- Procedure 21 updates order status -- 
DELIMITER $$
CREATE PROCEDURE UpdateOrderStatus(
    IN p_OrderID INT,
    IN p_Status enum('Pending','Processing','Completed','Cancelled')
)
BEGIN
    DECLARE current_status ENUM('Pending','Processing','Completed','Cancelled');

    START TRANSACTION;

    SELECT Status INTO current_status
    FROM orders
    WHERE OrderID = p_OrderID
    FOR UPDATE;

    IF current_status = p_NewStatus THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Status is already the same';
    END IF;

    UPDATE orders
    SET Status = p_NewStatus
    WHERE OrderID = p_OrderID;

    COMMIT;
END $$ DELIMITER ;



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
    UPDATE product
    SET quantity = quantity + addedquantity
    WHERE p_ProductID = ProductID AND p_BranchID = BranchID;
    
END $$ DELIMITER ;

-- Product 24 Change Product Price -- 
DELIMITER $$
CREATE PROCEDURE ChangeProductPrice(
    IN p_ProductID INT,
    IN NewPrice FLOAT
)
BEGIN
    UPDATE product
    SET price = NewPrice
    WHERE p_ProductID = ProductID;
    
END $$ DELIMITER ;

-- Procedure 25 Change User Information -- 
DELIMITER $$

CREATE PROCEDURE ChangeUserInformation(
    IN p_UserID INT,
    IN p_FirstName VARCHAR(100),
    IN p_LastName VARCHAR(100),
    IN p_MobileNumber VARCHAR(20),
    IN p_Email VARCHAR(255),
    IN p_Password VARCHAR(255)
)
BEGIN
    UPDATE users
    SET 
        FirstName =  p_FirstName,
        LastName =  p_LastName,
        MobileNumber =  p_MobileNumber,
        Email =  p_Email,
        Password =  p_Password
    WHERE UserID = p_UserID;
END $$

DELIMITER ;
-- Procedure 26 Delete a product -- 
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

-- Procedure 27 Delete a user 
DELIMITER $$
CREATE PROCEDURE DeleteUser(
    IN p_UserID INT
)
BEGIN
    DELETE FROM users
    WHERE UserID = p_UserID;
    ALTER TABLE users AUTO_INCREMENT = 1;
END $$ DELIMITER ;

-- Procedure 28 Delete an order(Completed or cancelled) Done after adding to the logs
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