const e = require("cors");
const { sql, poolPromise } = require("../config/db");

exports.getReceiveDetail = async (deliveryNo) => {
  const pool = await poolPromise; 

  const result = await pool
    .request()
    .input("deliveryNo", sql.NVarChar, deliveryNo)
    .execute("zsp_GetReceiveDetail");

  return {
    info: result.recordsets[0],
    data: result.recordsets[1]
  };
};

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

exports.confirmReceive = async (data) => {
  const pool = await poolPromise;

  const result = await pool
    .request()
    .input("Json", sql.NVarChar(sql.MAX), JSON.stringify(data))
    .execute("zsp_ConfirmReceive");

  return {
    info: result.recordsets[0],
    data: result.recordsets[1]
  };
};


