import sharp from 'sharp';
import fs from 'fs';
import path from 'path';

const SRCS = ['sanyangsam_이끼_20종_제품사진', 'sanyangsam_추가20종_제품사진'];
const OUT = 'storage/app/public/products';

fs.mkdirSync(OUT, { recursive: true });

let total = 0;
for (const SRC of SRCS) {
    if (!fs.existsSync(SRC)) { console.log('폴더 없음(건너뜀):', SRC); continue; }
    // 01_3yr_3roots.png → set-3yr-3.jpg
    const files = fs.readdirSync(SRC).filter(f => /\.png$/i.test(f)).sort();
    console.log(`[${SRC}] ${files.length}종 최적화`);

    for (const f of files) {
        const m = f.match(/^\d+_(\d+)yr_(\d+)roots/i);
        if (!m) { console.log('skip', f); continue; }
        const year = m[1], roots = m[2];
        const outName = `set-${year}yr-${roots}.jpg`;
        const input = path.join(SRC, f);

        // 대표(정사각 크롭, 상세/썸네일 공용) 1200px
        await sharp(input)
            .rotate()
            .resize({ width: 1200, height: 1200, fit: 'cover', position: 'centre' })
            .jpeg({ quality: 84, mozjpeg: true })
            .toFile(path.join(OUT, outName));

        process.stdout.write(`  ${outName} `);
        total++;
    }
    console.log('');
}
console.log(`완료 (총 ${total}종)`);
