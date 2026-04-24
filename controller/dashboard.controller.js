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

