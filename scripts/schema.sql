CREATE TABLE IF NOT EXISTS accounts (
  account INTEGER PRIMARY KEY,
  eoe INTEGER,
  name TEXT
);
CREATE TABLE IF NOT EXISTS departments (
  cost_unit INTEGER PRIMARY KEY,
  name TEXT
);
CREATE TABLE IF NOT EXISTS cost_units (
  cost_unit INTEGER PRIMARY KEY,
  department INTEGER,
  name TEXT
);
CREATE TABLE IF NOT EXISTS accountings (
  cost_unit INTEGER,
  account INTEGER,
  year INTEGER,
  boa INTEGER,
  amount INTEGER
);
