#!/bin/bash -e

IOS="INSERT OR REPLACE INTO antworten VALUES"
while test $# -gt 0; do
    for ((i=7;i<=12;++i)); do
	FRAGE=$(head -1 $1 | csvtool -u TAB col $i - | sed 's/\([0-9]\)\. .*/\1/g')
	tail -n +2 $1 | \
	    csvtool -u TAB col 1,$i - | \
	    awk -F"\t" '{gsub(/'"'"'/, "", $2); print "'"$IOS"' ( " $1 ", '"$FRAGE"', '"'"'" $2 "'"'"' );"}'
    done
    shift
done
