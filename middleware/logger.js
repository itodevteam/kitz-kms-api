const { sql, poolPromise } = require("../config/db");

module.exports = function (req, res, next) {
  const start = Date.now();

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

  res.on("finish", async () => {
    try {
      const pool = await poolPromise;

      const logData = [{
        LogType: "API",
        Module: req.baseUrl,
        Actions: req.path,
        ReqData: JSON.stringify(req.body),
        ResData: JSON.stringify(responseBody),
        Status: res.statusCode === 200 ? "SUCCESS" : "FAIL",
        Message: `Response time ${Date.now() - start} ms`,
        IPAddress: req.ip,
        CreateBy: "system"
      }];

      await pool.request()
        .input("Json", sql.NVarChar(sql.MAX), JSON.stringify(logData))
        .execute("zsp_InsertTracerLog");

    } catch (err) {
      console.error("Log error:", err.message);
    }
  });

  next();
};