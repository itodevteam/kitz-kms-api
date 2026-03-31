const express = require("express");
const router = express.Router();
const poController = require("../controller/po.controller");
const verifyToken = require('../middleware/verifyToken');

// PO Orders
router.post("/upload", poController.uploadPO);
router.post("/waitprepare", verifyToken, poController.getPOWaitPrepare);
router.post("/waitapprove", verifyToken, poController.getPOWaitApprove);
router.post("/approval", verifyToken, poController.poApproval);

module.exports = router;