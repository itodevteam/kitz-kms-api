const express = require("express");
const router = express.Router();
const { sql, poolPromise } = require("../config/db");

router.get("/getitem", async (req, res) => {
  try {
    const pool = await poolPromise;
    const result = await pool

      .request()
      .query("SELECT * FROM tbm_ItemMaster");

    res.status(200).json({
      success: true,
      message: "Item Data",
      data: result.recordset,
    });
  } catch (err) {
    res.status(500).send(err.message);
  }
});

router.post("/item", async (req, res) => {
  try {
    const { ownercode } = req.body;
    const pool = await poolPromise;
    const result = await pool

      .request()
      .input("ownercode", sql.NVarChar, ownercode)
      .query("SELECT * FROM tbm_ItemMaster");

    res.status(200).json({
      success: true,
      message: "Item Data",
      data: result.recordset,
    });
  } catch (err) {
    res.status(500).send(err.message);
  }
});

router.post("/vendor", async (req, res) => {
  try {
    const pool = await poolPromise;
    const result = await pool.query("EXEC zrp_GetVenderMaster");

    res.status(200).json({
      success: true,
      message: "Vender Data",
      data: result.recordset,
    });
  } catch (err) {
    res.status(500).send(err.message);
  }
});

module.exports = router;
