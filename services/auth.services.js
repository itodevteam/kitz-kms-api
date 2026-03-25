const jwt = require('jsonwebtoken');
const { sql, poolPromise } = require('../config/db');

// LOGIN
exports.login = async ({ username, password }) => {
  const pool = await poolPromise;

  const result = await pool
    .request()
    .input("username", sql.NVarChar, username)
    .input("password", sql.NVarChar, password)
    .query("EXEC zsp_GetLogin @username,@password");

  const user = result.recordset[0];

  if (!user) return null;

  const accessToken = jwt.sign(
    { uid: user.UserId },
    process.env.JWT_SECRET,
    { expiresIn: process.env.JWT_EXPIRE }
  );

  const refreshToken = jwt.sign(
    { uid: user.UserId },
    process.env.JWT_SECRET,
    { expiresIn: process.env.REFRESH_EXPIRE }
  );

  return { accessToken, refreshToken };
};

// REFRESH TOKEN
exports.refresh = (headers) => {
  const authHeader = headers['authorization'];
  const refreshToken = authHeader && authHeader.split(' ')[1];

  if (!refreshToken) {
    throw new Error('Refresh token required');
  }

  const decoded = jwt.verify(refreshToken, process.env.JWT_SECRET);

  const newAccessToken = jwt.sign(
    { uid: decoded.uid },
    process.env.JWT_SECRET,
    { expiresIn: process.env.JWT_EXPIRE }
  );

  return { accessToken: newAccessToken };
};