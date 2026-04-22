const express = require("express");
const router = express.Router();
const qcController = require("../controller/receive.controller");
const verifyToken = require('../middleware/verifyToken');

router.post("/detail", verifyToken, qcController.getReceiveDetail);
router.post("/confirm", verifyToken, qcController.confirmReceive);

module.exports = router;

