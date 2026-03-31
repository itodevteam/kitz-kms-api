const express = require("express");
const router = express.Router();
const masterController = require("../controller/master.controller");
const verifyToken = require('../middleware/verifyToken');

// Plant Master
//router.post("/setplant", verifyToken, masterController.setPlant);
//router.post("/saveplant", verifyToken, masterController.savePlant);
router.post("/setplant", masterController.setPlant);
router.post("/saveplant", masterController.savePlant);
router.post("/deleteplant", masterController.deletePlant);

<<<<<<< HEAD
// Category Master
router.post("/setcategory", masterController.setCategory);
router.post("/savecategory", masterController.saveCategory);
router.post("/deletecategory", masterController.deleteCategory);
=======
//router.post("/saveplant", verifyToken, masterController.savePlant);

// test 

>>>>>>> 6095ed643d3bdfc4db76fa292a4fe6761783eba4

// Unit Master
router.post("/setunit", masterController.setUnit);
router.post("/saveunit", masterController.saveUnit);
router.post("/deleteunit", masterController.deleteUnit);

// Language Master
router.post("/setlanguage", masterController.setLanguage);
router.post("/savelanguage", masterController.saveLanguage);
router.post("/deletelanguage", masterController.deleteLanguage);

// Employee Master
router.post("/setemployee", masterController.setEmployee);
router.post("/saveemployee", masterController.saveEmployee);
router.post("/deleteemployee", masterController.deleteEmployee);

// Currency Master
router.post("/setcurrency", masterController.setCurrency);
router.post("/savecurrency", masterController.saveCurrency);
router.post("/deletecurrency", masterController.deleteCurrency);

// Department Master
router.post("/setdepartment", masterController.setDepartment);
router.post("/savedepartment", masterController.saveDepartment);
router.post("/deletedepartment", masterController.deleteDepartment);

// Matrix Master
router.post("/setmatrix", masterController.setMatrix);
router.post("/savematrix", masterController.saveMatrix);
router.post("/deletematrix", masterController.deleteMatrix);

// Paymentterm Master
router.post("/setpaymentterm", masterController.setPaymentterm);
router.post("/savepaymentterm", masterController.savePaymentterm);
router.post("/deletepaymentterm", masterController.deletePaymentterm);

// Position Master
router.post("/setposition", masterController.setPosition);
router.post("/saveposition", masterController.savePosition);
router.post("/deleteposition", masterController.deletePosition);

// Positionpattern Master
router.post("/setpositionpattern", masterController.setPositionpattern);
router.post("/savepositionpattern", masterController.savePositionpattern);
router.post("/deletepositionpattern", masterController.deletePositionpattern);

// Sex Master
router.post("/setsex", masterController.setSex);
router.post("/savesex", masterController.saveSex);
router.post("/deletesex", masterController.deleteSex);

// Status Master
router.post("/setstatus", masterController.setStatus);
router.post("/savestatus", masterController.saveStatus);
router.post("/deletestatus", masterController.deleteStatus);
module.exports = router;

// Titlename Master
router.post("/settitlename", masterController.setTitlename);
router.post("/savetitlename", masterController.saveTitlename);
router.post("/deletetitlename", masterController.deleteTitlename);
module.exports = router;

// Vendor Master
router.post("/setvendor", masterController.setVendor);
router.post("/savevendor", masterController.saveVendor);
router.post("/deletevendor", masterController.deleteVendor);
module.exports = router;