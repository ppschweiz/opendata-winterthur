## @id $Id$

##       1         2         3         4         5         6         7         8
## 45678901234567890123456789012345678901234567890123456789012345678901234567890

DATA=data/*.xlsx

all: build ${DATA:data/%.xlsx=build/%-0.csv}

build:
	mkdir build

clean:
	-rm -rf build

build/%-0.csv: data/%.xlsx
	ssconvert -S -T Gnumeric_stf:stf_csv $< \
	  ${<:data/%.xlsx=build/%}-%n.csv
