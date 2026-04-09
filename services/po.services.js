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

exports.getPurOrderDetail = async (data) => {
  const pool = await poolPromise; 

  const result = await pool
    .request()
    .input("PlantNo", sql.NVarChar(50), data.PlantNo)
    .input("PurOrderNo", sql.NVarChar(50), data.PurOrderNo)
    .input("OrderStatus", sql.NVarChar(50), data.OrderStatus)
    .execute("zsp_GetPurOrderDetail");

  return result.recordset;
};

exports.getPOWaitPrepare = async () => {
  const pool = await poolPromise;
  const result = await pool
    .request()
    .query("zsp_GetPOWaitPrepare");
  
  return result.recordset;
};

exports.getPOWaitApprove = async (userNo) => {
  const pool = await poolPromise;
  const result = await pool
    .request()
    .input("userNo", sql.NVarChar, userNo)
    .query("zsp_GetPOWaitApprove");

  return result.recordset;
};

exports.createPOApproval = async (data, createBy) => {
  const pool = await poolPromise;

  const result = await pool
    .request()
    .input("Json", sql.NVarChar(sql.MAX), JSON.stringify(data))
    .input("CreateBy", sql.NVarChar(50), createBy)
    .execute("zsp_CreatePOApproval");

  return {
    info: result.recordsets[0],
    data: result.recordsets[1] 
  };
};

exports.updatePOApproval = async (data, createBy) => {
  const pool = await poolPromise;

  const result = await pool
    .request()
    .input("Json", sql.NVarChar(sql.MAX), JSON.stringify(data))
    .input("CreateBy", sql.NVarChar(50), createBy)
    .execute("zsp_UpdatePOApproval");

  return {
    info: result.recordsets[0],
    data: result.recordsets[1]
  };
};

exports.poApprovalConfirm = async (data) => {
  const pool = await poolPromise; 

  const result = await pool
    .request()
    .input("Json", sql.NVarChar(sql.MAX), JSON.stringify(data))
    .execute("zsp_POApprovalConfirm");

  return {
    info: result.recordsets[0],
    data: result.recordsets[1]
  };
};

exports.deleteParation = async (data) => {
  const pool = await poolPromise;
  const transaction = new sql.Transaction(pool);

  try {
    await transaction.begin();

    for (const row of data) {
      await new sql.Request(transaction)
        .input("flag", sql.NVarChar, row.flag || null)
        .input("cond", sql.NVarChar, row.cond || null)
        .execute("ope_purchaseorder");
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

exports.setPOApprove = async (flag, cond) => {
  const pool = await poolPromise;
  const result = await pool
    .request()
    .input("flag", sql.NVarChar, flag)
    .input("cond", sql.NVarChar, cond)
    .query("EXEC ope_purchaseorder @flag,@cond");

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
