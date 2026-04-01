const express = require("express");
const router = express.Router();
const poController = require("../controller/po.controller");
const verifyToken = require('../middleware/verifyToken');

// PO Orders
<<<<<<< HEAD
router.post("/upload", poController.uploadPO);
=======
router.post("/upload", verifyToken, poController.uploadPO);
router.get("/master", verifyToken, poController.getPOMaster);
>>>>>>> 1164fe9fec4c8ecfd2898e646c7a7eb6b7b80187
router.post("/waitprepare", verifyToken, poController.getPOWaitPrepare);
router.post("/waitapprove", verifyToken, poController.getPOWaitApprove);
router.post("/approval", verifyToken, poController.poApproval);

module.exports = router;