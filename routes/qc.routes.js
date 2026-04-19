const express = require("express");
const router = express.Router();
const verifyToken = require('../middleware/verifyToken');
const qcController = require("../controller/qc.controller");