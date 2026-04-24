const express = require("express");
const router = express.Router();
const receiveController = require("../controller/receive.controller");
const verifyToken = require('../middleware/verifyToken');

router.post("/master", receiveController.getDeliveryMaster);
router.post("/detail", receiveController.getDeliveryDetail);
router.post("/receive", receiveController.getReceiveDetail);
router.post("/confirm", receiveController.confirmReceive);

module.exports = router;

