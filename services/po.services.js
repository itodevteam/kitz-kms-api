const { sql, poolPromise } = require("../config/db");

exports.insertPO = async (data) => {
  const pool = await poolPromise;

  try {
    const result = await pool.request()
      .input("json", sql.NVarChar(sql.MAX), JSON.stringify(data))
      .execute("zsp_InsertPurchOrderUpload");

    return {
      status: result.recordsets[0][0], 
      data: result.recordsets[1] || []
    };

  } catch (error) {
    console.error("SERVICE ERROR:", error);
    throw error;
  }
};