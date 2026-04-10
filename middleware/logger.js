const fs = require('fs');
const path = require('path');

'use strict';


const LOG_DIR = process.env.LOG_DIR || path.join(process.cwd(), 'logs');
const LOG_FILE = path.join(LOG_DIR, 'app.log');

// ensure log directory exists
try { fs.mkdirSync(LOG_DIR, { recursive: true }); } catch (e) { /* ignore */ }

function timestamp() {
    return new Date().toISOString();
}

function safeStringify(obj, depth = 3) {
    const cache = new Set();
    return JSON.stringify(obj, function (key, value) {
        if (typeof value === 'object' && value !== null) {
            if (cache.has(value)) return '[Circular]';
            cache.add(value);
        }
        // avoid dumping very deep structures
        if (depth <= 0 && typeof value === 'object' && value !== null) return '[Object]';
        return value;
    }, 2);
}

function appendLog(line) {
    const entry = `${timestamp()} ${line}\n`;
    // best-effort append to file (non-blocking)
    fs.appendFile(LOG_FILE, entry, err => { /* ignore write errors */ });
    // also mirror to console
    if (line.startsWith('[ERROR]')) console.error(entry);
    else if (line.startsWith('[WARN]')) console.warn(entry);
    else console.log(entry);
}

function levelTag(statusOrLevel) {
    if (typeof statusOrLevel === 'number') {
        if (statusOrLevel >= 500) return '[ERROR]';
        if (statusOrLevel >= 400) return '[WARN]';
        return '[INFO]';
    }
    const lvl = String(statusOrLevel).toUpperCase();
    return `[${lvl}]`;
}

// Express request logger middleware
function requestLogger(req, res, next) {
    const start = process.hrtime();
    const { method, originalUrl } = req;
    const reqMeta = {
        params: req.params,
        query: req.query,
    };

    // capture request body safely (may be undefined)
    let bodySnapshot;
    try { bodySnapshot = req.body !== undefined ? req.body : undefined; } catch (e) { bodySnapshot = '[unavailable]'; }

    res.on('finish', () => {
        const diff = process.hrtime(start);
        const ms = Math.round((diff[0] * 1e3) + (diff[1] / 1e6));
        const tag = levelTag(res.statusCode);
        const msg = `${tag} ${method} ${originalUrl} ${res.statusCode} ${ms}ms params=${safeStringify(reqMeta.params)} query=${safeStringify(reqMeta.query)} body=${safeStringify(bodySnapshot)}`;
        appendLog(msg);
    });

    // in case of abort (client disconnect)
    res.on('close', () => {
        if (!res.finished) {
            const diff = process.hrtime(start);
            const ms = Math.round((diff[0] * 1e3) + (diff[1] / 1e6));
            appendLog(`[WARN] ${method} ${originalUrl} aborted after ${ms}ms`);
        }
    });

    next();
}

// Express error-logging middleware (must come after routes)
// usage: app.use(errorLogger);
function errorLogger(err, req, res, next) {
    const { method, originalUrl } = req || {};
    const message = `[ERROR] ${method || '-'} ${originalUrl || '-'} ${err && (err.stack || err.message)}`;
    appendLog(message);
    // pass through to default handlers
    next(err);
}

// simple programmatic logger
function log(level, ...args) {
    const tag = levelTag(level);
    const msg = args.map(a => (typeof a === 'string' ? a : safeStringify(a))).join(' ');
    appendLog(`${tag} ${msg}`);
}

module.exports = {
    requestLogger,
    errorLogger,
    log,
};