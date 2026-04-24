const e = require("cors");
const { sql, poolPromise } = require("../config/db");

exports.getDeliveryPlan = async (data) => {
  const pool = await poolPromise;

  const result = await pool
    .request()
    .input("plantNo", sql.NVarChar, data.plantNo)
    .execute("zsp_GetDeliveryPlan");

  return result.recordset;
}