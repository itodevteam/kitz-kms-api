const express = require("express");
const router = express.Router();
const mplantController = require("../controller/mplant.controller.js");
const verifyToken = require('../middleware/verifyToken');

// Plant Master
router.post("/setplant", verifyToken, mplantController.setPlant);
router.post("/saveplant", verifyToken, mplantController.savePlant);

module.exports = router;
