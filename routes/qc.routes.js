const express = require("express");
const router = express.Router();
const verifyToken = require('../middleware/verifyToken');
const qcController = require("../controller/qc.controller");

router.post("/iteminsp", qcController.getItemInspection);
router.post("/confirminsp", qcController.confirmInspection);

module.exports = router;