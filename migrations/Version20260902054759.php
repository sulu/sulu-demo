<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Creates the Sulu 3.0 content storage schema on top of an existing Sulu 2.6 database.
 */
final class Version20260902054759 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create the Sulu 3.0 content storage tables and drop the tables removed with 3.0';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE ar_article_dimension_content_additional_webspaces (name VARCHAR(64) NOT NULL, id INT AUTO_INCREMENT NOT NULL, article_dimension_content_id INT NOT NULL, INDEX IDX_3F9F33F37C1747D1 (article_dimension_content_id), INDEX idx_article_additional_webspace (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE ar_article_dimension_contents (title VARCHAR(191) DEFAULT NULL, customizeWebspaceSettings TINYINT NOT NULL, id INT AUTO_INCREMENT NOT NULL, stage VARCHAR(15) NOT NULL, locale VARCHAR(15) DEFAULT NULL, ghostLocale VARCHAR(15) DEFAULT NULL, availableLocales JSON DEFAULT NULL, version INT NOT NULL, shadowLocale VARCHAR(15) DEFAULT NULL, shadowLocales JSON DEFAULT NULL, templateKey VARCHAR(64) DEFAULT NULL, templateData JSON NOT NULL, seoData JSON NOT NULL, seoNoIndex TINYINT NOT NULL, seoNoFollow TINYINT NOT NULL, seoHideInSitemap TINYINT NOT NULL, excerptData JSON NOT NULL, excerptSegment VARCHAR(255) DEFAULT NULL, mainWebspace VARCHAR(255) DEFAULT NULL, authored DATETIME DEFAULT NULL, lastModified DATETIME DEFAULT NULL, workflowPlace VARCHAR(31) DEFAULT NULL, workflowPublished DATETIME DEFAULT NULL, created DATETIME NOT NULL, changed DATETIME NOT NULL, route_id INT DEFAULT NULL, articleUuid VARCHAR(36) NOT NULL, author_id INT DEFAULT NULL, idUsersCreator INT DEFAULT NULL, idUsersChanger INT DEFAULT NULL, INDEX IDX_5674F7BF34ECB4E6 (route_id), INDEX IDX_5674F7BFAE39C518 (articleUuid), INDEX IDX_5674F7BFF675F31B (author_id), INDEX IDX_5674F7BFDBF11E1D (idUsersCreator), INDEX IDX_5674F7BF30D07CD5 (idUsersChanger), INDEX idx_ar_article_dimension_contents_dimension (stage, locale), INDEX idx_ar_article_dimension_contents_locale (locale), INDEX idx_ar_article_dimension_contents_stage (stage), INDEX idx_ar_article_dimension_contents_version (version), INDEX idx_ar_article_dimension_contents_stage_version_locale (stage, version, locale), INDEX idx_ar_article_dimension_contents_resource_lookup (articleUuid, stage, version, locale, ghostLocale), INDEX idx_ar_article_dimension_contents_template_key (templateKey), INDEX idx_ar_article_dimension_contents_resource_template_lookup (articleUuid, stage, version, locale, templateKey), INDEX idx_ar_article_dimension_contents_workflow_place (workflowPlace), INDEX idx_ar_article_dimension_contents_workflow_published (workflowPublished), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE ar_article_dimension_content_excerpt_tags (article_dimension_content_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_B45854027C1747D1 (article_dimension_content_id), INDEX IDX_B4585402BAD26311 (tag_id), PRIMARY KEY (article_dimension_content_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE ar_article_dimension_content_excerpt_categories (article_dimension_content_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_971AE52D7C1747D1 (article_dimension_content_id), INDEX IDX_971AE52D12469DE2 (category_id), PRIMARY KEY (article_dimension_content_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE ar_articles (uuid VARCHAR(36) NOT NULL, created DATETIME NOT NULL, changed DATETIME NOT NULL, idUsersCreator INT DEFAULT NULL, idUsersChanger INT DEFAULT NULL, INDEX IDX_7F75CD17DBF11E1D (idUsersCreator), INDEX IDX_7F75CD1730D07CD5 (idUsersChanger), PRIMARY KEY (uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE cu_custom_url (title VARCHAR(255) NOT NULL, published TINYINT NOT NULL, base_domain VARCHAR(255) NOT NULL, webspace VARCHAR(255) NOT NULL, domain_parts JSON NOT NULL, target_document VARCHAR(255) DEFAULT NULL, target_locale VARCHAR(255) NOT NULL, canonical TINYINT NOT NULL, redirect TINYINT NOT NULL, no_follow TINYINT NOT NULL, no_index TINYINT NOT NULL, uuid VARCHAR(36) NOT NULL, created DATETIME NOT NULL, changed DATETIME NOT NULL, idUsersCreator INT DEFAULT NULL, idUsersChanger INT DEFAULT NULL, UNIQUE INDEX UNIQ_51A7F98D2B36786B (title), INDEX IDX_51A7F98DDBF11E1D (idUsersCreator), INDEX IDX_51A7F98D30D07CD5 (idUsersChanger), INDEX IDX_51A7F98DB055BCD4 (webspace), PRIMARY KEY (uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE cu_custom_url_route (path VARCHAR(255) NOT NULL, history TINYINT DEFAULT 0 NOT NULL, uuid VARCHAR(36) NOT NULL, created DATETIME NOT NULL, changed DATETIME NOT NULL, customUrl VARCHAR(36) NOT NULL, target_route_uuid VARCHAR(36) DEFAULT NULL, INDEX IDX_D2349CF4CB30A644 (customUrl), INDEX IDX_D2349CF44ED689B2 (target_route_uuid), INDEX custom_url_route_history_idx (history), UNIQUE INDEX cu_custom_url_route_unique (path), PRIMARY KEY (uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE pa_page_dimension_content_navigation_contexts (name VARCHAR(64) NOT NULL, id INT AUTO_INCREMENT NOT NULL, page_dimension_content_id INT NOT NULL, INDEX IDX_4C5FD8F767C2CFD5 (page_dimension_content_id), INDEX idx_page_navigation_context (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE pa_page_dimension_contents (title VARCHAR(191) DEFAULT NULL, id INT AUTO_INCREMENT NOT NULL, stage VARCHAR(15) NOT NULL, locale VARCHAR(15) DEFAULT NULL, ghostLocale VARCHAR(15) DEFAULT NULL, availableLocales JSON DEFAULT NULL, version INT NOT NULL, shadowLocale VARCHAR(15) DEFAULT NULL, shadowLocales JSON DEFAULT NULL, templateKey VARCHAR(64) DEFAULT NULL, templateData JSON NOT NULL, seoData JSON NOT NULL, seoNoIndex TINYINT NOT NULL, seoNoFollow TINYINT NOT NULL, seoHideInSitemap TINYINT NOT NULL, excerptData JSON NOT NULL, excerptSegment VARCHAR(255) DEFAULT NULL, authored DATETIME DEFAULT NULL, lastModified DATETIME DEFAULT NULL, workflowPlace VARCHAR(31) DEFAULT NULL, workflowPublished DATETIME DEFAULT NULL, linkProvider VARCHAR(32) DEFAULT NULL, linkData JSON DEFAULT NULL, created DATETIME NOT NULL, changed DATETIME NOT NULL, route_id INT DEFAULT NULL, pageUuid VARCHAR(36) NOT NULL, author_id INT DEFAULT NULL, idUsersCreator INT DEFAULT NULL, idUsersChanger INT DEFAULT NULL, INDEX IDX_209A42C034ECB4E6 (route_id), INDEX IDX_209A42C0F099EEF3 (pageUuid), INDEX IDX_209A42C0F675F31B (author_id), INDEX IDX_209A42C0DBF11E1D (idUsersCreator), INDEX IDX_209A42C030D07CD5 (idUsersChanger), INDEX idx_pa_page_dimension_contents_dimension (stage, locale), INDEX idx_pa_page_dimension_contents_locale (locale), INDEX idx_pa_page_dimension_contents_stage (stage), INDEX idx_pa_page_dimension_contents_version (version), INDEX idx_pa_page_dimension_contents_stage_version_locale (stage, version, locale), INDEX idx_pa_page_dimension_contents_resource_lookup (pageUuid, stage, version, locale, ghostLocale), INDEX idx_pa_page_dimension_contents_template_key (templateKey), INDEX idx_pa_page_dimension_contents_resource_template_lookup (pageUuid, stage, version, locale, templateKey), INDEX idx_pa_page_dimension_contents_workflow_place (workflowPlace), INDEX idx_pa_page_dimension_contents_workflow_published (workflowPublished), INDEX idx_pa_page_dimension_contents_link_provider (linkProvider), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE pa_page_dimension_content_excerpt_tags (page_dimension_content_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_66C81FDB67C2CFD5 (page_dimension_content_id), INDEX IDX_66C81FDBBAD26311 (tag_id), PRIMARY KEY (page_dimension_content_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE pa_page_dimension_content_excerpt_categories (page_dimension_content_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_BE45C16867C2CFD5 (page_dimension_content_id), INDEX IDX_BE45C16812469DE2 (category_id), PRIMARY KEY (page_dimension_content_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE pa_pages (webspaceKey VARCHAR(64) NOT NULL, lft INT NOT NULL, rgt INT NOT NULL, depth INT NOT NULL, uuid VARCHAR(36) NOT NULL, created DATETIME NOT NULL, changed DATETIME NOT NULL, parent_id VARCHAR(36) DEFAULT NULL, idUsersCreator INT DEFAULT NULL, idUsersChanger INT DEFAULT NULL, INDEX IDX_FF3DA1E2727ACA70 (parent_id), INDEX IDX_FF3DA1E2DBF11E1D (idUsersCreator), INDEX IDX_FF3DA1E230D07CD5 (idUsersChanger), PRIMARY KEY (uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE ro_routes (webspace VARCHAR(64) DEFAULT NULL, locale VARCHAR(15) NOT NULL, slug VARCHAR(255) NOT NULL, resource_key VARCHAR(32) NOT NULL, resource_id VARCHAR(70) NOT NULL, id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, INDEX IDX_671DB7A4727ACA70 (parent_id), INDEX ro_routes_resource_idx (locale, resource_key, resource_id), UNIQUE INDEX ro_routes_unique (webspace, locale, slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE sn_snippet_area (webspace_key VARCHAR(255) NOT NULL, area_key VARCHAR(255) NOT NULL, uuid VARCHAR(255) NOT NULL, idSnippet VARCHAR(36) DEFAULT NULL, INDEX IDX_8C978EE186A5E727 (idSnippet), PRIMARY KEY (uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE sn_snippet_dimension_contents (title VARCHAR(191) DEFAULT NULL, id INT AUTO_INCREMENT NOT NULL, stage VARCHAR(15) NOT NULL, locale VARCHAR(15) DEFAULT NULL, ghostLocale VARCHAR(15) DEFAULT NULL, availableLocales JSON DEFAULT NULL, version INT NOT NULL, templateKey VARCHAR(64) DEFAULT NULL, templateData JSON NOT NULL, excerptSegment VARCHAR(255) DEFAULT NULL, workflowPlace VARCHAR(31) DEFAULT NULL, workflowPublished DATETIME DEFAULT NULL, created DATETIME NOT NULL, changed DATETIME NOT NULL, snippetUuid VARCHAR(36) NOT NULL, idUsersCreator INT DEFAULT NULL, idUsersChanger INT DEFAULT NULL, INDEX IDX_46D6814477F33FFB (snippetUuid), INDEX IDX_46D68144DBF11E1D (idUsersCreator), INDEX IDX_46D6814430D07CD5 (idUsersChanger), INDEX idx_sn_snippet_dimension_contents_dimension (stage, locale), INDEX idx_sn_snippet_dimension_contents_locale (locale), INDEX idx_sn_snippet_dimension_contents_stage (stage), INDEX idx_sn_snippet_dimension_contents_version (version), INDEX idx_sn_snippet_dimension_contents_stage_version_locale (stage, version, locale), INDEX idx_sn_snippet_dimension_contents_resource_lookup (snippetUuid, stage, version, locale, ghostLocale), INDEX idx_sn_snippet_dimension_contents_template_key (templateKey), INDEX idx_sn_snippet_dimension_contents_resource_template_lookup (snippetUuid, stage, version, locale, templateKey), INDEX idx_sn_snippet_dimension_contents_workflow_place (workflowPlace), INDEX idx_sn_snippet_dimension_contents_workflow_published (workflowPublished), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE sn_snippet_dimension_content_excerpt_tags (snippet_dimension_content_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_96BD1E357891499D (snippet_dimension_content_id), INDEX IDX_96BD1E35BAD26311 (tag_id), PRIMARY KEY (snippet_dimension_content_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE sn_snippet_dimension_content_excerpt_categories (snippet_dimension_content_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_464EB1547891499D (snippet_dimension_content_id), INDEX IDX_464EB15412469DE2 (category_id), PRIMARY KEY (snippet_dimension_content_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE sn_snippets (uuid VARCHAR(36) NOT NULL, created DATETIME NOT NULL, changed DATETIME NOT NULL, idUsersCreator INT DEFAULT NULL, idUsersChanger INT DEFAULT NULL, INDEX IDX_E68115CFDBF11E1D (idUsersCreator), INDEX IDX_E68115CF30D07CD5 (idUsersChanger), PRIMARY KEY (uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE ar_article_dimension_content_additional_webspaces ADD CONSTRAINT FK_3F9F33F37C1747D1 FOREIGN KEY (article_dimension_content_id) REFERENCES ar_article_dimension_contents (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ar_article_dimension_contents ADD CONSTRAINT FK_5674F7BF34ECB4E6 FOREIGN KEY (route_id) REFERENCES ro_routes (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ar_article_dimension_contents ADD CONSTRAINT FK_5674F7BFAE39C518 FOREIGN KEY (articleUuid) REFERENCES ar_articles (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ar_article_dimension_contents ADD CONSTRAINT FK_5674F7BFF675F31B FOREIGN KEY (author_id) REFERENCES co_contacts (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ar_article_dimension_contents ADD CONSTRAINT FK_5674F7BFDBF11E1D FOREIGN KEY (idUsersCreator) REFERENCES se_users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ar_article_dimension_contents ADD CONSTRAINT FK_5674F7BF30D07CD5 FOREIGN KEY (idUsersChanger) REFERENCES se_users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ar_article_dimension_content_excerpt_tags ADD CONSTRAINT FK_B45854027C1747D1 FOREIGN KEY (article_dimension_content_id) REFERENCES ar_article_dimension_contents (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ar_article_dimension_content_excerpt_tags ADD CONSTRAINT FK_B4585402BAD26311 FOREIGN KEY (tag_id) REFERENCES ta_tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ar_article_dimension_content_excerpt_categories ADD CONSTRAINT FK_971AE52D7C1747D1 FOREIGN KEY (article_dimension_content_id) REFERENCES ar_article_dimension_contents (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ar_article_dimension_content_excerpt_categories ADD CONSTRAINT FK_971AE52D12469DE2 FOREIGN KEY (category_id) REFERENCES ca_categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ar_articles ADD CONSTRAINT FK_7F75CD17DBF11E1D FOREIGN KEY (idUsersCreator) REFERENCES se_users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ar_articles ADD CONSTRAINT FK_7F75CD1730D07CD5 FOREIGN KEY (idUsersChanger) REFERENCES se_users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE cu_custom_url ADD CONSTRAINT FK_51A7F98DDBF11E1D FOREIGN KEY (idUsersCreator) REFERENCES se_users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE cu_custom_url ADD CONSTRAINT FK_51A7F98D30D07CD5 FOREIGN KEY (idUsersChanger) REFERENCES se_users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE cu_custom_url_route ADD CONSTRAINT FK_D2349CF4CB30A644 FOREIGN KEY (customUrl) REFERENCES cu_custom_url (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cu_custom_url_route ADD CONSTRAINT FK_D2349CF44ED689B2 FOREIGN KEY (target_route_uuid) REFERENCES cu_custom_url_route (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pa_page_dimension_content_navigation_contexts ADD CONSTRAINT FK_4C5FD8F767C2CFD5 FOREIGN KEY (page_dimension_content_id) REFERENCES pa_page_dimension_contents (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pa_page_dimension_contents ADD CONSTRAINT FK_209A42C034ECB4E6 FOREIGN KEY (route_id) REFERENCES ro_routes (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE pa_page_dimension_contents ADD CONSTRAINT FK_209A42C0F099EEF3 FOREIGN KEY (pageUuid) REFERENCES pa_pages (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pa_page_dimension_contents ADD CONSTRAINT FK_209A42C0F675F31B FOREIGN KEY (author_id) REFERENCES co_contacts (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE pa_page_dimension_contents ADD CONSTRAINT FK_209A42C0DBF11E1D FOREIGN KEY (idUsersCreator) REFERENCES se_users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE pa_page_dimension_contents ADD CONSTRAINT FK_209A42C030D07CD5 FOREIGN KEY (idUsersChanger) REFERENCES se_users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE pa_page_dimension_content_excerpt_tags ADD CONSTRAINT FK_66C81FDB67C2CFD5 FOREIGN KEY (page_dimension_content_id) REFERENCES pa_page_dimension_contents (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pa_page_dimension_content_excerpt_tags ADD CONSTRAINT FK_66C81FDBBAD26311 FOREIGN KEY (tag_id) REFERENCES ta_tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pa_page_dimension_content_excerpt_categories ADD CONSTRAINT FK_BE45C16867C2CFD5 FOREIGN KEY (page_dimension_content_id) REFERENCES pa_page_dimension_contents (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pa_page_dimension_content_excerpt_categories ADD CONSTRAINT FK_BE45C16812469DE2 FOREIGN KEY (category_id) REFERENCES ca_categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pa_pages ADD CONSTRAINT FK_FF3DA1E2727ACA70 FOREIGN KEY (parent_id) REFERENCES pa_pages (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pa_pages ADD CONSTRAINT FK_FF3DA1E2DBF11E1D FOREIGN KEY (idUsersCreator) REFERENCES se_users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE pa_pages ADD CONSTRAINT FK_FF3DA1E230D07CD5 FOREIGN KEY (idUsersChanger) REFERENCES se_users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ro_routes ADD CONSTRAINT FK_671DB7A4727ACA70 FOREIGN KEY (parent_id) REFERENCES ro_routes (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE sn_snippet_area ADD CONSTRAINT FK_8C978EE186A5E727 FOREIGN KEY (idSnippet) REFERENCES sn_snippets (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sn_snippet_dimension_contents ADD CONSTRAINT FK_46D6814477F33FFB FOREIGN KEY (snippetUuid) REFERENCES sn_snippets (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sn_snippet_dimension_contents ADD CONSTRAINT FK_46D68144DBF11E1D FOREIGN KEY (idUsersCreator) REFERENCES se_users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE sn_snippet_dimension_contents ADD CONSTRAINT FK_46D6814430D07CD5 FOREIGN KEY (idUsersChanger) REFERENCES se_users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE sn_snippet_dimension_content_excerpt_tags ADD CONSTRAINT FK_96BD1E357891499D FOREIGN KEY (snippet_dimension_content_id) REFERENCES sn_snippet_dimension_contents (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sn_snippet_dimension_content_excerpt_tags ADD CONSTRAINT FK_96BD1E35BAD26311 FOREIGN KEY (tag_id) REFERENCES ta_tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sn_snippet_dimension_content_excerpt_categories ADD CONSTRAINT FK_464EB1547891499D FOREIGN KEY (snippet_dimension_content_id) REFERENCES sn_snippet_dimension_contents (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sn_snippet_dimension_content_excerpt_categories ADD CONSTRAINT FK_464EB15412469DE2 FOREIGN KEY (category_id) REFERENCES ca_categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sn_snippets ADD CONSTRAINT FK_E68115CFDBF11E1D FOREIGN KEY (idUsersCreator) REFERENCES se_users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE sn_snippets ADD CONSTRAINT FK_E68115CF30D07CD5 FOREIGN KEY (idUsersChanger) REFERENCES se_users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ca_category_meta DROP FOREIGN KEY `FK_2575BBB0B8075882`');
        $this->addSql('ALTER TABLE me_file_version_content_languages DROP FOREIGN KEY `FK_F3FD652C911ADE33`');
        $this->addSql('ALTER TABLE me_file_version_publish_languages DROP FOREIGN KEY `FK_195DAB3C911ADE33`');
        $this->addSql('ALTER TABLE se_group_roles DROP FOREIGN KEY `FK_9713F725937C91EA`');
        $this->addSql('ALTER TABLE se_group_roles DROP FOREIGN KEY `FK_9713F725A1FA6DDA`');
        $this->addSql('ALTER TABLE se_groups DROP FOREIGN KEY `FK_231E44EC30D07CD5`');
        $this->addSql('ALTER TABLE se_groups DROP FOREIGN KEY `FK_231E44ECBF274AB0`');
        $this->addSql('ALTER TABLE se_groups DROP FOREIGN KEY `FK_231E44ECDBF11E1D`');
        $this->addSql('ALTER TABLE se_user_groups DROP FOREIGN KEY `FK_E43ED0C8347E6F4`');
        $this->addSql('ALTER TABLE se_user_groups DROP FOREIGN KEY `FK_E43ED0C8937C91EA`');
        $this->addSql('ALTER TABLE ac_activities CHANGE timestamp timestamp DATETIME NOT NULL');
        $this->addSql('ALTER TABLE au_task DROP INDEX IDX_223B587E8DB60186, ADD UNIQUE INDEX UNIQ_223B587E8DB60186 (task_id)');
        $this->addSql('ALTER TABLE au_task DROP FOREIGN KEY `FK_223B587E8DB60186`');
        $this->addSql('ALTER TABLE au_task CHANGE id id CHAR(36) NOT NULL, CHANGE task_id task_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE au_task ADD CONSTRAINT FK_223B587E8DB60186 FOREIGN KEY (task_id) REFERENCES ta_tasks (uuid) ON DELETE SET NULL');
        // the MediaType entity is gone in 3.0, its name moves into me_media.type,
        // so the value has to be carried over before the table is dropped
        $this->addSql('ALTER TABLE me_media ADD COLUMN type VARCHAR(10) DEFAULT NULL');
        $this->addSql('UPDATE me_media m JOIN me_media_types mt ON mt.id = m.idMediaTypes SET m.type = mt.name');
        $this->addSql('ALTER TABLE me_media DROP FOREIGN KEY `FK_A694E57284671716`');
        $this->addSql('DROP INDEX IDX_A694E57284671716 ON me_media');
        $this->addSql('ALTER TABLE me_media MODIFY type VARCHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE me_media DROP idMediaTypes');
        $this->addSql('CREATE INDEX IDX_A694E5728CDE5729 ON me_media (type)');
        $this->addSql('ALTER TABLE pr_preview_links CHANGE lastVisit lastVisit DATETIME DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_5CEC3EEAE25D857EC242628A1FA6DDA ON se_permissions');
        $this->addSql('ALTER TABLE se_permissions DROP module');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5CEC3EEAE25D857EA1FA6DDA ON se_permissions (context, idRoles)');
        $this->addSql('ALTER TABLE se_roles DROP FOREIGN KEY `FK_13B749A0D02106C0`');
        $this->addSql('DROP TABLE ca_category_meta');
        $this->addSql('DROP TABLE me_file_version_content_languages');
        $this->addSql('DROP TABLE me_file_version_publish_languages');
        $this->addSql('DROP TABLE se_group_roles');
        $this->addSql('DROP TABLE se_groups');
        $this->addSql('DROP TABLE se_security_types');
        $this->addSql('DROP TABLE se_user_groups');
        $this->addSql('DROP TABLE me_media_types');
        $this->addSql('DROP INDEX IDX_13B749A0D02106C0 ON se_roles');
        $this->addSql('ALTER TABLE se_roles DROP idSecurityTypes');
        $this->addSql('ALTER TABLE se_users CHANGE idContacts idContacts INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ta_task_executions CHANGE uuid uuid CHAR(36) NOT NULL, CHANGE task_id task_id CHAR(36) DEFAULT NULL, CHANGE workload workload LONGTEXT NOT NULL, CHANGE result result LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE ta_tasks CHANGE uuid uuid CHAR(36) NOT NULL, CHANGE workload workload LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE tr_trash_items CHANGE storeTimestamp storeTimestamp DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE ca_category_meta (id INT AUTO_INCREMENT NOT NULL, meta_key VARCHAR(45) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, value VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, locale VARCHAR(15) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, idCategories INT NOT NULL, INDEX IDX_2575BBB0B8075882 (idCategories), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE me_file_version_content_languages (id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(15) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, idFileVersions INT DEFAULT NULL, INDEX IDX_F3FD652C911ADE33 (idFileVersions), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE me_file_version_publish_languages (id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(15) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, idFileVersions INT DEFAULT NULL, INDEX IDX_195DAB3C911ADE33 (idFileVersions), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE me_media_types (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(191) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_9A01D6E85E237E06 (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE se_group_roles (idGroups INT NOT NULL, idRoles INT NOT NULL, INDEX IDX_9713F725937C91EA (idGroups), INDEX IDX_9713F725A1FA6DDA (idRoles), PRIMARY KEY (idGroups, idRoles)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE se_groups (id INT AUTO_INCREMENT NOT NULL, lft INT NOT NULL, rgt INT NOT NULL, depth INT NOT NULL, name VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created DATETIME NOT NULL, changed DATETIME NOT NULL, idGroupsParent INT DEFAULT NULL, idUsersCreator INT DEFAULT NULL, idUsersChanger INT DEFAULT NULL, INDEX IDX_231E44EC30D07CD5 (idUsersChanger), INDEX IDX_231E44ECBF274AB0 (idGroupsParent), INDEX IDX_231E44ECDBF11E1D (idUsersCreator), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE se_security_types (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE se_user_groups (id INT AUTO_INCREMENT NOT NULL, locale LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, idGroups INT DEFAULT NULL, idUsers INT DEFAULT NULL, INDEX IDX_E43ED0C8347E6F4 (idUsers), INDEX IDX_E43ED0C8937C91EA (idGroups), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE ca_category_meta ADD CONSTRAINT `FK_2575BBB0B8075882` FOREIGN KEY (idCategories) REFERENCES ca_categories (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE me_file_version_content_languages ADD CONSTRAINT `FK_F3FD652C911ADE33` FOREIGN KEY (idFileVersions) REFERENCES me_file_versions (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE me_file_version_publish_languages ADD CONSTRAINT `FK_195DAB3C911ADE33` FOREIGN KEY (idFileVersions) REFERENCES me_file_versions (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE se_group_roles ADD CONSTRAINT `FK_9713F725937C91EA` FOREIGN KEY (idGroups) REFERENCES se_groups (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE se_group_roles ADD CONSTRAINT `FK_9713F725A1FA6DDA` FOREIGN KEY (idRoles) REFERENCES se_roles (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE se_groups ADD CONSTRAINT `FK_231E44EC30D07CD5` FOREIGN KEY (idUsersChanger) REFERENCES se_users (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('ALTER TABLE se_groups ADD CONSTRAINT `FK_231E44ECBF274AB0` FOREIGN KEY (idGroupsParent) REFERENCES se_groups (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE se_groups ADD CONSTRAINT `FK_231E44ECDBF11E1D` FOREIGN KEY (idUsersCreator) REFERENCES se_users (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('ALTER TABLE se_user_groups ADD CONSTRAINT `FK_E43ED0C8347E6F4` FOREIGN KEY (idUsers) REFERENCES se_users (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE se_user_groups ADD CONSTRAINT `FK_E43ED0C8937C91EA` FOREIGN KEY (idGroups) REFERENCES se_groups (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ar_article_dimension_content_additional_webspaces DROP FOREIGN KEY FK_3F9F33F37C1747D1');
        $this->addSql('ALTER TABLE ar_article_dimension_contents DROP FOREIGN KEY FK_5674F7BF34ECB4E6');
        $this->addSql('ALTER TABLE ar_article_dimension_contents DROP FOREIGN KEY FK_5674F7BFAE39C518');
        $this->addSql('ALTER TABLE ar_article_dimension_contents DROP FOREIGN KEY FK_5674F7BFF675F31B');
        $this->addSql('ALTER TABLE ar_article_dimension_contents DROP FOREIGN KEY FK_5674F7BFDBF11E1D');
        $this->addSql('ALTER TABLE ar_article_dimension_contents DROP FOREIGN KEY FK_5674F7BF30D07CD5');
        $this->addSql('ALTER TABLE ar_article_dimension_content_excerpt_tags DROP FOREIGN KEY FK_B45854027C1747D1');
        $this->addSql('ALTER TABLE ar_article_dimension_content_excerpt_tags DROP FOREIGN KEY FK_B4585402BAD26311');
        $this->addSql('ALTER TABLE ar_article_dimension_content_excerpt_categories DROP FOREIGN KEY FK_971AE52D7C1747D1');
        $this->addSql('ALTER TABLE ar_article_dimension_content_excerpt_categories DROP FOREIGN KEY FK_971AE52D12469DE2');
        $this->addSql('ALTER TABLE ar_articles DROP FOREIGN KEY FK_7F75CD17DBF11E1D');
        $this->addSql('ALTER TABLE ar_articles DROP FOREIGN KEY FK_7F75CD1730D07CD5');
        $this->addSql('ALTER TABLE cu_custom_url DROP FOREIGN KEY FK_51A7F98DDBF11E1D');
        $this->addSql('ALTER TABLE cu_custom_url DROP FOREIGN KEY FK_51A7F98D30D07CD5');
        $this->addSql('ALTER TABLE cu_custom_url_route DROP FOREIGN KEY FK_D2349CF4CB30A644');
        $this->addSql('ALTER TABLE cu_custom_url_route DROP FOREIGN KEY FK_D2349CF44ED689B2');
        $this->addSql('ALTER TABLE pa_page_dimension_content_navigation_contexts DROP FOREIGN KEY FK_4C5FD8F767C2CFD5');
        $this->addSql('ALTER TABLE pa_page_dimension_contents DROP FOREIGN KEY FK_209A42C034ECB4E6');
        $this->addSql('ALTER TABLE pa_page_dimension_contents DROP FOREIGN KEY FK_209A42C0F099EEF3');
        $this->addSql('ALTER TABLE pa_page_dimension_contents DROP FOREIGN KEY FK_209A42C0F675F31B');
        $this->addSql('ALTER TABLE pa_page_dimension_contents DROP FOREIGN KEY FK_209A42C0DBF11E1D');
        $this->addSql('ALTER TABLE pa_page_dimension_contents DROP FOREIGN KEY FK_209A42C030D07CD5');
        $this->addSql('ALTER TABLE pa_page_dimension_content_excerpt_tags DROP FOREIGN KEY FK_66C81FDB67C2CFD5');
        $this->addSql('ALTER TABLE pa_page_dimension_content_excerpt_tags DROP FOREIGN KEY FK_66C81FDBBAD26311');
        $this->addSql('ALTER TABLE pa_page_dimension_content_excerpt_categories DROP FOREIGN KEY FK_BE45C16867C2CFD5');
        $this->addSql('ALTER TABLE pa_page_dimension_content_excerpt_categories DROP FOREIGN KEY FK_BE45C16812469DE2');
        $this->addSql('ALTER TABLE pa_pages DROP FOREIGN KEY FK_FF3DA1E2727ACA70');
        $this->addSql('ALTER TABLE pa_pages DROP FOREIGN KEY FK_FF3DA1E2DBF11E1D');
        $this->addSql('ALTER TABLE pa_pages DROP FOREIGN KEY FK_FF3DA1E230D07CD5');
        $this->addSql('ALTER TABLE ro_routes DROP FOREIGN KEY FK_671DB7A4727ACA70');
        $this->addSql('ALTER TABLE sn_snippet_area DROP FOREIGN KEY FK_8C978EE186A5E727');
        $this->addSql('ALTER TABLE sn_snippet_dimension_contents DROP FOREIGN KEY FK_46D6814477F33FFB');
        $this->addSql('ALTER TABLE sn_snippet_dimension_contents DROP FOREIGN KEY FK_46D68144DBF11E1D');
        $this->addSql('ALTER TABLE sn_snippet_dimension_contents DROP FOREIGN KEY FK_46D6814430D07CD5');
        $this->addSql('ALTER TABLE sn_snippet_dimension_content_excerpt_tags DROP FOREIGN KEY FK_96BD1E357891499D');
        $this->addSql('ALTER TABLE sn_snippet_dimension_content_excerpt_tags DROP FOREIGN KEY FK_96BD1E35BAD26311');
        $this->addSql('ALTER TABLE sn_snippet_dimension_content_excerpt_categories DROP FOREIGN KEY FK_464EB1547891499D');
        $this->addSql('ALTER TABLE sn_snippet_dimension_content_excerpt_categories DROP FOREIGN KEY FK_464EB15412469DE2');
        $this->addSql('ALTER TABLE sn_snippets DROP FOREIGN KEY FK_E68115CFDBF11E1D');
        $this->addSql('ALTER TABLE sn_snippets DROP FOREIGN KEY FK_E68115CF30D07CD5');
        $this->addSql('DROP TABLE ar_article_dimension_content_additional_webspaces');
        $this->addSql('DROP TABLE ar_article_dimension_contents');
        $this->addSql('DROP TABLE ar_article_dimension_content_excerpt_tags');
        $this->addSql('DROP TABLE ar_article_dimension_content_excerpt_categories');
        $this->addSql('DROP TABLE ar_articles');
        $this->addSql('DROP TABLE cu_custom_url');
        $this->addSql('DROP TABLE cu_custom_url_route');
        $this->addSql('DROP TABLE pa_page_dimension_content_navigation_contexts');
        $this->addSql('DROP TABLE pa_page_dimension_contents');
        $this->addSql('DROP TABLE pa_page_dimension_content_excerpt_tags');
        $this->addSql('DROP TABLE pa_page_dimension_content_excerpt_categories');
        $this->addSql('DROP TABLE pa_pages');
        $this->addSql('DROP TABLE ro_routes');
        $this->addSql('DROP TABLE sn_snippet_area');
        $this->addSql('DROP TABLE sn_snippet_dimension_contents');
        $this->addSql('DROP TABLE sn_snippet_dimension_content_excerpt_tags');
        $this->addSql('DROP TABLE sn_snippet_dimension_content_excerpt_categories');
        $this->addSql('DROP TABLE sn_snippets');
        $this->addSql('ALTER TABLE ac_activities CHANGE timestamp timestamp DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE au_task DROP INDEX UNIQ_223B587E8DB60186, ADD INDEX IDX_223B587E8DB60186 (task_id)');
        $this->addSql('ALTER TABLE au_task DROP FOREIGN KEY FK_223B587E8DB60186');
        $this->addSql('ALTER TABLE au_task CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE task_id task_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE au_task ADD CONSTRAINT `FK_223B587E8DB60186` FOREIGN KEY (task_id) REFERENCES ta_tasks (uuid) ON UPDATE CASCADE ON DELETE SET NULL');
        $this->addSql('DROP INDEX IDX_A694E5728CDE5729 ON me_media');
        $this->addSql('ALTER TABLE me_media ADD idMediaTypes INT NOT NULL, DROP type');
        $this->addSql('ALTER TABLE me_media ADD CONSTRAINT `FK_A694E57284671716` FOREIGN KEY (idMediaTypes) REFERENCES me_media_types (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_A694E57284671716 ON me_media (idMediaTypes)');
        $this->addSql('ALTER TABLE pr_preview_links CHANGE lastVisit lastVisit DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('DROP INDEX UNIQ_5CEC3EEAE25D857EA1FA6DDA ON se_permissions');
        $this->addSql('ALTER TABLE se_permissions ADD module VARCHAR(60) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5CEC3EEAE25D857EC242628A1FA6DDA ON se_permissions (context, module, idRoles)');
        $this->addSql('ALTER TABLE se_roles ADD idSecurityTypes INT DEFAULT NULL');
        $this->addSql('ALTER TABLE se_roles ADD CONSTRAINT `FK_13B749A0D02106C0` FOREIGN KEY (idSecurityTypes) REFERENCES se_security_types (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_13B749A0D02106C0 ON se_roles (idSecurityTypes)');
        $this->addSql('ALTER TABLE se_users CHANGE idContacts idContacts INT NOT NULL');
        $this->addSql('ALTER TABLE ta_task_executions CHANGE workload workload LONGTEXT NOT NULL COMMENT \'(DC2Type:object)\', CHANGE result result LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:object)\', CHANGE uuid uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE task_id task_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE ta_tasks CHANGE workload workload LONGTEXT NOT NULL COMMENT \'(DC2Type:object)\', CHANGE uuid uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE tr_trash_items CHANGE storeTimestamp storeTimestamp DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
