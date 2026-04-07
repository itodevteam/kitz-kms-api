const express = require("express");
const router = express.Router();
const poController = require("../controller/po.controller");
const verifyToken = require('../middleware/verifyToken');

// PO Orders
router.post("/upload", poController.uploadPO);
router.post("/master", poController.getPOMaster);
router.post("/detail", poController.getPODetail);
router.post("/waitprepare", poController.getPOWaitPrepare);
router.post("/waitapprove", poController.getPOWaitApprove);
router.post("/waitapprovedetail", poController.getPOWaitApproveDetail);
router.post("/createapproval", poController.createPOApproval);
router.post("/updateapproval", poController.updatePOApproval);
router.post("/approvalconfirm", poController.poApprovalConfirm);
router.post("/deletepreparation", poController.deleteParation);

module.exports = router;