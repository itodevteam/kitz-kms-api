const express = require("express");
const verifyToken = require("../middleware/verifyToken");
const dashboardController = require("../controller/dashboard.controller");
const verifyToken = require('../middleware/verifyToken');

module.exports = function (io) {
  const router = express.Router();

  router.post(
    "/dashboard/waitingdata",
    verifyToken,
    dashboardController.getWaitingData(io)
  );

  return router;
};