-- Royal Village International Foundation MySQL Schema & Seed Data
CREATE DATABASE IF NOT EXISTS `royalcms` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `royalcms`;

-- Drop tables if they exist to allow clean installation
DROP TABLE IF EXISTS `blog_gallery`;
DROP TABLE IF EXISTS `site_settings`;
DROP TABLE IF EXISTS `beneficiaries`;
DROP TABLE IF EXISTS `team_members`;
DROP TABLE IF EXISTS `programs`;
DROP TABLE IF EXISTS `partners`;
DROP TABLE IF EXISTS `blog_posts`;
DROP TABLE IF EXISTS `contact_messages`;
DROP TABLE IF EXISTS `donations`;
DROP TABLE IF EXISTS `milestones`;

-- Create tables
CREATE TABLE `site_settings` (
  `id` varchar(50) NOT NULL,
  `value` longtext NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `beneficiaries` (
  `id` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `age` varchar(50) DEFAULT NULL,
  `hometown` varchar(255) DEFAULT NULL,
  `program` varchar(255) DEFAULT NULL,
  `destination` varchar(255) DEFAULT NULL,
  `year_sent` varchar(50) DEFAULT NULL,
  `institution` varchar(255) DEFAULT NULL,
  `study_field` varchar(255) DEFAULT NULL,
  `photo` varchar(1024) DEFAULT NULL,
  `short_story` text DEFAULT NULL,
  `full_story` text DEFAULT NULL,
  `quote` text DEFAULT NULL,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `team_members` (
  `id` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `photo` varchar(1024) DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `programs` (
  `id` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `image` varchar(1024) DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `partners` (
  `id` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `icon` varchar(100) DEFAULT 'graduation-cap',
  `color` varchar(50) DEFAULT '#7e22ce',
  `display_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `blog_posts` (
  `id` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` text DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `image` varchar(1024) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `blog_gallery` (
  `id` varchar(50) NOT NULL,
  `blog_post_id` varchar(50) NOT NULL,
  `image_url` varchar(1024) NOT NULL,
  `caption` varchar(500) DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `blog_post_id` (`blog_post_id`),
  FOREIGN KEY (`blog_post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `contact_messages` (
  `id` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `donations` (
  `id` varchar(50) NOT NULL,
  `stripe_session_id` varchar(255) NOT NULL,
  `stripe_payment_intent` varchar(255) DEFAULT NULL,
  `amount_cents` int(11) NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'usd',
  `donor_name` varchar(255) DEFAULT NULL,
  `donor_email` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `founder_info` (
  `id` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `profession` varchar(255) DEFAULT NULL,
  `photo` varchar(1024) DEFAULT NULL,
  `short_bio` text DEFAULT NULL,
  `full_story` text DEFAULT NULL,
  `quote` text DEFAULT NULL,
  `achievements` text DEFAULT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `milestones` (
  `id` varchar(50) NOT NULL,
  `year` varchar(10) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Seeding site_settings
INSERT INTO `site_settings` (`id`, `value`, `updated_at`) VALUES ('main', '{"logo":"https://d64gsuwffb70l.cloudfront.net/6a0c48e7482add8d9312f354_1779190001642_6ba746dd.png","stats":{"years":6,"programs":4,"scholars":50,"countries":4},"address":"Monrovia, Liberia","tagline":"Royal Village International Foundation","twitter":"https://twitter.com","facebook":"https://facebook.com","siteName":"Royal Village Int''l","aboutText":"Royal Village International Foundation (RVIF) is a nonprofit organization committed to empowering African youth through education, vocational training, and community development. Founded by Queen Georgia T. Nuahn, RVIF has helped dozens of young scholars pursue higher education in countries including Rwanda, India, and Liberia.","heroImage":"https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=1600","heroTitle":"Empowering Africa''s Future Through Education","instagram":"https://instagram.com","aboutTitle":"About Royal Village","contactEmail":"info@rvif.org","contactPhone":"+231 770 000 000","heroSubtitle":"Royal Village International Foundation provides scholarships, vocational training, and community support to transform lives across Africa.","backgroundMusic":""}', '2026-05-20T18:47:42.738Z');

-- Seeding beneficiaries
INSERT INTO `beneficiaries` (`id`, `name`, `age`, `hometown`, `program`, `destination`, `year_sent`, `institution`, `study_field`, `photo`, `short_story`, `full_story`, `quote`, `featured`, `visible`, `display_order`) VALUES ('ben-2', 'Linda N Gbarlea', '22', 'Soe Senlay Town, Monrovia, Liberia', 'rwanda', 'Kigali, Rwanda', '2024', 'Mount Kigali University', 'General Nursing', 'https://nemvhsacmwolcagmyusa.databasepad.com/storage/v1/object/public/rvif-media/beneficiaries/1779298186611-7zwmgt4ttm5.jpg', 'Linda is pursuing her dream of becoming a nurse through the RVIF scholarship program.', 'Linda is pursuing her dream of becoming a nurse through the RVIF scholarship program at Mount Kigali University in Rwanda. She comes from a humble background and is determined to serve her community through healthcare.', 'Through RVIF, I am becoming the nurse I always dreamed to be.', 1, 1, 2);
INSERT INTO `beneficiaries` (`id`, `name`, `age`, `hometown`, `program`, `destination`, `year_sent`, `institution`, `study_field`, `photo`, `short_story`, `full_story`, `quote`, `featured`, `visible`, `display_order`) VALUES ('ben-3', 'Archie K. Gonwoe Jr.', '21', 'Monrovia, Liberia', 'rwanda', 'Kigali, Rwanda', '2023', 'University of Lay Adventist of Kigali', 'Software Engineering', 'https://nemvhsacmwolcagmyusa.databasepad.com/storage/v1/object/public/rvif-media/beneficiaries/1779194944356-dl2r5yamt.jpeg', 'First in his family to attend university, pursuing Software Engineering in Rwanda.', 'My name is Archie K. Gonwoe Jr. I came into this world on March 15, 2003, in Senlay Town. I hail from a humble background where no one in my family has had the opportunity to attend university beside me. My educational journey began at Soe-Senlay Public School in 2011, graduating in 2019. I completed high school at Karn High School, graduating in 2022.', 'I am breaking barriers and creating opportunities for my family.', 1, 1, 3);
INSERT INTO `beneficiaries` (`id`, `name`, `age`, `hometown`, `program`, `destination`, `year_sent`, `institution`, `study_field`, `photo`, `short_story`, `full_story`, `quote`, `featured`, `visible`, `display_order`) VALUES ('ben-1', 'Johnathan Mansuo', '19', 'Monrovia, Liberia', 'liberia', 'Monrovia, Liberia', '2024', 'NURAH KUDEE INSTITUTE (K.K.I)', 'General Primary Studies', 'https://nemvhsacmwolcagmyusa.databasepad.com/storage/v1/object/public/rvif-media/beneficiaries/1779298166115-unpcx93eeeh.jpg', 'Johnathan Mansuo''s mother entrusted him to Royal Village International Foundation (RVIF), explaining that she could no longer provide for him.', 'In 2024, Johnathan Mansuo''s mother entrusted him to Royal Village International Foundation (RVIF), explaining that she could no longer provide for him. Since joining RVIF, Johnathan has shown exceptional academic performance and dedication to his studies.', 'Education has given me hope for a better future.', 0, 1, 1);
INSERT INTO `beneficiaries` (`id`, `name`, `age`, `hometown`, `program`, `destination`, `year_sent`, `institution`, `study_field`, `photo`, `short_story`, `full_story`, `quote`, `featured`, `visible`, `display_order`) VALUES ('ben-4', 'Amos Sontay Jr', '20', 'Nimba County, Liberia', 'rwanda', 'Kigali, Rwanda', '2024', 'Mount Kigali University', 'General Nursing', 'https://nemvhsacmwolcagmyusa.databasepad.com/storage/v1/object/public/rvif-media/beneficiaries/1779298198586-0a4wkpd1m436.jpg', 'Born in Nimba County, pursuing nursing to serve his community through healthcare.', 'I''m Amos Sontay Jr. Born in the vibrant landscapes of Nimba County, Liberia, I grew up with a deep desire to serve my community through healthcare. Through RVIF''s scholarship program, I am now studying General Nursing at Mount Kigali University in Rwanda.', 'Healthcare is my calling, and RVIF made it possible.', 0, 1, 4);
INSERT INTO `beneficiaries` (`id`, `name`, `age`, `hometown`, `program`, `destination`, `year_sent`, `institution`, `study_field`, `photo`, `short_story`, `full_story`, `quote`, `featured`, `visible`, `display_order`) VALUES ('ben-5', 'Marita K Dialor', '19', 'Ganta, Nimba County, Liberia', 'rwanda', 'Kigali, Rwanda', '2024', 'University of Lay Adventists of Kigali', 'Finance', 'https://nemvhsacmwolcagmyusa.databasepad.com/storage/v1/object/public/rvif-media/beneficiaries/1779298216835-hxc1y293ier.jpg', 'A determined young woman from Nimba County pursuing Finance studies.', 'I am Marita K Dialor, a determined young woman from Nimba County. Through RVIF''s support, I am now studying Finance at the University of Lay Adventists of Kigali, working towards my dream of financial independence and community development.', 'Education is the key to unlocking my potential.', 0, 1, 5);
INSERT INTO `beneficiaries` (`id`, `name`, `age`, `hometown`, `program`, `destination`, `year_sent`, `institution`, `study_field`, `photo`, `short_story`, `full_story`, `quote`, `featured`, `visible`, `display_order`) VALUES ('ben-6', 'Omasco B Gbalea', '21', 'Soe Senlay Town, Nimba County, Liberia', 'india', 'New Delhi, India', '2024', 'Sharda University, India', 'B.A Psychology', 'https://nemvhsacmwolcagmyusa.databasepad.com/storage/v1/object/public/rvif-media/beneficiaries/1779298237885-wp26gl7mfv.jpg', 'Passionate about psychology and mental health in Africa.', 'I am Prince Omasco B Gbalea. I am passionate about psychology and mental health in Africa. Through RVIF''s scholarship, I am studying B.A Psychology at Sharda University in India, with a GPA of 7.9.', 'Mental health awareness starts with education.', 1, 1, 6);
INSERT INTO `beneficiaries` (`id`, `name`, `age`, `hometown`, `program`, `destination`, `year_sent`, `institution`, `study_field`, `photo`, `short_story`, `full_story`, `quote`, `featured`, `visible`, `display_order`) VALUES ('ben-7', 'Luah Davis', '20', 'Monrovia, Liberia', 'rwanda', 'Kigali, Rwanda', '2024', 'University of Lay Adventist Kigali (UNILAK)', 'Human Resource Management', 'https://nemvhsacmwolcagmyusa.databasepad.com/storage/v1/object/public/rvif-media/beneficiaries/1779298250787-al6v5os6kk.jpg', 'Top student pursuing Human Resource Management in Rwanda.', 'My name is Luah Davis. I graduated from Levi H. Martin Baptist School where I was honored as one of the top students in my graduating class. Through RVIF''s scholarship program, I am now studying Human Resource Management at UNILAK in Rwanda with a GPA of 3.6.', 'Excellence is not an accident, it''s a habit.', 0, 1, 7);
INSERT INTO `beneficiaries` (`id`, `name`, `age`, `hometown`, `program`, `destination`, `year_sent`, `institution`, `study_field`, `photo`, `short_story`, `full_story`, `quote`, `featured`, `visible`, `display_order`) VALUES ('ben-8', 'Marthaline M. Cooper', '19', 'Monrovia, Liberia', 'rwanda', 'Kigali, Rwanda', '2024', 'University of Lay Adventist of Kigali (UNILAK)', 'Law', 'https://nemvhsacmwolcagmyusa.databasepad.com/storage/v1/object/public/rvif-media/beneficiaries/1779298269676-6h50qcs3wk.jpg', 'Former student body president now studying Law in Rwanda.', 'My name is Marthaline M. Cooper. I graduated from Haweh Academy High School in Liberia, where I served as the student body president. Through RVIF''s support, I am now pursuing Law at UNILAK in Rwanda.', 'Justice delayed is justice denied, but education makes justice possible.', 0, 1, 8);
INSERT INTO `beneficiaries` (`id`, `name`, `age`, `hometown`, `program`, `destination`, `year_sent`, `institution`, `study_field`, `photo`, `short_story`, `full_story`, `quote`, `featured`, `visible`, `display_order`) VALUES ('ben-9', 'Amb. Josephus B. Gbalea Jr', '22', 'Senlay Nimba, Liberia', 'india', 'New Delhi, India', '2024', 'Sharda University', 'Marketing Management', 'https://nemvhsacmwolcagmyusa.databasepad.com/storage/v1/object/public/rvif-media/beneficiaries/1779298289458-blkk9daz87g.jpg', 'RVIF scholarship recipient studying Marketing Management in India.', 'I''m Josephus B Gbalea Jr, a beneficiary of the Royal Village International Foundation scholarship studying Marketing Management at Sharda University, India. I plan to use my knowledge to contribute to Liberia''s economic development.', 'Marketing is about connecting people with opportunities.', 0, 1, 9);

-- Seeding team_members
INSERT INTO `team_members` (`id`, `name`, `role`, `bio`, `photo`, `display_order`, `visible`) VALUES ('t1', 'Queen Georgia T. Nuahn', 'Founder & Executive Director', 'A visionary leader dedicated to transforming lives through education and empowerment across Africa. Queen Georgia founded RVIF with a mission to break the cycle of poverty by giving young Africans access to world-class education and opportunities.', 'https://d64gsuwffb70l.cloudfront.net/6a0c48e7482add8d9312f354_1779190027990_b77df333.png', 1, 1);
INSERT INTO `team_members` (`id`, `name`, `role`, `bio`, `photo`, `display_order`, `visible`) VALUES ('t2', 'King James Ruzill', 'Co-Founder', 'Co-founder of Royal Village International Foundation, working alongside Queen Georgia to bring hope, opportunity, and dignity to underserved communities. A passionate advocate for youth empowerment.', 'https://d64gsuwffb70l.cloudfront.net/6a0c48e7482add8d9312f354_1779190179484_03d62da0.jpg', 2, 1);
INSERT INTO `team_members` (`id`, `name`, `role`, `bio`, `photo`, `display_order`, `visible`) VALUES ('t3', 'Clarice Logan', 'Chief Executive Officer (CEO)', 'Leading the day-to-day operations of RVIF with strategic vision and unwavering commitment. Clarice brings expertise in nonprofit management and a deep passion for educational equity.', 'https://d64gsuwffb70l.cloudfront.net/6a0c48e7482add8d9312f354_1779190193721_84a709be.jpg', 3, 1);

-- Seeding programs
INSERT INTO `programs` (`id`, `title`, `description`, `icon`, `image`, `display_order`, `visible`) VALUES ('p2', 'Vocational Training', 'Hands-on training programs equipping youth with practical skills in technology, healthcare, business, and the trades.', 'Wrench', 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?w=800', 2, 1);
INSERT INTO `programs` (`id`, `title`, `description`, `icon`, `image`, `display_order`, `visible`) VALUES ('p3', 'Community Outreach', 'Direct support to families in need including food, shelter, and emergency assistance across Liberia and West Africa.', 'Heart', 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=800', 3, 1);
INSERT INTO `programs` (`id`, `title`, `description`, `icon`, `image`, `display_order`, `visible`) VALUES ('p4', 'Women Empowerment', 'Programs focused on empowering girls and women through education, mentorship, and economic opportunity.', 'Sparkles', 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?w=800', 4, 1);
INSERT INTO `programs` (`id`, `title`, `description`, `icon`, `image`, `display_order`, `visible`) VALUES ('p1', 'Scholarship Program', 'Full scholarships for young Africans to pursue higher education in Rwanda, India, and beyond. Covering tuition, accommodation, and living expenses.', 'GraduationCap', 'https://nemvhsacmwolcagmyusa.databasepad.com/storage/v1/object/public/rvif-media/programs/1779299665287-33h2v58dnb.jpg', 1, 1);

-- Seeding blog_posts
INSERT INTO `blog_posts` (`id`, `title`, `excerpt`, `content`, `author`, `image`, `published`, `created_at`) VALUES ('b1', 'Sending Hope: 9 New Scholars Begin Their Journey', 'In 2024, RVIF proudly sent nine new scholars to universities across Rwanda, India, and Liberia.', 'In 2024, RVIF proudly sent nine new scholars to universities across Rwanda, India, and Liberia. Each story is a testament to the power of education and the generosity of our supporters.', 'Queen Georgia T. Nuahn', 'https://nemvhsacmwolcagmyusa.databasepad.com/storage/v1/object/public/rvif-media/blog/1779298332042-npfttmmc07a.jpg', 1, '2026-05-19T11:33:40.467Z');
INSERT INTO `blog_posts` (`id`, `title`, `excerpt`, `content`, `author`, `image`, `published`, `created_at`) VALUES ('b3', 'Stories from Kigali: Our Scholars Speak', 'Hear from our scholars in Rwanda about their daily lives, studies, and dreams.', 'Hear from our scholars in Rwanda about their daily lives, studies, and dreams for the future of their countries.', 'King James Ruzill', 'https://nemvhsacmwolcagmyusa.databasepad.com/storage/v1/object/public/rvif-media/blog/1779298354565-kxglh1yy6si.jpg', 1, '2026-05-19T11:33:40.467Z');
INSERT INTO `blog_posts` (`id`, `title`, `excerpt`, `content`, `author`, `image`, `published`, `created_at`) VALUES ('b2', 'Why Education Changes Everything', 'Education is the most powerful weapon you can use to change the world.', 'Education is the most powerful weapon you can use to change the world. At RVIF, we believe every child deserves the chance to learn, dream, and achieve.', 'Clarice Logan', 'https://nemvhsacmwolcagmyusa.databasepad.com/storage/v1/object/public/rvif-media/blog/1779298397367-21jwqxfmnc4.jpg', 1, '2026-05-19T11:33:40.467Z');

-- Seeding blog_gallery (sample gallery images for testing)
INSERT INTO `blog_gallery` (`id`, `blog_post_id`, `image_url`, `caption`, `display_order`, `created_at`) VALUES 
('g1', 'b1', 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=800&q=80', 'Students celebrating their scholarship awards', 1, '2026-05-19T11:35:00.000Z'),
('g2', 'b1', 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=800&q=80', 'Departure ceremony at the airport', 2, '2026-05-19T11:35:00.000Z'),
('g3', 'b1', 'https://images.unsplash.com/photo-1571260899304-425eee4c7efc?w=800&q=80', 'New scholars with their families', 3, '2026-05-19T11:35:00.000Z'),
('g4', 'b1', 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800&q=80', 'Orientation session before departure', 4, '2026-05-19T11:35:00.000Z'),
('g5', 'b1', 'https://images.unsplash.com/photo-1497486751825-1233686d5d80?w=800&q=80', 'Scholarship documents signing ceremony', 5, '2026-05-19T11:35:00.000Z'),
('g6', 'b3', 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?w=800&q=80', 'Students in their dormitory in Kigali', 1, '2026-05-19T11:36:00.000Z'),
('g7', 'b3', 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800&q=80', 'Study group session at the university', 2, '2026-05-19T11:36:00.000Z'),
('g8', 'b3', 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=800&q=80', 'Campus life in Rwanda', 3, '2026-05-19T11:36:00.000Z'),
('g9', 'b2', 'https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?w=800&q=80', 'Children in a classroom setting', 1, '2026-05-19T11:37:00.000Z'),
('g10', 'b2', 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=800&q=80', 'Educational materials and books', 2, '2026-05-19T11:37:00.000Z'),
('g11', 'b2', 'https://images.unsplash.com/photo-1544717297-fa95b6ee9643?w=800&q=80', 'Students engaged in learning', 3, '2026-05-19T11:37:00.000Z');

-- Seeding partners
INSERT INTO `partners` (`id`, `name`, `country`, `category`, `icon`, `color`, `display_order`) VALUES
('mku', 'Mount Kigali University', 'Rwanda', 'University', 'graduation-cap', '#7e22ce', 1),
('unilak', 'UNILAK', 'Rwanda', 'University', 'building', '#d97706', 2),
('sharda', 'Sharda University', 'India', 'University', 'book-open', '#059669', 3),
('nurah', 'NURAH KUDEE Institute', 'Liberia', 'Training Institute', 'award', '#dc2626', 4),
('gov_lib', 'Government of Liberia', 'Liberia', 'Government', 'landmark', '#0891b2', 5),
('moe_lib', 'Ministry of Education', 'Liberia', 'Government', 'school', '#7c3aed', 6),
('faith_net', 'Faith Partners Network', 'International', 'NGO', 'heart', '#db2777', 7),
('donors', 'Community Donors Circle', 'Global', 'Donors', 'users', '#ea580c', 8);

-- Seeding founder_info
INSERT INTO `founder_info` (`id`, `name`, `title`, `profession`, `photo`, `short_bio`, `full_story`, `quote`, `achievements`, `visible`) VALUES 
('founder-1', 'Queen Georgia T. Nuahn', 'Founder & Visionary', 'Registered Nurse, USA', '/ROYALCMS/uploads/founder.jpg', 'A registered nurse in America who left Liberia at age 8 and founded RVIF to give back to African youth.', 'Our founder''s story is one of resilience, determination, and an unwavering commitment to giving back. At just 8 years old, she left her small village in Liberia, embarking on a journey that would eventually lead her to become a registered nurse in America.\n\nGrowing up in rural Liberia, she witnessed firsthand the challenges that young people face when access to quality education and healthcare is limited. Despite the obstacles, her family''s sacrifice and her own determination opened doors to opportunities that seemed impossible.\n\nAfter years of hard work and dedication, she achieved her dream of becoming a registered nurse in the United States. But success didn''t make her forget her roots. Instead, it fueled her passion to create pathways for other young Africans to pursue their dreams.\n\nIn 2018, she founded the Royal Village International Foundation with a simple but powerful mission: to ensure that no child''s potential is limited by their circumstances. Today, she continues to serve as both a healthcare professional and a champion for African youth education.', 'Every child deserves the chance I was given. Through RVIF, we''re not just changing individual lives — we''re building the future leaders of Africa.', 'Registered Nurse in the United States\nFounder of Royal Village International Foundation (2018)\nChampion for African Youth Education\nAdvocate for Healthcare Access in Rural Communities', 1);

-- Seeding milestones
INSERT INTO `milestones` (`id`, `year`, `title`, `description`, `display_order`, `visible`) VALUES
('m1', '2018', 'Foundation Established', 'Royal Village International Foundation was founded with a mission to empower African youth through education.', 1, 1),
('m2', '2019', 'First Scholars Sent', 'RVIF sent its first cohort of scholarship recipients to pursue higher education in Rwanda.', 2, 1),
('m3', '2020', 'Expanded to India', 'Partnership with Sharda University established, expanding educational opportunities to India.', 3, 1),
('m4', '2021', '100+ Students Reached', 'Milestone of 100 students reached through our scholarship and vocational programs.', 4, 1),
('m5', '2022', 'Women Empowerment Program', 'Launched dedicated programs for women and girls education across Africa.', 5, 1),
('m6', '2023', 'Vocational Training Center', 'Opened our first vocational training center in Monrovia, Liberia.', 6, 1),
('m7', '2024', '50+ University Graduates', 'Celebrated over 50 scholars who have completed their university degrees.', 7, 1);
