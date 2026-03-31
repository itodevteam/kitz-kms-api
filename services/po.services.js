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

exports.getPOWaitPrepare = async (params) => {
  const pool = await poolPromise;

  try {
    const result = await pool.request()
      .input("json", sql.NVarChar(sql.MAX), JSON.stringify(params))
      .execute("zsp_GetPOWaitPrepare");

    return {
      status: (result.recordsets && result.recordsets[0] && result.recordsets[0][0]) || {},
      data: result.recordsets[1] || []
    };
  } catch (error) {
    console.error("SERVICE ERROR:", error);
    throw error;
  }
};

exports.getPOWaitApprove = async (params) => {
  const pool = await poolPromise;

  try {
    const result = await pool.request()
      .input("json", sql.NVarChar(sql.MAX), JSON.stringify(params))
      .execute("zsp_GetPOWaitForApprove");

    return {
      status: (result.recordsets && result.recordsets[0] && result.recordsets[0][0]) || {},
      data: result.recordsets[1] || []
    };
  } catch (error) {
    console.error("SERVICE ERROR:", error);
    throw error;
  }
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
