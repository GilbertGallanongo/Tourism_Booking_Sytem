import { readFileSync } from 'fs';
import https from 'https';
import { createHash } from 'crypto';

const localFiles = ['public/build/assets/app.css', 'public/build/assets/app.js'];
localFiles.forEach((file) => {
  try {
    const data = readFileSync(file);
    const hash = createHash('sha256').update(data).digest('hex');
    console.log(`LOCAL ${file} LENGTH:${data.length} SHA256:${hash}`);
  } catch (error) {
    console.error(`LOCAL ${file} ERROR: ${error.message}`);
  }
});

const urls = [
  'https://tourism-booking-system-production-b0bb.up.railway.app/build/assets/app.css',
  'https://tourism-booking-system-production-b0bb.up.railway.app/build/assets/app.js',
];

let pending = urls.length;
urls.forEach((url) => {
  https.get(url, (res) => {
    const chunks = [];
    res.on('data', (chunk) => chunks.push(chunk));
    res.on('end', () => {
      const data = Buffer.concat(chunks);
      const hash = createHash('sha256').update(data).digest('hex');
      console.log(`REMOTE ${url} STATUS:${res.statusCode} LENGTH:${data.length} SHA256:${hash}`);
      pending -= 1;
      if (pending === 0) process.exit(0);
    });
  }).on('error', (error) => {
    console.error(`REMOTE ${url} ERROR: ${error.message}`);
    pending -= 1;
    if (pending === 0) process.exit(1);
  });
});
