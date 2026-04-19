const e = require("cors");
const { sql, poolPromise } = require("../config/db");

exports.getReceiveData = async (data) => {
  const pool = await poolPromise;

  const result = await pool
    .request()
    .input("Json", sql.NVarChar(sql.MAX), JSON.stringify(data))
    .execute("zsp_GetReceiveData");

  return {
    info: result.recordsets[0],
    data: result.recordsets[1]
  };
};

exports.getBacklogData = async (data) => {
  const pool = await poolPromise;

  const result = await pool
    .request()
    .input("Json", sql.NVarChar(sql.MAX), JSON.stringify(data))
    .execute("zsp_GetBacklogData");

  return {
    info: result.recordsets[0],
    data: result.recordsets[1]
  };
};

exports.getInventoryData = async (data) => {
  const pool = await poolPromise;   

  const result = await pool
    .request()
    .input("Json", sql.NVarChar(sql.MAX), JSON.stringify(data))
    .execute("zsp_GetInventoryData");

  return {
    info: result.recordsets[0],
    data: result.recordsets[1]
  };
}
