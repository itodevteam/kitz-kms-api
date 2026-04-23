const express = require("express");
const router = express.Router();
const qcController = require("../controller/receive.controller");
const verifyToken = require('../middleware/verifyToken');

router.post("/master", verifyToken, qcController.getDeliveryMaster);
router.post("/detail", verifyToken, qcController.getDeliveryDetail);
router.post("/receive", verifyToken, qcController.getReceiveDetail);
router.post("/confirm", verifyToken, qcController.confirmReceive);

module.exports = router;

