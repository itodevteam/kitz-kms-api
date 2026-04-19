const express = require("express");
const router = express.Router();
const vendorController = require("../controller/vendor.controller");
const verifyToken = require('../middleware/verifyToken');
const { route } = require("./po.routes");

router.post("/confirm", vendorController.poVendorConfirm);
router.post("/create", vendorController.createDeliveryDetail);
router.post("/update", vendorController.updateDeliveryDetail);

module.exports = router;
