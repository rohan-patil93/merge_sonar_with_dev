CREATE TABLE `job_title_competencies` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `job_title_id` int(11) DEFAULT NULL,
  `competency_id` int(11) DEFAULT NULL,
  `competency_level_id` int(11) DEFAULT NULL,
  `remarks` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` int(11) DEFAULT NULL,
  `current_status` int(11) NOT NULL DEFAULT 0,
  `status_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE `job_title_competencies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

ALTER TABLE `job_title_competencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
