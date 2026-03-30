const express = require("express");
const router = express.Router();
const masterController = require("../controller/master.controller");
const verifyToken = require('../middleware/verifyToken');

// Plant Master
//router.post("/setplant", verifyToken, masterController.setPlant);
//router.post("/saveplant", verifyToken, masterController.savePlant);
router.post("/setplant", masterController.setPlant);
router.post("/saveplant", masterController.savePlant);

module.exports = router;