const e = require("cors");
const dashboardServices = require("../services/dashboard.services");

exports.getDeliveryPlan = (io) => {
  return async (req, res) => {
    try {
      const data  = req.body.data[0];

      const deliveryPlan = await dashboardServices.getDeliveryPlan(data);

      if (!deliveryPlan || deliveryPlan.length === 0) {
        return res.status(404).json({ message: "Not found delivery plan" });
      }

      // 🔥 realtime dashboard
      io.emit("dashboard-delivery-plan", deliveryPlan);

      res.status(200).json({
        result: "Success",
        message: "Dashboard Delivery Plan Data",
        data: deliveryPlan,
      });
    } catch (err) {
      console.error(err);
      res.status(500).send(err.message);
    }
  };
};

exports.getPODelay = (io) => {
  return async (req, res) => {
    try {
      const data  = req.body.data[0];

      const poDelay = await dashboardServices.getPODelay(data);

      if (!poDelay || poDelay.length === 0) {
        return res.status(404).json({ message: "Not found PO Delay" });
      }

      // 🔥 realtime dashboard
      io.emit("dashboard-po-delay", poDelay);

      res.status(200).json({
        result: "Success",
        message: "Dashboard PO Delay Data",
        data: poDelay,
      });
    } catch (err) {
      console.error(err);
      res.status(500).send(err.message);
    }
  };
};

exports.getPOStatus = (io) => {
  return async (req, res) => {
    try {
      const data  = req.body.data[0];

      const poStatus = await dashboardServices.getPOStatus(data);

      if (!poStatus || poStatus.length === 0) {
        return res.status(404).json({ message: "Not found PO Status" });
      }

      // 🔥 realtime dashboard
      io.emit("dashboard-po-status", poStatus);

      res.status(200).json({
        result: "Success",
        message: "Dashboard PO Status Data",
        data: poStatus,
      });
    } catch (err) {
      console.error(err);
      res.status(500).send(err.message);
    }
  };
};

exports.getRecentData = (io) => {
  return async (req, res) => {
    try {
      const data  = req.body.data[0];

      const recentData = await dashboardServices.getRecentData(data);

      if (!recentData || recentData.length === 0) {
        return res.status(404).json({ message: "Not found Recent Data" });
      }

      // 🔥 realtime dashboard
      io.emit("dashboard-recent-data", recentData);

      res.status(200).json({
        result: "Success",
        message: "Dashboard Recent Data",
        data: recentData,
      });
    } catch (err) {
      console.error(err);
      res.status(500).send(err.message);
    }
  };
};

exports.getCardsSummary = (io) => {
  return async (req, res) => {
    try {
      const data  = req.body.data[0];

      const cardsSummary = await dashboardServices.getCardSummary(data);

      if (!cardsSummary || cardsSummary.length === 0) {
        return res.status(404).json({ message: "Not found Cards Summary" });
      }

      // 🔥 realtime dashboard
      io.emit("dashboard-cards-summary", cardsSummary);

      res.status(200).json({
        result: "Success",
        message: "Dashboard Cards Summary",
        data: cardsSummary,
      });
    } catch (err) {
      console.error(err);
      res.status(500).send(err.message);
    }
  };
};

exports.getBacklog = (io) => {
  return async (req, res) => {
    try {
      const data  = req.body.data[0];

      const backlog = await dashboardServices.getBacklog(data);

      if (!backlog || backlog.length === 0) {
        return res.status(404).json({ message: "Not found Backlog" });
      }

      // 🔥 realtime dashboard
      io.emit("dashboard-backlog", backlog);

      res.status(200).json({
        result: "Success",
        message: "Dashboard Backlog Data",
        data: backlog,
      });
    } catch (err) {
      console.error(err);
      res.status(500).send(err.message);
    }
  };
};


