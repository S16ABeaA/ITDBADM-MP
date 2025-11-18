-- Trigger 1 Users cannot purchase any product out of stock(i.e. User tries to checkout and buy an out of stock ball-- 

USE AnimoBowl;
DELIMITER $$

CREATE TRIGGER InventoryAdjustmentManagement
BEFORE INSERT ON orderdetails
FOR EACH ROW
BEGIN
    DECLARE current_stock INT;
    DECLARE order_branch INT;



    SELECT BranchID INTO order_branch
    FROM orders
    WHERE OrderID = NEW.OrderID
    LIMIT 1;


    SELECT Quantity INTO current_stock
    FROM product
    WHERE ProductID = NEW.ProductID AND BranchID = order_branch
    LIMIT 1;

    IF current_stock IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Product not found in branch';
    END IF;

    IF current_stock = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Product is out of stock';
    END IF;

    IF current_stock < NEW.Quantity THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Insufficient stock for this product in selected branch';
    END IF;
	COMMIT;
END $$

DELIMITER ;

-- Trigger 2 Updates inventory at checkout-- 
DELIMITER $$

CREATE TRIGGER UpdateInventory
AFTER INSERT ON orderdetails
FOR EACH ROW
BEGIN
    DECLARE current_stock INT;
    DECLARE order_branch INT;

   
    SELECT BranchID INTO order_branch
    FROM orders
    WHERE OrderID = NEW.OrderID
    LIMIT 1;

  
    SELECT Quantity INTO current_stock
    FROM product
    WHERE ProductID = NEW.ProductID AND BranchID = order_branch
    LIMIT 1;

    UPDATE product
    SET Quantity = Quantity - NEW.Quantity
    WHERE ProductID = NEW.ProductID AND BranchID = order_branch;
END $$

DELIMITER ;


-- Trigger 3 Editing cart details(Concurrency is practiced)

DELIMITER $$

CREATE TRIGGER ChangeCartDetails
AFTER UPDATE ON orderdetails
FOR EACH ROW
BEGIN
    DECLARE order_branch INT;
    DECLARE new_product_total DECIMAL(10,2);
    DECLARE new_service_total DECIMAL(10,2);
    DECLARE new_total DECIMAL(10,2);

    -- Get branch
    SELECT BranchID INTO order_branch
    FROM orders
    WHERE OrderID = NEW.OrderID;

    -- Inventory adjustments
    IF NEW.Quantity > OLD.Quantity THEN
        UPDATE product
        SET Quantity = Quantity - (NEW.Quantity - OLD.Quantity)
        WHERE ProductID = NEW.ProductID AND BranchID = order_branch;
    ELSEIF NEW.Quantity < OLD.Quantity THEN
        UPDATE product
        SET Quantity = Quantity + (OLD.Quantity - NEW.Quantity)
        WHERE ProductID = NEW.ProductID AND BranchID = order_branch;
    ELSE
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'No inventory changes';
    END IF;

    -- Recalculate totals (force 0 when null)
    SELECT IFNULL(SUM(Price * Quantity), 0)
    INTO new_product_total
    FROM orderdetails
    WHERE OrderID = NEW.OrderID;

    SELECT IFNULL(SUM(Price), 0)
    INTO new_service_total
    FROM servicedetails
    WHERE OrderID = NEW.OrderID;

    SET new_total = new_product_total + new_service_total;

    -- Update order total
    UPDATE orders
    SET Total = new_total
    WHERE OrderID = NEW.OrderID;

END $$

DELIMITER ;





-- Trigger 4 No Negative Inventory
DELIMITER $$

CREATE TRIGGER NoNegativeInventory
BEFORE UPDATE ON product
FOR EACH ROW
BEGIN
		IF NEW.quantity < 0 
        THEN SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Inventory Cannot be negative ';
        END IF;
END $$ DELIMITER ;

DELIMITER $$

-- Trigger 5 Users cannot avail of any unavailable service-- 
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

-- Trigger 6,7 Update total after adding a product/service to cart -- 
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


-- Trigger 8,9,10 All orders are logged and updated from checkout to completion - 

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
END $$ DELIMITER ;

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
            'Status Updated');
    END IF;
END $$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER UpdateOrderStatus
BEFORE UPDATE ON orders
FOR EACH ROW
BEGIN
    -- Automatically set completion date when order is marked completed
    IF NEW.Status = 'Completed' AND OLD.Status <> 'Completed' THEN
        SET NEW.DateCompleted = NOW();
    END IF;

    -- Handle cancellation logic
    IF NEW.Status = 'Cancelled' AND OLD.Status <> 'Cancelled' THEN
        -- Return stock to inventory
        UPDATE product p
        JOIN orderdetails od
            ON p.ProductID = od.ProductID AND p.BranchID = od.BranchID
        SET p.Quantity = p.Quantity + od.Quantity
        WHERE od.OrderID = OLD.OrderID;

        -- Delete related details (not the order itself)
        DELETE FROM orderdetails WHERE OrderID = OLD.OrderID;
        DELETE FROM servicedetails WHERE OrderID = OLD.OrderID;
    END IF;
END $$

DELIMITER ;


-- Trigger 11-13 All inventory restocks/new products added are logged(ex. Stock is added to a ball, a new product is introduced) --
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
BEFORE INSERT ON product
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
		COALESCE(bb.Name, bs.Name, bg.Name, ba.Name, cs.Name)
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
		COALESCE(bb.Name, bs.Name, bg.Name, ba.Name, cs.Name)
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
            OLD.BranchID,
			NULL,
            NULL,
            OLD.Price,
            'Deleted Product'
        );
END $$

DELIMITER ;


-- Trigger 14 User delete-- 
CREATE TABLE user_logs (
    LogID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    Username VARCHAR(100),
    Role ENUM('Staff','User'),
    Status ENUM('Created','Deleted'),
    Time DATETIME DEFAULT CURRENT_TIMESTAMP
);
    

DELIMITER $$
CREATE TRIGGER DeleteUsers
AFTER DELETE ON users
FOR EACH ROW
BEGIN
    INSERT INTO user_logs(UserID, Username,Role,Status)
    VALUES (OLD.UserID, CONCAT(OLD.FirstName,' ',OLD.LastName),Role,'Deleted');
END $$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER AddUsers
AFTER INSERT ON users
FOR EACH ROW
BEGIN
    INSERT INTO user_logs(UserID, Username,Role,Status)
    VALUES (NEW.UserID, CONCAT(NEW.FirstName,' ',NEW.LastName),Role,'Created');
END $$
DELIMITER ;

-- Trigger 15 Log Currency Changes -- 
CREATE TABLE currency_changes_log (
    LogID INT AUTO_INCREMENT PRIMARY KEY,
    currency VARCHAR(3),
    previous_rate double(8,3),
    new_rate double(8,3),
    date_time DATETIME DEFAULT CURRENT_TIMESTAMP
);

DELIMITER $$
CREATE TRIGGER CurrencyChanges
AFTER UPDATE ON currency
FOR EACH ROW
BEGIN
    INSERT INTO currency_changes_log(currency,previous_rate,new_rate)
    VALUES (OLD.Currency_Name,OLD.Currency_Rate,NEW.Currency_rate);
END $$
DELIMITER ;


-- trigger 17,18 removal of item from cart -- 
DELIMITER $$

CREATE TRIGGER UpdateTotalAfterServiceRemoval
AFTER DELETE ON servicedetails
FOR EACH ROW
BEGIN
    DECLARE service_total DECIMAL(10,2);
    DECLARE product_total DECIMAL(10,2);

    
    SELECT IFNULL(SUM(Price), 0)
    INTO service_total
    FROM servicedetails
    WHERE OrderID = OLD.OrderID;


    SELECT IFNULL(SUM(Price * Quantity), 0)
    INTO product_total
    FROM orderdetails
    WHERE OrderID = OLD.OrderID;

   
    UPDATE orders
    SET Total = service_total + product_total
    WHERE OrderID = OLD.OrderID;
END$$

DELIMITER ;




DELIMITER $$

CREATE TRIGGER UpdateTotalAfterProductRemoval
AFTER DELETE ON orderdetails
FOR EACH ROW
BEGIN
    DECLARE new_product_total DECIMAL(10,2);
    DECLARE new_service_total DECIMAL(10,2);
    DECLARE order_branch INT;

   
    SELECT IFNULL(SUM(Price * Quantity), 0)
    INTO new_product_total
    FROM orderdetails
    WHERE OrderID = OLD.OrderID;

    SELECT IFNULL(SUM(Price), 0)
    INTO new_service_total
    FROM servicedetails
    WHERE OrderID = OLD.OrderID;

    
    SELECT BranchID
    INTO order_branch
    FROM orders
    WHERE OrderID = OLD.OrderID
    LIMIT 1;

    
    UPDATE product
    SET Quantity = Quantity + OLD.Quantity
    WHERE ProductID = OLD.ProductID
      AND BranchID = order_branch;

    
    UPDATE orders
    SET Total = new_product_total + new_service_total
    WHERE OrderID = OLD.OrderID;
END$$

DELIMITER ;
