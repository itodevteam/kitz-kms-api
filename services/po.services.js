const e = require("cors");
const { sql, poolPromise } = require("../config/db");

exports.insertPO = async (data) => {
  const pool = await poolPromise;

  try {
    const result = await pool.request()
      .input("json", sql.NVarChar(sql.MAX), JSON.stringify(data))
      .execute("zsp_PurchOrderUpload");

    return {
      status: result.recordsets[0][0], 
      data: result.recordsets[1] || []
    };

  } catch (error) {
    console.error("SERVICE ERROR:", error);
    throw error;
  }
};

exports.getPOMaster = async () => {
  const pool = await poolPromise;
  const result = await pool
    .request()
    .query("EXEC zsp_GetPOMaster");
  
  return result.recordset;
};

exports.getPODetail = async (purOrderNo) => {
  const pool = await poolPromise;
  const result = await pool
    .request()
    .input("purOrderNo", sql.NVarChar, purOrderNo)
    .query("EXEC zsp_GetPODetail @purOrderNo");

  return result.recordset;
};

exports.getPOWaitPrepare = async () => {
  const pool = await poolPromise;
  const result = await pool
    .request()
    .query("EXEC zsp_GetPOWaitPrepare");
  
  return result.recordset;
};


exports.getPOWaitApprove = async (userNo) => {
  const pool = await poolPromise;
  const result = await pool
    .request()
    .input("userNo", sql.NVarChar, userNo)
    .query("EXEC zsp_GetPOWaitApprove @userNo");

  return result.recordset;
};


exports.poApproval = async (data) => {
  const pool = await poolPromise;
  const transaction = new sql.Transaction(pool);

  const failed = [];
  let success = 0;

  try {
    await transaction.begin();

    for (const row of data) {
      try {
        await new sql.Request(transaction)
          .input("PurOrderNo", sql.NVarChar, row.PurOrderNo)
          .input("Action", sql.NVarChar, row.Action)
          .input("Remarks", sql.NVarChar, row.Remarks || null)
          .input("ApproveBy", sql.NVarChar, row.ApproveBy)
          .execute("zsp_POApprove");

        success++;
      } catch (err) {
        failed.push({
          data: row,
          error: err.message
        });
      }
    }

    await transaction.commit();

    return {
      total: data.length,
      success,
      failed
    };

  } catch (err) {
    await transaction.rollback();
    throw err;
  }
};

