const express = require("express");
const router = express.Router();
const poController = require("../controller/po.controller");
const verifyToken = require('../middleware/verifyToken');

// PO Orders
router.post("/uploadpo", verifyToken, poController.uploadPO);


module.exports = router;