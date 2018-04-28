

-- evento table token

-- primero iniciar los eventos
SET GLOBAL event_scheduler = ON;

-- creaar eventyo que elimine el token

CREATE EVENT deletetoken
   ON SCHEDULE EVERY 30 MINUTE
     STARTS CURRENT_TIMESTAMP
   DO
      DELETE FROM token WHERE token_created <= DATE_SUB(CURTIME(), INTERVAL 60 MINUTE);