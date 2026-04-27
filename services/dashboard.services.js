const e = require("cors");
const { sql, poolPromise } = require("../config/db");

exports.getDeliveryPlan = async (data) => {
  const pool = await poolPromise;

  const result = await pool
    .request()
    .input("plantNo", sql.NVarChar, data.plantNo)
    .execute("zsp_DashboardDeliveryPlan");

  return result.recordset;
}

exports.getPODelay = async (data) => {
  const pool = await poolPromise;

  const result = await pool
    .request()
    .input("plantNo", sql.NVarChar, data.plantNo)
    .execute("zsp_DashboardPODelay");

  return result.recordset;
}

exports.getPOStatus = async (data) => {
  const pool = await poolPromise;

  const result = await pool
    .request()
    .input("plantNo", sql.NVarChar, data.plantNo)
    .execute("zsp_DashboardPOStatus");

  return result.recordset;
}

exports.getRecentData = async (data) => {
  const pool = await poolPromise;

  const result = await pool
    .request()
    .input("plantNo", sql.NVarChar, data.plantNo)
    .execute("zsp_DashboardRecentData");

  return result.recordset;
}

exports.getCardSummary = async (data) => {
  const pool = await poolPromise;

  const result = await pool
    .request()
    .input("plantNo", sql.NVarChar, data.plantNo)
    .execute("zsp_DashboardCardSummary");

  return result.recordset;
}
