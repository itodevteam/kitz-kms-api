const e = require("cors");
const qcServices = require("../services/qc.services");

exports.getItemInspection = async (req, res) => {
  try {
    const data = req.body.data[0];
    const result = await qcServices.getItemInspection(data);

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

exports.confirmInspectionItem = async (req, res) => {
  try {
    const data = req.body.data[0];
    const result = await qcServices.confirmInspectionItem(data);

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

exports.confirmInspectionOrder = async (req, res) => {
  try {
    const data = req.body.data[0];
    const result = await qcServices.confirmInspectionOrder(data);

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

