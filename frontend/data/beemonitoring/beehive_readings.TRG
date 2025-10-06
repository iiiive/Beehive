TYPE=TRIGGERS
triggers='CREATE DEFINER=`root`@`localhost` TRIGGER set_beehive_status_before_insert\nBEFORE INSERT ON beehive_readings\nFOR EACH ROW\nBEGIN\n    IF (NEW.temperature BETWEEN 32 AND 36)\n       AND (NEW.humidity BETWEEN 40 AND 55) THEN\n        SET NEW.status = \'Good\';\n    ELSE\n        SET NEW.status = \'Bad\';\n    END IF;\nEND' 'CREATE DEFINER=`root`@`localhost` TRIGGER set_beehive_status_before_update\nBEFORE UPDATE ON beehive_readings\nFOR EACH ROW\nBEGIN\n    IF (NEW.temperature BETWEEN 32 AND 36)\n       AND (NEW.humidity BETWEEN 40 AND 55) THEN\n        SET NEW.status = \'Good\';\n    ELSE\n        SET NEW.status = \'Bad\';\n    END IF;\nEND'
sql_modes=1098907648 1098907648
definers='root@localhost' 'root@localhost'
client_cs_names='utf8mb4' 'utf8mb4'
connection_cl_names='utf8mb4_general_ci' 'utf8mb4_general_ci'
db_cl_names='utf8mb4_general_ci' 'utf8mb4_general_ci'
created=1759734308800609 1759734308811746
