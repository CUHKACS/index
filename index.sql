CREATE DATABASE  IF NOT EXISTS `index` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `index`;
-- MySQL dump 10.13  Distrib 5.7.17, for Linux (x86_64)
--
-- Host: localhost    Database: index
-- ------------------------------------------------------
-- Server version	5.5.5-10.1.16-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `borrow`
--

DROP TABLE IF EXISTS `borrow`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `borrow` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` mediumint(8) unsigned NOT NULL,
  `sid` int(10) unsigned NOT NULL,
  `date_borr` date NOT NULL COMMENT 'Borrowing date',
  `date_due` date NOT NULL COMMENT 'Expected return date',
  `date_return` date NOT NULL COMMENT 'Return date',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1743 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `borrow_item_frequency_view`
--

DROP TABLE IF EXISTS `borrow_item_frequency_view`;
/*!50001 DROP VIEW IF EXISTS `borrow_item_frequency_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `borrow_item_frequency_view` AS SELECT 
 1 AS `item_id`,
 1 AS `frequency`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `borrow_series_frequency_view`
--

DROP TABLE IF EXISTS `borrow_series_frequency_view`;
/*!50001 DROP VIEW IF EXISTS `borrow_series_frequency_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `borrow_series_frequency_view` AS SELECT 
 1 AS `series_id`,
 1 AS `frequency`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `borrow_view`
--

DROP TABLE IF EXISTS `borrow_view`;
/*!50001 DROP VIEW IF EXISTS `borrow_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `borrow_view` AS SELECT 
 1 AS `id`,
 1 AS `sid`,
 1 AS `date_borr`,
 1 AS `date_exp`,
 1 AS `date_return`,
 1 AS `item_id`,
 1 AS `series_id`,
 1 AS `volume`,
 1 AS `entry_date`,
 1 AS `barcode`,
 1 AS `language`,
 1 AS `status`,
 1 AS `title`,
 1 AS `author`,
 1 AS `location`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `hot_series_view`
--

DROP TABLE IF EXISTS `hot_series_view`;
/*!50001 DROP VIEW IF EXISTS `hot_series_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `hot_series_view` AS SELECT 
 1 AS `series_id`,
 1 AS `title`,
 1 AS `author`,
 1 AS `location`,
 1 AS `frequency`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `item`
--

DROP TABLE IF EXISTS `item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item` (
  `item_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `series_id` mediumint(8) unsigned NOT NULL,
  `volume` int(5) unsigned NOT NULL,
  `entry_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `barcode` varchar(30) DEFAULT NULL,
  `language` enum('English','中文','日本語','한국어','Other') NOT NULL,
  `status` enum('on-loan','on-shelf','hold','lost','archived','deleted') NOT NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12599 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `item_view`
--

DROP TABLE IF EXISTS `item_view`;
/*!50001 DROP VIEW IF EXISTS `item_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `item_view` AS SELECT 
 1 AS `item_id`,
 1 AS `series_id`,
 1 AS `title`,
 1 AS `author`,
 1 AS `volume`,
 1 AS `language`,
 1 AS `location`,
 1 AS `status`,
 1 AS `barcode`,
 1 AS `entry_date`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `series`
--

DROP TABLE IF EXISTS `series`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `series` (
  `series_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `author` varchar(100) NOT NULL,
  `location` varchar(50) NOT NULL COMMENT '索書號',
  PRIMARY KEY (`series_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2459 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Final view structure for view `borrow_item_frequency_view`
--

/*!50001 DROP VIEW IF EXISTS `borrow_item_frequency_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`soruly`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `borrow_item_frequency_view` AS select `borrow`.`item_id` AS `item_id`,count(1) AS `frequency` from `borrow` group by `borrow`.`item_id` order by count(1) desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `borrow_series_frequency_view`
--

/*!50001 DROP VIEW IF EXISTS `borrow_series_frequency_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`soruly`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `borrow_series_frequency_view` AS select `item`.`series_id` AS `series_id`,sum(`borrow_item_frequency_view`.`frequency`) AS `frequency` from (`borrow_item_frequency_view` left join `item` on((`item`.`item_id` = `borrow_item_frequency_view`.`item_id`))) group by `item`.`series_id` order by sum(`borrow_item_frequency_view`.`frequency`) desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `borrow_view`
--

/*!50001 DROP VIEW IF EXISTS `borrow_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`soruly`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `borrow_view` AS select `borrow`.`id` AS `id`,`borrow`.`sid` AS `sid`,`borrow`.`date_borr` AS `date_borr`,`borrow`.`date_due` AS `date_exp`,`borrow`.`date_return` AS `date_return`,`item`.`item_id` AS `item_id`,`item`.`series_id` AS `series_id`,`item`.`volume` AS `volume`,`item`.`entry_date` AS `entry_date`,`item`.`barcode` AS `barcode`,`item`.`language` AS `language`,`item`.`status` AS `status`,`series`.`title` AS `title`,`series`.`author` AS `author`,`series`.`location` AS `location` from ((`borrow` left join `item` on((`borrow`.`item_id` = `item`.`item_id`))) left join `series` on((`item`.`series_id` = `series`.`series_id`))) order by `borrow`.`date_borr` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `hot_series_view`
--

/*!50001 DROP VIEW IF EXISTS `hot_series_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`soruly`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `hot_series_view` AS select `series`.`series_id` AS `series_id`,`series`.`title` AS `title`,`series`.`author` AS `author`,`series`.`location` AS `location`,`borrow_series_frequency_view`.`frequency` AS `frequency` from (`borrow_series_frequency_view` left join `series` on((`borrow_series_frequency_view`.`series_id` = `series`.`series_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `item_view`
--

/*!50001 DROP VIEW IF EXISTS `item_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`soruly`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `item_view` AS select `item`.`item_id` AS `item_id`,`item`.`series_id` AS `series_id`,`series`.`title` AS `title`,`series`.`author` AS `author`,`item`.`volume` AS `volume`,`item`.`language` AS `language`,`series`.`location` AS `location`,`item`.`status` AS `status`,`item`.`barcode` AS `barcode`,`item`.`entry_date` AS `entry_date` from (`item` left join `series` on((`item`.`series_id` = `series`.`series_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-05-06  0:17:45
