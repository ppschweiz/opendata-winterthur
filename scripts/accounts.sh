#! /bin/bash

## @id $Id$

##       1         2         3         4         5         6         7         8
## 45678901234567890123456789012345678901234567890123456789012345678901234567890

# fill up accounts table

IOS="INSERT OR REPLACE INTO accounts VALUES"
while test $# -gt 0; do
    csvtool namedcol "Nr.","Bezeichnung" $1 \
        | sed -n 's/^\(3[0-9][0-9][0-9]\),"*\([^"]*\)"*$/'"$IOS"' ( \1, 0, "\2" );/gp' \
        | sort | uniq
    csvtool namedcol "Nr.","Bezeichnung" $1 \
        | sed -n 's/^\(4[0-9][0-9][0-9]\),"*\([^"]*\)"*$/'"$IOS"' ( \1, 1, "\2" );/gp' \
        | sort | uniq
    shift
done
