const express = require("express");
const router = express.Router();
const poController = require("../controller/po.controller");
const verifyToken = require('../middleware/verifyToken');

// PO Orders
router.post("/upload", poController.uploadPO);
router.get("/master", poController.getPOMaster);
router.post("/detail", poController.getPODetail);
router.post("/waitprepare", poController.getPOWaitPrepare);
router.post("/waitapprove", poController.getPOWaitApprove);
router.post("/approval", poController.poApproval);
router.post("/waitapprovedetail", poController.getPOWaitApprove);
module.exports = router;