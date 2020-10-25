CREATE TABLE wikiacls (
    page_tag  VARCHAR(50) NOT NULL DEFAULT '',
    privilege VARCHAR(20) NOT NULL DEFAULT '',
    list      TEXT        NOT NULL,
    PRIMARY KEY (page_tag, privilege)
)
    ENGINE = ISAM;
CREATE TABLE wikilinks (
    from_tag CHAR(50) NOT NULL DEFAULT '',
    to_tag   CHAR(50) NOT NULL DEFAULT '',
    UNIQUE KEY from_tag (from_tag, to_tag),
    KEY idx_from (from_tag),
    KEY idx_to (to_tag)
)
    ENGINE = ISAM;
CREATE TABLE wikipages (
    id         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    tag        VARCHAR(50)      NOT NULL DEFAULT '',
    time       DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00',
    body       TEXT             NOT NULL,
    body_r     TEXT             NOT NULL,
    owner      VARCHAR(50)      NOT NULL DEFAULT '',
    user       VARCHAR(50)      NOT NULL DEFAULT '',
    latest     ENUM ('Y','N')   NOT NULL DEFAULT 'N',
    handler    VARCHAR(30)      NOT NULL DEFAULT 'page',
    comment_on VARCHAR(50)      NOT NULL DEFAULT '',
    PRIMARY KEY (id),
    KEY idx_tag (tag),
    KEY idx_time (time),
    KEY idx_latest (latest),
    KEY idx_comment_on (comment_on),
    FULLTEXT KEY tag (tag, body)
)
    ENGINE = ISAM;
CREATE TABLE wikireferrers (
    page_tag CHAR(50)  NOT NULL DEFAULT '',
    referrer CHAR(150) NOT NULL DEFAULT '',
    time     DATETIME  NOT NULL DEFAULT '0000-00-00 00:00:00',
    KEY idx_page_tag (page_tag),
    KEY idx_time (time)
)
    ENGINE = ISAM;
INSERT INTO wikipages
VALUES (1, 'HomePage', '2003-06-18 17:38:52', 'Welcome to your Wakka site! Click on the "Edit this page" link at the bottom to get started.\n\nAlso don\'t forget to visit [[WakkaWiki:WakkaWiki WakkaWiki]]!\n\nUseful pages: OrphanedPages, WantedPages, TextSearch.', '', '', 'WakkaInstaller', 'Y',
        'page', '');
INSERT INTO wikipages
VALUES (2, 'RecentChanges', '2003-06-18 17:38:52', '{{RecentChanges}}', '', '', 'WakkaInstaller', 'Y', 'page', '');
INSERT INTO wikipages
VALUES (3, 'RecentlyCommented', '2003-06-18 17:38:52', '{{RecentlyCommented}}', '', '', 'WakkaInstaller', 'Y', 'page', '');
INSERT INTO wikipages
VALUES (4, 'PageIndex', '2003-06-18 17:38:52', '{{PageIndex}}', '', '', 'WakkaInstaller', 'Y', 'page', '');
INSERT INTO wikipages
VALUES (5, 'WantedPages', '2003-06-18 17:38:52', '{{WantedPages}}', '', '', 'WakkaInstaller', 'Y', 'page', '');
INSERT INTO wikipages
VALUES (6, 'OrphanedPages', '2003-06-18 17:38:52', '{{OrphanedPages}}', '', '', 'WakkaInstaller', 'Y', 'page', '');
INSERT INTO wikipages
VALUES (7, 'TextSearch', '2003-06-18 17:38:52', '{{TextSearch}}', '', '', 'WakkaInstaller', 'Y', 'page', '');
INSERT INTO wikipages
VALUES (8, 'MyPages', '2003-06-18 17:38:52', '{{MyPages}}', '', '', 'WakkaInstaller', 'Y', 'page', '');
INSERT INTO wikipages
VALUES (9, 'MyChanges', '2003-06-18 17:38:52', '{{MyChanges}}', '', '', 'WakkaInstaller', 'Y', 'page', '');
