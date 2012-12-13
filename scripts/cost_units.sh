#! /bin/bash

## @id $Id$

##       1         2         3         4         5         6         7         8
## 45678901234567890123456789012345678901234567890123456789012345678901234567890

# fill up cost units (product groups) table

IOS="INSERT OR REPLACE INTO cost_units VALUES "
while test $# -gt 0; do
    sed -n 's/.*Kostenstelle *: \([0-9]\)\([0-9]*\) *\([^"]*\)  *(PG).*/'"$IOS"'( \1\2, \100000, "\3" );/gp' $1 \
        | sort | uniq
    shift
done
