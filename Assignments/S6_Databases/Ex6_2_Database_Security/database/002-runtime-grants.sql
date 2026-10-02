REVOKE ALL PRIVILEGES, GRANT OPTION FROM 'guestbook_user'@'%';
GRANT SELECT, INSERT ON guestbook_db.entries TO 'guestbook_user'@'%';
GRANT SELECT, INSERT ON guestbook_db.images TO 'guestbook_user'@'%';
