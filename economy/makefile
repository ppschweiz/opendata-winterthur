## @id $Id$

##       1         2         3         4         5         6         7         8
## 45678901234567890123456789012345678901234567890123456789012345678901234567890

CSV=$(shell ls data/*.csv)
TABLES=firmen fragen antworten
DB=build/economy.db
SQL=sqlite3

all: ${DB} ${TABLES}

install: ${DB}
	cp ${DB} www/

build:
	mkdir build

${DB}: scripts/schema.sql build
	${SQL} $@ < scripts/schema.sql

%: scripts/%.sh ${DB}
	$< ${CSV} | ${SQL} ${DB}

clean:
	-rm -rf build

.phony: all install clean csv ${TABLES}
