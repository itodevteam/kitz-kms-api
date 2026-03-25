const { sql, poolPromise } = require("../config/db");

// GET ALL ITEM
exports.getItems = async () => {
  const pool = await poolPromise;
  const result = await pool.request().query("SELECT * FROM tbm_ItemMaster");
  return result.recordset;
};

// GET ITEM BY OWNER
exports.getItemByOwner = async (ownercode) => {
  const pool = await poolPromise;
  const result = await pool
    .request()
    .input("ownercode", sql.NVarChar, ownercode)
    .query("SELECT * FROM tbm_ItemMaster WHERE ownercode = @ownercode");

  return result.recordset;
};

// GET VENDOR
exports.getVendor = async () => {
  const pool = await poolPromise;
  const result = await pool.request().query("EXEC zrp_GetVenderMaster");
  return result.recordset;
};