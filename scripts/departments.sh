#! /bin/bash -e

## @id $Id$

##       1         2         3         4         5         6         7         8
## 45678901234567890123456789012345678901234567890123456789012345678901234567890

# fill up departments table

IOS1="INSERT OR REPLACE INTO departments VALUES "
IOS2="INSERT OR REPLACE INTO cost_units VALUES "
echo "$IOS1 (0, \"Stadt\");"
while test $# -gt 0; do
    sed -n 's/.*Kostenstelle *: \([0-9]*\) *\([^"]*\)  *(V).*/'"$IOS1"'( \1, "\2" );/gp' $1
    sed -n 's/.*Kostenstelle *: \([0-9]*\) *\([^"]*\)  *(V).*/'"$IOS2"'( \1, \1, "Total" );/gp' $1
    shift
done | sort | uniq
