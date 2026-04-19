const { poolPromise } = require("../config/db");

exports.getWaitingData = async (ownercode) => {
  const pool = await poolPromise;

  const result = await pool
    .request()
    .input("OwnerCode", sql.NVarChar, ownercode)
    .execute("zrp_WaitingData");

  return result.recordset;
}