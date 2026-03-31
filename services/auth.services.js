const jwt = require('jsonwebtoken');
const { sql, poolPromise } = require('../config/db');

// LOGIN
exports.login = async ({ username, password }) => {
  const pool = await poolPromise;

  const result = await pool
    .request()
    .input("username", sql.NVarChar, username)
    .input("password", sql.NVarChar, password)
    .query("EXEC zsp_GetLogin @username,@password");

  const user = result.recordset[0];

  if (!user) return null;

  const accessToken = jwt.sign(
    { uid: user.UserId },
    process.env.JWT_SECRET,
    { expiresIn: process.env.JWT_EXPIRE }
  );

  const refreshToken = jwt.sign(
    { uid: user.UserId },
    process.env.JWT_SECRET,
    { expiresIn: process.env.REFRESH_EXPIRE }
  );

  const userId = user.UserId;
  const PermissionCode = user.Permission;
  return { accessToken, refreshToken, userId, PermissionCode  };
};

// REFRESH TOKEN
exports.refresh = (headers) => {
  const authHeader = headers['authorization'];
  const refreshToken = authHeader && authHeader.split(' ')[1];

  if (!refreshToken) {
    throw new Error('Refresh token required');
  }

  const decoded = jwt.verify(refreshToken, process.env.JWT_SECRET);

  const newAccessToken = jwt.sign(
    { uid: decoded.uid },
    process.env.JWT_SECRET,
    { expiresIn: process.env.JWT_EXPIRE }
  );

  return { accessToken: newAccessToken };
};

// Menu Auth

exports.setMenu = async (flag, cond) => {
  const pool = await poolPromise;
  const result = await pool
    .request()
    .input("flag", sql.NVarChar, flag)
    .input("cond", sql.NVarChar, cond)
    .query("EXEC aut_menu @flag,@cond");

  return result.recordset;
};

exports.saveMenu = async (data) => {
  const pool = await poolPromise;
  const transaction = new sql.Transaction(pool);

  try {
    await transaction.begin();

    for (const row of data) {
      await new sql.Request(transaction)
        .input("flag", sql.NVarChar, row.flag || null)
        .input("cond", sql.NVarChar, row.cond || null)
        .input("groupmenu", sql.NVarChar, row.groupmenu || null)
        .input("menuname", sql.NVarChar, row.menuname || null)
        .input("url", sql.NVarChar, row.url || null)
        .input("sequence", sql.NVarChar, row.sequence || null)
        .input("classicon", sql.NVarChar, row.classicon || null)
        .input("isactive", sql.Bit, row.isactive ?? null)
        .input("createby", sql.NVarChar, row.createdby || null)
        .input("device", sql.NVarChar, row.device || null)
        .execute("aut_menu");
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
exports.deleteMenu = async (data) => {
  const pool = await poolPromise;
  const transaction = new sql.Transaction(pool);

  try {
    await transaction.begin();

    for (const row of data) {
      await new sql.Request(transaction)
        .input("flag", sql.NVarChar, row.flag || null)
        .input("cond", sql.NVarChar, row.cond || null)
        .execute("aut_menu");
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

// Submenu Auth

exports.setSubmenu = async (flag, cond) => {
  const pool = await poolPromise;
  const result = await pool
    .request()
    .input("flag", sql.NVarChar, flag)
    .input("cond", sql.NVarChar, cond)
    .query("EXEC aut_submenu @flag,@cond");

  return result.recordset;
};

exports.saveSubmenu = async (data) => {
  const pool = await poolPromise;
  const transaction = new sql.Transaction(pool);

  try {
    await transaction.begin();

    for (const row of data) {
      await new sql.Request(transaction)
        .input("flag", sql.NVarChar, row.flag || null)
        .input("cond", sql.NVarChar, row.cond || null)
        .input("menuid", sql.NVarChar, row.menuid || null)
        .input("submenuname", sql.NVarChar, row.submenuname || null)
        .input("url", sql.NVarChar, row.url || null)
        .input("sequence", sql.NVarChar, row.sequence || null)
        .input("isactive", sql.Bit, row.isactive ?? null)
        .input("createby", sql.NVarChar, row.createdby || null)
        .input("device", sql.NVarChar, row.device || null)
        .execute("aut_submenu");
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
exports.deleteSubmenu = async (data) => {
  const pool = await poolPromise;
  const transaction = new sql.Transaction(pool);

  try {
    await transaction.begin();

    for (const row of data) {
      await new sql.Request(transaction)
        .input("flag", sql.NVarChar, row.flag || null)
        .input("cond", sql.NVarChar, row.cond || null)
        .execute("aut_submenu");
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

// Permission Auth

exports.setPermission = async (flag, cond) => {
  const pool = await poolPromise;
  const result = await pool
    .request()
    .input("flag", sql.NVarChar, flag)
    .input("cond", sql.NVarChar, cond)
    .query("EXEC aut_permission @flag,@cond");

  return result.recordset;
};

exports.savePermission = async (data) => {
  const pool = await poolPromise;
  const transaction = new sql.Transaction(pool);

  try {
    await transaction.begin();

    for (const row of data) {
      await new sql.Request(transaction)
        .input("flag", sql.NVarChar, row.flag || null)
        .input("cond", sql.NVarChar, row.cond || null)
        .input("permissioncode", sql.NVarChar, row.permissioncode || null)
        .input("isactive", sql.Bit, row.isactive ?? null)
        .input("createby", sql.NVarChar, row.createdby || null)
        .input("device", sql.NVarChar, row.device || null)
        .execute("aut_permission");
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
exports.deletePermission = async (data) => {
  const pool = await poolPromise;
  const transaction = new sql.Transaction(pool);

  try {
    await transaction.begin();

    for (const row of data) {
      await new sql.Request(transaction)
        .input("flag", sql.NVarChar, row.flag || null)
        .input("cond", sql.NVarChar, row.cond || null)
        .execute("aut_permission");
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
// Account Auth

exports.setAccount = async (flag, cond) => {
  const pool = await poolPromise;
  const result = await pool
    .request()
    .input("flag", sql.NVarChar, flag)
    .input("cond", sql.NVarChar, cond)
    .query("EXEC aut_account @flag,@cond");

  return result.recordset;
};

exports.saveAccount = async (data) => {
  const pool = await poolPromise;
  const transaction = new sql.Transaction(pool);

  try {
    await transaction.begin();

    for (const row of data) {
      await new sql.Request(transaction)
        .input("flag", sql.NVarChar, row.flag || null)
        .input("cond", sql.NVarChar, row.cond || null)
        .input("permissioncode", sql.NVarChar, row.permissioncode || null)
        .input("username", sql.NVarChar, row.username || null)
        .input("password", sql.NVarChar, row.password || null)
        .input("passwordh", sql.NVarChar, row.passwordh || null)
        .input("isactive", sql.Bit, row.isactive ?? null)
        .input("createby", sql.NVarChar, row.createdby || null)
        .input("device", sql.NVarChar, row.device || null)
        .execute("aut_account");
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
exports.deleteAccount = async (data) => {
  const pool = await poolPromise;
  const transaction = new sql.Transaction(pool);

  try {
    await transaction.begin();

    for (const row of data) {
      await new sql.Request(transaction)
        .input("flag", sql.NVarChar, row.flag || null)
        .input("cond", sql.NVarChar, row.cond || null)
        .execute("aut_account");
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