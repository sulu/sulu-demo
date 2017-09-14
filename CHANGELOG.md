CHANGELOG for Sulu Demo
=======================

* dev-master
    * ENHANCEMENT #26 [GENERAL]   Updated sulu/article-bundle
    * ENHANCEMENT #14 [GENERAL]   Corrected README and cache lifetime
    * ENHANCEMENT #13 [AppBundle] Install command: Use of correct output

* 1.0.1 (2017-06-30)
    * ENHANCEMENT #12 [GENERAL] Updated to current demo data; Added github link
    * ENHANCEMENT #11 [GENERAL] Install command: Use of Sulu cache clearer

* 1.0.0 (2017-06-29)
    * FEATURE #10 [GENERAL] Automatically fill out username/password on admin login
    * FEATURE #9  [GENERAL] Varnish support, Fixed naming of webspace, Corrected robots.xt
    * FEATURE #6  [GENERAL] Template clean up
    * FEATURE #5  [GENERAL] Fixed homepage template
    * FEATURE #4  [GENERAL] Fixed service template
    * FEATURE #3  [GENERAL] Fixed sitemap template
    * FEATURE #2  [GENERAL] Artists and artist detail template clean up; Corrected blog overview template
    * FEATURE #1  [GENERAL] Implemented install command
    * FEATURE #51 [GENERAL] Updated to Sulu Version 1.6.0 RC1 and added SuluArticleBundle
        DEPLOY:
            bin/console doctrine:phpcr:nodes:update --query "SELECT * FROM [nt:base] AS n WHERE [i18n:en-author] IS NOT NULL AND ISDESCENDANTNODE(n, '/cmf')" --apply-closure="\$node->getProperty('i18n:en-author')->remove();"
            bin/console doctrine:phpcr:nodes:update --query "SELECT * FROM [nt:base] AS n WHERE [i18n:de-author] IS NOT NULL AND ISDESCENDANTNODE(n, '/cmf')" --apply-closure="\$node->getProperty('i18n:de-author')->remove();"
            bin/console doctrine:phpcr:nodes:update --query "SELECT * FROM [nt:base] AS n WHERE [i18n:en-author] IS NOT NULL AND ISDESCENDANTNODE(n, '/cmf')" --apply-closure="\$node->getProperty('i18n:en-author')->remove();" --session=live
            bin/console doctrine:phpcr:nodes:update --query "SELECT * FROM [nt:base] AS n WHERE [i18n:de-author] IS NOT NULL AND ISDESCENDANTNODE(n, '/cmf')" --apply-closure="\$node->getProperty('i18n:de-author')->remove();" --session=live
            bin/console phpcr:migrations:migrate
