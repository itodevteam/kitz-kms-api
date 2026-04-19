const express = require("express");
const router = express.Router();
const poController = require("../controller/po.controller");
const verifyToken = require('../middleware/verifyToken');

router.post("/upload", poController.uploadPO);
router.post("/master", poController.getPurOrderMaster);
router.post("/detail", poController.getPurOrderDetail);
router.post("/waitprepare", poController.getPOWaitPrepare);
router.post("/waitapprove", poController.getPOWaitApprove);
router.post("/createapproval", poController.createPOApproval);
router.post("/updateapproval", poController.updatePOApproval);
router.post("/approvalconfirm", poController.poApprovalConfirm);
router.post("/deletepreparation", poController.deleteParation);
router.post("/poapprove", poController.setPOApprove);
router.post("/sendingconfirm", poController.poSendingConfirm);
router.post("/reject", poController.poApprovalReject);
router.post("/renew", poController.poApprovalRenew);

module.exports = router;