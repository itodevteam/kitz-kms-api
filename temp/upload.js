const express = require("express");
const router = express.Router();
const { sql, poolPromise } = require("../config/db");

router.post("/upload", async (req, res) => {
  try {
    const { data } = req.body;

    const pool = await poolPromise;

    for (const row of data) {
      await pool.request()
        .input('Col1', sql.NVarChar, row.Col1)
        .input('Col2', sql.NVarChar, row.Col2)
        .execute('zrp_upload');
    }

    res.status(200).json({
      result: "Success",
      message: "Inserted completed",
    });
  } catch (err) {
    console.error("Error inserting:", err);
    res.status(500).send(err.message);
  }
});

module.exports = router;
