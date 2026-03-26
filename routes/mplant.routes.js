const express = require("express");
const router = express.Router();
const mplantController = require("../controller/mplant.controller.js");
const verifyToken = require('../middleware/verifyToken');

// Plant Master
router.post("/getplant", verifyToken, mplantController.getPlant);
//router.post("/uploadplant", verifyToken, mplantController.uploadPlant);

module.exports = router;
