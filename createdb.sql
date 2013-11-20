-- MySQL dump 10.13  Distrib 5.5.8, for Win32 (x86)
--
-- Host: localhost    Database: wx_platform
-- ------------------------------------------------------
-- Server version	5.5.8

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



DROP DATABASE IF EXISTS `winphp_test`;
create database `winphp_test` default charset=utf8;
use `winphp_test`;

DROP TABLE IF EXISTS `system_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL COMMENT '用户名',
  `passwd` varchar(32) NOT NULL COMMENT '密码',
  `valid` int(1) NOT NULL DEFAULT '0' COMMENT '是否有效 0:无效 1:有效',
  `privilege` int(1) unsigned NOT NULL DEFAULT '0' COMMENT '权限:0,只能浏览;1:普通,浏览修改不能发布;2:高级,处理升级消息及发布;3:超级,除有前面权限外，可增加系统用户。',
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统用户表';
/*!40101 SET character_set_client = @saved_cs_client */;
insert into system_user(name,passwd,valid,privilege,ctime) values('wp','b6ddd84a9cc636257258701ca934e763',1, 3 , unix_timestamp());
insert into system_user(name,passwd,valid,privilege,ctime) values('admin','21232f297a57a5a743894a0e4a801fc3',1, 3 , unix_timestamp());



/*

旅游团路线 travel_team_path
travel_team_id
local_travel_agency_id冗余
fee_type_id


游客表（按团/人次）tourist
identity身份证号
travel_team_id
local_travel_agency_id冗余
remote_travel_agency_id冗余
name姓名
phone手机号可选


收费种类（如每个卖门票的地点，算一个种类；每一种船票，算一个种类）fee_type
type（1代表门票种类，2代表交通种类，等等）
name
phone
address
contact
*/

