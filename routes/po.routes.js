const express = require("express");
const router = express.Router();
const poController = require("../controller/po.controller");
const verifyToken = require('../middleware/verifyToken');

// PO Orders
router.post("/upload", verifyToken, poController.uploadPO);
router.get("/master", verifyToken, poController.getPOMaster);
router.post("/waitprepare", verifyToken, poController.getPOWaitPrepare);
router.post("/waitapprove", verifyToken, poController.getPOWaitApprove);
router.post("/approval", verifyToken, poController.poApproval);

module.exports = router;