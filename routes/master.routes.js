const express = require("express");
const router = express.Router();
const masterController = require("../controller/master.controller");

// GET item All
router.get("/getitem", masterController.getItems);

// POST item (filter by ownercode)
router.post("/item", masterController.getItemByOwner);

// GET vendor
router.post("/vendor", masterController.getVendor);

module.exports = router;