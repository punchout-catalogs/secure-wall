Require:
"illuminate/database": "5.7.*",

Table to create:
CREATE TABLE `{TABLE_NAME}` (
  `id` varchar(255) NOT NULL COMMENT 'id',
  `value` text NOT NULL COMMENT 'value',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'created_at',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT 'updated_at',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='{TABLE_NAME} Table';
