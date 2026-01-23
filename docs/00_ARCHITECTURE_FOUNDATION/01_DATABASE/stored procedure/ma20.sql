DELIMITER $$
CREATE DEFINER=`thuy_admin_giaphuc`@`localhost` PROCEDURE `ma20`(IN desired_coin VARCHAR(50))
BEGIN
	DECLARE avg_close Double;
    
    SELECT AVG(close_price)
    INTO avg_close
    FROM (
        SELECT close_price
        FROM btcdatadb
        WHERE coin_id = desired_coin
        ORDER BY open_time DESC
        LIMIT 50
    ) AS sub;
    
    SELECT avg_close AS ma20;
END$$
DELIMITER ;