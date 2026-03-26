const { sql, poolPromise } = require("../config/db");

// Plant Master
exports.setPlant = async (flag, cond) => {
  const pool = await poolPromise;
  const result = await pool
    .request()
    .input("flag", sql.NVarChar, flag)
    .input("cond", sql.NVarChar, cond)
    .query("EXEC mas_plant @flag,@cond");

  return result.recordset;
};

exports.savePlant = async (data) => {
  const pool = await poolPromise;
  const transaction = new sql.Transaction(pool);

  try {
    await transaction.begin();

    for (const row of data) {
      await new sql.Request(transaction)
        .input("flag", sql.NVarChar, row.flag || null)
        .input("cond", sql.NVarChar, row.cond || null)
        .input("plantno", sql.NVarChar, row.plantno || null)
        .input("plantname", sql.NVarChar, row.plantname || null)
        .input("isactive", sql.Bit, row.isactive ?? null)
        .input("createby", sql.NVarChar, row.createdby || null)
        .input("device", sql.NVarChar, row.device || null)
        .execute("mas_plant");
    }

    await transaction.commit();

    return {
      success: true,
      message: "Transaction completed"
    };

  } catch (err) {

    console.error("TRANSACTION ERROR:", err.message);

    // ✅ ป้องกัน rollback ซ้ำ
    if (!transaction._aborted) {
      await transaction.rollback();
    }

    throw new Error(`Transaction failed: ${err.message}`);
  }
};