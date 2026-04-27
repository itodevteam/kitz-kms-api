const e = require("cors");
const { sql, poolPromise } = require("../config/db");

exports.getItemInspection = async (data) => {
  const pool = await poolPromise;

  const result = await pool
    .request()
    .input("Json", sql.NVarChar(sql.MAX), JSON.stringify(data))
    .execute("zsp_GetItemInspection");

  return {
    info: result.recordsets[0],
    data: result.recordsets[1]
  };
};