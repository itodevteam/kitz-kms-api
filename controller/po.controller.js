const poService = require("../services/po.service");

exports.uploadPO = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await poService.uploadPO(data);

    res.status(200).json({
      success: true,
      message: "Upload po data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};