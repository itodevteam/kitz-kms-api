const e = require("cors");
const qcServices = require("../services/receive.services");

exports.getDeliveryMaster = async (req, res) => {
  try {
    const data = req.body;  

    const result = await qcServices.getDeliveryMaster(data);

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

    const result = await qcServices.getDeliveryDetail(data);

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
    const { deliveryNo } = req.body;

    const result = await qcServices.getReceiveDetail(deliveryNo);

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
    const result = await qcServices.confirmReceive(data);

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
