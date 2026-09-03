CREATE TABLE IF NOT EXISTS blog_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL,
    description VARCHAR(500) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    UNIQUE KEY uq_blog_categories_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS blog_posts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    content LONGTEXT NULL,
    excerpt VARCHAR(500) NULL,
    featured_media_id BIGINT UNSIGNED NULL,
    status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    meta_title VARCHAR(255) NULL,
    meta_description VARCHAR(500) NULL,
    meta_keywords VARCHAR(500) NULL,
    og_image_media_id BIGINT UNSIGNED NULL,
    author_id BIGINT UNSIGNED NULL,
    published_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    UNIQUE KEY uq_blog_posts_slug (slug),
    KEY idx_blog_posts_status (status),
    CONSTRAINT fk_blog_posts_category FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL,
    CONSTRAINT fk_blog_posts_featured_media FOREIGN KEY (featured_media_id) REFERENCES media(id) ON DELETE SET NULL,
    CONSTRAINT fk_blog_posts_og_media FOREIGN KEY (og_image_media_id) REFERENCES media(id) ON DELETE SET NULL,
    CONSTRAINT fk_blog_posts_author FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
