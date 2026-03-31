const poService = require("../services/po.services");

exports.uploadPO = async (req, res) => {
  try {
    const { data } = req.body;

    if (!data || !Array.isArray(data) || data.length === 0) {
      return res.status(400).json({
        success: false,
        message: "Invalid data format"
      });
    }

    const result = await poService.insertPO(data);

    res.json({
      success: result.status.success === 1,
      message: result.status.message,
      totalRow: result.status.totalRow,
      data: result.data
    });

  } catch (error) {
    console.error("API ERROR:", error);

    res.status(500).json({
      success: false,
      message: "Internal Server Error",
      error: error.message
    });
  }
};

exports.getPOWaitPrepare = async (req, res) => {
  try {
    const { userno } = req.body;

    if (!userno) {
      return res.status(400).json({
        success: false,
        message: "Missing userno"
      });
    }

    const result = await poService.getPOWaitPrepare({ userno });

    res.json({
      success: result.status.success === 1,
      message: result.status.message,
      totalRow: result.status.totalRow,
      data: result.data
    });
  } catch (error) {
    console.error("API ERROR:", error);

    res.status(500).json({
      success: false,
      message: "Internal Server Error",
      error: error.message
    });
  }
};

exports.getPOWaitApprove = async (req, res) => {
  try {
    const { userNo } = req.body;   

    if (!userId || !plantId) {
      return res.status(400).json({
        success: false,
        message: "Missing userId or plantId"
      });
    }

    const result = await poService.getPOWaitForApprove({ userNo });
    res.json({
      success: result.status.success === 1,
      message: result.status.message,
      totalRow: result.status.totalRow,
      data: result.data
    });

  } catch (error) {
    console.error("API ERROR:", error);

    res.status(500).json({
      success: false,
      message: "Internal Server Error",
      error: error.message
    });
  }
};

exports.poApproval = async (req, res) => {
  try {
    const { payload } = req.body;

    if (!payload || !Array.isArray(payload) || payload.length === 0) {
      return res.status(400).json({
        success: false,
        message: "Invalid payload format"
      });
    }

    const result = await poService.poApproval(payload);

    res.json({
      success: result.success > 0,
      message: result.success > 0 ? "Approval successful" : "Approval failed",
      totalRow: result.total,
      data: result.failed
    });

  } catch (error) {
    console.error("API ERROR:", error);

    res.status(500).json({
      success: false,
      message: "Internal Server Error",
      error: error.message
    });
  }
};
