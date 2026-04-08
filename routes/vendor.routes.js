const express = require("express");
const router = express.Router();
const vendorController = require("../controller/vendor.controller");
const verifyToken = require('../middleware/verifyToken');
const { route } = require("./po.routes");

router.post("/waitconfirm", vendorController.getVendorWaitConfirm);
router.post("/confirm", vendorController.poVendorConfirm);

module.exports = router;
