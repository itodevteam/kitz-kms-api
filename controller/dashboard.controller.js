const dashboardServices = require("../services/dashboard.services");

exports.getWaitingData = (io) => {
  return async (req, res) => {
    try {
      const { ownercode } = req.body;

      const data = await dashboardServices.getWaitingData(ownercode);

      if (!data || data.length === 0) {
        return res.status(404).json({ message: "Not found waiting data" });
      }

      // 🔥 realtime dashboard
      io.emit("dashboard-waiting-data", data);

      res.status(200).json({
        result: "Success",
        message: "Dashboard Waiting Data",
        data,
      });
    } catch (err) {
      console.error(err);
      res.status(500).send(err.message);
    }
  };
};

