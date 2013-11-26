#!/bin/sh
dir_script='/tmp/';
# каталог из asterisk.conf
astspooldir='/var/spool/asterisk';
#
call_text="Channel: SIP/104
Context: miko_ajam
Extension: 10000666
Callerid: Alexey<104>
Setvar: v2=1385413520.14
Setvar: v1=SIP/104
Setvar: v6=Records
";

echo "$call_text" > /tmp/file.call;
mv '/tmp/file.call' "$astspooldir/outgoing/";

asterisk -rvvv;
