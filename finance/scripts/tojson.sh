#!/bin/bash -e

# call with sqlite3 finance database build/finance.db
db=${1:-build/finance.db}

# Creating «bern budget2013.json» compatible json file

function query() {
    echo "$*;" | sqlite3 $db
}

function budgets_or_accountings() {
    local indent="$1"
    local cost_unit="$2"
    local account=$3
    local boa=$4
    local komma=""
    for year in $(query "select year from years where boa=$boa"); do
	if [ -n "$komma" ]; then echo "$komma"; fi
        local sum=$(query select distinct amount from accountings where year=$year and boa=$boa and account=$account and cost_unit=$cost_unit)
	if [ -z "$sum" ]; then sum=0; fi
	echo -n "$indent\"$year\": $sum"
	komma=","
    done
    echo
}

function get_department_data() {
    local indent="$1"
    local department="$2"
    echo "$indent\"id\": \"$department\","
    echo "$indent\"name\": \"$(query select name from departments where cost_unit=$department)\","
    echo "$indent\"gross_cost\": {"
    echo "$indent  \"budgets\": {"
    budgets_or_accountings "$indent    " $department 3 0
    echo "$indent  },"
    echo "$indent  \"accounts\": {"
    budgets_or_accountings "$indent    " $department 3 1
    echo "$indent  }"
    echo "$indent},"
    echo "$indent\"revenue\": {"
    echo "$indent  \"budgets\": {"
    budgets_or_accountings "$indent    " $department 4 0
    echo "$indent  },"
    echo "$indent  \"accounts\": {"
    budgets_or_accountings "$indent    " $department 4 1
    echo "$indent  }"
    echo "$indent},"
}

function get_accounts_data() {
    local indent="$1"
    local cost_unit="$2"
    local komma=""
    for account in $(query "select account from accounts where account>999"); do
	if [ -n "$(query select amount from accountings where account=$account and cost_unit=$cost_unit)" ]; then
	    if [ -n "$komma" ]; then echo "$komma"; fi
	    echo "$indent{"
	    echo "$indent  \"id\": \"$cost_unit$account\","
	    echo "$indent  \"name\": \"$(query select name from accounts where account=$account)\","
	    if [ "-1" = "$(query select eoe from accounts where account=$account)" ]; then
		echo "$indent  \"gross_cost\": {"
		echo "$indent    \"budgets\": {"
		budgets_or_accountings "$indent      " $cost_unit $account 0
		echo "$indent    },"
		echo "$indent    \"accounts\": {"
		budgets_or_accountings "$indent      " $cost_unit $account 1
		echo "$indent    }"
		echo "$indent  }"
	    else
		echo "$indent  \"revenue\": {"
		echo "$indent    \"budgets\": {"
		budgets_or_accountings "$indent      " $cost_unit $account 0
		echo "$indent    },"
		echo "$indent    \"accounts\": {"
		budgets_or_accountings "$indent      " $cost_unit $account 1
		echo "$indent    }"
		echo "$indent  }"
	    fi
	    #echo "$indent  \"children\": []"
	    echo -n "$indent}"
	    komma=","
	fi
    done
    echo
}

function get_cost_units_data() {
    local indent="$1"
    local department="$2"
    local komma=""
    for cost_unit in $(query "select cost_unit from cost_units where cost_unit!=$department and department=$department"); do
	if [ -n "$komma" ]; then echo "$komma"; fi
	echo "$indent{"
	echo "$indent  \"id\": \"$cost_unit\","
	echo "$indent  \"name\": \"$(query select name from cost_units where cost_unit=$cost_unit)\","
	echo "$indent  \"gross_cost\": {"
	echo "$indent    \"budgets\": {"
	budgets_or_accountings "$indent      " $cost_unit 3 0
	echo "$indent    },"
	echo "$indent    \"accounts\": {"
	budgets_or_accountings "$indent      " $cost_unit 3 1
	echo "$indent    }"
	echo "$indent  },"
	echo "$indent  \"revenue\": {"
	echo "$indent    \"budgets\": {"
	budgets_or_accountings "$indent      " $cost_unit 4 0
	echo "$indent    },"
	echo "$indent    \"accounts\": {"
	budgets_or_accountings "$indent      " $cost_unit 4 1
	echo "$indent    }"
	echo "$indent  },"
	echo "$indent  \"children\": ["
	get_accounts_data "$indent    " $cost_unit
	echo "$indent  ]"
	echo -n "$indent}"
	komma=","
    done
    echo
}

## MAIN ##
echo "["
komma=""
for department in $(query 'select cost_unit from departments where cost_unit>99999'); do
    if [ -n "$komma" ]; then echo "$komma"; fi
    echo "  {"
    get_department_data "    " $department
    echo "    \"children\": ["
    get_cost_units_data "      " $department
    echo "    ]"
    echo -n "  }"
    komma=","
done
echo
echo "]"
