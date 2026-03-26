const { sql, poolPromise } = require("../config/db");

exports.getPlant = async (flag, cond) => {
  const pool = await poolPromise;
  const result = await pool
    .request()
    .input("flag", sql.NVarChar, flag)
    .input("cond", sql.NVarChar, cond)
    .query("EXEC mas_plant @flag,@cond");

  return result.recordset;
};


// exports.insPlant = async (flag,cond,data) => {
//   const pool = await poolPromise;
//   const result = await pool
//     .request()
//     .input("flag", sql.NVarChar, flag)
//     .input("cond", sql.NVarChar, cond)
//     .input("data", sql.NVarChar, data)
//     .query("EXEC mas_plant @flag,@cond,@data");
//   return result.recordset;
// };

// exports.uploadPlant = async (data) => {
//   const pool = await poolPromise;
//   const transaction = new sql.Transaction(pool);

//   try {
//     await transaction.begin();

//     for (const row of data) {
//       await new sql.Request(transaction)
//         .input("fig", sql.NVarChar, row.fig)
//         .input("cond", sql.NVarChar, row.condition || null)
//         .input("plantno", sql.NVarChar, row.plantno || null)
//         .input("plantname", sql.NVarChar, row.plantname || null)
//         .input("isactive", sql.NVarChar, row.isactive || null)
//         .input("createby", sql.NVarChar, row.createby || null)
//         .input("device", sql.NVarChar, row.device || null)
//         .execute("mas_plant");
//     }

//     await transaction.commit();
//     return true;

//   } catch (err) {
//     await transaction.rollback();
//     throw err;
//   }
// };
