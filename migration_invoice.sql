-- Invoice System Migration
-- Run once on your MySQL database
-- Compatible with PHP 7.4+

CREATE TABLE IF NOT EXISTS `invoices` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id`       INT UNSIGNED NOT NULL,
  `invoice_number` VARCHAR(30)  NOT NULL,
  `status`         ENUM('draft','sent','paid','cancelled') NOT NULL DEFAULT 'draft',
  `subtotal`       INT UNSIGNED NOT NULL DEFAULT 0,
  `discount`       INT UNSIGNED NOT NULL DEFAULT 0,
  `tax_pph23`      INT UNSIGNED NOT NULL DEFAULT 0,
  `total`          INT UNSIGNED NOT NULL DEFAULT 0,
  `due_date`       DATE         NULL,
  `paid_at`        DATETIME     NULL,
  `show_pph23`     TINYINT(1)   NOT NULL DEFAULT 1,
  `admin_notes`    TEXT         NULL,
  `token`          VARCHAR(64)  NOT NULL,
  `created_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_invoice_number` (`invoice_number`),
  UNIQUE KEY `uq_invoice_token`  (`token`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_status`   (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
