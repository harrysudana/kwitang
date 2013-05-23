ALTER TABLE `ct_articles` ADD COLUMN `foto_description` TEXT NULL  AFTER `foto`,
ADD COLUMN `source` VARCHAR(255) NULL  AFTER `lang_group`,
ADD COLUMN `source_link` VARCHAR(255) NULL  AFTER `source`;
