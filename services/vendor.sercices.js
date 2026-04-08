const e = require("cors");
const { sql, poolPromise } = require("../config/db");

exports.getSuppWaitConfirm = async (userNo) => {
  const pool = await poolPromise;
  const result = await pool
    .request()
    .input("userNo", sql.NVarChar, userNo)
    .query("EXEC zsp_GetVendorWaitConfirm @userNo");

  return result.recordset;
};