#! /bin/bash

# call with sqlite3 finance database build/finance.db
db=${1:-build/finance.db}

# Creating «bern budget2013.json» compatible json file

function query() {
  echo "$*;" | sqlite3 $db
}

# Mapping:
# directorates → agencies → product groups, Labels in table.js:37
# departments → cost units → accounts

echo "{"
for directorate in $(query "select cost_unit from departments where cost_unit!=0"); do
  echo "    \"$directorate\": {"
  
  echo "        \"number\": \"$directorate\","
  echo "        \"name\": \""$(query "select name from departments where cost_unit=$directorate")"\","
  echo "        \"gross_cost\": {"
  echo "            \"budgets\" {"
  for year in $(query "select year from years where boa=0"); do
    echo "                \"$year\": "$(query "select sum(amount) from accountings where boa=0 and year=$year and cost_unit in (select cost_unit from cost_units where department=$directorate) and account in (select account from accounts where eoe=-1)")","
  done
  echo "            },"
  echo "            \"accounts\" {"
  for year in $(query "select year from years where boa=1"); do
    echo "                \"$year\": "$(query "select sum(amount) from accountings where boa=1 and year=$year and cost_unit in (select cost_unit from cost_units where department=$directorate) and account in (select account from accounts where eoe=-1)")","
  done
  echo "            },"
  echo "        },"
  
  echo "        \"revenue\": {"
  echo "            \"budgets\" {"
  for year in $(query "select year from years where boa=0"); do
    echo "                \"$year\": "$(query "select sum(amount) from accountings where boa=0 and year=$year and cost_unit in (select cost_unit from cost_units where department=$directorate) and account in (select account from accounts where eoe=1)")","
  done
  echo "            },"
  echo "            \"accounts\" {"
  for year in $(query "select year from years where boa=1"); do
    echo "                \"$year\": "$(query "select sum(amount) from accountings where boa=1 and year=$year and cost_unit in (select cost_unit from cost_units where department=$directorate) and account in (select account from accounts where eoe=1)")","
  done
  echo "            },"
  echo "        },"
  echo "        \"agencies\": {"
  for agency in $(query "select cost_unit from cost_units where department==$directorate and cost_unit!=department"); do
    echo "            \"$agency\": {"
    
    echo "                \"number\": \"$agency\","
    echo "                \"name\": \""$(query "select name from cost_units where cost_unit=$agency and department=$directorate")"\","
    echo "                \"gross_cost\": {"
    echo "                    \"budgets\" {"
    for year in $(query "select year from years where boa=0"); do
      echo "                        \"$year\": "$(query "select sum(amount) from accountings where boa=0 and year=$year and cost_unit=$agency and account in (select account from accounts where eoe=-1)")","
    done
    echo "                    },"
    echo "                    \"accounts\" {"
    for year in $(query "select year from years where boa=1"); do
      echo "                        \"$year\": "$(query "select sum(amount) from accountings where boa=1 and year=$year and cost_unit=$agency and account in (select account from accounts where eoe=-1)")","
    done
    echo "                    },"
    echo "                },"
    echo "                \"revenue\": {"
    echo "                    \"budgets\" {"
    for year in $(query "select year from years where boa=0"); do
      echo "                        \"$year\": "$(query "select sum(amount) from accountings where boa=0 and year=$year and cost_unit=$agency and account in (select account from accounts where eoe=1)")","
    done
    echo "                    },"
    echo "                    \"accounts\" {"
    for year in $(query "select year from years where boa=1"); do
      echo "                        \"$year\": "$(query "select sum(amount) from accountings where boa=1 and year=$year and cost_unit=$agency and account in (select account from accounts where eoe=1)")","
    done
    echo "                    },"
    echo "                },"

    echo "                \"product_groups\": {"
    
    # all expenses
    for product_group in $(query "select distinct account from accountings where cost_unit=$agency and account in (select account from accounts where eoe=-1)"); do
      echo "                    \"$product_group\": {"
      
      echo "                        \"number\": \"$product_group\","
      echo "                        \"name\": \""$(query "select name from accounts where account=$product_group")"\","
      echo "                        \"gross_cost\": {"
      echo "                            \"budgets\" {"
      for year in $(query "select year from years where boa=0"); do
        echo "                                \"$year\": "$(query "select sum(amount) from accountings where boa=0 and year=$year and cost_unit=$agency and account=$product_group")","
      done
      echo "                            },"
      echo "                            \"accounts\" {"
      for year in $(query "select year from years where boa=1"); do
        echo "                                \"$year\": "$(query "select sum(amount) from accountings where boa=1 and year=$year and cost_unit=$agency and account=$product_group")","
      done
      echo "                            },"
      echo "                        },"
      echo "                        \"products\": {"
      echo "                        }"
    done
    
    # all earnings
    for product_group in $(query "select distinct account from accountings where cost_unit=$agency and account in (select account from accounts where eoe=1)"); do
      echo "                    \"$product_group\": {"
      
      echo "                        \"number\": \"$product_group\","
      echo "                        \"name\": \""$(query "select name from accounts where account=$product_group")"\","
      echo "                        \"revenue\": {"
      echo "                            \"budgets\" {"
      for year in $(query "select year from years where boa=0"); do
        echo "                                \"$year\": "$(query "select sum(amount) from accountings where boa=0 and year=$year and cost_unit=$agency and account=$product_group")","
      done
      echo "                            },"
      echo "                            \"accounts\" {"
      for year in $(query "select year from years where boa=1"); do
        echo "                                \"$year\": "$(query "select sum(amount) from accountings where boa=1 and year=$year and cost_unit=$agency and account=$product_group")","
      done
      echo "                            },"
      echo "                        },"
      echo "                        \"products\": {"
      echo "                        }"
    done
    echo "                    }"
    echo "                }"
    echo "            }"
  done
  echo "        }"
  echo "    }"
done
echo "}"
