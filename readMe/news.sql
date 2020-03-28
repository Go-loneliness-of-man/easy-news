-- MySQL dump 10.13  Distrib 8.0.13, for Win64 (x86_64)
--
-- Host: localhost    Database: news
-- ------------------------------------------------------
-- Server version	8.0.13

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
 SET NAMES utf8 ;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `discuss2_t`
--

DROP TABLE IF EXISTS `discuss2_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `discuss2_t` (
  `discuss2_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `entity_type` int(11) DEFAULT NULL,
  `good` int(11) DEFAULT NULL,
  `time` bigint(20) DEFAULT NULL,
  `text` text,
  `master` int(11) DEFAULT NULL,
  `discuss_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`discuss2_id`),
  KEY `user_id` (`user_id`),
  KEY `entity_id` (`entity_id`),
  KEY `master` (`master`),
  KEY `discuss_id` (`discuss_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discuss2_t`
--

LOCK TABLES `discuss2_t` WRITE;
/*!40000 ALTER TABLE `discuss2_t` DISABLE KEYS */;
/*!40000 ALTER TABLE `discuss2_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `discuss_t`
--

DROP TABLE IF EXISTS `discuss_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `discuss_t` (
  `discuss_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `entity_type` int(11) DEFAULT NULL,
  `good` int(11) DEFAULT NULL,
  `time` bigint(20) DEFAULT NULL,
  `text` text,
  PRIMARY KEY (`discuss_id`),
  KEY `user_id` (`user_id`),
  KEY `entity_id` (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discuss_t`
--

LOCK TABLES `discuss_t` WRITE;
/*!40000 ALTER TABLE `discuss_t` DISABLE KEYS */;
/*!40000 ALTER TABLE `discuss_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `good_t`
--

DROP TABLE IF EXISTS `good_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `good_t` (
  `user_id` int(11) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `entity_type` int(11) DEFAULT NULL,
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `good_t`
--

LOCK TABLES `good_t` WRITE;
/*!40000 ALTER TABLE `good_t` DISABLE KEYS */;
/*!40000 ALTER TABLE `good_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message_t`
--

DROP TABLE IF EXISTS `message_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `message_t` (
  `msg_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `read` int(11) DEFAULT NULL,
  `text` text,
  `title` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`msg_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_t`
--

LOCK TABLES `message_t` WRITE;
/*!40000 ALTER TABLE `message_t` DISABLE KEYS */;
/*!40000 ALTER TABLE `message_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news_t`
--

DROP TABLE IF EXISTS `news_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `news_t` (
  `news_id` int(11) NOT NULL AUTO_INCREMENT,
  `theme_id` int(11) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `click` int(11) DEFAULT NULL,
  `good` int(11) DEFAULT NULL,
  `file` varchar(200) DEFAULT NULL,
  `master` int(11) DEFAULT NULL,
  `branch` varchar(100) DEFAULT NULL,
  `time` bigint(20) DEFAULT NULL,
  `revise` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`news_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news_t`
--

LOCK TABLES `news_t` WRITE;
/*!40000 ALTER TABLE `news_t` DISABLE KEYS */;
/*!40000 ALTER TABLE `news_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `special_t`
--

DROP TABLE IF EXISTS `special_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `special_t` (
  `theme_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `click` int(11) DEFAULT NULL,
  `good` int(11) DEFAULT NULL,
  `master` int(11) DEFAULT NULL,
  `branch` varchar(100) DEFAULT NULL,
  `time` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`theme_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `special_t`
--

LOCK TABLES `special_t` WRITE;
/*!40000 ALTER TABLE `special_t` DISABLE KEYS */;
/*!40000 ALTER TABLE `special_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tag_t`
--

DROP TABLE IF EXISTS `tag_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `tag_t` (
  `tag_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  PRIMARY KEY (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tag_t`
--

LOCK TABLES `tag_t` WRITE;
/*!40000 ALTER TABLE `tag_t` DISABLE KEYS */;
/*!40000 ALTER TABLE `tag_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `type_t`
--

DROP TABLE IF EXISTS `type_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `type_t` (
  `entity_type` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `describe` varchar(100) DEFAULT NULL,
  `table` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`entity_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `type_t`
--

LOCK TABLES `type_t` WRITE;
/*!40000 ALTER TABLE `type_t` DISABLE KEYS */;
/*!40000 ALTER TABLE `type_t` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_t`
--

DROP TABLE IF EXISTS `user_t`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `user_t` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `mail` char(30) DEFAULT NULL,
  `password` varchar(30) DEFAULT NULL,
  `name` varchar(30) DEFAULT NULL,
  `signature` varchar(200) DEFAULT NULL,
  `head` varchar(200) DEFAULT NULL,
  `good` int(11) DEFAULT NULL,
  `time` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `mail` (`mail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_t`
--

LOCK TABLES `user_t` WRITE;
/*!40000 ALTER TABLE `user_t` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_t` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-03-28 17:47:00
