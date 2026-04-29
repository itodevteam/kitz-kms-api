const express = require("express");
const verifyToken = require("../middleware/verifyToken");
const dashboardController = require("../controller/dashboard.controller");

module.exports = function (io) {
  const router = express.Router();

  router.post("/deliveryplan",dashboardController.getDeliveryPlan(io));
  router.post("/podelay",dashboardController.getPODelay(io));
  router.post("/postatus",dashboardController.getPOStatus(io));
  router.post("/recentdata",dashboardController.getRecentData(io));
  router.post("/cardsummary",dashboardController.getCardsSummary(io));
  router.post("/backlog",dashboardController.getBacklog(io));
  
  return router;
};

