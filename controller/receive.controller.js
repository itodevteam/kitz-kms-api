const e = require("cors");
const receiveService = require("../services/receive.services");

exports.getDeliveryMaster = async (req, res) => {
  try {
    const data = req.body;  

    const result = await receiveService.getDeliveryMaster(data);

    res.json({
      success: result.info?.[0]?.success === 1,
      message: result.info?.[0]?.message || "Success",
      data: result.data || []
    });

  } catch (err) {
    console.error(err);
  }
};

exports.getDeliveryDetail = async (req, res) => {
  try {
    const data = req.body;

    const result = await receiveService.getDeliveryDetail(data);

    res.json({
      success: result.info?.[0]?.success === 1,
      message: result.info?.[0]?.message || "Success",
      data: result.data || []
    });

  } catch (err) {
    console.error(err);
  }
};

exports.getReceiveDetail = async (req, res) => {
  try {
    const data = req.body;

    const result = await receiveService.getReceiveDetail(data);

    res.json({
      success: result.info?.[0]?.success === 1,
      message: result.info?.[0]?.message || "Success",
      data: result.data || []
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};

exports.confirmReceive = async (req, res) => {
  try {
    const data = req.body;
    const result = await receiveService.confirmReceive(data);

    res.json({
      success: result.info?.[0]?.success === 1,
      message: result.info?.[0]?.message || "Success",
      data: result.data || []
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};
