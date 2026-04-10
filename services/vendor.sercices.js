const e = require("cors");
const { sql, poolPromise } = require("../config/db");


exports.poVendorConfirm = async (data) => {
  const pool = await poolPromise; 

  const result = await pool
    .request()
    .input("Json", sql.NVarChar(sql.MAX), JSON.stringify(data))
    .execute("zsp_POVendorConfirm");

  return {
    info: result.recordsets[0],
    data: result.recordsets[1]
  };
};

exports.createDeliveryDetail = async (data) => {
  const pool = await poolPromise; 

  const result = await pool
    .request()
    .input("Json", sql.NVarChar(sql.MAX), JSON.stringify(data))
    .execute("zsp_CreateDeliveryDetail");

  return {
    info: result.recordsets[0],
    data: result.recordsets[1]
  };
};

exports.updateDeliveryDetail = async (data) => {
  const pool = await poolPromise; 

  const result = await pool
    .request()
    .input("Json", sql.NVarChar(sql.MAX), JSON.stringify(data))
    .execute("zsp_UpdateDeliveryDetail");

  return {
    info: result.recordsets[0],
    data: result.recordsets[1]
  };
};
