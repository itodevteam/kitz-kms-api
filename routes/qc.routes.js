const express = require("express");
const router = express.Router();
const verifyToken = require('../middleware/verifyToken');
const qcController = require("../controller/qc.controller");

router.post("/getiteminsp", qcController.getItemInspection);
router.post("/confirminspitem", qcController.confirmInspectionItem);
router.post("/confirminsporder", qcController.confirmInspectionOrder);

module.exports = router;