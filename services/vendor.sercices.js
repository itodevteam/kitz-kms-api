const e = require("cors");
const { sql, poolPromise } = require("../config/db");

exports.getVendorWaitConfirm = async (userNo) => {
  const pool = await poolPromise;
  const result = await pool
    .request()
    .input("userNo", sql.NVarChar, userNo)
    .query("EXEC zsp_GetVendorWaitConfirm @userNo");

  return result.recordset;
};

exports.poSendingConfirm = async (data) => {
  const pool = await poolPromise; 

  const result = await pool
    .request()
    .input("Json", sql.NVarChar(sql.MAX), JSON.stringify(data))
    .execute("zsp_POSendingConfirm");

  return {
    info: result.recordsets[0],
    data: result.recordsets[1]
  };
};

exports.getPurOrderMaster = async (data) => {
  const pool = await poolPromise; 

  const result = await pool
    .request()
    .input("PlantNo", sql.NVarChar(50), data.PlantNo)
    .input("VendorNo", sql.NVarChar(50), data.VendorNo)
    .input("OrderStatus", sql.NVarChar(50), data.OrderStatus)
    .execute("zsp_GetPurOrderMaster");

  return result.recordset;
};


