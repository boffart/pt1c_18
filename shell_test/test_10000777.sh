#!/bin/sh
dir_script='/tmp/';
# каталог из asterisk.conf
astspooldir='/var/spool/asterisk';
#
call_text="Channel: SIP/104
Context: miko_ajam
Extension: 10000777
Callerid: Alexey<104>
Setvar: uniqueid1c=1385413520.14
Setvar: chan=SIP/104";

echo "$call_text" > /tmp/file.call;
mv '/tmp/file.call' "$astspooldir/outgoing/";

asterisk -rvvv;
