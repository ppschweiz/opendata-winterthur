## @id $Id$

##       1         2         3         4         5         6         7         8
## 45678901234567890123456789012345678901234567890123456789012345678901234567890

TABLES=years accounts departments cost_units accountings
DB=build/finance.db
SQL=sqlite3
JSON=build/finance.json
WORDPRESS=build/opendatacommunityfinances.zip
EXCEL=$(shell ls data/*.xlsx)
CSV0=${EXCEL:data/%.xlsx=build/%-0.csv}
CSV1=${EXCEL:data/%.xlsx=build/%-1.csv}
CSV2=${EXCEL:data/%.xlsx=build/%-2.csv}
CSV_FILES=${CSV0} ${CSV1} ${CSV2}
SQL_FILES=${TABLES:%=build/%.sql}

all: ${JSON} ${SQL_FILES} ${DB}

wordpress: ${WORDPRESS}

build/%.zip: ${SQL_FILES}
	cp ${DB} wordpress/${@:build/%.zip=%}/
	cd wordpress && zip -r ../$@ ${@:build/%.zip=%}

install: ${JSON} ${DB}
	cp ${JSON} ${DB} www/

build:
	mkdir build

build/%-0.csv build/%-1.csv build/%-2.csv: data/%.xlsx
	ssconvert -S -T Gnumeric_stf:stf_csv $< \
	  ${<:data/%.xlsx=build/%}-%n.csv

${DB}: scripts/schema.sql build
	${SQL} $@ < $<

${JSON}: scripts/tojson.sh ${TABLES:%=build/%.sql}
	$< ${DB} | python -m simplejson.tool > $@

build/%.sql: scripts/%.sh ${DB} ${CSV_FILES}
	$< ${CSV_FILES} > $@
	${SQL} ${DB} < $@

clean:
	-rm -rf build

.phony: all install clean
