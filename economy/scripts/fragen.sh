#!/bin/sh -e

IOS="INSERT OR REPLACE INTO fragen VALUES"
while test $# -gt 0; do
    head -1 $1 | \
	csvtool -u TAB col 7-12 - | \
	sed "s/\t/\n/g" | \
	sed 's/\([0-9]*\)\. [0-9. ]*\(.*\)/'"$IOS"' ( \1, "\2" );/g'
    shift
done
