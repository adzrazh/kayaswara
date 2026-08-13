-- Add-ons Migration (run once on existing installations)
-- Skip if using fresh install — install.php already includes this column.
ALTER TABLE `orders`
  ADD COLUMN IF NOT EXISTS `addons` TEXT NULL DEFAULT NULL
  AFTER `price`;
