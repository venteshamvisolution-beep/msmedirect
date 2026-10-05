## 24-09-2026 Worked Files

---

AuthController
EditUserData
register.blade
2026_09_24_200000_add_msme_business_data_id_to_user_tables
business_campaign.blade
BusinessDataMail
ListMsmeBusinessData
views/emails/business_campaign.blade

## 24-09-2026 -> Query

---

ALTER TABLE `users`
ADD COLUMN `msme_business_data_id` BIGINT(20) UNSIGNED NULL AFTER `id`,
ADD INDEX `users_msme_business_data_id_index` (`msme_business_data_id`),
ADD CONSTRAINT `users_msme_business_data_id_foreign`
FOREIGN KEY (`msme_business_data_id`)
REFERENCES `msme_business_data` (`id`)
ON DELETE SET NULL
ON UPDATE CASCADE;
