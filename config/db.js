const sql = require("mssql");

const dbConfig = {
  user: 'sa',
  password: 'P@$$2024',
  server: '45.136.236.233',
  database: 'DB-KMS',
  options: {
    encrypt: false,
    trustServerCertificate: true,
  },
};

const poolPromise = new sql.ConnectionPool(dbConfig)
  .connect()
  .then((pool) => {
    console.log("Connected to SQL Server");
    return pool;
  })
  .catch((err) => {
    console.error("Database connection failed!", err);
    throw err;
  });

module.exports = {
  sql,
  poolPromise,
};
