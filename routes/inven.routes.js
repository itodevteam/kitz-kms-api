const express = require("express");
const router = express.Router();
const verifyToken = require('../middleware/verifyToken');
const invenController = require("../controller/inven.controller");

router.post("/getReceiveData", verifyToken, invenController.getReceiveData);
router.post("/getBacklogData", verifyToken, invenController.getBacklogData);

module.exports = router;