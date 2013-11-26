#!/bin/sh
dir_script='/tmp/';
# каталог из asterisk.conf
astspooldir='/var/spool/asterisk';
#
call_text="Channel: SIP/104
Context: miko_ajam
Extension: 10000109
Callerid: Alexey<104>
Setvar: number=104
Setvar: tehnology=SIP";

echo "$call_text" > /tmp/file.call;
mv '/tmp/file.call' "$astspooldir/outgoing/";

asterisk -rvvv;
