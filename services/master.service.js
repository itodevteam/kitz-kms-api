const { sql, poolPromise } = require("../config/db");

// GET ALL ITEM
exports.getItems = async () => {
  const pool = await poolPromise;
  const result = await pool.request().query("SELECT * FROM tbm_ItemMaster");
  return result.recordset;
};

// GET ITEM BY OWNER
exports.getItemByOwner = async (vendno) => {
  const pool = await poolPromise;
  const result = await pool
    .request()
    .input("vendno", sql.NVarChar, vendno)
    .query("SELECT * FROM tbm_ItemMaster");

  return result.recordset;
};

// GET VENDOR
exports.getVendor = async () => {
  const pool = await poolPromise;
  const result = await pool.request().query("EXEC zsp_GetVendor");
  return result.recordset;
};