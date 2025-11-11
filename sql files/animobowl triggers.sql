-- Trigger 1 Users cannot purchase any product out of stock(i.e. User tries to checkout and buy an out of stock ball-- 
DELIMITER $$

CREATE TRIGGER InventoryAdjustmentManagement
BEFORE INSERT ON orderdetails
FOR EACH ROW
BEGIN
    DECLARE current_stock INT;


    SELECT Quantity INTO current_stock
    FROM inventory
    WHERE ProductID = NEW.ProductID AND BranchID = NEW.BranchID
    LIMIT 1;

    -- Product Not found -- 
   IF current_stock IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Product not found';
    END IF;
    -- If no matching inventory found, block the transaction
    IF current_stock = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Product is out of stock';
    END IF;

    -- If insufficient stock, block the transaction
    IF current_stock < NEW.Quantity THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Insufficient stock for this product in selected branch';
    END IF;

END $$

DELIMITER ;
-- Trigger 2 Updates inventory at checkout-- 
DELIMITER $$

CREATE TRIGGER UpdateInventory
AFTER INSERT ON orderdetails
FOR EACH ROW
BEGIN
    UPDATE inventory
    SET Quantity = Quantity - NEW.Quantity
    WHERE ProductID = NEW.ProductID AND BranchID = NEW.BranchID;
END $$

DELIMITER ;

-- Trigger 3 Users cannot avail of any unavailable service-- 
DELIMITER $$

CREATE TRIGGER ServiceAvailabilityManagement
BEFORE INSERT ON servicedetails
FOR EACH ROW
BEGIN
    DECLARE is_available BOOLEAN;

    SELECT Availability 
    INTO is_available
    FROM services
    WHERE ServiceID = NEW.ServiceID;


    IF is_available = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Service is currently unavailable!';
    END IF;
END $$

DELIMITER ;

-- Trigger 4,5 Update total after adding a product/service to cart -- 
DELIMITER $$

CREATE TRIGGER UpdateTotalAfterService
AFTER INSERT ON servicedetails
FOR EACH ROW
BEGIN
    DECLARE new_total DECIMAL(10,2);

    SELECT SUM(Price) INTO new_total
    FROM servicedetails
    WHERE OrderID = NEW.OrderID;

 
    SELECT IFNULL(SUM(Price * Quantity), 0) INTO @Total
    FROM orderdetails
    WHERE OrderID = NEW.OrderID;

    SET new_total = new_total + @Total;

    UPDATE orders
    SET Total = new_total
    WHERE OrderID = NEW.OrderID;
END $$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER UpdateTotalAfterOrder
AFTER INSERT ON orderdetails
FOR EACH ROW
BEGIN
    DECLARE new_total DECIMAL(10,2);

    SELECT SUM(Price * Quantity) INTO new_total
    FROM orderdetails
    WHERE OrderID = NEW.OrderID;

    SELECT IFNULL(SUM(Price),0) INTO @servicetotal
    FROM servicedetails
    WHERE OrderID = NEW.OrderID;

    SET new_total = new_total + @servicetotal;

    UPDATE orders
    SET Total = new_total
    WHERE OrderID = NEW.OrderID;
END $$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER UpdateTotalAfterOrder
AFTER INSERT ON orderdetails
FOR EACH ROW
BEGIN
    DECLARE new_total DECIMAL(10,2);

    SELECT SUM(Price * Quantity) INTO new_total
    FROM orderdetails
    WHERE OrderID = NEW.OrderID;

    SELECT IFNULL(SUM(Price),0) INTO @servicetotal
    FROM servicedetails
    WHERE OrderID = NEW.OrderID;

    SET new_total = new_total + @servicetotal;

    UPDATE orders
    SET Total = new_total
    WHERE OrderID = NEW.OrderID;
END $$

DELIMITER ;
-- Trigger 6,7,8 All orders are logged and updated from checkout to completion - 

CREATE TABLE order_log (
    LogID INT AUTO_INCREMENT PRIMARY KEY,
    OrderID INT NOT NULL,
    OldStatus ENUM('Pending','Processing','Completed','Cancelled'),
    NewStatus ENUM('Pending','Processing','Completed','Cancelled'),
    Total FLOAT,
    PaymentMode ENUM('Cash','Credit Card','Online'),
    DeliveryMethod ENUM('Pickup','Delivery'),
    ChangeDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    ActionType ENUM('Created','Status Updated') NOT NULL,
    FOREIGN KEY (OrderID) REFERENCES orders(OrderID)
);

DELIMITER $$

CREATE TRIGGER OrderInsertLog
AFTER INSERT ON orders
FOR EACH ROW
BEGIN
    INSERT INTO order_log (
        OrderID,
        OldStatus,
        NewStatus,
        Total,
        PaymentMode,
        DeliveryMethod,
        ActionType
    )
    VALUES (
        NEW.OrderID,
        NULL,
        NEW.Status,
        NEW.Total,
        NEW.PaymentMode,
        NEW.DeliveryMethod,
        'Created'
    );
END $$

DELIMITER $$

CREATE TRIGGER OrderUpdateLog
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF OLD.Status <> NEW.Status THEN
        INSERT INTO order_log (
            OrderID,
            OldStatus,
            NewStatus,
            Total,
            PaymentMode,
            DeliveryMethod,
            ActionType
        )
        VALUES (
            NEW.OrderID,
            OLD.Status,
            NEW.Status,
            NEW.Total,
            NEW.PaymentMode,
            NEW.DeliveryMethod,
            'Status Updated'
        );
    END IF;
END $$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER UpdateOrderStatus
BEFORE UPDATE ON orders
FOR EACH ROW
BEGIN
    IF NEW.Status = 'Completed' AND OLD.Status <> 'Completed' THEN
        SET NEW.DateCompleted = NOW();
    END IF;
     IF NEW.Status = 'Cancelled' AND OLD.Status <> 'Cancelled' THEN

        UPDATE inventory i
        JOIN orderdetails od
          ON i.ProductID = od.ProductID AND i.BranchID = od.BranchID
        SET i.Quantity = i.Quantity + od.Quantity
        WHERE od.OrderID = OLD.OrderID;

        DELETE FROM orderdetails WHERE OrderID = OLD.OrderID;
        DELETE FROM servicedetails WHERE OrderID = OLD.OrderID;

        DELETE FROM orders WHERE OrderID = OLD.OrderID;

        ALTER TABLE orders AUTO_INCREMENT = 1;
        ALTER TABLE orderdetails AUTO_INCREMENT = 1;
        ALTER TABLE servicedetails AUTO_INCREMENT = 1;

    END IF;
END $$

DELIMITER ;

-- Trigger 9-11 All inventory restocks/new products added are logged(ex. Stock is added to a ball, a new product is introduced) --
CREATE TABLE inventory_log (
    LogID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(100),
    BranchID INT NOT NULL,
    OldQuantity INT,
    NewQuantity INT,
    Price FLOAT,
    ChangeType ENUM('New Product','Restock','Deleted Product') NOT NULL,
    ChangedAt DATETIME DEFAULT CURRENT_TIMESTAMP
);

DELIMITER $$

CREATE TRIGGER InventoryInsertLog
AFTER INSERT ON product
FOR EACH ROW
BEGIN
    DECLARE prod_name VARCHAR(100);

    SELECT COALESCE(bb.Name, bs.Name, bg.Name, ba.Name, cs.Name)
    INTO prod_name
    FROM product p
    LEFT JOIN bowlingball bb ON p.ProductID = bb.ProductID
    LEFT JOIN bowlingshoes bs ON p.ProductID = bs.ProductID
    LEFT JOIN bowlingbag bg ON p.ProductID = bg.ProductID
    LEFT JOIN bowlingaccessories ba ON p.ProductID = ba.ProductID
    LEFT JOIN cleaningsupplies cs ON p.ProductID = cs.ProductID
    LIMIT 1;

    INSERT INTO inventory_log (
        Name,       
        BranchID,
        OldQuantity,
        NewQuantity,
		Price,
        ChangeType
    )
    VALUES (
        prod_name,
        NEW.BranchID,
        NULL,
        NEW.Quantity,
        NEW.Price,
        'New Product'
    );
END $$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER InventoryUpdateLog
BEFORE UPDATE ON product
FOR EACH ROW
BEGIN
    DECLARE prod_name VARCHAR(100);

    IF NEW.Quantity > OLD.Quantity THEN

        SELECT 
		COALESCE(bb.Name, bs.Name, bag.Name, acc.Name, sup.Name)
        INTO prod_name
         FROM product p
		LEFT JOIN bowlingball bb ON p.ProductID = bb.ProductID
		LEFT JOIN bowlingshoes bs ON p.ProductID = bs.ProductID
		LEFT JOIN bowlingbag bg ON p.ProductID = bg.ProductID
		LEFT JOIN bowlingaccessories ba ON p.ProductID = ba.ProductID
		LEFT JOIN cleaningsupplies cs ON p.ProductID = cs.ProductID
		LIMIT 1;

   
        INSERT INTO inventory_log (
            Name,
            BranchID,
            OldQuantity,
            NewQuantity,
             Price,
            ChangeType
        )
        VALUES (
            prod_name,
            NEW.BranchID,
            OLD.Quantity,
            NEW.Quantity,
            NEW.Price,
            'Restock'
        );
    END IF;
END $$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER InventoryDeleteLog
AFTER DELETE ON product
FOR EACH ROW
BEGIN
        DECLARE prod_name VARCHAR(100);
        SELECT 
		COALESCE(bb.Name, bs.Name, bag.Name, acc.Name, sup.Name)
        INTO prod_name
         FROM product p
		LEFT JOIN bowlingball bb ON p.ProductID = bb.ProductID
		LEFT JOIN bowlingshoes bs ON p.ProductID = bs.ProductID
		LEFT JOIN bowlingbag bg ON p.ProductID = bg.ProductID
		LEFT JOIN bowlingaccessories ba ON p.ProductID = ba.ProductID
		LEFT JOIN cleaningsupplies cs ON p.ProductID = cs.ProductID
		LIMIT 1;

        INSERT INTO inventory_log (
            Name,
            BranchID,
            OldQuantity,
            NewQuantity,
             Price,
            ChangeType
        )
        VALUES (
            prod_name,
            NEW.BranchID,
			NULL,
            NULL,
            NEW.Price,
            'Restock'
        );
END $$

DELIMITER ;
-- Trigger 12 User delete-- 
CREATE TABLE user_deletion_log (
    LogID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    Username VARCHAR(100),
    Role ENUM('Staff','User'),
    DeletedAt DATETIME DEFAULT CURRENT_TIMESTAMP
);
    
DELIMITER $$
CREATE TRIGGER DeleteUsers
AFTER DELETE ON users
FOR EACH ROW
BEGIN
    INSERT INTO user_deletion_log (UserID, Username,Role)
    VALUES (OLD.UserID, OLD.Username,Role);
END $$
DELIMITER ;

-- Trigger 13 Log Currency Changes -- 
CREATE TABLE currency_changes_log (
    LogID INT AUTO_INCREMENT PRIMARY KEY,
    currency VARCHAR(3),
    previous_rate double(8,3),
    new_rate double(8,3)
);

CREATE TRIGGER CurrencyChanges
AFTER UPDATE ON currency
FOR EACH ROW
BEGIN
    INSERT INTO currency_changes_log(currency,previous_rate,new_rate)
    VALUES (OLD.Currency_Name,OLD.Currency_Rate,NEW.Currency_rate)
END $$
DELIMITER ;

-- Trigger 14 User Information Changes
DELIMITER $$

CREATE TRIGGER UserChangeChecker
BEFORE UPDATE ON users
FOR EACH ROW
BEGIN
    
    IF 
        OLD.FirstName = NEW.FirstName AND
        OLD.LastName = NEW.LastName AND
        OLD.MobileNumber = NEW.MobileNumber AND
        OLD.Email = NEW.Email AND
        OLD.Password = NEW.Password
    THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'No fields were changed. Update aborted.';
    END IF;
END $$

DELIMITER ;

