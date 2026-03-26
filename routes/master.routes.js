const express = require("express");
const router = express.Router();
const masterController = require("../controller/master.controller");
const verifyToken = require('../middleware/verifyToken');

// Item Master
router.get("/getitem", verifyToken, masterController.getItems);
router.post("/getitem", verifyToken, masterController.getItemByOwner);

// GET vendor




module.exports = router;