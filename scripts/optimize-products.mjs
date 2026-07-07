import sharp from 'sharp';
import fs from 'fs';
import path from 'path';

const SRCS = ['sanyangsam_이끼_20종_제품사진', 'sanyangsam_추가20종_제품사진', '추가 카테고리'];
const OUT = 'storage/app/public/products';

fs.mkdirSync(OUT, { recursive: true });

// 파일명 → 출력명 규칙
function outNameFor(f) {
    let m = f.match(/^\d+_(\d+)yr_(\d+)roots/i);   // 01_3yr_3roots → set-3yr-3.jpg
    if (m) return `set-${m[1]}yr-${m[2]}.jpg`;
    m = f.match(/^([ABC]\d+)_/i);                  // A01_..., B01_..., C01_... → a01.jpg
    if (m) return `${m[1].toLowerCase()}.jpg`;
    return null;
}

let total = 0;
for (const SRC of SRCS) {
    if (!fs.existsSync(SRC)) { console.log('폴더 없음(건너뜀):', SRC); continue; }
    const files = fs.readdirSync(SRC).filter(f => /\.png$/i.test(f)).sort();
    console.log(`[${SRC}] ${files.length}장 최적화`);

    for (const f of files) {
        const outName = outNameFor(f);
        if (!outName) { console.log('skip', f); continue; }
        const input = path.join(SRC, f);

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
console.log(`완료 (총 ${total}장)`);
