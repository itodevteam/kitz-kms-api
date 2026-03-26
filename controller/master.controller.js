const masterService = require("../services/master.service");

// Plant Master
exports.setPlant = async (req, res) => {
  try {
    const { flag, cond } = req.body;

    const data = await masterService.setPlant(flag, cond);

    res.json({
      success: true,
      message: "Set plant data completed",
      data: data
    });

  } catch (error) {
    res.status(500).json({
      success: false,
      message: error.message
    });
  }
};

exports.savePlant = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data)) {
      return res.status(400).json({
        message: "data must be array"
      });
    }

    await masterService.savePlant(data);

    res.status(200).json({
      success: true,
      message: "Upload plant data completed"
    });

  } catch (err) {
    console.error(err);
    res.status(500).json({
      success: false,
      message: err.message
    });
  }
};


