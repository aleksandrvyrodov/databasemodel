/*
 Navicat Premium Data Transfer

 Source Server         : LOCAL
 Source Server Type    : MySQL
 Source Server Version : 50733
 Source Host           : localhost:3306
 Source Schema         : models

 Target Server Type    : MySQL
 Target Server Version : 50733
 File Encoding         : 65001

 Date: 09/08/2022 16:11:55
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for _table_1
-- ----------------------------
DROP TABLE IF EXISTS `_table_1`;
CREATE TABLE `_table_1`  (
  `num` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`num`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of _table_1
-- ----------------------------
INSERT INTO `_table_1` VALUES (1, 'Moscow');
INSERT INTO `_table_1` VALUES (2, 'Saint-P');
INSERT INTO `_table_1` VALUES (3, 'Rostow');
INSERT INTO `_table_1` VALUES (4, 'Ekaterinburg');
INSERT INTO `_table_1` VALUES (5, 'Murmansk');
INSERT INTO `_table_1` VALUES (6, 'Khabarovsk');
INSERT INTO `_table_1` VALUES (7, 'Vorkuta');

-- ----------------------------
-- Table structure for _table_2
-- ----------------------------
DROP TABLE IF EXISTS `_table_2`;
CREATE TABLE `_table_2`  (
  `num` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`num`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of _table_2
-- ----------------------------
INSERT INTO `_table_2` VALUES (1, 'Red');
INSERT INTO `_table_2` VALUES (2, 'Green');
INSERT INTO `_table_2` VALUES (3, 'Blue');
INSERT INTO `_table_2` VALUES (4, 'Yellow');
INSERT INTO `_table_2` VALUES (5, 'Pink');
INSERT INTO `_table_2` VALUES (6, 'Orange');
INSERT INTO `_table_2` VALUES (7, 'Violet');

-- ----------------------------
-- Table structure for _table_3
-- ----------------------------
DROP TABLE IF EXISTS `_table_3`;
CREATE TABLE `_table_3`  (
  `num` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`num`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of _table_3
-- ----------------------------
INSERT INTO `_table_3` VALUES (1, 'Carrot');
INSERT INTO `_table_3` VALUES (2, 'Paper');
INSERT INTO `_table_3` VALUES (3, 'Tomato');
INSERT INTO `_table_3` VALUES (4, 'Banana');
INSERT INTO `_table_3` VALUES (5, 'Zucchini');
INSERT INTO `_table_3` VALUES (6, 'Cucumber');
INSERT INTO `_table_3` VALUES (7, 'Potato');

-- ----------------------------
-- Table structure for _table_4
-- ----------------------------
DROP TABLE IF EXISTS `_table_4`;
CREATE TABLE `_table_4`  (
  `num` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`num`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of _table_4
-- ----------------------------
INSERT INTO `_table_4` VALUES (1, 'BMW');
INSERT INTO `_table_4` VALUES (2, 'Mercedes');
INSERT INTO `_table_4` VALUES (3, 'Audi');
INSERT INTO `_table_4` VALUES (4, 'Ford');
INSERT INTO `_table_4` VALUES (5, 'Nissan');
INSERT INTO `_table_4` VALUES (6, 'Kia');
INSERT INTO `_table_4` VALUES (7, 'Lada');

-- ----------------------------
-- Table structure for _table_5
-- ----------------------------
DROP TABLE IF EXISTS `_table_5`;
CREATE TABLE `_table_5`  (
  `num` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`num`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of _table_5
-- ----------------------------
INSERT INTO `_table_5` VALUES (1, 'Winston');
INSERT INTO `_table_5` VALUES (2, 'Camel');
INSERT INTO `_table_5` VALUES (3, 'Marlboro');
INSERT INTO `_table_5` VALUES (4, 'Danhil');
INSERT INTO `_table_5` VALUES (5, 'PhilipMoris');
INSERT INTO `_table_5` VALUES (6, 'Petr-I');
INSERT INTO `_table_5` VALUES (7, 'TU-134');

-- ----------------------------
-- Table structure for fk_only
-- ----------------------------
DROP TABLE IF EXISTS `fk_only`;
CREATE TABLE `fk_only`  (
  `table_3` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  INDEX `table_3-FK`(`table_3`) USING BTREE,
  CONSTRAINT `table_3-FK` FOREIGN KEY (`table_3`) REFERENCES `_table_3` (`num`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of fk_only
-- ----------------------------
INSERT INTO `fk_only` VALUES (1, 'like');
INSERT INTO `fk_only` VALUES (1, 'very like');
INSERT INTO `fk_only` VALUES (1, 'meet');
INSERT INTO `fk_only` VALUES (5, 'fuuu');
INSERT INTO `fk_only` VALUES (5, 'dontlike');

-- ----------------------------
-- Table structure for fk_pk
-- ----------------------------
DROP TABLE IF EXISTS `fk_pk`;
CREATE TABLE `fk_pk`  (
  `num` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `table_2` int(11) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`num`) USING BTREE,
  INDEX `table_2-FK`(`table_2`) USING BTREE,
  CONSTRAINT `table_2-FK` FOREIGN KEY (`table_2`) REFERENCES `_table_2` (`num`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of fk_pk
-- ----------------------------
INSERT INTO `fk_pk` VALUES (1, 'beee', 1);
INSERT INTO `fk_pk` VALUES (2, 'okay', 1);
INSERT INTO `fk_pk` VALUES (3, 'lignt', 7);
INSERT INTO `fk_pk` VALUES (4, 'dark', 5);
INSERT INTO `fk_pk` VALUES (5, 'like', 3);

-- ----------------------------
-- Table structure for fk_pk_twice
-- ----------------------------
DROP TABLE IF EXISTS `fk_pk_twice`;
CREATE TABLE `fk_pk_twice`  (
  `table_4` int(11) UNSIGNED NOT NULL,
  `table_5` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`table_4`, `table_5`) USING BTREE,
  INDEX `table_5-FK`(`table_5`) USING BTREE,
  CONSTRAINT `table_4-FK` FOREIGN KEY (`table_4`) REFERENCES `_table_4` (`num`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `table_5-FK` FOREIGN KEY (`table_5`) REFERENCES `_table_5` (`num`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of fk_pk_twice
-- ----------------------------
INSERT INTO `fk_pk_twice` VALUES (1, 1, 'ok');
INSERT INTO `fk_pk_twice` VALUES (1, 5, 'fuu');
INSERT INTO `fk_pk_twice` VALUES (2, 6, 'great');
INSERT INTO `fk_pk_twice` VALUES (3, 6, 'normal');
INSERT INTO `fk_pk_twice` VALUES (7, 1, 'maybe');

-- ----------------------------
-- Table structure for fk_this_pk
-- ----------------------------
DROP TABLE IF EXISTS `fk_this_pk`;
CREATE TABLE `fk_this_pk`  (
  `table_1` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`table_1`) USING BTREE,
  CONSTRAINT `table_1-FK` FOREIGN KEY (`table_1`) REFERENCES `_table_1` (`num`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of fk_this_pk
-- ----------------------------
INSERT INTO `fk_this_pk` VALUES (1, 'very big');
INSERT INTO `fk_this_pk` VALUES (2, 'big');
INSERT INTO `fk_this_pk` VALUES (3, 'middle');
INSERT INTO `fk_this_pk` VALUES (4, 'middle');
INSERT INTO `fk_this_pk` VALUES (7, 'cold');

-- ----------------------------
-- Table structure for pk_only
-- ----------------------------
DROP TABLE IF EXISTS `pk_only`;
CREATE TABLE `pk_only`  (
  `phone` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`phone`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pk_only
-- ----------------------------
INSERT INTO `pk_only` VALUES (123, 'Ivan');
INSERT INTO `pk_only` VALUES (147, 'Aleksandr');
INSERT INTO `pk_only` VALUES (258, 'Gevorg');
INSERT INTO `pk_only` VALUES (369, 'Andre');
INSERT INTO `pk_only` VALUES (465, 'Viktoria');
INSERT INTO `pk_only` VALUES (700, 'dnoB');
INSERT INTO `pk_only` VALUES (789, 'John');

-- ----------------------------
-- Table structure for pk_only_ai
-- ----------------------------
DROP TABLE IF EXISTS `pk_only_ai`;
CREATE TABLE `pk_only_ai`  (
  `num` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`num`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pk_only_ai
-- ----------------------------
INSERT INTO `pk_only_ai` VALUES (1, 'Vodka');
INSERT INTO `pk_only_ai` VALUES (2, 'Cogniak');
INSERT INTO `pk_only_ai` VALUES (3, 'Shampain');
INSERT INTO `pk_only_ai` VALUES (4, 'Scotch');
INSERT INTO `pk_only_ai` VALUES (5, 'Visky');
INSERT INTO `pk_only_ai` VALUES (6, 'Beer');
INSERT INTO `pk_only_ai` VALUES (7, 'Tequila');

-- ----------------------------
-- Table structure for pk_twice
-- ----------------------------
DROP TABLE IF EXISTS `pk_twice`;
CREATE TABLE `pk_twice`  (
  `num` int(11) UNSIGNED NOT NULL,
  `region` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`num`, `region`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pk_twice
-- ----------------------------
INSERT INTO `pk_twice` VALUES (123, 11, 'Samsung');
INSERT INTO `pk_twice` VALUES (147, 44, 'Sony');
INSERT INTO `pk_twice` VALUES (258, 55, 'Atom');
INSERT INTO `pk_twice` VALUES (369, 66, 'Ericson');
INSERT INTO `pk_twice` VALUES (456, 22, 'Siemens');
INSERT INTO `pk_twice` VALUES (555, 77, 'HP');
INSERT INTO `pk_twice` VALUES (789, 33, 'Nokia');

-- ----------------------------
-- Table structure for pk_twice_ai
-- ----------------------------
DROP TABLE IF EXISTS `pk_twice_ai`;
CREATE TABLE `pk_twice_ai`  (
  `num` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `region` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`num`, `region`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pk_twice_ai
-- ----------------------------
INSERT INTO `pk_twice_ai` VALUES (1, 12, 'Russia');
INSERT INTO `pk_twice_ai` VALUES (2, 23, 'China');
INSERT INTO `pk_twice_ai` VALUES (3, 34, 'India');
INSERT INTO `pk_twice_ai` VALUES (4, 45, 'Armenia');
INSERT INTO `pk_twice_ai` VALUES (5, 56, 'Italia');
INSERT INTO `pk_twice_ai` VALUES (6, 67, 'England');
INSERT INTO `pk_twice_ai` VALUES (7, 78, 'Cuba');

-- ----------------------------
-- Table structure for prop_only
-- ----------------------------
DROP TABLE IF EXISTS `prop_only`;
CREATE TABLE `prop_only`  (
  `prop` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of prop_only
-- ----------------------------
INSERT INTO `prop_only` VALUES ('table-1', 'city');
INSERT INTO `prop_only` VALUES ('table-2', 'color');
INSERT INTO `prop_only` VALUES ('table-3', 'veggie');
INSERT INTO `prop_only` VALUES ('table-4', 'car');
INSERT INTO `prop_only` VALUES ('table-5', 'sigaret');
INSERT INTO `prop_only` VALUES ('pk-only', 'name');
INSERT INTO `prop_only` VALUES ('pk-twice', 'company');

SET FOREIGN_KEY_CHECKS = 1;
