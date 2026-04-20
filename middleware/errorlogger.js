const { sql, poolPromise } = require("../config/db");

module.exports = async (err, req, res, next) => {
  try {
    const pool = await poolPromise;

    let responseBody; // 🔥 เก็บ response

    // ✅ ดัก res.json
    const originalJson = res.json;
    res.json = function (body) {
      responseBody = body;
      return originalJson.call(this, body);
    };

    // ✅ ดัก res.send
    const originalSend = res.send;
    res.send = function (body) {
      responseBody = body;
      return originalSend.call(this, body);
    };

    const logData = [{
      LogType: "ERROR",
      Module: req.baseUrl,
      Actions: req.path,
      OrderNo: req.body?.OrderNo || null,
      OrderType: req.body?.OrderType || null,
      ReqData: JSON.stringify(req.body)?.substring(0, 4000),
      ResData: JSON.stringify(responseBody)?.substring(0, 4000),
      Status: "FAIL",
      Message: err.message,
      IPAddress: req.ip,
      CreateBy: req.user?.userNo || "system",
      StackTrace: err.stack 
    }];

    await pool.request()
      .input("Json", sql.NVarChar(sql.MAX), JSON.stringify(logData))
      .execute("zsp_InsertTracerLog");

  } catch (e) {
    console.error("Error logging failed:", e.message);
  }

  res.status(500).json({
    success: false,
    message: err.message
  });

  next();
};