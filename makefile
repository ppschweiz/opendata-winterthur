## @id $Id$

##       1         2         3         4         5         6         7         8
## 45678901234567890123456789012345678901234567890123456789012345678901234567890

EXCEL=$(shell ls data/*.xlsx)
CSV0=${EXCEL:data/%.xlsx=build/%-0.csv}
CSV1=${EXCEL:data/%.xlsx=build/%-1.csv}
CSV2=${EXCEL:data/%.xlsx=build/%-2.csv}
CSV=${CSV0} ${CSV1} ${CSV2}
DB=build/finance.db
SQL=sqlite3

all: accounts departments cost_units

build:
	mkdir build

csv: build ${CSV}

build/%-0.csv build/%-1.csv build/%-2.csv: data/%.xlsx
	ssconvert -S -T Gnumeric_stf:stf_csv $< \
	  ${<:data/%.xlsx=build/%}-%n.csv

${DB}: scripts/schema.sql csv
	${SQL} $@ < scripts/schema.sql

%: scripts/%.sh ${DB}
	$< ${CSV} | ${SQL} ${DB}

clean:
	-rm -rf build

.phony: all clean csv accounts departments cost_units accountings
