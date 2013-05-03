#!/bin/bash -e

IOS="INSERT OR REPLACE INTO firmen VALUES"
while test $# -gt 0; do
    tail -n +2 $1 | \
    csvtool -u TAB cols 1-5,14-17 - | \
	awk -F"\t" '{print "'"$IOS"' ( " $1 ", \"" $2 "\", \"" $3 "\", \"" $4 "\", \"" $5 "\", \"" $6 "\", \"" $7 "\", \"" $8 "\", \"" $9 "\" );"}'
    shift
done | sort | uniq
