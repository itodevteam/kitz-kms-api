const express = require('express');
const router = express.Router();
const authController = require('../controller/auth.controller');
const verifyToken = require('../middleware/verifyToken');

// login
router.post('/login', authController.login);

// refresh token
router.post('/refresh', authController.refresh);

// Menu Auth
router.post("/setmenu", authController.setMenu);
router.post("/savemenu", authController.saveMenu);
router.post("/deletemenu", authController.deleteMenu);

// Submenu Auth
router.post("/setsubmenu", authController.setSubmenu);
router.post("/savesubmenu", authController.saveSubmenu);
router.post("/deletesubmenu", authController.deleteSubmenu);

// Permission Auth
router.post("/setpermission", authController.setPermission);
router.post("/savepermission", authController.savePermission);
router.post("/deletepermission", authController.deletePermission);

// Account Auth
router.post("/setaccount", authController.setAccount);
router.post("/saveaccount", authController.saveAccount);
router.post("/deleteaccount", authController.deleteAccount);
module.exports = router;