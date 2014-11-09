#! /bin/bash -e

## @id $Id$

##       1         2         3         4         5         6         7         8
## 45678901234567890123456789012345678901234567890123456789012345678901234567890

# fill up accounts table

IOS="INSERT OR REPLACE INTO accountings VALUES"
while test $# -gt 0; do
    cols=()
    year=()
    IFS=,
    for c in $(head -1 $1); do
        case "$c" in
            (*VO*)
                boa=0
                cols+=( $(echo $c | sed -n 's/"*\([^"]*\)"*/\1/gp') )
                year+=( $(echo $c | sed -n 's/.* \(2[0-9][0-9][0-9]\).*/\1/gp') )
                ;;
            (*RE*)
                boa=1
                cols+=( $(echo $c | sed -n 's/"*\([^"]*\)"*/\1/gp') )
                year+=( $(echo $c | sed -n 's/.* \(2[0-9][0-9][0-9]\).*/\1/gp') )
                ;;
        esac
    done
    for ((i=0; i<${#cols[@]}; ++i)); do
        awk -F, '
          BEGIN {
            cost_unit=0
          }
          NR==1 {
            print "BOA,Year,\"Cost Unit\"," $0
            next
          }
          /.*Kostenstelle *: [0-9]+ .*/ {
            cost_unit=gensub(/.*Kostenstelle *: ([0-9]+) .*/, "\\1", "g")
            next
          }
          $1=="" && $2=="" || $1=="Nr." || $2=="Nr." {
            next
          }
          {
            print "'$boa,${year[$i]}'," cost_unit "," $0
          }
        ' $1  | \
            csvtool namedcol "Cost Unit,Nr.,Year,BOA,${cols[$i]}" - | \
            awk -F, '
              BEGIN {
                print "'$IOS'"
                firstline=1
                limitcnt=1;
              }
              NR>1 && firstline==0 && $5~/^[-.0-9]+$/  {
                print ",( " $1 ", " $2 ", " $3 ", " $4" , " ($2==4||$2>=4000&&$2<5000?sprintf("%0d",(-$5)):$5) " )"
                ++limitcnt;
              }
              NR>1 && firstline==1 && $5~/^[-.0-9]+$/ {
                print "( " $1 ", " $2 ", " $3 ", " $4" , " ($2==4||$2>=4000&&$2<5000?sprintf("%0d",(-$5)):$5) " )"
                firstline=0
              }
              limitcnt==400 {
                print ";\n'$IOS'"
                limitcnt=0
                firstline=1
              }
              END {
                print ";"
              }'
    done
    shift
done