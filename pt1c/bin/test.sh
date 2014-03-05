# mysql -sse 'select * from cdr' -u'freepbxuser' -p'gosyXpuOrI7T' asteriskcdrdb
# mysql -sse 'select * from cel' -u'freepbxuser' -p'gosyXpuOrI7T' asteriskcdrdb
mysql -sse 'select id,src,dst from PT1C_cdr' -u'freepbxuser' -p'gosyXpuOrI7T' asteriskcdrdb

# mysql -sse 'SHOW TABLES' -u'freepbxuser' -p'gosyXpuOrI7T' asteriskcdrdb