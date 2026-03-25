const express = require("express");
const { sql, poolPromise } = require("../config/db");
const verifyToken = require('../middleware/verifyToken');


module.exports = function (io) {
    const router = express.Router();
  
    router.post("/waitingdata", verifyToken , async (req, res) => {
      try {
        const { ownercode } = req.body;
        const pool = await poolPromise;
  
        const result = await pool
          .request()
          .query("EXEC zrp_SBMWaitingData");
  
        if (result.recordset.length === 0) {
          return res.status(401).json({ message: "Not found waiting data" });
        }
  
        io.emit("waiting-data", result.recordset);
  
        res.status(200).json({
          result: "Success",
          message: "Waiting Data",
          data: result.recordset,
        });
      } catch (err) {
        res.status(500).send(err.message);
      }
    });

    return router;
  };
  