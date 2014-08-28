DROP TABLE IF EXISTS `Address`;
CREATE TABLE `Address` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `firstName` varchar(255),
  `lastName` varchar(255),
  `email` varchar(255),
  `phone` varchar(255),
  `postCode` varchar(255),
  `line1` varchar(255),
  `line2` varchar(255),
  `countryId` int(11) UNSIGNED NULL,
  `cityId` int(11) UNSIGNED NULL,
  `deletedAt` TIMESTAMP NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `Purchase`;
CREATE TABLE `Purchase` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniqueKey` varchar(100),
  `responseData` TEXT,
  `completedAt` TIMESTAMP NULL,
  `isSuccessful` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `billingId` int(11) UNSIGNED NULL,
  `shippingId` int(11) UNSIGNED NULL,
  `currency` varchar(3) NULL,
  `value` int(11) NULL,
  `isFrozen` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `createdAt` TIMESTAMP NULL,
  `updatedAt` TIMESTAMP NULL,
  `deletedAt` TIMESTAMP NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PurchaseItem`;
CREATE TABLE `PurchaseItem` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `class` varchar(255),
  `purchaseId` int(11) UNSIGNED NULL,
  `storePurchaseId` int(11) UNSIGNED NULL,
  `sourceId` int(11) UNSIGNED NULL,
  `parentId` int(11) UNSIGNED NULL,
  `quantity` int(11) UNSIGNED NOT NULL DEFAULT 1,
  `value` int(11) NULL,
  `isFrozen` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `parameter` varchar(255),
  `deletedAt` TIMESTAMP NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `Product`;
CREATE TABLE `Product` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `storeId` int(11) UNSIGNED NULL,
  `shippingProfileId` int(11) UNSIGNED NULL,
  `name` varchar(255),
  `value` int(11) NULL,
  `currency` varchar(3) NULL,
  `deletedAt` TIMESTAMP NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `StorePurchase`;
CREATE TABLE `StorePurchase` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniqueKey` varchar(100),
  `status` TINYINT(1) UNSIGNED NULL,
  `isFrozen` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `currency` varchar(3) NULL,
  `value` int(11) NULL,
  `purchaseId` int(11) UNSIGNED NULL,
  `storeId` int(11) UNSIGNED NULL,
  `deletedAt` TIMESTAMP NULL,
  `createdAt` TIMESTAMP NULL,
  `updatedAt` TIMESTAMP NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `Refund`;
CREATE TABLE `Refund` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `isSuccessful` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `isFrozen` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `currency` varchar(3) NULL,
  `value` int(11) NULL,
  `responseData` TEXT,
  `storePurchaseId` int(11) UNSIGNED NULL,
  `completedAt` TIMESTAMP NULL,
  `deletedAt` TIMESTAMP NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `Store`;
CREATE TABLE `Store` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255),
  `deletedAt` TIMESTAMP NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `Location`;
CREATE TABLE `Location` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NULL,
  `class` varchar(255) NULL,
  `parentId` int(11) UNSIGNED NULL,
  `path` varchar(255) NULL,
  `code` varchar(255) NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ShippingProfile`;
CREATE TABLE `ShippingProfile` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NULL,
  `currency` varchar(3) NULL,
  `storeId` int(11) UNSIGNED NULL,
  `shipsFromId` int(11) UNSIGNED NULL,
  `days` int(11) NULL,
  `deletedAt` TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `Shipping`;
CREATE TABLE `Shipping` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `class` varchar(255) NULL,
  `value` int(11) NULL,
  `days` int(11) NULL,
  `shippingProfileId` int(11) UNSIGNED NULL,
  `shippingMethodId` int(11) UNSIGNED NULL,
  `locationId` int(11) UNSIGNED NULL,
  `deletedAt` TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ShippingMethod`;
CREATE TABLE `ShippingMethod` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NULL,
  `storeId` int(11) UNSIGNED NULL,
  `deletedAt` TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `Location` (`id`, `name`, `class`, `code`, `parentId`, `path`)
VALUES
  (1, 'Bulgaria', 'Harp\\Locations\\Country', 'BG', 2, '3'),
  (2, 'Sofia', 'Harp\\Locations\\City', NULL, 1, '3/1'),
  (3, 'Everywhere', 'Harp\\Locations\\Region', NULL, 0, '');

INSERT INTO `Address`
(`id`, `firstName`, `lastName`, `email`, `phone`, `postCode`, `line1`, `line2`, `cityId`, `countryId`)
VALUES
  (1, 'John', 'Doe', 'john@example.com', '123123', '1000', 'Moskovska', '132', 2, 1);

INSERT INTO `Purchase`
(`id`, `uniqueKey`, `responseData`, `currency`, `isSuccessful`, `billingId`, `deletedAt`)
VALUES
  (1, 'FH2WO3EH', '{"amount":"380.00","reference":"53a43cc327040","success":true,"message":"Success"}', 'GBP', 1, 1, NULL),
  (2, 'FVNDWZEH', NULL, 'GBP', 0, 0, NULL);

INSERT INTO `PurchaseItem`
(`id`, `class`, `purchaseId`, `storePurchaseId`, `sourceId`, `parentId`, `quantity`, `value`, `isFrozen`, `deletedAt`)
VALUES
  (1, 'CL\\Shipping\\Test\\Model\\ProductItem', 1, 1, 1, NULL, 1, 1000, 1, NULL),
  (2, 'CL\\Shipping\\Test\\Model\\ProductItem', 1, 1, 2, NULL, 2, 2000, 1, NULL),
  (3, 'CL\\Shipping\\Test\\Model\\ProductItem', 1, 1, 3, NULL, 1, 3000, 1, NULL),
  (4, 'CL\\Shipping\\Test\\Model\\ProductItem', 1, 2, 4, NULL, 1, 4000, 1, NULL),
  (5, 'CL\\Shipping\\ShippingItem', 1, 1, 1, NULL, 1, 2300, 1, NULL),
  (6, 'CL\\Shipping\\ShippingItem', 1, 2, 2, NULL, 2, 1300, 1, NULL),
  (7, 'CL\\Shipping\\ShippingItem', 1, 2, 3, NULL, 1, 2300, 1, NULL),
  (8, 'CL\\Shipping\\ShippingItem', 1, 2, 4, NULL, 1, 2300, 1, NULL),
  (9, 'CL\\Purchases\\ProductItem', 1, 2, 5, NULL, 1, 5000, 1, '2014-02-03 00:00:00');

INSERT INTO `StorePurchase`
(`id`, `uniqueKey`, `status`, `purchaseId`, `storeId`, `createdAt`, `updatedAt`, `deletedAt`)
VALUES
  (1, '32NDWZEH', 2, 1, 1, '2014-01-03 10:00:00', '2014-03-03 12:00:00', NULL),
  (2, 'QTEOCKJT', 2, 1, 1, '2014-01-03 10:00:00', '2014-06-03 12:00:00', NULL);

INSERT INTO `Product`
(`id`, `name`, `storeId`, `value`, `currency`, `deletedAt`)
VALUES
  (1, 'Product 1', 1, 1000, 'GBP', NULL),
  (2, 'Product 2', 1, 2000, 'GBP', NULL),
  (3, 'Product 3', 1, 3000, 'GBP', NULL),
  (4, 'Product 4', 1, 4000, 'GBP', NULL),
  (5, 'Product 5', 1, 5000, 'GBP', NULL),
  (6, 'Product 6', 1, 6000, 'EUR', NULL),
  (7, 'Product 7', 2, 7000, 'GBP', NULL);

INSERT INTO `ShippingProfile`
(`id`, `name`, `currency`, `storeId`, `shipsFromId`, `days`, `deletedAt`)
VALUES
  (1, 'Shipping Cheap', 'GBP', 1, 2, '2|3', NULL),
  (2, 'Shipping Expensive', 'EUR', 1, 2, '4|7', NULL);

INSERT INTO `ShippingMethod`
(`id`, `name`, `storeId`, `deletedAt`)
VALUES
  (1, 'Post', 1, NULL),
  (2, 'Courier', 1, NULL);

INSERT INTO `Shipping`
(`id`, `class`, `value`, `days`, `shippingProfileId`, `shippingMethodId`, `locationId`, `deletedAt`)
VALUES
  (1, 'CL\\Shipping\\ShippingManual', 2300, '2|5', 1, 1, 3, NULL),
  (2, 'CL\\Shipping\\ShippingManual', 6300, '1|2', 1, 2, 1, NULL);

INSERT INTO `Refund`
(`id`, `responseData`, `isSuccessful`, `isFrozen`, `value`, `currency`, `storePurchaseId`, `deletedAt`)
VALUES
(1, NULL, 1, 1, 4000, 'GBP', 1, NULL);

INSERT INTO `Store`
(`id`, `name`, `deletedAt`)
VALUES
(1, 'Store 1', NULL),
(2, 'Store 2', NULL);
