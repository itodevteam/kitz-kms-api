const express = require("express");
const router = express.Router();
const qcController = require("../controller/qc.controller");
const verifyToken = require('../middleware/verifyToken');