CREATE TABLE IF NOT EXISTS firmen (
  id INTEGER PRIMARY KEY,
  resumecode TEXT,
  start TEXT,
  datum TEXT,
  status TEXT,
  branche TEXT,
  mitarbeiter TEXT,
  umsatz TEXT,
  ort TEXT
);

CREATE TABLE IF NOT EXISTS fragen (
  id INTEGER PRIMARY KEY,
  text TEXT
);

CREATE TABLE IF NOT EXISTS antworten (
  firma INTEGER,
  frage INTEGER,
  antwort TEXT
);
