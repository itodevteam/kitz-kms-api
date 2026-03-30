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

// Category Master
router.post("/setcategory", masterController.setCategory);
router.post("/savecategory", masterController.saveCategory);
router.post("/deletecategory", masterController.deleteCategory);

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


module.exports = router;