const express = require('express');
const bodyParser = require('body-parser');
const path = require('path');
const app = express();
const port = 3000;

// Basic server config
app.use(bodyParser.urlencoded({ extended: true }));
app.use(bodyParser.json());
app.use(express.static(path.join(__dirname, 'public')));

// Token → flag mapping (for demo only)
const VALID_TOKENS = {
  'LAB-TOKEN-john-12345': 'FLAG{lab_plaintext_flag_123}',
  'LAB-TOKEN-john-hash-12345': 'FLAG{lab_hashed_flag_456}'
};

// Protected endpoint expects token (Bearer header or cookie auth_token)
app.get('/getflag', (req, res) => {
  const auth = (req.get('Authorization') || '').trim();
  const cookieHeader = req.get('Cookie') || '';
  let token = null;

  if (auth.startsWith('Bearer ')) token = auth.slice(7);
  else {
    const m = cookieHeader.match(/auth_token=([^;]+)/);
    if (m) token = m[1];
  }

  if (token && VALID_TOKENS[token]) {
    res.json({ ok: true, flag: VALID_TOKENS[token] });
  } else {
    res.status(401).json({ ok: false, msg: 'Unauthorized - token missing or invalid' });
  }
});

// Simple exfil collector — prints to server console
app.post('/collect', (req, res) => {
  console.log('EXFILTRATION RECEIVED:', req.body);
  res.json({ ok: true });
});

app.listen(port, () => console.log(`Lab running: http://localhost:${port}`));
