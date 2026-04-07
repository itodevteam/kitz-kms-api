const { poolPromise } = require("../config/db");

exports.getWaitingData = async () => {
  const pool = await poolPromise;

  const result = await pool
    .request()
    .query("EXEC zrp_WaitingData");

  return result.recordset;
};