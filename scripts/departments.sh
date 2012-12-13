#! /bin/bash

## @id $Id$

##       1         2         3         4         5         6         7         8
## 45678901234567890123456789012345678901234567890123456789012345678901234567890

# fill up departments table

IOS="INSERT OR REPLACE INTO departments VALUES "
while test $# -gt 0; do
    sed -n 's/.*Kostenstelle *: \([0-9]*\) *\([^"]*\)  *(V).*/'"$IOS"'( \1, "\2" );/gp' $1 \
        | sort | uniq
    shift
done
