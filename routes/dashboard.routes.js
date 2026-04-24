const express = require("express");
const verifyToken = require("../middleware/verifyToken");
const dashboardController = require("../controller/dashboard.controller");

module.exports = function (io) {
  const router = express.Router();

  router.post("/deliveryplan",dashboardController.getDeliveryPlan(io));
  
  return router;
};
