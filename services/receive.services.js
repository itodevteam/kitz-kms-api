const e = require("cors");
const { sql, poolPromise } = require("../config/db");

exports.getDeliveryMaster = async (data) => {
  const pool = await poolPromise; 

  const result = await pool
    .request()
    .input("deliveryNo", sql.NVarChar, data.deliveryNo)
    .input("roundNo", sql.NVarChar, data.roundNo)
    .input("orderStatus", sql.NVarChar, data.orderStatus)
    .execute("zsp_GetDeliveryMaster");

  return {
    info: result.recordsets[0],
    data: result.recordsets[1]
  };
};

exports.getDeliveryDetail = async (data) => {
  const pool = await poolPromise; 

  const result = await pool
    .request()
    .input("deliveryNo", sql.NVarChar, data.deliveryNo)
    .input("roundNo", sql.NVarChar, data.roundNo)
    .input("itemStatus", sql.NVarChar, data.itemStatus)
    .execute("zsp_GetDeliveryDetail");

  return {
    info: result.recordsets[0],
    data: result.recordsets[1]
  };
};

exports.getReceiveDetail = async (data) => {
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


