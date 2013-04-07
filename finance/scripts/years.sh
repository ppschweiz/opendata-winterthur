#! /bin/bash -e

## @id $Id$

##       1         2         3         4         5         6         7         8
## 45678901234567890123456789012345678901234567890123456789012345678901234567890

# fill up accounts table

IOS="INSERT OR REPLACE INTO years VALUES"
while test $# -gt 0; do
    IFS=,
    for c in $(head -1 $1); do
        case "$c" in
            (*VO*)
                echo "$IOS ( $(echo $c | sed -n 's/.* \(2[0-9][0-9][0-9]\).*/\1/gp'), 0 );"
                ;;
            (*RE*)
                echo "$IOS ( $(echo $c | sed -n 's/.* \(2[0-9][0-9][0-9]\).*/\1/gp'), 1 );"
                ;;
        esac
    done
    shift
done | sort | uniq
